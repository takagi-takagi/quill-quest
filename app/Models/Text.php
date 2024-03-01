<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'body',
        'is_posted'
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }
}