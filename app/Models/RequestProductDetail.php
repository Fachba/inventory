<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestProductDetail extends Model
{
    use SoftDeletes;

    protected $table = 'request_product_detail';
    protected $primaryKey = 'request_product_detail_id';

    protected $fillable = [
        'request_product_id',
        'product_id',
        'qty_rp',
        'user_add',
        'user_upd',
        'user_del'
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(RequestProduct::class, 'request_product_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}