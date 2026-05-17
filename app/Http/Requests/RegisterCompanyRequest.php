<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterCompanyRequest extends FormRequest
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
        
        'company_name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',

        // company data
        'company_email' => 'required|email',
        'company_code' => 'required',
        'company_address' => 'required',
        'services' => 'nullable|string',
        'bio' => 'nullable|string',

        'accreditation_certificate' => 'nullable|file|mimes:pdf,jpg,png',
    ];
    }
}
