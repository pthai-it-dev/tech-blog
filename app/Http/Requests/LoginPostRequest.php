<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use const App\Helpers\ALLOWED_THIRD_PARTY;

class LoginPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize () : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, mixed>
     */
    public function rules () : array
    {
        if ($this->route('third_party') == null)
        {
            return [
                'email'    => ['required', 'string', 'email:rfc,dns'],
                'password' => ['required', 'string']
            ];
        }
        else
        {
            return ['token' => ['required', 'string']];
        }
    }
}
