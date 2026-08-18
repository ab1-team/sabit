<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VideoLanding extends Model
{
    public const SOURCE_YOUTUBE = 'youtube';
    public const SOURCE_LOCAL = 'local';

    protected $table = 'lp_video';

    protected $fillable = [
        'title',
        'description',
        'youtube_url',
        'source',
        'file_path',
        'poster',
        'thumbnail',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function isLocal(): bool
    {
        return ($this->source ?? self::SOURCE_YOUTUBE) === self::SOURCE_LOCAL;
    }

    public function isYoutube(): bool
    {
        if ($this->isLocal()) return false;
        return ! empty($this->youtube_url);
    }

    public function getYoutubeIdAttribute(): ?string
    {
        if ($this->isLocal()) return null;
        return \App\Http\Controllers\AdminLandingController::extractYoutubeId($this->youtube_url);
    }

    /**
     * URL publik untuk ditampilkan/diputar — YouTube embed atau file lokal.
     * Mengembalikan null bila tidak ada sumber yang valid.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->isLocal()) {
            $path = $this->file_path;
            return $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
        }

        if (! empty($this->youtube_url)) {
            $id = \App\Http\Controllers\AdminLandingController::extractYoutubeId($this->youtube_url);
            return $id ? 'https://www.youtube.com/embed/' . $id : null;
        }

        return null;
    }

    /**
     * URL mentah untuk embed YouTube (sama dengan embed_url — alias).
     */
    public function getYoutubeEmbedAttribute(): ?string
    {
        if ($this->isLocal()) return null;
        $id = \App\Http\Controllers\AdminLandingController::extractYoutubeId($this->youtube_url);
        return $id ? 'https://www.youtube.com/embed/' . $id : null;
    }

    /**
     * URL thumbnail untuk sumber apapun:
     *  - local : poster kustom atau ''
     *  - youtube: hqdefault YouTube
     */
    public function getDisplayThumbAttribute(): ?string
    {
        if ($this->isLocal()) {
            if (! empty($this->poster)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->url($this->poster);
            }
            return null;
        }

        $id = \App\Http\Controllers\AdminLandingController::extractYoutubeId($this->youtube_url);
        return $id ? 'https://i.ytimg.com/vi/' . $id . '/hqdefault.jpg' : null;
    }
}

