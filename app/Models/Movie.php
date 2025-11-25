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
    
    protected $appends = ['genre_list'];
    
    public function getGenreListAttribute()
    {
        if (is_string($this->genre)) {
            $genres = json_decode($this->genre, true);
            return is_array($genres) ? $genres : [];
        }
        
        return is_array($this->genre) ? $this->genre : [];
    }
    
    public function setGenreAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['genre'] = json_encode($value);
        } else {
            $this->attributes['genre'] = $value;
        }
    }
    
    public function getGenreAttribute($value)
    {
        if (is_string($value)) {
            $genres = json_decode($value, true);
            return is_array($genres) ? $genres : [];
        }
        
        return is_array($value) ? $value : [];
    }
    
    public function roles()
    {
        return $this->hasMany(MovieRole::class);
    }
    
    public function auditions()
    {
        return $this->hasMany(Audition::class);
    }
}
