<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audition extends Model
{
    protected $fillable = [
        'user_id',
        'movie_id',
        'role',
        'applicant_name',
        'uploaded_videos',
        'old_video_backups',
        'notes',
        'status'
    ];

    protected $casts = [
        'uploaded_videos' => 'array',
        'old_video_backups' => 'array'
    ];

    /**
     * Get the user that owns the audition.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the movie associated with the audition.
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}