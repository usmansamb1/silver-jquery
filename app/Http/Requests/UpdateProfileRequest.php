<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $user = $this->user();
        $rules = [
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ];

        if ($this->requiresPassword()) {
            // Password is only for validation/confirmation, NOT for updating the user's password
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        if ($user->registration_type === 'personal') {
            $rules = array_merge($rules, [
                'name' => ['required', 'string', 'max:255'],
                'gender' => ['required', 'in:male,female'],
                'region' => ['required', 'string', 'max:255'],
            ]);
        } else { // company
            $rules = array_merge($rules, [
                'company_type' => ['required', 'in:private,semi Govt.,Govt'],
                'company_name' => ['required', 'string', 'max:255'],
                'cr_number' => ['required', 'string', 'max:50'],
                'vat_number' => ['required', 'string', 'max:50'],
                'city' => ['required', 'string', 'max:255'],
                'building_number' => ['nullable', 'string', 'max:50'],
                'zip_code' => ['required', 'string', 'max:20'],
                'company_region' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
            ]);
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'avatar.max' => 'The profile image must not be larger than 5MB.',
            'avatar.mimes' => 'The profile image must be a JPEG, PNG, or JPG file.',
            'email.unique' => 'This email address is already in use.',
            'password.required' => 'Please confirm your password to make changes.',
            'cr_number.required' => 'The CR number is required for company profiles.',
            'vat_number.required' => 'The VAT number is required for company profiles.',
        ];
    }

    /**
     * Check if password confirmation is required for profile updates.
     * Note: This password is only used for validation/confirmation,
     * NOT for updating the user's actual password.
     */
    protected function requiresPassword(): bool
    {
        $rolesRequiringPassword = [
            'admin', 'finance', 'audit', 'it', 
            'contractor', 'validation', 'activation'
        ];

        return $this->user()->roles->pluck('name')
            ->intersect($rolesRequiringPassword)
            ->isNotEmpty();
    }
} 