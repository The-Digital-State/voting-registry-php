<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailsListUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'sometimes|required|string',
            'emails' => 'sometimes|required|array',
            'emails.*' => 'email:filter',
        ];
    }
}
