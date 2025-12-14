<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanMahasiswaController extends Controller
{
    // CREATE: Buat laporan baru (foto, lokasi, deskripsi)
    public function create()
    {
        return view('mahasiswa.laporan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
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
            'foto' => $fotoPath,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.laporan.index')
            ->with('success', 'Laporan berhasil dibuat dan menunggu verifikasi.');
    }

    // READ: Lihat semua laporan milik sendiri
    public function index()
    {
        $laporans = Auth::user()->laporans()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mahasiswa.laporan.index', compact('laporans'));
    }

    public function show($id)
    {
        $laporan = Auth::user()->laporans()->findOrFail($id);
        return view('mahasiswa.laporan.show', compact('laporan'));
    }

    // UPDATE: Edit laporan yang masih pending
    public function edit($id)
    {
        $laporan = Auth::user()->laporans()
            ->where('status', 'pending')
            ->findOrFail($id);

        return view('mahasiswa.laporan.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $laporan = Auth::user()->laporans()
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
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

    // DELETE: Batalkan laporan sebelum diproses
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