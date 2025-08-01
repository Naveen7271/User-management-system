<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'role_permissions';

    protected $fillable = [
        'role',
        'permission_id',
    ];

    /**
     * Get the permission associated with this role permission
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Check if a role has a specific permission
     */
    public static function hasPermission($role, $permissionSlug)
    {
        return self::where('role', $role)
            ->whereHas('permission', function ($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug);
            })
            ->exists();
    }
}
