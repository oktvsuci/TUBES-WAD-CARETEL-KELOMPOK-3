<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Laporan;

class DashboardMahasiswaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistik
        $stats = [
            'total' => $user->laporan()->count(),
            'pending' => $user->laporan()->where('status', 'pending')->count(),
            'diproses' => $user->laporan()->where('status', 'diproses')->count(),
            'selesai' => $user->laporan()->where('status', 'selesai')->count(),
        ];

        // Recent reports
        $recentReports = $user->laporan()
            ->with(['kategori', 'teknisi'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Chart data (12 bulan terakhir)
        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = $user->laporan()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $chartData[] = $count;
        }

        return view('mahasiswa.dashboard.index', compact('stats', 'recentReports', 'chartData'));
    }
}