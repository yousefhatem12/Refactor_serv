<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Faq;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\TeamMember;
use App\Models\Feature;
use App\Models\Statistic;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    private function getLang()
    {
        $lang = request('lang', request()->header('Accept-Language', 'en'));
        return str_starts_with(strtolower($lang), 'ar') ? 'ar' : 'en';
    }

    private function trans($field, $lang)
    {
        if (!is_array($field)) {
            return $field;
        }
        return $field[$lang] ?? $field['en'] ?? '';
    }

    public function getServices()
    {
        $lang = $this->getLang();
        $services = Service::where('is_active', true)->get();

        $mapped = $services->map(function ($service, $index) use ($lang) {
            $gradients = [
                ['from' => '#A855F7', 'to' => '#6366F1', 'dot' => '#A855F7', 'shadow' => 'shadow-[0_0_20px_#A855F7]'],
                ['from' => '#3B82F6', 'to' => '#22C55E', 'dot' => '#22C55E', 'shadow' => 'shadow-[0_0_20px_#22C55E]'],
                ['from' => '#F97316', 'to' => '#EAB308', 'dot' => '#F97316', 'shadow' => 'shadow-[0_0_20px_#F97316]'],
                ['from' => '#10B981', 'to' => '#059669', 'dot' => '#10B981', 'shadow' => 'shadow-[0_0_20px_#10B981]'],
            ];
            $style = $gradients[$index % count($gradients)];

            $name = $this->trans($service->name, $lang);
            return [
                'id' => $service->slug,
                'title' => strtolower($this->trans($service->name, 'en')), // Keep internal title as english if needed, or translate
                'label' => $name,
                'description' => $this->trans($service->description, $lang),
                'img' => $service->icon ?: '/Services/Purple Planet.png',
                'gradient' => "from-[{$style['from']}] to-[{$style['to']}]",
                'dotColor' => "bg-[{$style['dot']}]",
                'shadow' => $style['shadow'],
            ];
        });

        return response()->json($mapped);
    }

    public function getProjects()
    {
        $lang = $this->getLang();
        $projects = Project::all()->map(function ($project) use ($lang) {
            return [
                'id' => $project->id,
                'name' => $this->trans($project->name, $lang),
                'category' => $project->category,
                'description' => $this->trans($project->description, $lang),
                'image' => $project->image ? asset('storage/' . $project->image) : null,
                'client' => $project->client,
                'url' => $project->url,
            ];
        });
        return response()->json($projects);
    }

    public function getPosts()
    {
        $lang = $this->getLang();
        $posts = Post::with('author')->where('is_published', true)->latest()->get()->map(function ($post) use ($lang) {
            return [
                'id' => $post->id,
                'title' => $this->trans($post->title, $lang),
                'slug' => $post->slug,
                'content' => $this->trans($post->content, $lang),
                'author' => $post->author?->name ?? 'Admin',
                'category' => $post->category,
                'image' => $post->image ? asset('storage/' . $post->image) : null,
                'published_at' => $post->created_at->format('M d, Y'),
            ];
        });
        return response()->json($posts);
    }

    public function getPost($slug)
    {
        $lang = $this->getLang();
        $post = Post::with('author')->where('slug', $slug)->where('is_published', true)->firstOrFail();
        return response()->json([
            'id' => $post->id,
            'title' => $this->trans($post->title, $lang),
            'slug' => $post->slug,
            'category' => $post->category,
            'content' => $this->trans($post->content, $lang),
            'author' => $post->author?->name ?? 'Admin',
            'image' => $post->image ? asset('storage/' . $post->image) : null,
            'published_at' => $post->created_at->format('M d, Y'),
            'seo' => [
                'title' => $this->trans($post->seo_title, $lang) ?: $this->trans($post->title, $lang),
                'description' => $this->trans($post->seo_description, $lang),
            ]
        ]);
    }

    public function getFaqs()
    {
        $lang = $this->getLang();
        $faqs = Faq::where('is_active', true)->get()->map(function ($faq) use ($lang) {
            return [
                'id' => $faq->id,
                'question' => $this->trans($faq->question, $lang),
                'answer' => $this->trans($faq->answer, $lang),
            ];
        });
        return response()->json($faqs);
    }

    public function submitContactMessage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
            'type' => 'nullable|string|max:50',
        ]);

        $validated['user_id'] = auth()->id();
        if (empty($validated['type'])) {
            $validated['type'] = 'inquiry';
        }
        $message = ContactMessage::create($validated);

        $admins = User::role('super_admin')->get();
        
        Notification::make()
            ->title('New Contact Message')
            ->body("You have received a new {$validated['type']} from {$validated['name']}.")
            ->icon('heroicon-o-envelope')
            ->success()
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->link()
                    ->url(fn () => \App\Filament\Resources\ContactMessageResource::getUrl('index', ['activeTab' => $validated['type']]))
                    ->markAsRead(),
            ])
            ->sendToDatabase($admins);

        return response()->json(['message' => 'Message sent successfully', 'data' => $message], 201);
    }

    public function getSettings()
    {
        $settings = Setting::all()->mapWithKeys(function ($setting) {
            $value = $setting->value;
            if ($setting->type === 'image' && $setting->value_source === 'file' && $value) {
                $value = asset('storage/' . $value);
            }
            return [$setting->key => $value];
        });
        return response()->json($settings);
    }

    public function getTestimonials()
    {
        $testimonials = Testimonial::where('is_active', true)->get()->map(function ($t) {
            return [
                'id' => $t->id,
                'client_name' => $t->client_name,
                'position' => $t->position,
                'review' => $t->review,
                'image' => $t->image ? asset('storage/' . $t->image) : null,
            ];
        });
        return response()->json($testimonials);
    }

    public function getTeamMembers()
    {
        $team = TeamMember::where('is_active', true)->get()->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'role' => $member->role,
                'bio' => $member->bio,
                'image' => $member->image ? asset('storage/' . $member->image) : null,
                'facebook_url' => $member->facebook_url,
                'linkedin_url' => $member->linkedin_url,
            ];
        });
        return response()->json($team);
    }

    public function getFeatures()
    {
        $features = Feature::where('is_active', true)->get()->map(function ($f) {
            return [
                'id' => $f->id,
                'title' => $f->title,
                'description' => $f->description,
                'icon' => $f->icon,
            ];
        });
        return response()->json($features);
    }

    public function getStatistics()
    {
        $stats = Statistic::where('is_active', true)->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'label' => $s->label,
                'count' => $s->count,
                'icon' => $s->icon,
            ];
        });
        return response()->json($stats);
    }

    public function getFooterData()
    {
        $texts = [
            'heading' => Setting::where('key', 'footer_heading')->value('value') ?? 'Have a Good Web Design<br/>With Serv5.',
            'description' => Setting::where('key', 'footer_description')->value('value') ?? 'Serv5 specializes in modern web solutions...',
            'button_text' => Setting::where('key', 'footer_button_text')->value('value') ?? 'Get Started',
            'copyright' => Setting::where('key', 'footer_copyright')->value('value') ?? '2026 © Serv5. All Rights Reserved.'
        ];

        $links = \App\Models\FooterLink::orderBy('sort_order')->get()->groupBy('column_name')->map(function ($group) {
            return $group->map(function ($link) {
                return [
                    'id' => $link->id,
                    'label' => $link->label,
                    'url' => $link->url,
                ];
            });
        });

        $socials = \App\Models\SocialLink::where('is_active', true)->get()->map(function ($social) {
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
    public function getNavLinks()
    {
        $lang = $this->getLang();
        $links = \App\Models\NavLink::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($link) use ($lang) {
                $data = [
                    'name' => $this->trans($link->label, $lang),
                    'href' => $link->url,
                ];

                if ($link->children->count() > 0) {
                    $data['dropdown'] = $link->children->map(function ($child) use ($lang) {
                        return [
                            'name' => $this->trans($child->label, $lang),
                            'href' => $child->url,
                        ];
                    });
                }

                return $data;
            });

        return response()->json($links);
    }

    public function getPageContent($slug)
    {
        $lang = $this->getLang();
        $page = \App\Models\Page::where('slug', $slug)->first();

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return response()->json([
            'slug' => $page->slug,
            'title' => $this->trans($page->title, $lang),
            'subtitle' => $this->trans($page->subtitle, $lang),
            'hero_image' => $page->hero_image ? asset('storage/' . $page->hero_image) : null,
            'content' => $this->trans($page->content, $lang),
            'seo' => [
                'title' => $this->trans($page->seo_title, $lang) ?: $this->trans($page->title, $lang),
                'description' => $this->trans($page->seo_description, $lang) ?: $this->trans($page->subtitle, $lang),
            ],
            'sections' => $page->sections[$lang] ?? [],
        ]);
    }

    public function getPricingPlans()
    {
        $lang = $this->getLang();
        $serviceId = request('service_id');

        $query = \App\Models\PricingPlan::where('is_active', true);

        if ($serviceId) {
            if (!is_numeric($serviceId)) {
                $service = \App\Models\Service::where('slug', $serviceId)->first();
                $serviceId = $service ? $service->id : 999999;
            }
            $query->where('service_id', $serviceId);
        }

        $plans = $query->orderBy('sort_order')
            ->get()
            ->map(function ($plan) use ($lang) {
                // $plan->features is an array of arrays if using repeater e.g. [{"feature": "f1"}, {"feature": "f2"}]
                // But we used a repeater with state path `features.en` which creates `{"en": [{"feature": "x"}], "ar": [{"feature": "y"}]}`
                $features = [];
                if (is_array($plan->features)) {
                    $langFeatures = $plan->features[$lang] ?? $plan->features['en'] ?? [];
                    if (is_array($langFeatures)) {
                        foreach ($langFeatures as $f) {
                            if (isset($f['feature'])) {
                                $features[] = $f['feature'];
                            } elseif (is_string($f)) {
                                $features[] = $f;
                            }
                        }
                    }
                }

                return [
                    'id' => $plan->id,
                    'name' => $this->trans($plan->name, $lang),
                    'price' => $plan->price,
                    'currency' => $plan->currency,
                    'period' => $this->trans($plan->period, $lang),
                    'features' => $features,
                    'is_popular' => $plan->is_popular,
                    'button_text' => $this->trans($plan->button_text, $lang),
                    'button_url' => $plan->button_url,
                ];
            });

        return response()->json($plans);
    }
}
