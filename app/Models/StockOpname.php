<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpname extends Model
{
    use SoftDeletes;

    protected $table = 'stock_opnames';
    protected $primaryKey = 'stock_opname_id';

    protected $fillable = [
        'stock_opname_period',
        'stock_opname_year',
        'status_id'
    ];

    public function details()
    {
        return $this->hasMany(StockOpnameDetail::class, 'stock_opname_id');
    }
}
