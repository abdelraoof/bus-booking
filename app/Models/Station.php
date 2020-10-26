<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_slug', 'slug');
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }
}
