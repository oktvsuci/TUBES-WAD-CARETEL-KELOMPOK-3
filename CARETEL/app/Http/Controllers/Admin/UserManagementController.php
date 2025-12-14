<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users (READ)
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filter by role
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $status = $request->status == 'active' ? 1 : 0;
            $query->where('is_active', $status);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $users = $query->paginate(15);
        
        $stats = [
            'total' => User::count(),
            'mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'teknisi' => User::where('role', 'teknisi')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user (CREATE - Form)
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user (CREATE - Store)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nim_nip' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:mahasiswa,teknisi,admin',
            'phone' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nim_nip' => $request->nim_nip,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => true,
        ];
        
        // Handle profile photo upload
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profile', $filename, 'public');
            $data['foto_profil'] = $path;
        }
        
        User::create($data);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User/Teknisi baru berhasil didaftarkan.');
    }

    /**
     * Display the specified user (READ - Detail)
     */
    public function show($id)
    {
        $user = User::with(['laporan', 'tugasTeknisi'])->findOrFail($id);
        
        // Get user statistics
        $stats = [];
        
        if ($user->role == 'mahasiswa') {
            $stats = [
                'total_laporan' => $user->laporan()->count(),
                'pending' => $user->laporan()->where('status', 'pending')->count(),
                'diproses' => $user->laporan()->where('status', 'diproses')->count(),
                'selesai' => $user->laporan()->where('status', 'selesai')->count(),
            ];
        } elseif ($user->role == 'teknisi') {
            $stats = [
                'total_tugas' => $user->tugasTeknisi()->count(),
                'pending' => $user->tugasTeknisi()->where('status', 'pending')->count(),
                'diproses' => $user->tugasTeknisi()->where('status', 'diproses')->count(),
                'selesai' => $user->tugasTeknisi()->where('status', 'selesai')->count(),
            ];
        }
        
        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the user (UPDATE - Form)
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user (UPDATE - Store)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nim_nip' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:mahasiswa,teknisi,admin',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nim_nip' => $request->nim_nip,
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->is_active,
        ];
        
        // Handle profile photo upload
        if ($request->hasFile('foto_profil')) {
            // Delete old photo
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            
            $file = $request->file('foto_profil');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profile', $filename, 'public');
            $data['foto_profil'] = $path;
        }
        
        $user->update($data);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Data user berhasil diubah.');
    }

    /**
     * Reset user password (UPDATE)
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        return redirect()->back()
            ->with('success', 'Password user berhasil direset.');
    }

    /**
     * Toggle user status (UPDATE)
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'is_active' => !$user->is_active
        ]);
        
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Akun user berhasil {$status}.");
    }

    /**
     * Soft delete user (DELETE)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting self
        if ($user->id == auth()->id()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }
        
        // Soft delete: set is_active to false
        $user->update(['is_active' => false]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Akun user yang melanggar berhasil dinonaktifkan.');
    }

    /**
     * Bulk action for users
     */
    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;
        
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada user yang dipilih'
            ], 400);
        }
        
        // Prevent action on self
        if (in_array(auth()->id(), $ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat melakukan aksi pada akun sendiri'
            ], 400);
        }
        
        $count = 0;
        
        switch ($action) {
            case 'activate':
                $count = User::whereIn('id', $ids)->update(['is_active' => true]);
                $message = "$count user berhasil diaktifkan";
                break;
                
            case 'deactivate':
                $count = User::whereIn('id', $ids)->update(['is_active' => false]);
                $message = "$count user berhasil dinonaktifkan";
                break;
                
            case 'delete':
                $count = User::whereIn('id', $ids)->update(['is_active' => false]);
                $message = "$count user berhasil dihapus";
                break;
                
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Aksi tidak valid'
                ], 400);
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $query = User::query();
        
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }
        
        $users = $query->get();
        
        $filename = 'users_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama', 'Email', 'NIM/NIP', 'Role', 'Telepon', 'Status', 'Terdaftar']);
            
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->nim_nip,
                    ucfirst($user->role),
                    $user->phone ?? '-',
                    $user->is_active ? 'Aktif' : 'Nonaktif',
                    $user->created_at->format('d/m/Y')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}