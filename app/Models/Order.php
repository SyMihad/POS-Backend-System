<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TenantScope;

class Order extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'customer_id',
        'status',
        'total_amount',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
