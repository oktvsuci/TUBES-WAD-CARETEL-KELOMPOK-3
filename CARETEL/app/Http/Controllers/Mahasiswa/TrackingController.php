<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    /**
     * INDEX: Tracking semua laporan dengan filter status
     */
    public function index(Request $request)
    {
        $query = Auth::user()->laporans()
            ->with(['kategori', 'penugasan.teknisi']);
        
        // Filter by status
        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        $laporans = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Stats
        $stats = [
            'active' => Auth::user()->laporans()
                ->whereNotIn('status', ['selesai', 'ditolak'])
                ->count(),
            'pending' => Auth::user()->laporans()
                ->where('status', 'pending')
                ->count(),
            'progress' => Auth::user()->laporans()
                ->where('status', 'diproses')
                ->count(),
            'completed' => Auth::user()->laporans()
                ->where('status', 'selesai')
                ->count(),
        ];
        
        return view('mahasiswa.tracking.index', compact('laporans', 'stats'));
    }

    /**
     * SHOW: Detail tracking laporan dengan history lengkap
     */
    public function show($id)
    {
        $laporan = Auth::user()->laporans()
            ->with([
                'kategori',
                'penugasan.teknisi', // ← Teknisi lewat penugasan
                'statusHistories.user' // ← Plural
            ])
            ->findOrFail($id);
        
        // Hitung response time (waktu dari pending ke diproses)
        $responseTime = '-';
        if ($laporan->status != 'pending') {
            $firstProcess = $laporan->statusHistories()
                ->where('status', 'diproses')
                ->first();
            
            if ($firstProcess) {
                $hours = $laporan->created_at->diffInHours($firstProcess->created_at);
                $responseTime = $hours . ' jam';
            }
        }
        
        // Stats untuk detail page
        $stats = [
            'total_updates' => $laporan->statusHistories->count(),
            'comments' => $laporan->statusHistories()
                ->whereNotNull('catatan')
                ->count(),
            'days_open' => $laporan->created_at->diffInDays(now()),
            'response_time' => $responseTime,
        ];
        
        // History dengan user yang membuat update
        $history = $laporan->statusHistories()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('mahasiswa.tracking.show', compact('laporan', 'stats', 'history'));
    }
}