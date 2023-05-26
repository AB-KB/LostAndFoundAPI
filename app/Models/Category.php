<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\Factories\HasFactory;
class Category extends Model
{
    use HasFactory;    public $table = 'categories';

    public $fillable = [
        'name',
        'color',
    ];

    protected $casts = [
        'name' => 'string'
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
        'teal' => 'required|string|max:10',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];


}
