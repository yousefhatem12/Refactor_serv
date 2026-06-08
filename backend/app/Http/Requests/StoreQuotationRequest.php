<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'request_type' => 'nullable|string|max:255',
            'budget' => 'nullable|string|max:255',
            'website_link' => 'nullable|string|max:255',
            'content_managers' => 'nullable|string|max:255',
            'pre_designed_logo' => 'nullable|string|max:255',
            'design_images' => 'nullable|string|max:255',
            'vision_goals' => 'nullable|string',
            'product_brief' => 'nullable|string',
            'color_preferences' => 'nullable|string',
            'reference_sites' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'files.*' => 'nullable|file|image|max:10240',
        ];
    }
}
