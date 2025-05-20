<?php

namespace App\Http\Controllers;

use App\Models\PlantillaUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Block a user account
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function block(Request $request, $id)
    {
        $user = PlantillaUser::findOrFail($id);
        $authUser = Auth::user();
        
        // Check if user has permission to block
        if (!$authUser->hasPermission(PlantillaUser::PERMISSION_BLOCK_USER)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if user can manage target user
        if (!$authUser->canManageUser($user)) {
            return response()->json(['error' => 'Cannot manage this user'], 403);
        }

        if ($user->blocked) {
            return response()->json(['error' => 'User is already blocked'], 400);
        }

        $user->blocked = true;
        $user->save();

        return response()->json(['success' => 'User has been blocked']);
    }

    /**
     * Unblock a user account
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unblock(Request $request, $id)
    {
        $user = PlantillaUser::findOrFail($id);
        $authUser = Auth::user();
        
        // Check if user has permission to unblock
        if (!$authUser->hasPermission(PlantillaUser::PERMISSION_UNBLOCK_USER)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if user can manage target user
        if (!$authUser->canManageUser($user)) {
            return response()->json(['error' => 'Cannot manage this user'], 403);
        }

        if (!$user->blocked) {
            return response()->json(['error' => 'User is not blocked'], 400);
        }

        $user->blocked = false;
        $user->save();

        return response()->json(['success' => 'User has been unblocked']);
    }

    /**
     * Get user accounts with block status
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function accounts(Request $request)
    {
        // Build query
        $query = PlantillaUser::query();
        
        // Handle date filter
        $filter = $request->input('filter', '');
        if ($filter) {
            $today = now()->format('Y-m-d');
            
            switch($filter) {
                case 'today':
                    $query->whereDate('created_at', $today);
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ]);
                    break;
                case 'year':
                    $query->whereBetween('created_at', [
                        now()->startOfYear(),
                        now()->endOfYear()
                    ]);
                    break;
            }
        }

        // Handle custom date range
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        // Get personnel based on filters
        $personnel = $query->get();

        // Get all currently locked accounts
        $lockedAccounts = DB::table('login_attempts')
            ->whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->select(
                'username', 
                'ip_address',
                DB::raw('MAX(locked_until) as locked_until'),
                DB::raw('MAX(last_attempt_at) as last_attempt_at')
            )
            ->groupBy('username', 'ip_address')
            ->orderBy('locked_until', 'desc')
            ->get();

        return view('Plantilla.Pages.Accounts', [
            'personnel' => $personnel,
            'lockedAccounts' => $lockedAccounts,
            'filter' => $filter
        ]);
    }

    public function store(Request $request)
    {
        try {
            $authUser = Auth::user();

            // Only superadmin can create users
            if (!$authUser->hasPermission(PlantillaUser::PERMISSION_CREATE_USER)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\-\']+$/'],
                'username' => ['required', 'string', 'max:50', 'unique:plantilla_users'],
                'password' => ['required', 'string', 'min:8'],
                'role' => ['required', 'in:admin,user,superadmin'],
                'permissions' => ['nullable', 'array'],
            ], [
                'name.required' => 'Full name is required',
                'name.regex' => 'Full name can only contain letters and spaces',
                'username.required' => 'Username is required',
                'username.unique' => 'This username is already taken',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 8 characters',
                'password.confirmed' => 'Password confirmation does not match',
                'role.required' => 'Please select a role',
                'role.in' => 'Please select a valid role',
            ]);

            // Create user
            $user = PlantillaUser::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'permissions' => $validated['permissions'] ?? ['view'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user account
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $authUser = Auth::user();
            
            // Superadmin has all permissions
            if ($authUser->role === PlantillaUser::ROLE_SUPERADMIN) {
                $validated = $request->validate([
                    'id' => ['required', 'integer', 'exists:plantilla_users,id'],
                    'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\-\']+$/'],
                    'username' => ['required', 'string', 'max:50'],
                    'password' => ['nullable', 'string', 'min:8'],
                    'password_confirmation' => ['nullable', 'string', 'min:8', 'same:password'],
                    'role' => ['required', 'in:admin,user,superadmin'],
                    'permissions' => ['nullable', 'array'],
                ], [
                    'name.required' => 'Full name is required',
                    'name.regex' => 'Full name can only contain letters and spaces',
                    'username.required' => 'Username is required',
                    'password.min' => 'Password must be at least 8 characters',
                    'password_confirmation.same' => 'Password confirmation does not match',
                    'role.required' => 'Please select a role',
                    'role.in' => 'Please select a valid role',
                ]);
            }
            // Admin can update users except other admins/superadmins
            elseif ($authUser->role === PlantillaUser::ROLE_ADMIN) {
                // Admins need specific permissions to update users
                if (!$authUser->hasPermission(PlantillaUser::PERMISSION_UPDATE_USER)) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                
                $validated = $request->validate([
                    'id' => ['required', 'integer', 'exists:plantilla_users,id'],
                    'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\-\']+$/'],
                    'username' => ['required', 'string', 'max:50'],
                    'password' => ['nullable', 'string', 'min:8'],
                    'password_confirmation' => ['nullable', 'string', 'min:8', 'same:password'],
                    'role' => ['required', 'in:admin,user,superadmin'],
                    'permissions' => ['nullable', 'array'],
                ], [
                    'name.required' => 'Full name is required',
                    'name.regex' => 'Full name can only contain letters and spaces',
                    'username.required' => 'Username is required',
                    'password.min' => 'Password must be at least 8 characters',
                    'password_confirmation.same' => 'Password confirmation does not match',
                    'role.required' => 'Please select a role',
                    'role.in' => 'Please select a valid role',
                ]);
            }
            // Regular users cannot update other users
            else {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $user = PlantillaUser::findOrFail($validated['id']);

            // Check if user can manage target user
            if (!$authUser->canManageUser($user)) {
                return response()->json(['error' => 'Cannot manage this user'], 403);
            }

            // Update user
            $user->name = $validated['name'];
            $user->username = $validated['username'];
            $user->role = $validated['role'];
            
            // Only update password if new password is provided
            if ($validated['password']) {
                $user->password = Hash::make($validated['password']);
            }

            // Update permissions
            $user->permissions = $validated['permissions'] ?? ['view'];
            
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
