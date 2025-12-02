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
        'role',
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
    
    // Removed the role relationship since we're using a string field now
}