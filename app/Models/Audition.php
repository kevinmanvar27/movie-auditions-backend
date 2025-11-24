<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Audition extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'movie_id',
        'movie_role_id',
        'applicant_name',
        'applicant_email',
        'audition_date',
        'audition_time',
        'notes',
        'status',
    ];
    
    protected $casts = [
        'audition_date' => 'date',
        'audition_time' => 'string', // Changed from 'time' to 'string'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
    
    public function role()
    {
        return $this->belongsTo(MovieRole::class, 'movie_role_id');
    }
}