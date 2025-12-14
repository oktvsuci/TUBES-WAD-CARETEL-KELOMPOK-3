<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardMahasiswaController extends Controller
{
    // CREATE: -
    // Tidak ada fitur create untuk dashboard

    // READ: Lihat statistik laporan sendiri
    public function index()
    {
        $user = Auth::user();

        // Statistik laporan
        $totalLaporan = $user->laporans()->count();
        $laporanPending = $user->laporans()->where('status', 'pending')->count();
        $laporanDiproses = $user->laporans()->where('status', 'diproses')->count();
        $laporanSelesai = $user->laporans()->where('status', 'selesai')->count();
        $laporanDitolak = $user->laporans()->where('status', 'ditolak')->count();

        // Laporan terbaru
        $laporanTerbaru = $user->laporans()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Statistik per bulan (6 bulan terakhir)
        $statistikBulanan = $user->laporans()
            ->select(
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        // Statistik per kategori/lokasi
        $statistikLokasi = $user->laporans()
            ->select('lokasi', DB::raw('COUNT(*) as total'))
            ->groupBy('lokasi')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        return view('mahasiswa.dashboard.index', compact(
            'totalLaporan',
            'laporanPending',
            'laporanDiproses',
            'laporanSelesai',
            'laporanDitolak',
            'laporanTerbaru',
            'statistikBulanan',
            'statistikLokasi'
        ));
    }

    // UPDATE: -
    // Tidak ada fitur update untuk dashboard

    // DELETE: -
    // Tidak ada fitur delete untuk dashboard
}