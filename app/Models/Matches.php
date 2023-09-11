<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Matches extends Model
{
    use HasFactory;

    public $table = 'matches';

    public $fillable = [
        'item_id',
        'with_item_id',
        'percentage'
    ];

    protected $casts = [
        'percentage' => 'float'
    ];

    public static array $rules = [
        'item_id' => 'required',
        'with_item_id' => 'required',
        'percentage' => 'required|numeric',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function item(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Item::class, 'item_id');
    }

    public function withItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Item::class, 'with_item_id');
    }
}
