<?php

namespace App\Models;

use App\Traits\BelongsToTenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory, BelongsToTenantTrait;

    protected $fillable = ['name', 'phone', 'about', 'logo', 'slug'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
