<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'key',
        'title',
        'tagline',
        'description',
        'category',
        'type', // 'work' or 'personal'
        'color',
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

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class);
    }

    public function epics(): HasMany
    {
        return $this->hasMany(Task::class)->where('issue_type', 'epic');
    }

    public function getEffectiveKeyAttribute(): string
    {
        if (!empty($this->key)) {
            return strtoupper($this->key);
        }
        // Fallback key from title/slug
        $words = preg_split('/[\s_-]+/', $this->slug ?: $this->title);
        $key = '';
        foreach ($words as $w) {
            if (!empty($w)) $key .= strtoupper($w[0]);
            if (strlen($key) >= 4) break;
        }
        return $key ?: ('PRJ' . $this->id);
    }

    /**
     * Scope for work projects
     */
    public function scopeWork($query)
    {
        return $query->where('type', 'work');
    }

    /**
     * Scope for personal projects
     */
    public function scopePersonal($query)
    {
        return $query->where('type', 'personal');
    }

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
