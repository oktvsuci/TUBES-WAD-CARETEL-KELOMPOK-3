<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        // Fitur Filter Status (Opsional, biar keren)
        $query = Laporan::with(['pelapor', 'kategori']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Ambil data terbaru dulu (latest) dan pagination 10 per halaman
        $laporans = $query->latest()->paginate(10);

        return view('admin.monitoring.index', compact('laporans'));
    }

    public function updateStatus(Request $request, $id)
    {
        // Ini fitur "Override" Admin
        $laporan = Laporan::findOrFail($id);
        
        $laporan->update([
            'status' => $request->status_baru
        ]);

        // 

        return redirect()->back()->with('success', 'Status laporan berhasil diperbarui Admin!');
    }
}