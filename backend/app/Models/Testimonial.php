<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['client_name', 'position', 'review', 'image', 'is_active', 'rating', 'video_url', 'audio_url'];

    protected $casts = [
        'client_name' => 'json',
        'position' => 'json',
        'review' => 'json',
    ];
}
