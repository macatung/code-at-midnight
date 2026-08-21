<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'tagline',
        'description',
        'category',
        'cover_gradient',
        'tags',
        'tech_stack',
        'metrics',
        'architecture_highlights',
        'midnight_fact',
        'live_url',
        'github_url',
        'featured',
        'order',
    ];

    protected $casts = [
        'tags' => 'array',
        'tech_stack' => 'array',
        'metrics' => 'array',
        'architecture_highlights' => 'array',
        'featured' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope for featured projects
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope for ordered projects
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'desc');
    }
}
