<?php

namespace App\Http\Requests\API;

use App\Models\Item;
use InfyOm\Generator\Request\APIRequest;

class CreateItemAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255|in:found,lost',
            "image" => 'required|string|url',
            'cell_id' => 'required|integer|exists:cells,id',
            'category_id' => 'required|integer|exists:categories,id',
            'additional_info' => "nullable"
        ];
    }
}
