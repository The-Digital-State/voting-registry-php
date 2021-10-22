<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PollCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'description' => 'required_if:publish,true|max:1500',
            'shortDescription' => 'required_if:publish,true|max:350',
            'start' => 'required_if:publish,true',
            'end' => 'required_if:publish,true',
            'question' => 'array',
            'question.title' => 'required_if:publish,true|max:500',
            'question.options' => 'required_if:publish,true|array|min:2',
            'question.options.*' => 'required_if:publish,true|distinct|nullable|string',
            'emailsListId' => 'required_if:publish,true',
            'publish' => 'boolean',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator(\Illuminate\Validation\Validator $validator)
    {
        $validator->sometimes('start', 'date|before:end', function ($input) {
            return !empty($input->end);
        });

        $validator->sometimes('end', 'date|after:start', function ($input) {
            return !empty($input->start);
        });

        $validator->sometimes('emailsListId', 'integer|exists:emails_lists,id,owner_id,' . $this->user()->id, function ($input) {
            return !empty($input->emailsListId);
        });
    }
}
