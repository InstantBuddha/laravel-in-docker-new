<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:8'],
            'email' => ['required', 'email', 'min:8'],
            'phone_number' => ['required', 'string', 'min:7', 'max:20'],
            'zipcode' => ['string', 'max:15'],
            'city' => ['string', 'max:40'],
            'address' => ['string', 'max:80'],
            'comment' => ['string', 'max:250'],
            'is_subscribed_to_mailing_list' => ['required', 'boolean'],
        ];
    }
}
