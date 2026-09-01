<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_domain',
        'paired_article_id',
        'title',
        'title_en',
        'pali_title',
        'slug',
        'category',
        'excerpt',
        'excerpt_en',
        'author',
        'content',
        'content_en',
        'tags',
        'pali_terms',
        'audio_chanting_url',
        'reading_time_min',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'paired_article_id' => 'integer',
        'tags' => 'array',
        'pali_terms' => 'array',
        'is_published' => 'boolean',
        'reading_time_min' => 'integer',
        'published_at' => 'datetime',
    ];

    public function pairedArticle()
    {
        return $this->belongsTo(Article::class, 'paired_article_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('published_at', 'desc');
    }

    public function scopeForMain($query)
    {
        return $query->where('site_domain', 'main');
    }

    public function scopeForTheravada($query)
    {
        return $query->where('site_domain', 'theravada');
    }
}
