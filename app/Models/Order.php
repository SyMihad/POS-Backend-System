<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }
}
