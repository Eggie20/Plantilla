<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PlantillaUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'plantilla_users';

    // Role constants
    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    // Permission constants
    const PERMISSION_CREATE_USER = 'create_user';
    const PERMISSION_UPDATE_USER = 'update_user';
    const PERMISSION_DELETE_USER = 'delete_user';
    const PERMISSION_BLOCK_USER = 'block_user';
    const PERMISSION_UNBLOCK_USER = 'unblock_user';
    const PERMISSION_VIEW_LOGS = 'view_logs';
    const PERMISSION_MANAGE_ROLES = 'manage_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'permissions',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Check if user has a specific permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        // Superadmin has all permissions
        if ($this->role === self::ROLE_SUPERADMIN) {
            return true;
        }

        // Admin has all permissions except managing other admins/superadmins
        if ($this->role === self::ROLE_ADMIN) {
            if ($permission === self::PERMISSION_MANAGE_ROLES) {
                return false;
            }
            return true;
        }

        // Check specific permissions for regular users
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Check if user can perform a specific action
     *
     * @param string|array $abilities
     * @param array $arguments
     * @return bool
     */
    public function can($abilities, $arguments = [])
    {
        if (is_array($abilities)) {
            foreach ($abilities as $ability) {
                if (!$this->hasPermission($ability)) {
                    return false;
                }
            }
            return true;
        }

        return $this->hasPermission($abilities);
    }

    /**
     * Check if user can manage another user
     *
     * @param PlantillaUser $targetUser
     * @return bool
     */
    public function canManageUser(PlantillaUser $targetUser)
    {
        // Superadmin can manage anyone
        if ($this->role === self::ROLE_SUPERADMIN) {
            return true;
        }

        // Admin cannot manage other admins or superadmins
        if ($this->role === self::ROLE_ADMIN && 
            ($targetUser->role === self::ROLE_ADMIN || $targetUser->role === self::ROLE_SUPERADMIN)) {
            return false;
        }

        return true;
    }

    /**
     * Get the login identifier for the user.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }
}
