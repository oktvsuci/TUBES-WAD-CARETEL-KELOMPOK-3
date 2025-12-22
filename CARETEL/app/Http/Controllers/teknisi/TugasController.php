<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Penugasan;
use App\Models\Laporan;
use App\Models\User;
use App\Models\StatusHistory;
use App\Models\Notifikasi;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $teknisiId = Auth::id();
        
        $query = Penugasan::with(['laporan.user', 'laporan.kategori'])  // ✅ FIXED
            ->where('teknisi_id', $teknisiId);
        
        if ($request->status) {
            $query->whereHas('laporan', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }
        
        if ($request->prioritas) {
            $query->where('prioritas', $request->prioritas);
        }
        
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('laporan', function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }
        
        $query->orderBy('created_at', 'desc');
        
        $tugas = $query->paginate(10);
        
        $statistik = [
            'total' => Penugasan::where('teknisi_id', $teknisiId)->count(),
            'pending' => Penugasan::where('teknisi_id', $teknisiId)
                ->whereHas('laporan', function($q) {
                    $q->where('status', 'pending');
                })->count(),
            'diproses' => Penugasan::where('teknisi_id', $teknisiId)
                ->whereHas('laporan', function($q) {
                    $q->where('status', 'diproses');
                })->count(),
            'selesai' => Penugasan::where('teknisi_id', $teknisiId)
                ->whereHas('laporan', function($q) {
                    $q->where('status', 'selesai');
                })->count(),
        ];
        
        return view('teknisi.tugas.index', compact('tugas', 'statistik'));
    }

    public function show($id)
    {
        $teknisiId = Auth::id();
        
        $tugas = Penugasan::with([
            'laporan.user',  // ✅ FIXED
            'laporan.kategori',
            'laporan.statusHistory'  // ✅ FIXED: plural
        ])
        ->where('teknisi_id', $teknisiId)
        ->findOrFail($id);
        
        return view('teknisi.tugas.show', compact('tugas'));
    }

    public function updateEstimasi(Request $request, $id)
    {
        $teknisiId = Auth::id();
        
        $request->validate([
            'estimasi_selesai' => 'required|date|after:now',
            'catatan_estimasi' => 'nullable|string|max:500'
        ], [
            'estimasi_selesai.required' => 'Estimasi waktu wajib diisi',
            'estimasi_selesai.date' => 'Format tanggal salah',
            'estimasi_selesai.after' => 'Estimasi harus di masa depan',
            'catatan_estimasi.max' => 'Catatan maksimal 500 karakter'
        ]);
        
        $tugas = Penugasan::where('teknisi_id', $teknisiId)->findOrFail($id);
        
        DB::beginTransaction();
        try {
            $tugas->update([
                'estimasi_waktu' => $request->estimasi_selesai,  // ✅ Sesuaikan dengan fillable
                'catatan' => $request->catatan_estimasi
            ]);
            
            DB::commit();
            
            return redirect()->route('teknisi.tugas.show', $id)
                ->with('success', 'Estimasi waktu berhasil diupdate');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal update estimasi')
                ->withInput();
        }
    }

    public function terima(Request $request, $id)
    {
        $teknisiId = Auth::id();
        
        $tugas = Penugasan::where('teknisi_id', $teknisiId)->findOrFail($id);
        
        if ($tugas->laporan->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Tugas sudah tidak pending');
        }
        
        DB::beginTransaction();
        try {
            $laporan = $tugas->laporan;
            
            $laporan->update([
                'status' => 'diproses',
            ]);
            
            $tugas->update([
                'status' => 'diterima',  // ✅ Update status penugasan
            ]);
            
            StatusHistory::create([
                'laporan_id' => $laporan->id,
                'status_lama' => 'pending',
                'status_baru' => 'diproses',
                'keterangan' => 'Tugas diterima teknisi',
                'user_id' => $teknisiId,
            ]);
            
            Notifikasi::create([
                'user_id' => $laporan->user_id,  // ✅ FIXED
                'judul' => 'Laporan Diproses',
                'pesan' => "Laporan #{$laporan->id} sedang dikerjakan teknisi",
                'type' => 'status_update',
                'laporan_id' => $laporan->id,
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('teknisi.tugas.show', $id)
                ->with('success', 'Tugas berhasil diterima');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal terima tugas: ' . $e->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        $teknisiId = Auth::id();
        
        $request->validate([
            'alasan_penolakan' => 'required|string|min:10|max:500'
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi',
            'alasan_penolakan.min' => 'Alasan minimal 10 karakter',
            'alasan_penolakan.max' => 'Alasan maksimal 500 karakter'
        ]);
        
        $tugas = Penugasan::where('teknisi_id', $teknisiId)->findOrFail($id);
        
        DB::beginTransaction();
        try {
            $tugas->update([
                'status' => 'ditolak',
                'catatan' => $request->alasan_penolakan,
            ]);
            
            $admins = User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                Notifikasi::create([
                    'user_id' => $admin->id,
                    'judul' => 'Penugasan Ditolak',
                    'pesan' => "Teknisi tolak laporan #{$tugas->laporan->id}. Alasan: {$request->alasan_penolakan}",
                    'type' => 'penugasan_ditolak',
                    'laporan_id' => $tugas->laporan_id,
                    'is_read' => false,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('teknisi.tugas.index')
                ->with('success', 'Tugas berhasil ditolak');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal tolak tugas')
                ->withInput();
        }
    }
}