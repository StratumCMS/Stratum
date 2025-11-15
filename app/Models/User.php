<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'last_login_at',
        'avatar_url',
        'bio',
        'location',
        'website',
        'social_links',
        'backup_codes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'backup_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'social_links' => 'array',
    ];

    public function roles(){
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole($role){
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }

        if ($role instanceof Role) {
            return $this->roles()->where('id', $role->id)->exists();
        }

        return false;
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        if ($role instanceof Role) {
            return $this->roles()->syncWithoutDetaching([$role->id]);
        }

        return false;
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        if ($role instanceof Role) {
            return $this->roles()->detach($role->id);
        }

        return false;
    }

    public function syncRoles($roles)
    {
        $roleIds = [];

        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = Role::where('name', $role)->firstOrFail();
            }

            if ($role instanceof Role) {
                $roleIds[] = $role->id;
            } elseif (is_numeric($role)) {
                $roleIds[] = $role;
            }
        }

        return $this->roles()->sync($roleIds);
    }

    public function permissions()
    {
        return $this->roles->flatMap->permissions;
    }

    public function hasPermission($permission)
    {
        return $this->permissions()->contains('name', $permission);
    }

    public function hasAdminAccess(): bool
    {
        return $this->can('access_dashboard');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }


    public function likedArticles()
    {
        return $this->belongsToMany(Article::class, 'likes')->withTimestamps();
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPassed2FA(): bool
    {
        return session()->get('2fa_verified') === true;
    }

    public function isAdmin(): bool
    {
        return $this->role->is_admin;
    }
}
