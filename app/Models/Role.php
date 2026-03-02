<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $table = 'role';
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role_name',
        'user_add',
        'user_upd',
        'user_del'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(RoleAction::class);
    }
}
