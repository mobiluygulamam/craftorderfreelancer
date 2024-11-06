<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'status',
        'cost',
        'summary',
        'progress',
        'start_date',
        'end_date'
    ];
}
