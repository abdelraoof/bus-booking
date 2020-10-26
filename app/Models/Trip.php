<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'leaves_at' => 'datetime',
    ];

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function captain()
    {
        return $this->belongsTo(Captain::class);
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }
}
