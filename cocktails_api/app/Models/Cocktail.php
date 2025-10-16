<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cocktail extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
    ];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)
                    ->withPivot('amount')
                    ->withTimestamps();
    }
}
