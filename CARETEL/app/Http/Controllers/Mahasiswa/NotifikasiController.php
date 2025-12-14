<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    /**
     * Constructor - Pastikan user sudah login
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * READ - Lihat semua notifikasi mahasiswa
     */
    public function index()
    {
        try {
            $userId = Auth::id();

            $notifikasi = Notifikasi::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($notif) {
                    return [
                        'id' => $notif->id,
                        'judul' => $notif->judul,
                        'pesan' => $notif->pesan,
                        'type' => $notif->type,
                        'dibaca' => $notif->is_read,
                        'laporan_id' => $notif->laporan_id,
                        'waktu_relatif' => $notif->created_at->diffForHumans(),
                        'waktu_lengkap' => $notif->created_at->format('d M Y H:i'),
                        'created_at' => $notif->created_at->toISOString()
                    ];
                });

            $unreadCount = $notifikasi->where('dibaca', false)->count();

            return response()->json([
                'success' => true,
                'message' => 'Data notifikasi berhasil diambil',
                'data' => $notifikasi,
                'unread_count' => $unreadCount,
                'total' => $notifikasi->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * READ - Lihat detail notifikasi tertentu
     */
    public function show($id)
    {
        try {
            $userId = Auth::id();

            $notifikasi = Notifikasi::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$notifikasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            if (!$notifikasi->is_read) {
                $notifikasi->is_read = true;
                $notifikasi->read_at = now();
                $notifikasi->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail notifikasi berhasil diambil',
                'data' => $notifikasi
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UPDATE - Tandai notifikasi sebagai sudah dibaca
     */
    public function markAsRead($id)
    {
        try {
            $userId = Auth::id();

            $notifikasi = Notifikasi::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$notifikasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            $notifikasi->is_read = true;
            $notifikasi->read_at = now();
            $notifikasi->save();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UPDATE - Tandai semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        try {
            $userId = Auth::id();

            $updated = Notifikasi::where('user_id', $userId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sudah dibaca',
                'total_updated' => $updated
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE - Hapus notifikasi lama
     */
    public function destroy($id)
    {
        try {
            $userId = Auth::id();

            $notifikasi = Notifikasi::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$notifikasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            $notifikasi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE - Hapus semua notifikasi yang sudah dibaca
     */
    public function deleteRead()
    {
        try {
            $userId = Auth::id();

            $deleted = Notifikasi::where('user_id', $userId)
                ->where('is_read', true)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "$deleted notifikasi berhasil dihapus"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET - Hitung jumlah notifikasi belum dibaca
     */
    public function unreadCount()
    {
        try {
            $userId = Auth::id();

            $count = Notifikasi::where('user_id', $userId)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $count
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}