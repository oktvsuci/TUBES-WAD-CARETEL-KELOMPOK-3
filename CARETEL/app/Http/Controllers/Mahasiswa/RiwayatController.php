<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
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
     * Riwayat dibuat otomatis ketika status laporan berubah menjadi "Selesai"
     * Tidak perlu method khusus, dilakukan di LaporanController atau StatusController
     */
    
    /**
     * READ - Lihat semua riwayat laporan yang sudah selesai
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $userId = Auth::id();

            // Ambil semua laporan yang statusnya "Selesai" milik user yang login
            $riwayat = Laporan::where('user_id', $userId)
                ->where('status', 'Selesai')
                ->with(['teknisi', 'rating', 'kategori']) // Eager loading untuk efisiensi
                ->orderBy('updated_at', 'desc') // Urutkan dari yang terbaru
                ->get()
                ->map(function($laporan) {
                    return [
                        'id' => $laporan->id,
                        'judul' => $laporan->judul,
                        'deskripsi' => $laporan->deskripsi,
                        'kategori' => $laporan->kategori->nama ?? 'Tidak ada kategori',
                        'lokasi' => $laporan->lokasi,
                        'tanggal_lapor' => $laporan->created_at->format('d M Y'),
                        'tanggal_selesai' => $laporan->updated_at->format('d M Y'),
                        'teknisi' => $laporan->teknisi->nama ?? 'Tidak ditugaskan',
                        'teknisi_id' => $laporan->teknisi_id,
                        'rating' => $laporan->rating->rating ?? 0,
                        'feedback' => $laporan->rating->feedback ?? null,
                        'has_rating' => $laporan->rating ? true : false,
                        'foto' => $laporan->foto ? Storage::url($laporan->foto) : null
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data riwayat berhasil diambil',
                'data' => $riwayat,
                'total' => $riwayat->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data riwayat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * READ - Lihat detail riwayat laporan tertentu
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $userId = Auth::id();

            // Ambil laporan dengan validasi kepemilikan dan status
            $laporan = Laporan::where('id', $id)
                ->where('user_id', $userId)
                ->where('status', 'Selesai')
                ->with(['teknisi', 'rating', 'kategori', 'dokumentasi'])
                ->first();

            if (!$laporan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan atau belum selesai'
                ], 404);
            }

            $data = [
                'id' => $laporan->id,
                'judul' => $laporan->judul,
                'deskripsi' => $laporan->deskripsi,
                'kategori' => $laporan->kategori->nama ?? 'Tidak ada kategori',
                'kategori_id' => $laporan->kategori_id,
                'lokasi' => $laporan->lokasi,
                'status' => $laporan->status,
                'prioritas' => $laporan->prioritas ?? 'Normal',
                'tanggal_lapor' => $laporan->created_at->format('d M Y H:i'),
                'tanggal_selesai' => $laporan->updated_at->format('d M Y H:i'),
                'durasi_penyelesaian' => $laporan->created_at->diffForHumans($laporan->updated_at),
                'teknisi' => [
                    'id' => $laporan->teknisi->id ?? null,
                    'nama' => $laporan->teknisi->nama ?? 'Tidak ditugaskan',
                    'phone' => $laporan->teknisi->phone ?? null
                ],
                'foto_before' => $laporan->foto ? Storage::url($laporan->foto) : null,
                'foto_after' => $laporan->dokumentasi->foto_after ?? null,
                'catatan_teknisi' => $laporan->dokumentasi->catatan ?? null,
                'rating' => [
                    'rating' => $laporan->rating->rating ?? 0,
                    'feedback' => $laporan->rating->feedback ?? null,
                    'created_at' => $laporan->rating ? $laporan->rating->created_at->format('d M Y H:i') : null
                ],
                'has_rating' => $laporan->rating ? true : false
            ];

            return response()->json([
                'success' => true,
                'message' => 'Detail riwayat berhasil diambil',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail riwayat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UPDATE - Beri rating dan feedback untuk perbaikan yang sudah selesai
     * 
     * @param Request $request
     * @param int $laporanId
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitRating(Request $request, $laporanId)
    {
        try {
            $userId = Auth::id();

            // Validasi input
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'feedback' => 'nullable|string|max:500'
            ]);

            // Cek apakah laporan ada, milik user, dan sudah selesai
            $laporan = Laporan::where('id', $laporanId)
                ->where('user_id', $userId)
                ->where('status', 'Selesai')
                ->first();

            if (!$laporan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan atau belum selesai'
                ], 404);
            }

            // Cek apakah sudah pernah memberi rating
            $existingRating = Rating::where('laporan_id', $laporanId)->first();

            DB::beginTransaction();

            if ($existingRating) {
                // Update rating yang sudah ada
                $existingRating->update([
                    'rating' => $request->rating,
                    'feedback' => $request->feedback,
                    'updated_at' => now()
                ]);

                $rating = $existingRating;
                $message = 'Rating berhasil diperbarui';
            } else {
                // Buat rating baru
                $rating = Rating::create([
                    'laporan_id' => $laporanId,
                    'user_id' => $userId,
                    'teknisi_id' => $laporan->teknisi_id,
                    'rating' => $request->rating,
                    'feedback' => $request->feedback
                ]);

                $message = 'Rating berhasil diberikan';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $rating->id,
                    'laporan_id' => $rating->laporan_id,
                    'rating' => $rating->rating,
                    'feedback' => $rating->feedback,
                    'created_at' => $rating->created_at->format('d M Y H:i')
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memberikan rating',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * UPDATE - Edit rating yang sudah diberikan
     * 
     * @param Request $request
     * @param int $ratingId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRating(Request $request, $ratingId)
    {
        try {
            $userId = Auth::id();

            // Validasi input
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'feedback' => 'nullable|string|max:500'
            ]);

            // Cek apakah rating milik user
            $rating = Rating::where('id', $ratingId)
                ->where('user_id', $userId)
                ->first();

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating tidak ditemukan'
                ], 404);
            }

            // Update rating
            $rating->update([
                'rating' => $request->rating,
                'feedback' => $request->feedback,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil diperbarui',
                'data' => $rating
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui rating',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET - Statistik riwayat laporan mahasiswa
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistik()
    {
        try {
            $userId = Auth::id();

            $stats = [
                'total_selesai' => Laporan::where('user_id', $userId)
                    ->where('status', 'Selesai')
                    ->count(),
                
                'sudah_rating' => Rating::where('user_id', $userId)->count(),
                
                'belum_rating' => Laporan::where('user_id', $userId)
                    ->where('status', 'Selesai')
                    ->whereDoesntHave('rating')
                    ->count(),
                
                'rata_rata_rating' => Rating::where('user_id', $userId)
                    ->avg('rating') ?? 0,
                
                'kategori_terbanyak' => Laporan::where('user_id', $userId)
                    ->where('status', 'Selesai')
                    ->select('kategori_id', DB::raw('count(*) as total'))
                    ->groupBy('kategori_id')
                    ->with('kategori')
                    ->orderBy('total', 'desc')
                    ->first()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistik berhasil diambil',
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

    /**
     * DELETE - Tidak ada (data historis tidak boleh dihapus)
     * Riwayat laporan bersifat permanen untuk keperluan audit dan tracking
     */
}