<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Text extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_text_id',
        'project_id',
        'body',
        'is_posted',
        'type'
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }
    public function bodyHead() {
        return Str::substr($this->body, 0, 25) . "... (" .Str::length($this->body). "文字)";
    }
}
