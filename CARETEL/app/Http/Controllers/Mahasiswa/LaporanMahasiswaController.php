<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Kategori;

class LaporanMahasiswaController extends Controller
{
    // CREATE: Form laporan baru
    public function create()
    {
        $kategoris = Kategori::where('is_active', true)->get();
        return view('mahasiswa.laporan.create', compact('kategoris'));
    }

    // STORE: Simpan laporan baru
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('laporan', 'public');
        }

        $laporan = Auth::user()->laporans()->create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
            'kategori_id' => $request->kategori_id,
            'foto' => $fotoPath,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.laporan.index')
            ->with('success', 'Laporan berhasil dibuat dan menunggu verifikasi.');
    }

    // INDEX: Lihat semua laporan milik sendiri
    public function index()
    {
        $laporans = Auth::user()->laporans()
            ->with('kategori')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mahasiswa.laporan.index', compact('laporans'));
    }

    // SHOW: Detail laporan
    public function show($id)
    {
        $laporan = Auth::user()->laporans()
            ->with(['kategori', 'penugasan.teknisi', 'statusHistories.user'])
            ->findOrFail($id);
        
        return view('mahasiswa.laporan.show', compact('laporan'));
    }

    // EDIT: Form edit laporan (hanya status pending)
    public function edit($id)
    {
        $laporan = Auth::user()->laporans()
            ->where('status', 'pending')
            ->findOrFail($id);
        
        $kategoris = Kategori::where('is_active', true)->get();
        
        return view('mahasiswa.laporan.edit', compact('laporan', 'kategoris'));
    }

    // UPDATE: Update laporan (hanya status pending)
    public function update(Request $request, $id)
    {
        $laporan = Auth::user()->laporans()
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
            'kategori_id' => $request->kategori_id,
        ];

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($laporan->foto) {
                Storage::disk('public')->delete($laporan->foto);
            }
            $data['foto'] = $request->file('foto')->store('laporan', 'public');
        }

        $laporan->update($data);

        return redirect()->route('mahasiswa.laporan.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    // DESTROY: Batalkan laporan (hanya status pending)
    public function destroy($id)
    {
        $laporan = Auth::user()->laporans()
            ->where('status', 'pending')
            ->findOrFail($id);

        // Hapus foto
        if ($laporan->foto) {
            Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        return redirect()->route('mahasiswa.laporan.index')
            ->with('success', 'Laporan berhasil dibatalkan.');
    }
}