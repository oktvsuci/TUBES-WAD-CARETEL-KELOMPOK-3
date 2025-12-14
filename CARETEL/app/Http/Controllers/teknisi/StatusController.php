<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Penugasan;
use App\Models\Laporan;
use App\Models\StatusHistory;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        $teknisiId = Auth::id();
        
        $query = Penugasan::with(['laporan.mahasiswa', 'laporan.kategori'])
            ->where('teknisi_id', $teknisiId);
        
        if ($request->status) {
            $query->whereHas('laporan', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }
        
        $query->orderBy('updated_at', 'desc');
        
        $tugas = $query->paginate(10);
        
        return view('teknisi.status.index', compact('tugas'));
    }

    public function edit($id)
    {
        $teknisiId = Auth::id();
        
        $tugas = Penugasan::with(['laporan.mahasiswa', 'laporan.kategori'])
            ->where('teknisi_id', $teknisiId)
            ->findOrFail($id);
        
        if ($tugas->status_penerimaan !== 'diterima') {
            return redirect()->route('teknisi.status.index')
                ->with('error', 'Terima tugas dulu sebelum update status');
        }
        
        return view('teknisi.status.edit', compact('tugas'));
    }

    public function update(Request $request, $id)
    {
        $teknisiId = Auth::id();
        
        $request->validate([
            'status_baru' => 'required|in:pending,diproses,selesai',
            'keterangan' => 'nullable|string|max:500',
            'catatan_teknisi' => 'nullable|string|max:1000'
        ], [
            'status_baru.required' => 'Status baru wajib dipilih',
            'status_baru.in' => 'Status tidak valid',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
            'catatan_teknisi.max' => 'Catatan maksimal 1000 karakter'
        ]);
        
        $tugas = Penugasan::where('teknisi_id', $teknisiId)->findOrFail($id);
        $laporan = $tugas->laporan;
        
        $statusLama = $laporan->status;
        $statusBaru = $request->status_baru;
        
        $validTransitions = [
            'pending' => ['diproses'],
            'diproses' => ['selesai', 'pending'],
            'selesai' => []
        ];
        
        if (!in_array($statusBaru, $validTransitions[$statusLama])) {
            return redirect()->back()
                ->with('error', "Tidak bisa ubah status dari {$statusLama} ke {$statusBaru}");
        }
        
        DB::beginTransaction();
        try {
            $laporan->update([
                'status' => $statusBaru,
                'tanggal_selesai' => $statusBaru === 'selesai' ? now() : null
            ]);
            
            $tugas->update([
                'catatan_teknisi' => $request->catatan_teknisi
            ]);
            
            DB::table('status_histories')->insert([
                'laporan_id' => $laporan->id,
                'status_lama' => $statusLama,
                'status_baru' => $statusBaru,
                'keterangan' => $request->keterangan ?? "Status diubah oleh teknisi",
                'user_id' => $teknisiId,
                'created_at' => now()
            ]);
            
            $pesanNotif = $this->buatPesanNotifikasi($statusBaru, $laporan->kode_laporan);
            
            DB::table('notifikasi')->insert([
                'user_id' => $laporan->mahasiswa_id,
                'judul' => "Status Laporan: " . ucfirst($statusBaru),
                'pesan' => $pesanNotif,
                'tipe' => 'status_update',
                'laporan_id' => $laporan->id,
                'dibaca' => false,
                'created_at' => now()
            ]);
            
            if ($statusBaru === 'selesai') {
                $admins = DB::table('users')->where('role', 'admin')->pluck('id');
                foreach ($admins as $adminId) {
                    DB::table('notifikasi')->insert([
                        'user_id' => $adminId,
                        'judul' => 'Laporan Selesai',
                        'pesan' => "Teknisi selesai kerjakan laporan #{$laporan->kode_laporan}",
                        'tipe' => 'laporan_selesai',
                        'laporan_id' => $laporan->id,
                        'dibaca' => false,
                        'created_at' => now()
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('teknisi.status.index')
                ->with('success', 'Status berhasil diupdate ke ' . ucfirst($statusBaru));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal update status: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function createProgress(Request $request, $id)
    {
        $teknisiId = Auth::id();
        
        $request->validate([
            'keterangan' => 'required|string|min:10|max:500'
        ], [
            'keterangan.required' => 'Keterangan progress wajib diisi',
            'keterangan.min' => 'Keterangan minimal 10 karakter',
            'keterangan.max' => 'Keterangan maksimal 500 karakter'
        ]);
        
        $tugas = Penugasan::where('teknisi_id', $teknisiId)->findOrFail($id);
        $laporan = $tugas->laporan;
        
        DB::beginTransaction();
        try {
            DB::table('status_histories')->insert([
                'laporan_id' => $laporan->id,
                'status_lama' => $laporan->status,
                'status_baru' => $laporan->status,
                'keterangan' => $request->keterangan,
                'user_id' => $teknisiId,
                'created_at' => now()
            ]);
            
            DB::table('notifikasi')->insert([
                'user_id' => $laporan->mahasiswa_id,
                'judul' => 'Update Progress',
                'pesan' => "Progress laporan #{$laporan->kode_laporan}: {$request->keterangan}",
                'tipe' => 'progress_update',
                'laporan_id' => $laporan->id,
                'dibaca' => false,
                'created_at' => now()
            ]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Log progress berhasil ditambahkan');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal tambah log progress')
                ->withInput();
        }
    }

    public function history($id)
    {
        $teknisiId = Auth::id();
        
        $tugas = Penugasan::with('laporan')
            ->where('teknisi_id', $teknisiId)
            ->findOrFail($id);
        
        $histories = DB::table('status_histories')
            ->join('users', 'status_histories.user_id', '=', 'users.id')
            ->where('laporan_id', $tugas->laporan_id)
            ->select('status_histories.*', 'users.name as user_name')
            ->orderBy('status_histories.created_at', 'desc')
            ->get();
        
        return view('teknisi.status.history', compact('tugas', 'histories'));
    }

    private function buatPesanNotifikasi($status, $kodeLaporan)
    {
        $pesan = [
            'pending' => "Laporan #{$kodeLaporan} kembali ke status pending",
            'diproses' => "Laporan #{$kodeLaporan} sedang dikerjakan teknisi",
            'selesai' => "Laporan #{$kodeLaporan} sudah selesai diperbaiki. Silakan cek hasil perbaikan"
        ];
        
        return $pesan[$status] ?? "Status laporan #{$kodeLaporan} diupdate";
    }
}