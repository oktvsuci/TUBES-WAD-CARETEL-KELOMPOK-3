<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan; // ⚠️ PENTING: Sesuaikan nama ini dengan Model buatan temanmu (misal: Report/Pengaduan)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokumentasiController extends Controller
{
    /**
     * Menampilkan daftar tugas yang sedang diproses dan butuh dokumentasi.
     */
    public function index()
    {
        // Ambil laporan yang statusnya 'Diproses' milik teknisi yang sedang login
        $tugasAktif = Laporan::where('teknisi_id', Auth::id())
                             ->where('status', 'Diproses')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return view('teknisi.dokumentasi.index', compact('tugasAktif'));
    }

    /**
     * Menampilkan form untuk upload bukti perbaikan.
     */
    public function edit($id)
    {
        $laporan = Laporan::findOrFail($id);

        // Keamanan: Pastikan teknisi yang login adalah pemilik tugas ini
        if ($laporan->teknisi_id != Auth::id()) {
            return abort(403, 'Akses Ditolak. Ini bukan tugas Anda.');
        }

        return view('teknisi.dokumentasi.edit', compact('laporan'));
    }

    /**
     * Proses simpan foto bukti dan update status jadi 'Selesai'.
     */
    public function update(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'foto_perbaikan'  => 'required|image|mimes:jpeg,png,jpg|max:5120', // Wajib foto, max 5MB
            'catatan_teknisi' => 'required|string|min:10', // Catatan minimal 10 karakter
        ]);

        // 2. Cari Laporan
        $laporan = Laporan::findOrFail($id);

        // Pastikan teknisi yang login berhak mengedit
        if ($laporan->teknisi_id != Auth::id()) {
            return abort(403, 'Akses Ditolak.');
        }

        // 3. Proses Upload Foto
        if ($request->hasFile('foto_perbaikan')) {
            // Simpan file ke folder 'public/bukti_perbaikan'
            $path = $request->file('foto_perbaikan')->store('bukti_perbaikan', 'public');
            
            // Simpan path gambar ke database
            $laporan->foto_after = $path; 
        }

        // 4. Update Data Lainnya
        $laporan->catatan_teknisi = $request->catatan_teknisi;
        $laporan->status = 'Selesai'; // Ubah status tugas jadi Selesai
        $laporan->tanggal_selesai = now(); // Catat waktu selesai
        
        // Simpan perubahan
        $laporan->save();

        // 5. Kembali ke halaman index dengan pesan sukses
        return redirect()->route('teknisi.dokumentasi.index')
                         ->with('success', 'Laporan berhasil diselesaikan dan didokumentasikan!');
    }
}