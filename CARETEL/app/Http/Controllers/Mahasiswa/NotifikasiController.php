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
     * CREATE - Otomatis dari sistem
     * Notifikasi dibuat otomatis melalui Event & Listener atau Observer
     * Contoh: Ketika status laporan berubah, admin assign teknisi, dll
     */
    
    /**
     * READ - Lihat semua notifikasi mahasiswa
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $userId = Auth::id();

            // Ambil semua notifikasi user yang login
            $notifikasi = Notifikasi::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($notif) {
                    return [
                        'id' => $notif->id,
                        'judul' => $notif->judul,
                        'pesan' => $notif->pesan,
                        'type' => $notif->type, // success, info, warning, error
                        'dibaca' => $notif->is_read,
                        'laporan_id' => $notif->laporan_id,
                        'waktu_relatif' => $notif->created_at->diffForHumans(), // "2 jam lalu"
                        'waktu_lengkap' => $notif->created_at->format('d M Y H:i'),
                        'created_at' => $notif->created_at->toISOString()
                    ];
                });

            // Hitung jumlah notifikasi belum dibaca
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
     * Auto mark as read ketika dibuka
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $userId = Auth::id();

            // Ambil notifikasi dengan validasi kepemilikan
            $notifikasi = Notifikasi::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$notifikasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            // Auto mark as read ketika notifikasi dibuka
            if (!$notifikasi->is_read) {
                $notifikasi->is_read = true;
                $notifikasi->read_at = now();
                $notifikasi->save();
            }

            $data = [
                'id' => $notifikasi->id,
                'judul' => $notifikasi->judul,
                'pesan' => $notifikasi->pesan,
                'type' => $notifikasi->type,
                'dibaca' => $notifikasi->is_read,
                'laporan_id' => $notifikasi->laporan_id,
                'waktu_relatif' => $notifikasi->created_at->diffForHumans(),
                'waktu_lengkap' => $notifikasi->created_at->format('d M Y H:i'),
                'dibaca_pada' => $notifikasi->read_at ? $notifikasi->read_at->format('d M Y H:i') : null
            ];

            return response()->json([
                'success' => true,
                'message' => 'Detail notifikasi berhasil diambil',
                'data' => $data
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
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        try {
            $userId = Auth::id();

            // Cari notifikasi milik user
            $notifikasi = Notifikasi::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$notifikasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            // Update status menjadi sudah dibaca
            $notifikasi->is_read = true;
            $notifikasi->read_at = now();
            $notifikasi->save();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca',
                'data' => [
                    'id' => $notifikasi->id,
                    'is_read' => $notifikasi->is_read,
                    'read_at' => $notifikasi->read_at->format('d M Y H:i')
                ]
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
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        try {
            $userId = Auth::id();

            // Update semua notifikasi yang belum dibaca
            $updated = Notifikasi::where('user_id', $userId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => "Semua notifikasi ditandai sudah dibaca",
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
     * UPDATE - Tandai notifikasi sebagai belum dibaca (unread)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsUnread($id)
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

            // Update status menjadi belum dibaca
            $notifikasi->is_read = false;
            $notifikasi->read_at = null;
            $notifikasi->save();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai belum dibaca',
                'data' => [
                    'id' => $notifikasi->id,
                    'is_read' => $notifikasi->is_read
                ]
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
     * DELETE - Hapus notifikasi lama
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $userId = Auth::id();

            // Cari notifikasi milik user
            $notifikasi = Notifikasi::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$notifikasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            // Hapus notifikasi
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
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRead()
    {
        try {
            $userId = Auth::id();

            // Hapus semua notifikasi yang sudah dibaca
            $deleted = Notifikasi::where('user_id', $userId)
                ->where('is_read', true)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "$deleted notifikasi berhasil dihapus",
                'total_deleted' => $deleted
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
     * DELETE - Hapus semua notifikasi (read & unread)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAll()
    {
        try {
            $userId = Auth::id();

            // Hapus semua notifikasi user
            $deleted = Notifikasi::where('user_id', $userId)->delete();

            return response()->json([
                'success' => true,
                'message' => "Semua notifikasi berhasil dihapus",
                'total_deleted' => $deleted
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
     * DELETE - Hapus notifikasi lama (lebih dari X hari)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteOld(Request $request)
    {
        try {
            $userId = Auth::id();
            $days = $request->input('days', 30); // Default 30 hari

            // Hapus notifikasi lebih dari X hari
            $deleted = Notifikasi::where('user_id', $userId)
                ->where('created_at', '<', now()->subDays($days))
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Notifikasi lebih dari $days hari berhasil dihapus",
                'total_deleted' => $deleted
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi lama',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET - Hitung jumlah notifikasi belum dibaca
     * 
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * GET - Filter notifikasi berdasarkan type
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterByType(Request $request)
    {
        try {
            $userId = Auth::id();
            $type = $request->input('type'); // success, info, warning, error

            $query = Notifikasi::where('user_id', $userId);

            if ($type) {
                $query->where('type', $type);
            }

            $notifikasi = $query->orderBy('created_at', 'desc')
                ->get()
                ->map(function($notif) {
                    return [
                        'id' => $notif->id,
                        'judul' => $notif->judul,
                        'pesan' => $notif->pesan,
                        'type' => $notif->type,
                        'dibaca' => $notif->is_read,
                        'waktu_relatif' => $notif->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil difilter',
                'data' => $notifikasi,
                'total' => $notifikasi->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memfilter notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET - Statistik notifikasi
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistik()
    {
        try {
            $userId = Auth::id();

            $stats = [
                'total' => Notifikasi::where('user_id', $userId)->count(),
                'unread' => Notifikasi::where('user_id', $userId)->where('is_read', false)->count(),
                'read' => Notifikasi::where('user_id', $userId)->where('is_read', true)->count(),
                'by_type' => [
                    'success' => Notifikasi::where('user_id', $userId)->where('type', 'success')->count(),
                    'info' => Notifikasi::where('user_id', $userId)->where('type', 'info')->count(),
                    'warning' => Notifikasi::where('user_id', $userId)->where('type', 'warning')->count(),
                    'error' => Notifikasi::where('user_id', $userId)->where('type', 'error')->count(),
                ],
                'today' => Notifikasi::where('user_id', $userId)
                    ->whereDate('created_at', today())
                    ->count(),
                'this_week' => Notifikasi::where('user_id', $userId)
                    ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistik notifikasi berhasil diambil',
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}