<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterJobSeekerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
     
    return [
        // user data
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',

        // job seeker data
        'id_number' => 'required',
        'major' => 'required',
        'experience_area' => 'required',
        'about_me' => 'nullable',

        'certificate' => 'nullable|file|mimes:pdf,jpg,png',
        'photo' => 'nullable|image',
    ];
    }
}
