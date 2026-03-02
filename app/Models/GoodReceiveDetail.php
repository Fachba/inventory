<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodReceiveDetail extends Model
{
    use SoftDeletes;

    protected $table = 'good_receive_detail';
    protected $primaryKey = 'good_receive_detail_id';

    protected $fillable = [
        'good_receive_id',
        'purchase_order_detail_id',
        'product_id',
        'qty_gr'
    ];

    public function goodReceive()
    {
        return $this->belongsTo(GoodReceive::class, 'good_receive_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
