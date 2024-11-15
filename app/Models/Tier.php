<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    use HasFactory;

    protected $table = 'tier';

    protected $fillable = [
        'name',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'tier_id');
    }
}