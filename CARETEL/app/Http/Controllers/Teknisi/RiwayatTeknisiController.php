<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan; // ⚠️ PENTING: Sesuaikan nama Model dengan teman kelompokmu
use Illuminate\Support\Facades\Auth;

class RiwayatTeknisiController extends Controller
{
    /**
     * 1. Menampilkan daftar pekerjaan yang SUDAH SELESAI.
     */
    public function index()
    {
        // Ambil data laporan milik teknisi login yang statusnya 'Selesai'
        // Diurutkan dari yang paling baru selesai (descending)
        $riwayat = Laporan::where('teknisi_id', Auth::id())
                          ->where('status', 'Selesai')
                          ->orderBy('created_at', 'desc') // Atau 'tanggal_selesai' jika kolom itu ada
                          ->paginate(10); // Batasi 10 data per halaman (pagination)

        return view('teknisi.riwayat.index', compact('riwayat'));
    }

    /**
     * 2. Menampilkan detail lengkap satu riwayat pekerjaan.
     */
    public function show($id)
    {
        $laporan = Laporan::findOrFail($id);

        // Keamanan: Pastikan teknisi cuma bisa lihat riwayat kerjanya sendiri
        if ($laporan->teknisi_id != Auth::id()) {
            return abort(403, 'Akses Ditolak.');
        }

        return view('teknisi.riwayat.show', compact('laporan'));
    }

    /**
     * 3. (Opsional) Mengupdate catatan pada pekerjaan yang sudah selesai.
     * Sesuai tugas modul B: "UPDATE: Lengkapi detail perbaikan"
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'catatan_tambahan' => 'required|string|max:500',
        ]);

        $laporan = Laporan::findOrFail($id);

        // Cek kepemilikan
        if ($laporan->teknisi_id != Auth::id()) {
            return abort(403);
        }

        // Update: Tambahkan teks baru ke catatan lama
        $laporan->catatan_teknisi = $laporan->catatan_teknisi . "\n[Update]: " . $request->catatan_tambahan;
        $laporan->save();

        return redirect()->back()->with('success', 'Catatan riwayat berhasil diperbarui.');
    }
}