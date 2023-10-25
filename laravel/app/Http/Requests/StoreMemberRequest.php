<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {   
        return true;    //supposedly this needs to be true instead
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email'],
            'phone_number' => ['required', 'string'],
            'zipcode' => ['string'],
            'city' => ['string'],
            'address' => ['string'],
            'comment' => ['string'],
            'mailinglist' => ['required', 'boolean'],
        ];
    }
}
