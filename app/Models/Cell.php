<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cell extends Model
{
    use HasFactory;
    public $table = 'cells';

    public $fillable = [
        'name',
        'sector_id',
    ];

    protected $casts = [
        'name' => 'string',
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
        'sector_id' => 'required',
    ];

    public function sector(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Sector::class, 'sector_id');
    }

    public function villages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\village::class, 'cell_id');
    }
}
