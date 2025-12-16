<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class AdminUserController extends Controller
{
    /**
     * Admin Dashboard - User Management
     */
    public function index(Request $request)
    {
        // Security: Check admin access
        if (Auth::user()->peran != 'admin') {
            abort(403, 'Hanya Admin yang bisa akses');
        }

        $query = Pengguna::query();

        // Filter by role
        if ($request->has('peran') && $request->peran != '') {
            $query->where('peran', $request->peran);
        }

        // Search by name or email
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->paginate(15);

        // Get statistics
        $stats = [
            'total_users' => Pengguna::count(),
            'nelayan' => Pengguna::where('peran', 'nelayan')->count(),
            'tengkulak' => Pengguna::where('peran', 'tengkulak')->count(),
            'pembeli' => Pengguna::where('peran', 'pembeli')->count(),
            'sopir' => Pengguna::where('peran', 'sopir')->count(),
            'staff' => Pengguna::where('peran', 'staff')->count(),
            'owner' => Pengguna::where('peran', 'owner')->count(),
        ];

        $roles = ['nelayan', 'tengkulak', 'pembeli', 'sopir', 'staff', 'owner'];

        return view('admin.user-management.index', compact('users', 'stats', 'roles'));
    }

    /**
     * Show create user form
     */
    public function showCreateForm()
    {
        if (Auth::user()->peran != 'admin') {
            abort(403, 'Unauthorized');
        }

        $roles = ['nelayan', 'tengkulak', 'pembeli', 'sopir', 'staff', 'owner'];
        return view('admin.user-management.create', compact('roles'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        if (Auth::user()->peran != 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:penggunas,email',
            'password' => 'required|string|min:6|confirmed',
            'peran' => 'required|in:nelayan,tengkulak,pembeli,sopir,staff,owner',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:255',
        ]);

        try {
            $user = Pengguna::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'peran' => $validated['peran'],
                'no_telepon' => $validated['no_telepon'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ]);

            \Log::info('New user created', [
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'role' => $validated['peran'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan!',
                'redirect' => route('admin.users.index'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show edit user form
     */
    public function showEditForm($id)
    {
        if (Auth::user()->peran != 'admin') {
            abort(403, 'Unauthorized');
        }

        $user = Pengguna::findOrFail($id);
        $roles = ['nelayan', 'tengkulak', 'pembeli', 'sopir', 'staff', 'owner'];

        return view('admin.user-management.edit', compact('user', 'roles'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->peran != 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = Pengguna::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:penggunas,email,' . $id,
            'peran' => 'required|in:nelayan,tengkulak,pembeli,sopir,staff,owner',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $update_data = [
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'peran' => $validated['peran'],
                'no_telepon' => $validated['no_telepon'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ];

            // Update password only if provided
            if (!empty($validated['password'])) {
                $update_data['password'] = Hash::make($validated['password']);
            }

            $user->update($update_data);

            \Log::info('User updated', [
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'new_role' => $validated['peran'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diubah!',
                'redirect' => route('admin.users.index'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * View user detail
     */
    public function show($id)
    {
        if (Auth::user()->peran != 'admin') {
            abort(403, 'Unauthorized');
        }

        $user = Pengguna::findOrFail($id);

        // Get user activity/stats based on role
        $stats = $this->getUserStats($user->id, $user->peran);

        return view('admin.user-management.show', compact('user', 'stats'));
    }

    /**
     * Delete user
     */
    public function destroy(Request $request, $id)
    {
        if (Auth::user()->peran != 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $user = Pengguna::findOrFail($id);

            // Prevent deleting self
            if ($user->id == Auth::id()) {
                return response()->json(['error' => 'Tidak bisa menghapus akun sendiri'], 400);
            }

            // Log before delete
            $username = $user->nama;
            $user_id = $user->id;

            $user->delete();

            \Log::warning('User deleted', [
                'user_id' => $user_id,
                'username' => $username,
                'deleted_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get user statistics based on role
     */
    private function getUserStats($userId, $role)
    {
        $stats = [];

        switch ($role) {
            case 'nelayan':
                $stats['penawaran'] = \App\Models\Penawaran::where('nelayan_id', $userId)->count();
                $stats['penawaran_approved'] = \App\Models\Penawaran::where('nelayan_id', $userId)
                    ->where('status', 'approved')->count();
                $stats['penawaran_rejected'] = \App\Models\Penawaran::where('nelayan_id', $userId)
                    ->where('status', 'rejected')->count();
                break;

            case 'tengkulak':
                $stats['penawaran_reviewed'] = \App\Models\Penawaran::where('approved_by', $userId)->count();
                $stats['penawaran_approved'] = \App\Models\Penawaran::where('approved_by', $userId)
                    ->where('status', 'approved')->count();
                break;

            case 'pembeli':
                $stats['orders'] = \DB::table('transaksis')->where('pembeli_id', $userId)->count();
                $stats['total_spent'] = \DB::table('transaksis')->where('pembeli_id', $userId)
                    ->sum('total_harga') ?? 0;
                break;

            case 'sopir':
                $stats['pengiriman'] = \DB::table('pengirimens')->where('sopir_id', $userId)->count();
                break;

            default:
                break;
        }

        return $stats;
    }

    /**
     * Change user status (active/inactive)
     */
    public function toggleStatus(Request $request, $id)
    {
        if (Auth::user()->peran != 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $user = Pengguna::findOrFail($id);

            // Prevent deactivating self
            if ($user->id == Auth::id()) {
                return response()->json(['error' => 'Tidak bisa menonaktifkan akun sendiri'], 400);
            }

            // If using is_active field, toggle it
            if (Schema::hasColumn('penggunas', 'is_active')) {
                $user->is_active = !$user->is_active;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Status user berhasil diubah!',
                'is_active' => $user->is_active ?? true,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
