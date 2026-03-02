<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpnameDetail extends Model
{
    use SoftDeletes;

    protected $table = 'stock_opname_detail';
    protected $primaryKey = 'stock_opname_detail_id';

    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'system_stock',
        'physical_stock'
    ];
}