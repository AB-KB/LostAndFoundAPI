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
            "image"=> 'file|mimes:png,jpg,jpeg,gif',
            'cell_id' => 'required',
            'category_id' => 'required',
            'additional_info'=> "nullable"
        ];
    }
}
