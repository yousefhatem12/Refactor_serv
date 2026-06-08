<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\TeamMember;
use App\Models\Feature;
use App\Models\FooterLink;
use App\Models\SocialLink;
use App\Models\NavLink;
use App\Models\BankAccount;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Http\Resources\TestimonialResource;
use App\Http\Resources\TeamMemberResource;
use App\Http\Resources\FeatureResource;
use App\Http\Resources\NavLinkResource;
use App\Http\Resources\BankAccountResource;
use App\Http\Resources\PricingPlanResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends BaseApiController
{
    public function settings()
    {
        $lang = $this->getLang();
        $cacheKey = "settings_{$lang}";

        $settings = Cache::remember($cacheKey, 3600, function () use ($lang) {
            return Setting::all()->mapWithKeys(function ($setting) use ($lang) {
                $value = $setting->value;

                // Handle multilingual JSON settings
                if (is_string($value) && str_starts_with($value, '{')) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        if (array_key_exists($lang, $decoded)) {
                            $value = $decoded[$lang];
                        } elseif (array_key_exists('en', $decoded)) {
                            $value = $decoded['en'];
                        } elseif (array_key_exists('ar', $decoded)) {
                            $value = $decoded['ar'];
                        } else {
                            $value = '';
                        }
                    }
                }

                // Handle array JSON settings (like multiple image upload)
                if (is_string($value) && str_starts_with($value, '[')) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        if ($setting->type === 'image' && $setting->value_source === 'file') {
                            $value = array_map(function ($img) {
                                return $img ? asset('storage/' . $img) : null;
                            }, $decoded);
                        } else {
                            $value = $decoded;
                        }
                        return [$setting->key => $value];
                    }
                }

                if ($setting->type === 'image' && $setting->value_source === 'file' && $value) {
                    $value = asset('storage/' . $value);
                }
                return [$setting->key => $value];
            });
        });
        
        return response()->json($settings);
    }

    public function testimonials()
    {
        $lang = $this->getLang();
        $cacheKey = "testimonials_{$lang}";

        $testimonials = Cache::remember($cacheKey, 3600, function () {
            $records = Testimonial::where('is_active', true)->get();
            return TestimonialResource::collection($records);
        });
        
        return response()->json($testimonials);
    }

    public function team()
    {
        $lang = $this->getLang();
        $cacheKey = "team_members_{$lang}";

        $team = Cache::remember($cacheKey, 3600, function () {
            $records = TeamMember::where('is_active', true)->get();
            return TeamMemberResource::collection($records);
        });
        
        return response()->json($team);
    }

    public function features()
    {
        $lang = $this->getLang();
        $cacheKey = "features_{$lang}";

        $features = Cache::remember($cacheKey, 3600, function () {
            $records = Feature::where('is_active', true)->get();
            return FeatureResource::collection($records);
        });
        
        return response()->json($features);
    }

    public function footer()
    {
        $lang = $this->getLang();
        $texts = [
            'heading' => Setting::where('key', 'footer_heading_' . $lang)->value('value') 
                ?: Setting::where('key', 'footer_heading_en')->value('value') 
                ?: 'Have a Good Web Design<br/>With Serv5.',
            'description' => Setting::where('key', 'footer_description_' . $lang)->value('value') 
                ?: Setting::where('key', 'footer_description_en')->value('value') 
                ?: 'Serv5 specializes in modern web solutions...',
            'button_text' => Setting::where('key', 'footer_button_text_' . $lang)->value('value') 
                ?: Setting::where('key', 'footer_button_text_en')->value('value') 
                ?: 'Get Started',
            'copyright' => Setting::where('key', 'footer_copyright_' . $lang)->value('value') 
                ?: Setting::where('key', 'footer_copyright_en')->value('value') 
                ?: '2026 © Serv5. All Rights Reserved.'
        ];

        $links = FooterLink::orderBy('sort_order')->get()->groupBy('column_name')->map(function ($group) use ($lang) {
            return $group->map(function ($link) use ($lang) {
                return [
                    'id' => $link->id,
                    'label' => $this->trans($link->label, $lang),
                    'url' => $link->url,
                ];
            });
        });

        $socials = SocialLink::where('is_active', true)->get()->map(function ($social) {
            return [
                'id' => $social->id,
                'platform' => $social->platform,
                'url' => $social->url,
            ];
        });

        return response()->json([
            'texts' => $texts,
            'links' => [
                'usefulLinks' => $links['useful_links'] ?? [],
                'quickLinks' => $links['quick_links'] ?? [],
                'offers' => $links['offers'] ?? [],
                'company' => $links['company'] ?? [],
            ],
            'socials' => $socials
        ]);
    }

    public function navLinks()
    {
        $links = NavLink::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return response()->json(NavLinkResource::collection($links));
    }

    public function pricingPlans()
    {
        $lang = $this->getLang();
        $serviceId = request('service_id');
        $cacheKey = "pricing_plans_{$lang}_service_" . ($serviceId ?: 'all');

        $plans = Cache::remember($cacheKey, 3600, function () use ($serviceId) {
            $query = PricingPlan::where('is_active', true);

            if ($serviceId) {
                if (!is_numeric($serviceId)) {
                    $service = Service::where('slug', $serviceId)->first();
                    $serviceId = $service ? $service->id : 999999;
                }
                $query->where('service_id', $serviceId);
            }

            $records = $query->orderBy('sort_order')->get();
            return PricingPlanResource::collection($records);
        });

        return response()->json($plans);
    }

    public function bankAccounts(Request $request)
    {
        $lang = $this->getLang();
        $country = $request->query('country');
        $page = $request->query('page');
        $perPage = $request->query('per_page', 9);

        $cacheKey = "bank_accounts_{$lang}_country_" . ($country ?: 'all') . "_page_" . ($page ?: 'none') . "_per_" . $perPage;

        $response = Cache::remember($cacheKey, 3600, function () use ($country, $page, $perPage) {
            $query = BankAccount::orderBy('sort_order', 'asc');

            if ($country) {
                $query->where('country', $country);
            }

            if ($page) {
                $accountsPaginated = $query->paginate($perPage);
                return [
                    'type' => 'paginated',
                    'data' => [
                        'data' => BankAccountResource::collection($accountsPaginated->items()),
                        'current_page' => $accountsPaginated->currentPage(),
                        'last_page' => $accountsPaginated->lastPage(),
                        'has_more' => $accountsPaginated->hasMorePages(),
                    ]
                ];
            }

            $accounts = $query->get();
            return [
                'type' => 'flat',
                'data' => BankAccountResource::collection($accounts)
            ];
        });

        return response()->json($response['data']);
    }
}
