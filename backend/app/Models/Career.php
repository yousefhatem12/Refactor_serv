<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'department',
        'location',
        'salary',
        'description',
        'responsibilities',
        'requirements',
        'is_active',
    ];

    protected $casts = [
        'title' => 'json',
        'location' => 'json',
        'salary' => 'json',
        'description' => 'json',
        'responsibilities' => 'json',
        'requirements' => 'json',
        'is_active' => 'boolean',
    ];

    public function applications()
    {
        return $this->hasMany(CareerApplication::class);
    }
}
