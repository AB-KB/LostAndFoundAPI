<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;
    public $table = 'messages';

    public $fillable = [
        'text',
        'is_from_admin',
        'item_message_thread_id'
    ];

    protected $casts = [
        'text' => 'string',
        'is_from_admin' => 'boolean'
    ];

    public static array $rules = [
        'text' => 'required|string|max:65535',
        'is_from_admin' => 'required|boolean',
        'item_message_thread_id' => 'required',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function thread(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\ItemMessageThread::class, 'item_message_thread_id');
    }
}
