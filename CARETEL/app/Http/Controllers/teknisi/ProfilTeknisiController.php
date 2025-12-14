<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProfilTeknisiController extends Controller
{
    public function index()
    {
        $teknisi = Auth::user();
        
        $statistik = [
            'total_tugas' => DB::table('penugasan')
                ->where('teknisi_id', $teknisi->id)
                ->count(),
            
            'tugas_selesai' => DB::table('penugasan')
                ->join('laporan', 'penugasan.laporan_id', '=', 'laporan.id')
                ->where('penugasan.teknisi_id', $teknisi->id)
                ->where('laporan.status', 'selesai')
                ->count(),
            
            'tugas_dikerjakan' => DB::table('penugasan')
                ->join('laporan', 'penugasan.laporan_id', '=', 'laporan.id')
                ->where('penugasan.teknisi_id', $teknisi->id)
                ->where('laporan.status', 'diproses')
                ->count(),
            
            'rating_rata' => DB::table('rating')
                ->join('laporan', 'rating.laporan_id', '=', 'laporan.id')
                ->join('penugasan', 'laporan.id', '=', 'penugasan.laporan_id')
                ->where('penugasan.teknisi_id', $teknisi->id)
                ->avg('rating.rating') ?? 0
        ];
        
        return view('teknisi.profil.index', compact('teknisi', 'statistik'));
    }

    public function edit()
    {
        $teknisi = Auth::user();
        
        return view('teknisi.profil.edit', compact('teknisi'));
    }

    public function update(Request $request)
    {
        $teknisi = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teknisi->id,
            'phone' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:500',
            'keahlian' => 'nullable|string|max:255',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'phone.max' => 'Nomor telepon maksimal 15 karakter',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'keahlian.max' => 'Keahlian maksimal 255 karakter',
            'foto_profil.image' => 'File harus berupa gambar',
            'foto_profil.mimes' => 'Format gambar: jpeg, png, jpg',
            'foto_profil.max' => 'Ukuran gambar maksimal 2MB'
        ]);
        
        DB::beginTransaction();
        try {
            $dataUpdate = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'alamat' => $request->alamat,
                'keahlian' => $request->keahlian,
                'updated_at' => now()
            ];
            
            if ($request->hasFile('foto_profil')) {
                if ($teknisi->foto_profil && Storage::disk('public')->exists($teknisi->foto_profil)) {
                    Storage::disk('public')->delete($teknisi->foto_profil);
                }
                
                $fotoPath = $request->file('foto_profil')->store('profil', 'public');
                $dataUpdate['foto_profil'] = $fotoPath;
            }
            
            $teknisi->update($dataUpdate);
            
            DB::table('log_aktivitas')->insert([
                'user_id' => $teknisi->id,
                'aktivitas' => 'Update Profil',
                'deskripsi' => 'Memperbarui data profil teknisi',
                'ip_address' => $request->ip(),
                'created_at' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('teknisi.profil.index')
                ->with('success', 'Profil berhasil diupdate');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal update profil: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editPassword()
    {
        return view('teknisi.profil.edit-password');
    }

    public function updatePassword(Request $request)
    {
        $teknisi = Auth::user();
        
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed',
            'password_baru_confirmation' => 'required'
        ], [
            'password_lama.required' => 'Password lama wajib diisi',
            'password_baru.required' => 'Password baru wajib diisi',
            'password_baru.min' => 'Password minimal 8 karakter',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok',
            'password_baru_confirmation.required' => 'Konfirmasi password wajib diisi'
        ]);
        
        if (!Hash::check($request->password_lama, $teknisi->password)) {
            return redirect()->back()
                ->with('error', 'Password lama salah')
                ->withInput();
        }
        
        if (Hash::check($request->password_baru, $teknisi->password)) {
            return redirect()->back()
                ->with('error', 'Password baru tidak boleh sama dengan password lama')
                ->withInput();
        }
        
        DB::beginTransaction();
        try {
            $teknisi->update([
                'password' => Hash::make($request->password_baru)
            ]);
            
            DB::table('log_aktivitas')->insert([
                'user_id' => $teknisi->id,
                'aktivitas' => 'Ganti Password',
                'deskripsi' => 'Mengubah password akun',
                'ip_address' => $request->ip(),
                'created_at' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('teknisi.profil.index')
                ->with('success', 'Password berhasil diubah');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal ubah password: ' . $e->getMessage());
        }
    }

    public function deleteFoto()
    {
        $teknisi = Auth::user();
        
        if (!$teknisi->foto_profil) {
            return redirect()->back()
                ->with('error', 'Tidak ada foto profil');
        }
        
        DB::beginTransaction();
        try {
            if (Storage::disk('public')->exists($teknisi->foto_profil)) {
                Storage::disk('public')->delete($teknisi->foto_profil);
            }
            
            $teknisi->update([
                'foto_profil' => null
            ]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Foto profil berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal hapus foto profil');
        }
    }

    public function aktivitas()
    {
        $teknisi = Auth::user();
        
        $aktivitas = DB::table('log_aktivitas')
            ->where('user_id', $teknisi->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('teknisi.profil.aktivitas', compact('aktivitas'));
    }

    public function rating()
    {
        $teknisi = Auth::user();
        
        $ratings = DB::table('rating')
            ->join('laporan', 'rating.laporan_id', '=', 'laporan.id')
            ->join('penugasan', 'laporan.id', '=', 'penugasan.laporan_id')
            ->join('users', 'laporan.mahasiswa_id', '=', 'users.id')
            ->where('penugasan.teknisi_id', $teknisi->id)
            ->select(
                'rating.*',
                'laporan.kode_laporan',
                'laporan.judul',
                'users.name as mahasiswa_name'
            )
            ->orderBy('rating.created_at', 'desc')
            ->paginate(10);
        
        $rataRating = DB::table('rating')
            ->join('laporan', 'rating.laporan_id', '=', 'laporan.id')
            ->join('penugasan', 'laporan.id', '=', 'penugasan.laporan_id')
            ->where('penugasan.teknisi_id', $teknisi->id)
            ->avg('rating.rating') ?? 0;
        
        return view('teknisi.profil.rating', compact('ratings', 'rataRating'));
    }
}