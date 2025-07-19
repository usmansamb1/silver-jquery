<?php

namespace App\Http\Requests\ApprovalWorkflow;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:approval_workflows,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The workflow name is required.',
            'name.unique' => 'This workflow name is already in use.',
            'name.max' => 'The workflow name cannot exceed 255 characters.',
            'description.max' => 'The description cannot exceed 1000 characters.',
        ];
    }
} 