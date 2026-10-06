<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'status' => true,
            'data' => $user,
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $user->update($validated);

        LogActivity::log($user->id, "Memperbarui profil", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Profil berhasil diperbarui!',
            'data' => $user,
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password saat ini tidak cocok!',
            ], 400);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        LogActivity::log($user->id, "Mengubah password", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diubah!',
        ]);
    }

    /**
     * Get user notifications
     */
    public function notifications(Request $request)
    {
        $userId = $request->user()->id;

        $query = Notifikasi::where('user_id', $userId);

        if ($request->has('unread') && $request->unread) {
            $query->where('is_read', false);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->limit($request->get('limit', 20))
            ->get();

        $unreadCount = Notifikasi::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => true,
            'data' => [
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notifikasi $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak memiliki akses.',
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'Notifikasi ditandai sebagai sudah dibaca.',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        Notifikasi::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Semua notifikasi ditandai sebagai sudah dibaca.',
        ]);
    }

    /**
     * Admin: Get all users
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }

    /**
     * Admin: Create user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'petugas', 'user'])],
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        $user = User::create($validated);

        LogActivity::log(auth()->id(), "Menambahkan user baru: {$user->name}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'User berhasil ditambahkan!',
            'data' => $user,
        ], 201);
    }

    /**
     * Admin: Get single user
     */
    public function showUser(User $user)
    {
        return response()->json([
            'status' => true,
            'data' => $user,
        ]);
    }

    /**
     * Admin: Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'petugas', 'user'])],
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => 'nullable|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        LogActivity::log(auth()->id(), "Memperbarui user: {$user->name}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'User berhasil diperbarui!',
            'data' => $user,
        ]);
    }

    /**
     * Admin: Delete user
     */
    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak dapat menghapus akun sendiri!',
            ], 400);
        }

        $name = $user->name;
        $user->delete();

        LogActivity::log(auth()->id(), "Menghapus user: {$name}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'User berhasil dihapus!',
        ]);
    }

    /**
     * Admin: Toggle user status
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak dapat mengubah status sendiri!',
            ], 400);
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        LogActivity::log(auth()->id(), "Mengubah status user {$user->name} menjadi {$user->status}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Status user berhasil diubah!',
            'data' => $user,
        ]);
    }

    /**
     * Admin: Get system settings
     */
    public function settings()
    {
        // You can implement settings from database or config
        $settings = [
            'max_borrow_per_day' => 2,
            'late_fee_per_day' => 5000,
            'grace_period_days' => 2,
        ];

        return response()->json([
            'status' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Admin: Update settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'max_borrow_per_day' => 'integer|min:1',
            'late_fee_per_day' => 'integer|min:0',
            'grace_period_days' => 'integer|min:0',
        ]);

        // Save to database or config
        // For now, just return success

        LogActivity::log(auth()->id(), "Memperbarui pengaturan sistem", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Pengaturan berhasil diperbarui!',
            'data' => $validated,
        ]);
    }
}