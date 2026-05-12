<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = ['label', 'count', 'icon', 'is_active'];
}
