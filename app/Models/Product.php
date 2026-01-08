<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TenantScope;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'sku',  
        'price',
        'stock',
        'low_stock_threshold',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }
}
