<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\User;
use App\Models\Kategori;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LaporanAdminController extends Controller
{
    /**
     * Display a listing of all reports (READ)
     */
    public function index(Request $request)
    {
        $query = Laporan::with(['user', 'kategori', 'teknisi']);
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by kategori
        if ($request->has('kategori_id') && $request->kategori_id != 'all') {
            $query->where('kategori_id', $request->kategori_id);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $laporan = $query->paginate(10);
        $kategoris = Kategori::all();
        
        return view('admin.laporan.index', compact('laporan', 'kategoris'));
    }

    /**
     * Show the form for creating a new report (CREATE - Form)
     */
    public function create()
    {
        $kategoris = Kategori::all();
        $users = User::where('role', 'mahasiswa')->get();
        
        return view('admin.laporan.create', compact('kategoris', 'users'));
    }

    /**
     * Store a newly created report (CREATE - Store)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'kategori_id' => 'required|exists:kategoris,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['status'] = 'pending';
        $data['created_by_admin'] = true;
        
        // Handle file upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('laporan', $filename, 'public');
            $data['foto'] = $path;
        }
        
        Laporan::create($data);
        
        return redirect()->route('admin.laporan.index')
            ->with('success', 'Laporan berhasil dibuat secara manual.');
    }

    /**
     * Display the specified report (READ - Detail)
     */
    public function show($id)
    {
        $laporan = Laporan::with(['user', 'kategori', 'teknisi', 'statusHistory'])
            ->findOrFail($id);
        
        return view('admin.laporan.show', compact('laporan'));
    }

    /**
     * Show the form for editing the report (UPDATE - Form)
     */
    public function edit($id)
    {
        $laporan = Laporan::findOrFail($id);
        $kategoris = Kategori::all();
        $users = User::where('role', 'mahasiswa')->get();
        
        return view('admin.laporan.edit', compact('laporan', 'kategoris', 'users'));
    }

    /**
     * Update the specified report (UPDATE - Store)
     */
    public function update(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required|exists:kategoris,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'prioritas' => 'required|in:rendah,sedang,tinggi,darurat',
            'status' => 'required|in:pending,diproses,selesai,ditolak',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Handle file upload
        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($laporan->foto && Storage::disk('public')->exists($laporan->foto)) {
                Storage::disk('public')->delete($laporan->foto);
            }
            
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('laporan', $filename, 'public');
            $data['foto'] = $path;
        }
        
        $laporan->update($data);
        
        return redirect()->route('admin.laporan.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Remove the specified report (DELETE)
     */
    public function destroy($id)
    {
        $laporan = Laporan::findOrFail($id);
        
        // Delete photo if exists
        if ($laporan->foto && Storage::disk('public')->exists($laporan->foto)) {
            Storage::disk('public')->delete($laporan->foto);
        }
        
        $laporan->delete();
        
        return redirect()->route('admin.laporan.index')
            ->with('success', 'Laporan spam/duplikat berhasil dihapus.');
    }

    /**
     * Bulk delete reports (DELETE - Multiple)
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada laporan yang dipilih'
            ], 400);
        }
        
        $laporan = Laporan::whereIn('id', $ids)->get();
        
        foreach ($laporan as $item) {
            if ($item->foto && Storage::disk('public')->exists($item->foto)) {
                Storage::disk('public')->delete($item->foto);
            }
            $item->delete();
        }
        
        return response()->json([
            'success' => true,
            'message' => count($ids) . ' laporan berhasil dihapus'
        ]);
    }

    /**
     * Export reports to CSV
     */
    public function export(Request $request)
    {
        $query = Laporan::with(['user', 'kategori', 'teknisi']);
        
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        $laporan = $query->get();
        
        $filename = 'laporan_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($laporan) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Pelapor', 'Kategori', 'Judul', 'Lokasi', 'Prioritas', 'Status', 'Teknisi', 'Tanggal']);
            
            foreach ($laporan as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->user->name ?? '-',
                    $item->kategori->nama ?? '-',
                    $item->judul,
                    $item->lokasi,
                    $item->prioritas,
                    $item->status,
                    $item->teknisi->name ?? '-',
                    $item->created_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}