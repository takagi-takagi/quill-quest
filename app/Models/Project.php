<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function texts() {
        return $this->hasMany(Text::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
