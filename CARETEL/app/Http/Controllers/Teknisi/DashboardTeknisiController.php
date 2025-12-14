<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan; // ⚠️ PENTING: Pastikan nama Model ini sesuai dengan database tim kamu
use Illuminate\Support\Facades\Auth;

class DashboardTeknisiController extends Controller
{
    /**
     * Menampilkan dashboard teknisi dengan statistik kinerja.
     */
    public function index()
    {
        // 1. Ambil ID Teknisi yang sedang login
        $idTeknisi = Auth::id();

        // 2. Hitung jumlah tugas yang SUDAH SELESAI dikerjakan
        $tugasSelesai = Laporan::where('teknisi_id', $idTeknisi)
                               ->where('status', 'Selesai')
                               ->count();

        // 3. Hitung jumlah tugas yang SEDANG DIPROSES
        $tugasProses = Laporan::where('teknisi_id', $idTeknisi)
                              ->where('status', 'Diproses')
                              ->count();

        // 4. Hitung jumlah tugas BARU (Pending) yang belum disentuh
        $tugasPending = Laporan::where('teknisi_id', $idTeknisi)
                               ->where('status', 'Pending')
                               ->count();

        // 5. Kirim data statistik ke View
        return view('teknisi.dashboard.index', compact('tugasSelesai', 'tugasProses', 'tugasPending'));
    }
}