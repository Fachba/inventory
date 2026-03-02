<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $table = 'user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'role_id',
        'user_name',
        'user_email',
        'password',
        'is_active',
        'user_add',
        'user_upd',
        'user_del'
    ];

    protected $hidden = ['password'];

    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission($menu, $permission)
    {
        return $this->roles()
            ->whereHas('action_menus', function ($q) use ($menu, $permission) {
                $q->where('menu', $menu)->where('action', $permission);
            })
            ->exists();
    }
}
