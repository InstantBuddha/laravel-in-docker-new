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
            'name' => ['required', 'string', 'regex:/^(?=[\p{L}. -]{5,30}$)(?!(?:.*[.-]){2})\p{L}.*\p{L}[.\p{L} -]*$/u'],
            'email' => ['required', 'email'],
            'phone_number' => ['required', 'string', 'regex:/^\d{10,16}$/'],
            'zipcode' => ['string', 'regex:/^[A-Za-z0-9 -]{4,10}$/'],
            'city' => ['string', 'regex:/^[\p{L}'.'\s-]{2,20}$/u'],
            'address' => ['string', 'regex:/^(?=.*\p{L})[a-zA-Z0-9\p{L}'.',\/\s-]{5,40}$/u'],
            'comment' => ['string', 'regex:/^[0-9\p{L}.,:!?\s]{5,100}$/u'],
            'mailinglist' => ['required', 'boolean'],
        ];
    }
}
