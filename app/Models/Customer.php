<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
    ];
    
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }
}
