<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $table = 'notifications';

    public $fillable = [
        'title',
        'content',
        'user_id',
    ];

    protected $casts = [
        'title' => 'string',
        'content' => 'string',
    ];

    public static array $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string|max:65535',
        'user_id' => 'required',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
