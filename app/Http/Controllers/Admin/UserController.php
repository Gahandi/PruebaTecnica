<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Space;
use App\Models\RoleSpace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by verification status
        if ($request->filled('verified')) {
            if ($request->verified === 'yes') {
                $query->whereNotNull('verified_at');
            } else {
                $query->whereNull('verified_at');
            }
        }

        // Order
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        $users = $query->paginate(20);

        // Stats
        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'users' => User::where('role', 'user')->count(),
            'verified' => User::whereNotNull('verified_at')->count(),
            'today' => User::whereDate('created_at', today())->count(),
        ];

        ActivityLog::log('viewed', 'Viewed users list', 'User', null);

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $spaces = Space::all();
        $roleSpaces = RoleSpace::all();

        return view('admin.users.create', compact('spaces', 'roleSpaces'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'staff', 'user'])],
            'verified' => ['boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('password', 'image');
        $data['password'] = Hash::make($request->password);

        if ($request->verified) {
            $data['verified'] = true;
            $data['verified_at'] = now();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
            $data['image'] = $imagePath;
        }

        $user = User::create($data);

        // Assign to spaces if provided
        if ($request->filled('spaces')) {
            foreach ($request->spaces as $spaceId => $roleSpaceId) {
                if ($roleSpaceId) {
                    $user->spaces()->attach($spaceId, ['role_space_id' => $roleSpaceId]);
                }
            }
        }

        ActivityLog::log('created', "Created user: {$user->name} ({$user->email})", 'User', $user->id, [
            'role' => $user->role,
            'email' => $user->email
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['spaces.users', 'orders', 'checkins']);

        // Get user statistics
        $stats = [
            'total_orders' => $user->orders()->count(),
            'completed_orders' => $user->orders()->where('orders.status', 'completed')->count(),
            'total_spent' => $user->orders()
                ->where('orders.status', 'completed')
                ->join('payments', 'orders.id', '=', 'payments.order_id')
                ->sum('payments.total'),
            'total_tickets' => $user->orders()
                ->join('tickets', 'orders.id', '=', 'tickets.order_id')
                ->count(),
            'spaces_count' => $user->spaces()->count(),
            'checkins_made' => $user->checkins()->count(),
        ];

        // Recent activity
        $recentActivity = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        ActivityLog::log('viewed', "Viewed user profile: {$user->name}", 'User', $user->id);

        return view('admin.users.show', compact('user', 'stats', 'recentActivity'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $spaces = Space::all();
        $roleSpaces = RoleSpace::all();
        $userSpaces = $user->spaces()->get()->keyBy('id');

        return view('admin.users.edit', compact('user', 'spaces', 'roleSpaces', 'userSpaces'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'staff', 'user'])],
            'verified' => ['boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldData = $user->only(['name', 'email', 'role', 'verified']);

        $data = $request->except('password', 'image');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->verified && !$user->verified_at) {
            $data['verified'] = true;
            $data['verified_at'] = now();
        } elseif (!$request->verified) {
            $data['verified'] = false;
            $data['verified_at'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && \Storage::disk('public')->exists($user->image)) {
                \Storage::disk('public')->delete($user->image);
            }

            $imagePath = $request->file('image')->store('users', 'public');
            $data['image'] = $imagePath;
        }

        $user->update($data);

        // Update spaces
        if ($request->has('spaces')) {
            $user->spaces()->detach();
            foreach ($request->spaces as $spaceId => $roleSpaceId) {
                if ($roleSpaceId) {
                    $user->spaces()->attach($spaceId, ['role_space_id' => $roleSpaceId]);
                }
            }
        }

        $changes = array_diff_assoc($user->only(['name', 'email', 'role', 'verified']), $oldData);

        ActivityLog::log('updated', "Updated user: {$user->name}", 'User', $user->id, [
            'changes' => $changes
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        ActivityLog::log('deleted', "Deleted user: {$userName} ({$userEmail})", 'User', $user->id);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(['admin', 'staff', 'user'])]
        ]);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No puedes cambiar tu propio rol de administrador.'
            ], 403);
        }

        $oldRole = $user->role;
        $user->update(['role' => $request->role]);

        ActivityLog::log('updated', "Changed user role: {$user->name} from {$oldRole} to {$request->role}", 'User', $user->id, [
            'old_role' => $oldRole,
            'new_role' => $request->role
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado exitosamente.'
        ]);
    }

    /**
     * Assign user to space
     */
    public function assignSpace(Request $request, User $user)
    {
        $request->validate([
            'space_id' => ['required', 'exists:spaces,id'],
            'role_space_id' => ['required', 'exists:role_spaces,id']
        ]);

        $user->spaces()->syncWithoutDetaching([
            $request->space_id => ['role_space_id' => $request->role_space_id]
        ]);

        $space = Space::find($request->space_id);
        $roleSpace = RoleSpace::find($request->role_space_id);

        ActivityLog::log('updated', "Assigned user {$user->name} to space {$space->name} as {$roleSpace->name}", 'User', $user->id, [
            'space_id' => $space->id,
            'role_space_id' => $roleSpace->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario asignado al espacio exitosamente.'
        ]);
    }

    /**
     * Remove user from space
     */
    public function removeSpace(Request $request, User $user)
    {
        $request->validate([
            'space_id' => ['required', 'exists:spaces,id']
        ]);

        $space = Space::find($request->space_id);
        $user->spaces()->detach($request->space_id);

        ActivityLog::log('updated', "Removed user {$user->name} from space {$space->name}", 'User', $user->id, [
            'space_id' => $space->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario removido del espacio exitosamente.'
        ]);
    }

    /**
     * Toggle user verification
     */
    public function toggleVerification(User $user)
    {
        if ($user->verified_at) {
            $user->update([
                'verified' => false,
                'verified_at' => null
            ]);
            $message = 'Usuario desverificado';
        } else {
            $user->update([
                'verified' => true,
                'verified_at' => now()
            ]);
            $message = 'Usuario verificado';
        }

        ActivityLog::log('updated', "{$message}: {$user->name}", 'User', $user->id);

        return response()->json([
            'success' => true,
            'message' => $message . ' exitosamente.'
        ]);
    }
}
