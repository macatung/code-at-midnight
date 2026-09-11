<?php

declare(strict_types=1);

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
        // Video Production Pipeline Attributes
        'video_status',
        'video_long_url',
        'video_short_url',
        'youtube_url',
        'playlist',
        'episode_number',
        'thumbnail_long_url',
        'thumbnail_short_url',
        'video_long_duration',
        'video_short_duration',
        'script_long',
        'script_short',
        'seo_title',
        'seo_description',
        'social_caption',
        'hashtags',
        'pipeline_task_id',
        'pipeline_started_at',
        'pipeline_completed_at',
        'pipeline_error',
    ];

    protected $appends = [
        'youtube_id',
        'has_video',
    ];

    protected $casts = [
        'paired_article_id' => 'integer',
        'episode_number' => 'integer',
        'tags' => 'array',
        'pali_terms' => 'array',
        'hashtags' => 'array',
        'is_published' => 'boolean',
        'reading_time_min' => 'integer',
        'published_at' => 'datetime',
        'pipeline_started_at' => 'datetime',
        'pipeline_completed_at' => 'datetime',
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

    public function scopeWithVideoStatus($query, ?string $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('video_status', $status);
        }

        return $query;
    }

    public function scopeInPlaylist($query, ?string $playlist)
    {
        if ($playlist && $playlist !== 'all') {
            if ($playlist === 'unassigned') {
                return $query->whereNull('playlist');
            }

            return $query->where('playlist', $playlist);
        }

        return $query;
    }

    public function getHasVideoAttribute(): bool
    {
        return !empty($this->video_long_url) || !empty($this->video_short_url) || !empty($this->youtube_url);
    }

    public function getYoutubeIdAttribute(): ?string
    {
        if (empty($this->youtube_url)) {
            return null;
        }

        $url = trim($this->youtube_url);
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}

