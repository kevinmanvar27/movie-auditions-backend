<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CanResetPassword, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'status',
        'mobile_number',
        'profile_photo',
        'image_gallery',
        'date_of_birth',
        'gender',
        'otp_code',
        'otp_expires_at',
        'is_verified',
        'device_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'image_gallery' => 'array',
            'otp_expires_at' => 'datetime',
        ];
    }
    
    /**
     * Get the role for the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission($permission)
    {
        // If the user has a role_id, load the role relationship and check if the role has the permission
        if ($this->role_id) {
            $role = $this->role()->first();
            if ($role) {
                return $role->hasPermission($permission);
            }
        }
        
        // If the user doesn't have a role relationship but has the old role field, check against it
        if ($this->attributes['role'] ?? null) {
            // For backward compatibility, map old roles to permissions
            $rolePermissions = [
                'admin' => [
                    'manage_users',
                    'manage_movies',
                    'manage_roles'
                ],
                'user' => [
                    'view_movies'
                ]
            ];
            
            return in_array($permission, $rolePermissions[$this->attributes['role']] ?? []);
        }
        
        return false;
    }
    
    /**
     * Check if the user has a specific role.
     */
    public function hasRole($role)
    {
        // If the user has a role_id, load the role relationship and check the role name
        if ($this->role_id) {
            $userRole = $this->role()->first();
            if ($userRole) {
                return $userRole->name === $role;
            }
        }
        
        // Otherwise, check against the old role field
        return ($this->attributes['role'] ?? null) === $role;
    }
    
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordAPI($token));
    }
    
    // Add relationship with Movies
    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}