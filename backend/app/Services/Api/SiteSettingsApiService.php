<?php

namespace App\Services\Api;

use App\Contracts\Api\ApiCacheServiceInterface;
use App\Http\Resources\BankAccountResource;
use App\Http\Resources\FeatureResource;
use App\Http\Resources\NavLinkResource;
use App\Http\Resources\PricingPlanResource;
use App\Http\Resources\TeamMemberResource;
use App\Http\Resources\TestimonialResource;
use App\Models\BankAccount;
use App\Models\Feature;
use App\Models\FooterLink;
use App\Models\NavLink;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Models\Setting;
use App\Models\SocialLink;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\LocalizationHelper;
use Illuminate\Http\Request;

class SiteSettingsApiService
{
    private const UPLOADED_MEDIA_KEYS = [
        'site_logo',
        'site_favicon',
        'social_share_image',
    ];

    public function __construct(
        private readonly ApiCacheServiceInterface $cache
    ) {}

    public function getSettings(string $lang): mixed
    {
        $cacheKey = "settings_v2_{$lang}";

        return $this->cache->remember($cacheKey, function () use ($lang) {
            return Setting::all()->mapWithKeys(function ($setting) use ($lang) {
                $value = $setting->value;
                $isUploadedMedia = in_array($setting->key, self::UPLOADED_MEDIA_KEYS, true)
                    || ($setting->type === 'image' && $setting->value_source === 'file');

                if ($setting->key === 'features_partners') {
                    return [$setting->key => self::uploadedMediaList($value)];
                }

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

                if (is_string($value) && str_starts_with($value, '[')) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        if ($isUploadedMedia) {
                            $value = array_map(static function ($img) {
                                return $img ? self::uploadedMediaUrl($img) : null;
                            }, $decoded);
                        } else {
                            $value = $decoded;
                        }

                        return [$setting->key => $value];
                    }
                }

                if ($isUploadedMedia && $value) {
                    $value = self::uploadedMediaUrl($value);
                }

                return [$setting->key => $value];
            });
        });
    }

    public function getTestimonials(Request $request, string $lang): array
    {
        $ver = $this->cache->version('testimonials');
        $cacheKey = "testimonials:v{$ver}:{$lang}";

        return $this->cache->remember($cacheKey, static function () use ($request): array {
            $records = Testimonial::where('is_active', true)->get();

            return TestimonialResource::collection($records)->resolve($request);
        });
    }

    public function getTeamMembers(Request $request, string $lang): array
    {
        $ver = $this->cache->version('team');
        $cacheKey = "team_members:v{$ver}:{$lang}";

        return $this->cache->remember($cacheKey, static function () use ($request): array {
            $records = TeamMember::where('is_active', true)->get();

            return TeamMemberResource::collection($records)->resolve($request);
        });
    }

    public function getFeatures(Request $request, string $lang): array
    {
        $ver = $this->cache->version('features');
        $cacheKey = "features:v{$ver}:{$lang}";

        return $this->cache->remember($cacheKey, static function () use ($request): array {
            $records = Feature::where('is_active', true)->get();

            return FeatureResource::collection($records)->resolve($request);
        });
    }

    public function getFooterData(string $lang): array
    {
        $ver = $this->cache->version('footer');
        $cacheKey = "footer_data:v{$ver}:{$lang}";

        return $this->cache->remember($cacheKey, static function () use ($lang): array {
            $keys = [
                'footer_heading_' . $lang,
                'footer_heading_en',
                'footer_description_' . $lang,
                'footer_description_en',
                'footer_button_text_' . $lang,
                'footer_button_text_en',
                'footer_copyright_' . $lang,
                'footer_copyright_en',
            ];
            $settingsMap = Setting::whereIn('key', array_unique($keys))
                ->get()
                ->pluck('value', 'key');

            $texts = [
                'heading' => $settingsMap['footer_heading_' . $lang]
                    ?? $settingsMap['footer_heading_en']
                    ?? 'Have a Good Web Design<br/>With Serv5.',
                'description' => $settingsMap['footer_description_' . $lang]
                    ?? $settingsMap['footer_description_en']
                    ?? 'Serv5 specializes in modern web solutions...',
                'button_text' => $settingsMap['footer_button_text_' . $lang]
                    ?? $settingsMap['footer_button_text_en']
                    ?? 'Get Started',
                'copyright' => $settingsMap['footer_copyright_' . $lang]
                    ?? $settingsMap['footer_copyright_en']
                    ?? '2026 © Serv5. All Rights Reserved.',
            ];

            if ($lang === 'ar') {
                $texts = [
                    'heading' => ($settingsMap['footer_heading_ar'] ?? null)
                        ?: "احصل على تصميم ويب\nرائع اليوم",
                    'description' => ($settingsMap['footer_description_ar'] ?? null)
                        ?: 'خبرة عالية المستوى في تصميم وتطوير الويب وإنتاج أعمال عالية الجودة.',
                    'button_text' => ($settingsMap['footer_button_text_ar'] ?? null)
                        ?: 'ابدأ الآن',
                    'copyright' => ($settingsMap['footer_copyright_ar'] ?? null)
                        ?: '© 2026 جميع الحقوق محفوظة - Serv5',
                ];
            }

            $links = FooterLink::orderBy('sort_order')->get()->groupBy('column_name')->map(static function ($group) use ($lang) {
                return $group->map(static function ($link) use ($lang) {
                    return [
                        'id' => $link->id,
                        'label' => LocalizationHelper::trans($link->label, $lang),
                        'url' => $link->url,
                    ];
                });
            });

            $socials = SocialLink::where('is_active', true)->get()->map(static function ($social) {
                return [
                    'id' => $social->id,
                    'platform' => $social->platform,
                    'url' => $social->url,
                ];
            });

            return [
                'texts' => $texts,
                'links' => [
                    'usefulLinks' => $links['useful_links'] ?? [],
                    'quickLinks' => $links['quick_links'] ?? [],
                    'offers' => $links['offers'] ?? [],
                    'company' => $links['company'] ?? [],
                ],
                'socials' => $socials,
            ];
        });
    }

    public function getNavLinks(Request $request, string $lang): array
    {
        $ver = $this->cache->version('nav_links');

        return $this->cache->remember("nav_links:v{$ver}:{$lang}", static function () use ($request): array {
            $records = NavLink::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => static function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();

            return NavLinkResource::collection($records)->resolve($request);
        });
    }

    public function getPricingPlans(Request $request, string $lang, mixed $serviceIdRaw, string $serviceKey): array
    {
        if ($serviceIdRaw !== null && $serviceIdRaw !== '' && $serviceKey === 'invalid') {
            return [];
        }

        $ver = $this->cache->version('pricing_plans');
        $cacheKey = "pricing_plans:v{$ver}:{$lang}:service_{$serviceKey}";

        return $this->cache->remember($cacheKey, static function () use ($serviceKey, $request): array {
            $query = PricingPlan::where('is_active', true);

            if ($serviceKey !== 'all') {
                $serviceId = $serviceKey;
                if (! is_numeric($serviceId)) {
                    $service = Service::where('slug', $serviceKey)->select(['id'])->first();
                    if (! $service) {
                        return [];
                    }
                    $serviceId = $service->id;
                }
                $query->where('service_id', (int) $serviceId);
            }

            $records = $query->orderBy('sort_order')->get();

            return PricingPlanResource::collection($records)->resolve($request);
        });
    }

    public function getBankAccounts(
        Request $request,
        string $lang,
        mixed $countryRaw,
        ?string $country,
        int $page,
        int $perPage
    ): array {
        if ($countryRaw !== null && $countryRaw !== '' && $country === null) {
            return [
                'data' => [],
                'current_page' => $page,
                'last_page' => 1,
                'total' => 0,
                'has_more' => false,
            ];
        }

        $ver = $this->cache->version('bank_accounts');
        $countryKey = $country ? strtolower(str_replace(' ', '_', $country)) : 'all';
        $cacheKey = "bank_accounts:v{$ver}:{$lang}:country_{$countryKey}:page_{$page}:per_{$perPage}";

        return $this->cache->remember($cacheKey, static function () use ($country, $page, $perPage, $request): array {
            $query = BankAccount::orderBy('sort_order', 'asc');

            if ($country) {
                $query->where('country', $country);
            }

            $accountsPaginated = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'data' => BankAccountResource::collection($accountsPaginated->items())->resolve($request),
                'current_page' => $accountsPaginated->currentPage(),
                'last_page' => $accountsPaginated->lastPage(),
                'total' => $accountsPaginated->total(),
                'has_more' => $accountsPaginated->hasMorePages(),
            ];
        });
    }

    private static function uploadedMediaUrl(string $value): string
    {
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        $path = preg_replace('#^/?storage/#', '', $value);

        return asset('storage/' . ltrim((string) $path, '/'));
    }

    /**
     * @return list<string>
     */
    private static function uploadedMediaList(mixed $value): array
    {
        if (is_string($value)) {
            $trimmed = trim($value);
            $decoded = json_decode($trimmed, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            } elseif ($trimmed === '') {
                return [];
            } else {
                $value = [$trimmed];
            }
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(static function ($item) {
            if (! is_string($item) || trim($item) === '') {
                return null;
            }

            return self::uploadedMediaUrl(trim($item));
        }, array_values($value))));
    }
}
