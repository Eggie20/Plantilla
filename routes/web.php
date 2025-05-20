<?php

use App\Http\Controllers\OfficeController;
use App\Http\Controllers\OfficeOfTheMayorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonnelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\Admin\AuthLogController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UserController;

// Route for the homepage
Route::get('/', function () {
    return view('Plantilla.welcome');
});

// Authentication routes
Route::get('/login', function() {
    return redirect('/')->with('openLoginModal', true);
})->name('login.show');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Routes that require authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard route accessible to all authenticated users
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Route for fetching positions
    Route::get('/get-positions', [PositionController::class, 'getPositions'])->name('positions.get');
    
    // Profile routes accessible to all authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Index route
    Route::get('/index', [AuthController::class, 'index'])->name('index');
    
    // Accounts route (protected by auth middleware above)
    Route::get('/Plantilla.Pages.Accounts', [UserController::class, 'accounts'])->name('Plantilla.Pages.Accounts');
    
    // Block/Unblock user routes
    Route::post('/users/{id}/block', [UserController::class, 'block'])->name('users.block');
    Route::post('/users/{id}/unblock', [UserController::class, 'unblock'])->name('users.unblock');

    // Activity Log route
    Route::get('/activity-log', [ActivityLogController::class, 'index'])
        ->name('activity.log')
        ->middleware('auth');

    // Plantilla Item route
    Route::get('/Plantilla.Pages.vacant', [AuthController::class, 'vacant'])->name('Plantilla.Pages.vacant');

    // Locked accounts routes
    Route::get('/locked-accounts', [AuthController::class, 'showLockedAccounts'])->name('locked.accounts');
    Route::post('/unlock-account', [AuthController::class, 'unlockAccount'])->name('auth.unlock');

    // Remarks route
    Route::get('/Plantilla.Pages.Remarks', [AuthController::class, 'remarks'])->name('Plantilla.Pages.Remarks');
    
    // Years of Service route
    Route::get('/Plantilla.Pages.YearsOfService', [AuthController::class, 'yearsOfService'])->name('Plantilla.Pages.YearsOfService');

    // Service Report route
    Route::post('/service-report', [AuthController::class, 'generateServiceReport'])->name('service.report');

    // NOSA route
    // Route::get('/Plantilla.Pages.Nosa', [\App\Http\Controllers\NosaController::class, 'index'])->name('Plantilla.Pages.Nosa');

    // User management routes
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::put('/user/update', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/delete', [UserController::class, 'delete'])->name('user.delete');

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin routes protected by auth middleware
    // Position routes
    Route::get('/positions', [PositionController::class, 'index'])->name('position.index');
    Route::get('/positions/vacant', [PositionController::class, 'getVacantPositions'])->name('position.vacant');
    
    Route::post('/positions', [PositionController::class, 'store'])->name('position.store');
    Route::get('/positions/{id}', [PositionController::class, 'show'])->name('position.show');
    Route::get('/positions/{id}/edit', [PositionController::class, 'edit'])->name('position.edit');
    Route::put('/positions/{id}', [PositionController::class, 'update'])->name('position.update');
    Route::delete('/positions/{id}', [PositionController::class, 'destroy'])->name('positions.destroy');
    Route::post('/positions/check-itemno', [PositionController::class, 'checkItemNo'])->name('positions.checkItemNo');

    // Personnel routes
    Route::post('/store-personnel', [PersonnelController::class, 'store'])->name('store.personnel');
    Route::post('/update-personnel/{id}', [PersonnelController::class, 'update'])->name('update.personnel');
    Route::delete('/delete-personnel/{id}', [PersonnelController::class, 'destroy'])->name('delete.personnel');
    Route::post('/assign-personnel', [PersonnelController::class, 'assign'])->name('personnel.assign');
    Route::post('/retire-personnel', [PersonnelController::class, 'retire'])->name('personnel.retire');
    Route::get('/personnel/item-no/{itemNo}', [PersonnelController::class, 'getPersonnelByItemNo'])->name('personnel.item-no');

    // Auth Logs route 
    Route::get('/auth-logs', [AuthLogController::class, 'index'])
        ->name('admin.authlogs.index')
        ->middleware('auth');

    // auth logs 
    Route::get('/admin/authlogs', [AuthLogController::class, 'index'])
        ->name('admin.authlogs.index')
        ->middleware('auth');

    // Plantilla Item routes
    Route::post('/plantilla-item/store', [OfficeController::class, 'storeVacant'])->name('plantilla-item.store');
    Route::put('/plantilla-item/update', [OfficeController::class, 'updateVacant'])->name('plantilla-item.update');
    Route::delete('/plantilla-item/delete', [OfficeController::class, 'deleteVacant'])->name('plantilla-item.delete');
    Route::get('/position/{id}/edit', [OfficeController::class, 'edit'])->name('position.edit');
    Route::delete('/position/{id}', [OfficeController::class, 'destroy'])->name('position.delete');

    // Office routes
    Route::get('/office', [OfficeController::class, 'index'])->name('office.index');
    Route::post('/office', [OfficeController::class, 'store'])->name('office.store');
    Route::get('/office/check-duplicate', [OfficeController::class, 'checkDuplicate'])->name('office.check-duplicate');
    Route::get('/office/{office}/edit', [OfficeController::class, 'edit'])->name('office.edit');
    Route::put('/office/{office}', [OfficeController::class, 'update'])->name('office.update');
    Route::delete('/office/{office}', [OfficeController::class, 'destroy'])->name('office.destroy');

    // Test routes
    Route::get('/test-activity', function() {
        $lastActivity = \Spatie\Activitylog\Models\Activity::latest()->first();
        dd($lastActivity);
    });

    Route::get('/check-activity-log', function() {
        $activities = \Spatie\Activitylog\Models\Activity::with('causer', 'subject')
            ->latest()
            ->take(5)
            ->get();
        
        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    });

    // Locked Accounts Management
    Route::get('/locked-accounts', [AuthController::class, 'showLockedAccounts'])->name('auth.locked-accounts');
    Route::post('/unlock-account', [AuthController::class, 'unlockAccount'])->name('auth.unlock');
    
    // Admin dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // User management routes
    Route::get('/Plantilla.Pages.Accounts', [UserController::class, 'accounts'])->name('Plantilla.Pages.Accounts');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::put('/user/update', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/delete', [UserController::class, 'delete'])->name('user.delete');
    Route::post('/auth/lock', [AuthController::class, 'lockUser'])->name('auth.lock');
    Route::post('/auth/block', [AuthController::class, 'blockUser'])->name('auth.block');
});
