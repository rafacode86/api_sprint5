<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'origin',
        'classification',
    ];

    
    public function cocktails()
    {
        return $this->belongsToMany(Cocktail::class)
                    ->withPivot('amount')
                    ->withTimestamps();
    }
}
