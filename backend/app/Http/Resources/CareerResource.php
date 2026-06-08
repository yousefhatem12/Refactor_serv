<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = request('lang', request()->header('Accept-Language', 'en'));
        if (is_array($lang)) {
            $lang = reset($lang);
        }
        $lang = str_starts_with(strtolower((string)$lang), 'ar') ? 'ar' : 'en';

        $responsibilities = ['en' => [], 'ar' => []];
        if (is_array($this->responsibilities)) {
            foreach (['en', 'ar'] as $l) {
                $list = $this->responsibilities[$l] ?? [];
                if (is_array($list)) {
                    foreach ($list as $item) {
                        $val = $item['responsibility'] ?? (is_string($item) ? $item : '');
                        if ($val) $responsibilities[$l][] = $val;
                    }
                }
            }
        }

        $requirements = ['en' => [], 'ar' => []];
        if (is_array($this->requirements)) {
            foreach (['en', 'ar'] as $l) {
                $list = $this->requirements[$l] ?? [];
                if (is_array($list)) {
                    foreach ($list as $item) {
                        $val = $item['requirement'] ?? (is_string($item) ? $item : '');
                        if ($val) $requirements[$l][] = $val;
                    }
                }
            }
        }

        return [
            'id' => $this->id,
            'department' => $this->department,
            'title' => $this->title,
            'location' => $this->location,
            'salary' => $this->salary,
            'description' => $this->description,
            'responsibilities' => $responsibilities,
            'requirements' => $requirements,
        ];
    }
}
