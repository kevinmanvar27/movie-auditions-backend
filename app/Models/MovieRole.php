<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovieRole extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'movie_id',
        'character_name',
        'description',
        'status',
    ];
    
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
    
    public function auditions()
    {
        return $this->hasMany(Audition::class);
    }
}
