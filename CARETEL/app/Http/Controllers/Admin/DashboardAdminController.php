<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Kategori;
use App\Models\User; // Asumsi ada model User
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        // 1. DATA KARTU STATISTIK (Counter)
        $totalLaporan = Laporan::count();
        $laporanSelesai = Laporan::where('status', 'Selesai')->count();
        $laporanProses = Laporan::where('status', 'Diproses')->count();
        $laporanPending = Laporan::where('status', 'Pending')->count();

        // 2. DATA UNTUK GRAFIK (Chart)
        // Mengambil jumlah laporan per kategori
        // Hasil: [Hardware: 5, Software: 3, Jaringan: 2]
        $statsKategori = Laporan::selectRaw('kategori_id, count(*) as total')
                        ->groupBy('kategori_id')
                        ->with('kategori')
                        ->get();

        // Pisahkan label dan datanya untuk dikirim ke Chart.js
        $chartLabels = $statsKategori->map(function($item) {
            return $item->kategori->nama_kategori ?? 'Lainnya';
        });
        
        $chartData = $statsKategori->pluck('total');

        return view('admin.dashboard.index', compact(
            'totalLaporan', 
            'laporanSelesai', 
            'laporanProses', 
            'laporanPending',
            'chartLabels',
            'chartData'
        ));
    }

    // Fitur Tambahan: CETAK LAPORAN (PDF/Print View)
    public function export()
    {
        $laporans = Laporan::with(['pelapor', 'kategori', 'teknisi'])->latest()->get();
        return view('admin.dashboard.cetak', compact('laporans'));
    }
}