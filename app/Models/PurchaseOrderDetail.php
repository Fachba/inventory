<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderDetail extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_order_detail';
    protected $primaryKey = 'purchase_order_detail_id';

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'qty_po',
        'user_add',
        'user_upd',
        'user_del'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
