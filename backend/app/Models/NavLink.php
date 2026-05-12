<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class NavLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'url',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'label' => 'json',
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(NavLink::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(NavLink::class, 'parent_id')->orderBy('sort_order');
    }
}
