<?php

namespace App\Repositories;

use App\Models\Item;
use App\Repositories\BaseRepository;

class ItemRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'type',
        'status',
        'cell_id',
        'category_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Item::class;
    }
}
