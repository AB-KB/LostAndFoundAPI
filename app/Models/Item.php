<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'added_by',
        'cell_id',
        'category_id',
        'additional_info',
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


    public function addedBy()
    {

        return $this->belongsTo(User::class, "added_by");
    }


    /**
     * Get the additional_info
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function additionalInfo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true),
            set: fn ($value) => json_encode($value),
        );
    }
}
