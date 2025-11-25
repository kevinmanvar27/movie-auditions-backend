<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovieRole extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'movie_id',
        'character_name',
        'description',
        'status',
        'role_type',
        'gender',
        'age_range',
        'dialogue_sample',
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