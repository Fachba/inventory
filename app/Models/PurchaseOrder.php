<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_orders';
    protected $primaryKey = 'purchase_order_id';

    protected $fillable = [
        'po_date',
        'vendor',
        'status_id',
        'user_add',
        'user_upd',
        'user_del'
    ];

    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id');
    }
}
