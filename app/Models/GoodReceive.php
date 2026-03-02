<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodReceive extends Model
{
    use SoftDeletes;

    protected $table = 'good_receives';
    protected $primaryKey = 'good_receive_id';

    protected $fillable = [
        'purchase_order_id',
        'gr_date',
        'status_id'
    ];

    public function details()
    {
        return $this->hasMany(GoodReceiveDetail::class, 'good_receive_id');
    }
}