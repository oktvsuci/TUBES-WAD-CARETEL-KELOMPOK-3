<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\Penugasan;
use Illuminate\Support\Facades\Auth;

class DashboardTeknisiController extends Controller
{
    public function index()
    {
        $idTeknisi = Auth::id();

        $tugasSelesai = Penugasan::where('teknisi_id', $idTeknisi)
            ->where('status', 'selesai')
            ->count();

        $tugasProses = Penugasan::where('teknisi_id', $idTeknisi)
            ->where('status', 'diterima')
            ->count();

        $tugasPending = Penugasan::where('teknisi_id', $idTeknisi)
            ->where('status', 'pending')
            ->count();

        return view('teknisi.dashboard.index', compact('tugasSelesai', 'tugasProses', 'tugasPending'));
    }
}