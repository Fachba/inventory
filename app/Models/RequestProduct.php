<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestProduct extends Model
{
    use SoftDeletes;

    protected $table = 'request_products';
    protected $primaryKey = 'request_product_id';

    protected $fillable = [
        'request_date',
        'status_id',
        'user_add',
        'user_upd',
        'user_del'
    ];

    public function details()
    {
        return $this->hasMany(RequestProductDetail::class, 'request_product_id');
    }
}
