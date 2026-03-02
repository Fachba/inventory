<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleAction extends Model
{
    use SoftDeletes;

    protected $table = 'action_menus';
    protected $primaryKey = 'action_menu_id';

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
