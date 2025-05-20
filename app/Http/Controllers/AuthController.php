<?php

namespace App\Http\Controllers;

use App\Models\PlantillaUser;
use App\Models\Personnel;
use App\Models\Office;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function lockUser(Request $request)
    {
        $request->validate([
            'username' => ['required'],
        ]);

        $user = PlantillaUser::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Update user's locked status
        $user->locked = true;
        $user->locked_until = null; // Lock is permanent until admin unlocks
        $user->save();

        // Create login attempt record with current IP
        DB::table('login_attempts')
            ->updateOrInsert(
                [
                    'username' => $request->username,
                    'ip_address' => request()->ip()
                ],
                [
                    'attempts' => 0,
                    'locked_until' => null, // Lock is permanent
                    'last_attempt_at' => now()
                ]
            );

        return response()->json([
            'success' => true, 
            'message' => 'User locked successfully',
            'locked_until' => 'Permanent (requires admin to unlock)'
        ]);
    }

    public function blockUser(Request $request)
    {
        $request->validate([
            'username' => ['required'],
        ]);

        $user = PlantillaUser::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Update user's blocked status
        $user->blocked = true;
        $user->blocked_until = null; // Block is permanent until admin unblocks
        $user->save();

        // Create login attempt record with current IP
        DB::table('login_attempts')
            ->updateOrInsert(
                [
                    'username' => $request->username,
                    'ip_address' => request()->ip()
                ],
                [
                    'attempts' => 0,
                    'locked_until' => null, // Block is permanent
                    'last_attempt_at' => now()
                ]
            );

        return response()->json([
            'success' => true, 
            'message' => 'User blocked successfully',
            'blocked_until' => 'Permanent (requires admin to unblock)'
        ]);
    }

    public function unlockAccount(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'ip_address' => ['required'],
        ]);

        // Delete the login attempt record
        $deleted = DB::table('login_attempts')
            ->where('username', $request->username)
            ->where('ip_address', $request->ip_address)
            ->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Account unlocked successfully');
        }

        return redirect()->back()->with('error', 'No locked account found for this username and IP address');
    }

    public function login(Request $request)
    {
        // Constants for login security
        $MAX_ATTEMPTS = 5; // Maximum number of attempts before lockout
        $LOCKOUT_DURATION = 30 * 60; // 30 minutes lockout duration
        $ATTEMPT_WINDOW = 15 * 60; // 15 minutes window for attempts

        // Validate only that the fields are present
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Get or create login attempt record
        $attempt = DB::table('login_attempts')
            ->where('username', $request->username)
            ->where('ip_address', $request->ip())
            ->first();

        // If no existing record, create a default array
        if (!$attempt) {
            $attempt = [
                'username' => $request->username,
                'ip_address' => $request->ip(),
                'attempts' => 0,
                'locked_until' => null,
                'last_attempt_at' => null
            ];
        } else {
            // Convert database record to array
            $attempt = [
                'username' => $attempt->username,
                'ip_address' => $attempt->ip_address,
                'attempts' => $attempt->attempts,
                'locked_until' => $attempt->locked_until,
                'last_attempt_at' => $attempt->last_attempt_at
            ];
        }

        // Check if user is locked out
        if ($attempt['locked_until'] && now() < \Carbon\Carbon::parse($attempt['locked_until'])) {
            return back()->withErrors([
                'username' => "Your account is currently locked. Please contact an administrator to unlock it.",
            ])
            ->withInput($request->except('password'))
            ->with('locked_until', \Carbon\Carbon::parse($attempt['locked_until'])->toISOString());
        }

        // Check if the last attempt was within the attempt window
        if ($attempt['last_attempt_at'] && 
            now() < \Carbon\Carbon::parse($attempt['last_attempt_at'])->addMinutes($ATTEMPT_WINDOW)) {
            
            // Increment attempts if within window
            $attempt['attempts']++;
            
            // Check if max attempts reached
            if ($attempt['attempts'] >= $MAX_ATTEMPTS) {
                // Set lockout time
                $lockedUntil = now()->addMinutes(30);
                
                DB::table('login_attempts')
                    ->updateOrInsert(
                        [
                            'username' => $request->username,
                            'ip_address' => $request->ip()
                        ],
                        [
                            'attempts' => $attempt['attempts'],
                            'locked_until' => $lockedUntil,
                            'last_attempt_at' => now()
                        ]
                    );
                
                return back()->withErrors([
                    'username' => "Too many failed login attempts. Account is locked for 30 minutes.",
                ])
                ->withInput($request->except('password'))
                ->with('locked_until', $lockedUntil->toISOString());
            }
        } else {
            // Reset attempts if outside window
            $attempt['attempts'] = 1;
        }

        // Manually find the user by username
        $user = PlantillaUser::where('username', $request->username)->first();
        
        // Add detailed logging
        \Log::info('Login attempt', [
            'username' => $request->username,
            'user_found' => $user ? 'yes' : 'no',
            'ip_address' => $request->ip(),
            'attempts' => $attempt['attempts'] ?? 0,
            'locked_until' => $attempt['locked_until'] ?? null
        ]);
        
        // Check if user exists and password matches
        if ($user && Hash::check($request->password, $user->password)) {
            // Check if user is blocked
            if ($user->blocked) {
                return back()->withErrors([
                    'username' => 'Your account has been blocked. Please contact an administrator.',
                ])->withInput($request->except('password'));
            }

            // Reset login attempts
            DB::table('login_attempts')
                ->updateOrInsert(
                    [
                        'username' => $request->username,
                        'ip_address' => $request->ip()
                    ],
                    [
                        'attempts' => 0,
                        'locked_until' => null,
                        'last_attempt_at' => now()
                    ]
                );

            // Log in the user manually without triggering password validations
            Auth::login($user);
            
            // Regenerate session for security
            $request->session()->regenerate();
            
            \Log::info('User authenticated successfully', [
                'user' => $user->username, 
                'role' => $user->role,
                'ip_address' => $request->ip()
            ]);
            
            // Clear any existing errors in the session
            $request->session()->forget('errors');
            
            // Check if a redirect_to parameter was provided
            if ($request->has('redirect_to')) {
                return redirect($request->input('redirect_to'));
            }
            
            // Default redirect based on role
            return redirect()->intended(route('index'));
        }

        // Handle failed login attempt
        // Get the current attempts count
        $currentAttempts = $attempt['attempts'] ?? 0;
        
        // Increment attempts
        $newAttempts = $currentAttempts + 1;
        
        // Calculate remaining attempts
        $remainingAttempts = $MAX_ATTEMPTS - $newAttempts;
        
        // Check if max attempts reached
        if ($newAttempts >= $MAX_ATTEMPTS) {
            // Set lockout time
            $lockedUntil = now()->addMinutes(30);
            
            DB::table('login_attempts')
                ->updateOrInsert(
                    [
                        'username' => $request->username,
                        'ip_address' => $request->ip()
                    ],
                    [
                        'attempts' => $newAttempts,
                        'locked_until' => $lockedUntil,
                        'last_attempt_at' => now()
                    ]
                );
            
            return back()->withErrors([
                'username' => "Too many failed login attempts. Account is locked for 30 minutes.",
            ])->withInput($request->except('password'))
            ->with('locked_until', $lockedUntil->toISOString());
        }

        // Update attempt count
        DB::table('login_attempts')
            ->updateOrInsert(
                [
                    'username' => $request->username,
                    'ip_address' => $request->ip()
                ],
                [
                    'attempts' => $newAttempts,
                    'last_attempt_at' => now()
                ]
            );

        // Return with remaining attempts
        return back()->withErrors([
            'username' => "Invalid credentials. {$remainingAttempts} attempt(s) remaining",
        ])->withInput($request->except('password'));
        $newCount = $currentCount + 1;
        
        DB::table('login_attempts')
            ->where('username', $request->username)
            ->where('ip_address', $request->ip())
            ->update([
                'attempt_count' => $newCount,
                'updated_at' => now(),
                'last_attempt_at' => now(),
                'locked_until' => $newCount >= $MAX_ATTEMPTS ? now()->addSeconds($LOCKOUT_DURATION) : null
            ]);

        if ($newCount < $MAX_ATTEMPTS) {
            $remainingAttempts = $MAX_ATTEMPTS - $newCount;
            return back()->withErrors([
                'username' => "Invalid credentials. {$remainingAttempts} attempts remaining",
            ])->withInput($request->except('password'));
        }

        // If no attempts remaining, lock account
        $lockedUntil = now()->addSeconds($LOCKOUT_DURATION);
        return back()->withErrors([
            'username' => "Account locked. Please contact an administrator to unlock your account.",
        ])
        ->withInput($request->except('password'))
        ->with('locked_until', $lockedUntil->toIso8601String());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }



    public function index()
    {
        $user = Auth::user();
        $personnels = Personnel::all();
        
        // Get all offices for the dropdown
        $offices = Office::all();
        
        // Get permanent personnel for the add personnel modal
        $permanentPersonnels = Personnel::whereIn('status', ['permanent', 'Regular Permanent'])
            ->get();
        
        // Get all positions with their office relationships
        $positions = Position::with('office')
            ->whereHas('office') // Only get positions that have an associated office
            ->get()
            ->sortBy('position'); // Sort by position name
        
        // Get all currently locked accounts
        $lockedAccounts = DB::table('login_attempts')
            ->whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->get();

        return view('Plantilla.index', [
            'personnels' => $personnels, 
            'userRole' => $user->role,
            'userPermissions' => $user->permissions ?? [],
            'offices' => $offices,
            'permanentPersonnels' => $permanentPersonnels,
            'positions' => $positions,
            'lockedAccounts' => $lockedAccounts
        ]);
    }

    public function vacant()
    {
        $user = Auth::user();
        
        // Get vacant plantilla items
        $vacantItems = Personnel::where('lastName', null)
                                           ->orWhere('lastName', '')
                                           ->get();
        
        // Get all offices for the dropdown - with explicit selection of required fields
        $offices = Office::select('id', 'code', 'name', 'abbreviation')
                        ->whereNotNull('code')
                        ->whereNotNull('name')
                        ->whereNotNull('abbreviation')
                        ->get();
        
        return view('Plantilla.Pages.vacant', [
            'user' => $user,
            'vacantItems' => $vacantItems,
            'offices' => $offices
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeUser(UserRequest $request)
    {
        try {
            $user = new PlantillaUser();
            $user->firstName = $request->firstName;
            $user->lastName = $request->lastName;
            $user->username = $request->username;
            $user->password = Hash::make($request->password);
            $user->role = $request->role;
            $user->permissions = ['view'];
            $user->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully.'
                ]);
            }

            return redirect()->route('Plantilla.Pages.Accounts')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $request->validator->errors()->messages()
                ], 422);
            }

            return back()->withErrors($request->validator->errors())
                ->withInput();
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:plantilla_users,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:plantilla_users,username,'.$request->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|in:admin,user',
            'permissions' => 'nullable|array',
        ]);

        $user = PlantillaUser::findOrFail($request->id);
        $user->name = $request->name;
        $user->username = $request->username;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->role = $request->role;
        $user->permissions = $request->permissions ?? ['view'];
        $user->save();

        return redirect()->route('Plantilla.Pages.Accounts')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteUser(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:plantilla_users,id',
        ]);

        // Prevent deleting the currently logged-in user
        if ($request->id == Auth::id()) {
            return redirect()->route('Plantilla.Pages.Accounts')
                ->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the last admin user
        $user = PlantillaUser::findOrFail($request->id);
        if ($user->role === 'admin') {
            $adminCount = PlantillaUser::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->route('Plantilla.Pages.Accounts')
                    ->with('error', 'Cannot delete the last admin user.');
            }
        }

        $user->delete();

        return redirect()->route('Plantilla.Pages.Accounts')
            ->with('success', 'User deleted successfully.');
    }
    
    /**
     * Display the remarks page.
     *
     * @return \Illuminate\Http\Response
     */
    public function remarks()
    {
        $user = Auth::user();
        $personnels = Personnel::all();
        
        return view('Plantilla.Pages.Remarks', [
            'user' => $user,
            'personnels' => $personnels,
            'userPermissions' => $user->permissions ?? []
        ]);
    }
    
    /**
     * Display the years of service page.
     *
     * @return \Illuminate\Http\Response
     */
    public function yearsOfService()
    {
        $user = Auth::user();
        
        // Get all personnel with office relationship
        $personnels = Personnel::with('office')
            ->get();
    
        // Get all offices for the dropdown
        $offices = Office::all();
    
        return view('Plantilla.Pages.YearsOfService', [
            'user' => $user,
            'personnels' => $personnels,
            'userPermissions' => $user->permissions ?? [],
            'offices' => $offices
        ]);
    }
    
    public function generateServiceReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'report_type' => 'required|string|in:all,retirable,retired,service_milestone',
                'report_office' => 'nullable|string',
                'report_format' => 'required|string|in:print',
                'milestone_years' => 'nullable|integer|min:5',
            ]);

            $query = Personnel::whereIn('status', ['permanent', 'Regular Permanent']);

            if ($validated['report_type'] === 'retirable') {
                $query->where('pendingRetirement', true);
            } elseif ($validated['report_type'] === 'retired') {
                $query->whereNotNull('retirement_date');
            } elseif ($validated['report_type'] === 'service_milestone') {
                $years = $validated['milestone_years'] ?? 5;
                $query->whereRaw("DATEDIFF(CURDATE(), originalAppointment) >= ?", [$years * 365]);
            }

            if ($validated['report_office']) {
                $query->where('office', $validated['report_office']);
            }

            $personnels = $query->with('office')->get();

            // Calculate years of service for each personnel
            foreach ($personnels as $personnel) {
                if ($personnel->originalAppointment) {
                    $date1 = new \DateTime($personnel->originalAppointment);
                    $date2 = new \DateTime();
                    $interval = $date1->diff($date2);
                    $personnel->yearsOfService = $interval->y;
                }
            }

            return view('Plantilla.reports.print_years_of_service', [
                'personnels' => $personnels,
                'params' => $validated,
                'today' => now()->format('F j, Y')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating service report: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }
    
    // Helper method to create a new user (you can use this in tinker or create a proper registration form)
    public function createUser($username, $password, $name, $role = 'user')
    {
        return PlantillaUser::create([
            'username' => $username,
            'password' => Hash::make($password),
            'name' => $name,
            'role' => $role,
        ]);
    }

    public function showLockedAccounts()
    {
        // Get all currently locked accounts
        $lockedAccounts = DB::table('login_attempts')
            ->whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->get();

        return view('auth.locked-accounts', compact('lockedAccounts'));
    }
}
