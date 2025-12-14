<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // 1. READ: Tampilkan semua kategori
    public function index()
    {
        $kategoris = Kategori::all();
        return view('admin.kategori.index', compact('kategoris'));
    }

    // 2. CREATE: Simpan kategori baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        // Simpan ke database
        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
            'icon' => 'fa-folder', // Default icon dulu
            'is_active' => 1
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    // 3. UPDATE: Update data
    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil diupdate!');
    }

    // 4. DELETE: Hapus data
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}