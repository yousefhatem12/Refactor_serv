<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectConsultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_type',
        'has_docs',
        'budget',
        'timeline',
        'decision_maker',
        'references',
        'name',
        'phone',
        'email',
        'company',
        'status',
        'read_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
