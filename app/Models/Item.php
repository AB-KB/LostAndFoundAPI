<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Saad\ModelImages\Contracts\ImageableContract;
use Saad\ModelImages\Traits\HasImages;

class Item extends Model  implements ImageableContract
{
    use HasFactory, HasImages;

    public $table = 'items';

    public $fillable = [
        'name',
        'type',
        'status',
        'cell_id',
        'category_id'
    ];

    protected $casts = [
        'name' => 'string',
        'type' => 'string',
        'status' => 'string'
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|string|max:255|in:found,lost',
        'status' => 'required|string|max:255|in:pending,processed',
        'cell_id' => 'required',
        'category_id' => 'required',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    public function cell(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Cell::class, 'cell_id');
    }

    public static function imageableFields(): array
    {
        return ['image'];
    }
}
