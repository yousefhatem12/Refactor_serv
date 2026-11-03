<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_type' => 'nullable|string|max:255',
            'has_docs' => 'nullable|string|max:255',
            'budget' => 'nullable|string|max:255',
            'timeline' => 'nullable|string|max:255',
            'decision_maker' => 'nullable|string|max:255',
            'references' => 'nullable|string|max:5000',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
        ];
    }
}
