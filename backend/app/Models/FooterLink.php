<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    protected $fillable = ['label', 'url', 'column_name', 'sort_order'];

    protected $casts = [
        'label' => 'json',
    ];
}
