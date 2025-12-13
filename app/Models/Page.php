<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'order',
        'show_in_menu',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'show_in_menu' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
            // Ensure unique slug
            $originalSlug = $page->slug;
            $count = 1;
            while (static::where('slug', $page->slug)->exists()) {
                $page->slug = $originalSlug . '-' . $count++;
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title') && !$page->isDirty('slug')) {
                $page->slug = Str::slug($page->title);
                // Ensure unique slug
                $originalSlug = $page->slug;
                $count = 1;
                while (static::where('slug', $page->slug)->where('id', '!=', $page->id)->exists()) {
                    $page->slug = $originalSlug . '-' . $count++;
                }
            }
        });
    }

    /**
     * Get the user who created the page.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the page.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for published pages
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for menu pages
     */
    public function scopeInMenu($query)
    {
        return $query->where('show_in_menu', true)->orderBy('order');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'published' => 'bg-green-100 text-green-800',
            'draft' => 'bg-yellow-100 text-yellow-800',
            'archived' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
