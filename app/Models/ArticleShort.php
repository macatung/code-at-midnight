<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleShort extends Model
{
    use HasFactory;

    protected $table = 'article_shorts';

    protected $fillable = [
        'article_id',
        'title',
        'focus_hook',
        'visual_style',
        'video_url',
        'thumbnail_url',
        'duration',
        'script',
        'display_text',
        'spoken_text',
        'youtube_shorts_url',
        'tiktok_url',
        'reels_url',
        'order_index',
        'status',
    ];

    protected $casts = [
        'article_id' => 'integer',
        'order_index' => 'integer',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}