<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }
}
