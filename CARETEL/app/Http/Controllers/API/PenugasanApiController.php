<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penugasan;

class PenugasanApiController extends Controller
{
    // GET: semua tugas
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Penugasan::with(['laporan'])->get()
        ], 200);
    }

    // POST: buat tugas
    public function store(Request $request)
    {
        $request->validate([
            'laporan_id' => 'required',
            'teknisi_id' => 'required',
            'status' => 'required'
        ]);

        $penugasan = Penugasan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Penugasan berhasil dibuat',
            'data' => $penugasan
        ], 201);
    }

    // GET: detail tugas
    public function show($id)
    {
        $penugasan = Penugasan::find($id);

        if (!$penugasan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($penugasan, 200);
    }

    // PUT: update status
    public function update(Request $request, $id)
    {
        $penugasan = Penugasan::find($id);

        if (!$penugasan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $penugasan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Penugasan berhasil diupdate',
            'data' => $penugasan
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        Penugasan::destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Penugasan dihapus'
        ]);
    }
}