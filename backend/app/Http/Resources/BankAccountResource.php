<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $trans = function ($field, $lang) {
            if (is_string($field)) {
                $decoded = json_decode($field, true);
                if (is_array($decoded)) {
                    $field = $decoded;
                }
            }
            if (!is_array($field)) {
                return (string) $field;
            }
            return (string) ($field[$lang] ?? $field['en'] ?? '');
        };

        return [
            'id' => (string) $this->id,
            'name' => [
                'en' => $trans($this->name, 'en'),
                'ar' => $trans($this->name, 'ar'),
            ],
            'owner' => [
                'en' => $trans($this->owner, 'en'),
                'ar' => $trans($this->owner, 'ar'),
            ],
            'account' => [
                'en' => $trans($this->account, 'en'),
                'ar' => $trans($this->account, 'ar'),
            ],
            'image' => $this->image ? (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/') ? $this->image : asset('storage/' . $this->image)) : null,
            'type' => $this->type,
            'country' => $this->country,
            'brandColor' => $this->brand_color ?: 'from-[#05050f] to-[#101025]',
            'shadow' => $this->shadow ?: 'rgba(0,0,0,0)',
        ];
    }
}
