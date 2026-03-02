<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogStatus extends Model
{
    use SoftDeletes;

    protected $table = 'log_status';
    protected $primaryKey = 'log_status_id';

    protected $fillable = [
        'modul_name',
        'data_id',
        'old_status',
        'new_status',
        'user_add',
        'user_upd',
        'user_del'
    ];
}