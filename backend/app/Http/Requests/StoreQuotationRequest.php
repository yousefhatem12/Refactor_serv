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
            'website_link' => 'nullable|url:http,https|max:2048',
            'content_managers' => 'nullable|string|max:255',
            'pre_designed_logo' => 'nullable|string|max:255',
            'design_images' => 'nullable|string|max:255',
            'vision_goals' => 'nullable|string|max:5000',
            'product_brief' => 'nullable|string|max:5000',
            'color_preferences' => 'nullable|string|max:5000',
            'reference_sites' => 'nullable|string|max:5000',
            'additional_notes' => 'nullable|string|max:5000',
            'files' => 'nullable|array|max:5',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp|mimetypes:image/jpeg,image/png,image/webp|max:5120|dimensions:max_width=4096,max_height=4096',
        ];
    }
}
