<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'genre',
        'release_date',
        'director',
        'status',
    ];
    
    protected $casts = [
        'release_date' => 'date',
    ];
    
    public function roles()
    {
        return $this->hasMany(MovieRole::class);
    }
    
    public function auditions()
    {
        return $this->hasMany(Audition::class);
    }
}
