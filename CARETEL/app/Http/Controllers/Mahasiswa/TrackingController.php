<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    // CREATE: - (otomatis)
    // Status tracking dibuat otomatis saat laporan dibuat

    // READ: Melacak status laporan real-time
    public function index()
    {
        $laporans = Auth::user()->laporans()
            ->with('statusHistories')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mahasiswa.tracking.index', compact('laporans'));
    }

    public function show($id)
    {
        $laporan = Auth::user()->laporans()
            ->with(['statusHistories' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->findOrFail($id);

        return view('mahasiswa.tracking.show', compact('laporan'));
    }

    // UPDATE: Tambah catatan/komentar
    public function addComment(Request $request, $id)
    {
        $laporan = Auth::user()->laporans()->findOrFail($id);

        $request->validate([
            'komentar' => 'required|string',
        ]);

        $laporan->komentars()->create([
            'user_id' => Auth::id(),
            'komentar' => $request->komentar,
        ]);

        return redirect()->back()
            ->with('success', 'Komentar berhasil ditambahkan.');
    }

    // DELETE: -
    // Tidak ada fitur delete untuk tracking
}