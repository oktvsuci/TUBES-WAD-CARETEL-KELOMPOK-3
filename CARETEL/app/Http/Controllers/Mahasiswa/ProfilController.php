<?php

// ========================================
// FILE 1: ProfilController.php
// Path: Controllers/Mahasiswa/ProfilController.php
// ========================================

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * CREATE - Registrasi akun mahasiswa baru
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|unique:users,nim',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:15'
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'nim' => $request->nim,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'mahasiswa',
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun mahasiswa berhasil dibuat',
            'data' => $user
        ], 201);
    }

    /**
     * READ - Lihat profil sendiri
     */
    public function show()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'nim' => $user->nim,
                'email' => $user->email,
                'phone' => $user->phone,
                'foto' => $user->foto ? Storage::url($user->foto) : null,
                'created_at' => $user->created_at
            ]
        ]);
    }

    /**
     * UPDATE - Edit profil (foto, HP, password)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:8|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Update phone
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        // Update password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Update foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                Storage::delete($user->foto);
            }
            
            $path = $request->file('foto')->store('profile_photos', 'public');
            $user->foto = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $user
        ]);
    }

    /**
     * DELETE - Nonaktifkan akun sendiri
     */
    public function deactivate()
    {
        $user = Auth::user();
        
        $user->status = 'inactive';
        $user->save();

        // Logout user
        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Akun Anda telah dinonaktifkan'
        ]);
    }
}
