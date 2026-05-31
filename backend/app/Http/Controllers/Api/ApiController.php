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
        if (is_array($lang)) {
            $lang = reset($lang);
        }
        return str_starts_with(strtolower((string)$lang), 'ar') ? 'ar' : 'en';
    }

    private function trans($field, $lang)
    {
        if (is_string($field)) {
            $decoded = json_decode($field, true);
            if (is_array($decoded)) {
                $field = $decoded;
            }
        }

        if (!is_array($field)) {
            return $field;
        }
        return $field[$lang] ?? $field['en'] ?? '';
    }

    private function translateCategory($category, $lang)
    {
        if (!$category) return '';
        $map = [
            'design' => ['en' => 'Design', 'ar' => 'تصميم'],
            'التصميم' => ['en' => 'Design', 'ar' => 'تصميم'],
            'hosting' => ['en' => 'Hosting', 'ar' => 'استضافة'],
            'الاستضافة' => ['en' => 'Hosting', 'ar' => 'استضافة'],
            'marketing' => ['en' => 'Marketing', 'ar' => 'تسويق'],
            'التسويق' => ['en' => 'Marketing', 'ar' => 'تسويق'],
            'تسويق' => ['en' => 'Marketing', 'ar' => 'تسويق'],
            'development' => ['en' => 'Development', 'ar' => 'تطوير'],
            'التطوير' => ['en' => 'Development', 'ar' => 'تطوير'],
            'security' => ['en' => 'Security', 'ar' => 'حماية'],
            'الحماية' => ['en' => 'Security', 'ar' => 'حماية'],
            
            // Other possible categories
            'artificial intelligence' => ['en' => 'Artificial Intelligence', 'ar' => 'الذكاء الاصطناعي'],
            'الذكاء الاصطناعي' => ['en' => 'Artificial Intelligence', 'ar' => 'الذكاء الاصطناعي'],
            'software' => ['en' => 'Software', 'ar' => 'برمجيات'],
            'برمجيات' => ['en' => 'Software', 'ar' => 'برمجيات'],
            'startup' => ['en' => 'Startup', 'ar' => 'شركات ناشئة'],
            'شركات ناشئة' => ['en' => 'Startup', 'ar' => 'شركات ناشئة'],
            'technology' => ['en' => 'Technology', 'ar' => 'تكنولوجيا'],
            'تكنولوجيا' => ['en' => 'Technology', 'ar' => 'تكنولوجيا'],
            'web development' => ['en' => 'Web Development', 'ar' => 'تطوير الويب'],
            'تطوير الويب' => ['en' => 'Web Development', 'ar' => 'تطوير الويب'],
            'drop shipping' => ['en' => 'Drop Shipping', 'ar' => 'دروب شيبنج'],
            'دروب شيبنج' => ['en' => 'Drop Shipping', 'ar' => 'دروب شيبنج'],
            'hardware' => ['en' => 'Hardware', 'ar' => 'عتاد هاردوير'],
            'عتاد هاردوير' => ['en' => 'Hardware', 'ar' => 'عتاد هاردوير'],
        ];
        
        $key = strtolower(trim($category));
        if (isset($map[$key])) {
            return $map[$key][$lang] ?? $category;
        }
        return $category;
    }

    private function formatDate($date, $lang)
    {
        if (!$date) return '';
        if ($lang === 'ar') {
            $months = [
                'Jan' => 'يناير', 'Feb' => 'فبراير', 'Mar' => 'مارس', 'Apr' => 'أبريل',
                'May' => 'مايو', 'Jun' => 'يونيو', 'Jul' => 'يوليو', 'Aug' => 'أغسطس',
                'Sep' => 'سبتمبر', 'Oct' => 'أكتوبر', 'Nov' => 'نوفمبر', 'Dec' => 'ديسمبر'
            ];
            $formatted = $date->format('d M Y'); // e.g. "21 May 2026"
            foreach ($months as $en => $ar) {
                $formatted = str_replace($en, $ar, $formatted);
            }
            return $formatted;
        }
        return $date->format('M d, Y');
    }

    public function getServices()
    {
        $lang = $this->getLang();
        $cacheKey = "services_{$lang}";

        $mapped = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang) {
            $services = Service::where('is_active', true)->get();

            return $services->map(function ($service, $index) use ($lang) {
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
                    'title' => $lang === 'en' ? strtolower($name) : $name,
                    'label' => $name,
                    'description' => $this->trans($service->description, $lang),
                    'img' => $service->icon ?: '/Services/Purple Planet.png',
                    'gradient' => "from-[{$style['from']}] to-[{$style['to']}]",
                    'dotColor' => "bg-[{$style['dot']}]",
                    'shadow' => $style['shadow'],
                ];
            });
        });

        return response()->json($mapped);
    }

    public function getService($slug)
    {
        $lang = $this->getLang();
        $service = Service::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        $sections = $service->sections ?: [];
        
        // ─── NEW FORMAT: blocks array from Page Builder ───
        $blocks = null;
        if (!empty($sections['blocks']) && is_array($sections['blocks'])) {
            $blocks = collect($sections['blocks'])->map(function ($block) use ($lang) {
                $data = $block['data'] ?? [];
                // Translate items/lists inside each block
                if (!empty($data['items']) && is_array($data['items'])) {
                    $data['items'] = collect($data['items'])->map(function ($item) use ($lang) {
                        return [
                            'title' => $item['title'] ?? null, // keep bilingual for frontend
                            'desc' => $item['desc'] ?? null,
                            'content' => $item['content'] ?? null,
                        ];
                    })->toArray();
                }
                return [
                    'type' => $block['type'],
                    'data' => $data,
                ];
            })->toArray();
        }

        // ─── OLD FORMAT: flat keys for backward compat ───
        $featuresList = collect($sections['features_list'] ?? [])->map(function($item) use ($lang) {
            return [
                'title' => $this->trans($item['title'] ?? null, $lang),
                'desc' => $this->trans($item['desc'] ?? null, $lang),
            ];
        })->toArray();

        $howWeWorkList = collect($sections['how_we_work_list'] ?? [])->map(function($item) use ($lang) {
            return [
                'title' => $this->trans($item['title'] ?? null, $lang),
                'desc' => $this->trans($item['desc'] ?? null, $lang),
            ];
        })->toArray();

        $hostingOffersList = collect($sections['hosting_offers_list'] ?? [])->map(function($item) use ($lang) {
            return [
                'title' => $this->trans($item['title'] ?? null, $lang),
                'desc' => $this->trans($item['desc'] ?? null, $lang),
            ];
        })->toArray();

        $digitalMarketingList = collect($sections['digital_marketing_list'] ?? [])->map(function($item) use ($lang) {
            return [
                'title' => $this->trans($item['title'] ?? null, $lang),
                'desc' => $this->trans($item['desc'] ?? null, $lang),
            ];
        })->toArray();

        $whyChooseUsList = collect($sections['why_choose_us_list'] ?? [])->map(function($item) use ($lang) {
            return [
                'title' => $this->trans($item['title'] ?? null, $lang),
                'desc' => $this->trans($item['desc'] ?? null, $lang),
            ];
        })->toArray();
        // Look up the template's hero_style
        $templateSlug = $sections['template'] ?? null;
        $heroStyle = 'social_media_hero'; // default
        $defaultHeroImage = null;
        if ($templateSlug) {
            $tmpl = \App\Models\ServiceTemplate::where('slug', $templateSlug)->first();
            if ($tmpl) {
                $heroStyle = $tmpl->hero_style;
                $defaultHeroImage = $tmpl->default_hero_image;
            }
        }

        return response()->json([
            'id' => $service->slug,
            'title' => $this->trans($service->name, $lang),
            'description' => $this->trans($service->description, $lang),
            'icon' => $service->icon,
            'template' => $templateSlug,
            'hero_style' => $heroStyle,
            'default_hero_image' => $defaultHeroImage,
            // NEW: blocks array for page builder
            'blocks' => $blocks,
            // OLD: flat keys for backward compat
            'hero_title' => $this->trans($sections['hero_title'] ?? null, $lang),
            'hero_subtitle' => $this->trans($sections['hero_subtitle'] ?? null, $lang),
            'features_title' => $this->trans($sections['features_title'] ?? null, $lang),
            'features_subtitle' => $this->trans($sections['features_subtitle'] ?? null, $lang),
            'features_list' => $featuresList,
            'how_we_work_list' => $howWeWorkList,
            'hosting_offers_list' => $hostingOffersList,
            'digital_marketing_title' => $this->trans($sections['digital_marketing_title'] ?? null, $lang),
            'digital_marketing_subtitle' => $this->trans($sections['digital_marketing_subtitle'] ?? null, $lang),
            'digital_marketing_list' => $digitalMarketingList,
            'why_choose_us_title' => $this->trans($sections['why_choose_us_title'] ?? null, $lang),
            'why_choose_us_subtitle' => $this->trans($sections['why_choose_us_subtitle'] ?? null, $lang),
            'why_choose_us_list' => $whyChooseUsList,
            'show_hero' => $sections['show_hero'] ?? true,
            'show_features' => $sections['show_features'] ?? true,
            'show_how_we_work' => $sections['show_how_we_work'] ?? true,
            'show_portfolio' => $sections['show_portfolio'] ?? true,
            'show_request' => $sections['show_request'] ?? true,
            'show_blogs' => $sections['show_blogs'] ?? true,
            'show_hosting_offers' => $sections['show_hosting_offers'] ?? true,
            'show_digital_marketing' => $sections['show_digital_marketing'] ?? true,
            'show_why_choose_us' => $sections['show_why_choose_us'] ?? true,
        ]);
    }

    public function getProjects(\Illuminate\Http\Request $request)
    {
        $lang = $this->getLang();
        $type = $request->query('type');
        $category = $request->query('category');
        $limit = $request->query('limit');
        $page = $request->query('page');
        $perPage = $request->query('per_page', 9);

        // Generate cache key containing all query parameters
        $cacheKey = "projects_{$lang}_type_" . ($type ?: 'all') . "_cat_" . ($category ?: 'all') . "_limit_" . ($limit ?: 'none') . "_page_" . ($page ?: 'none') . "_per_" . $perPage;

        $response = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang, $type, $category, $limit, $page, $perPage) {
            $query = Project::query()->orderBy('id', 'desc');

            if ($type) {
                $query->where('type', $type);
            }

            if ($category && strtolower($category) !== 'all') {
                $query->where('category', strtolower($category));
            }

            if ($limit) {
                $projects = $query->take($limit)->get()->map(function ($project) use ($lang) {
                    return [
                        'id' => $project->id,
                        'name' => $this->trans($project->name, $lang),
                        'category' => $project->category,
                        'description' => $this->trans($project->description, $lang),
                        'image' => $project->image ? (str_starts_with($project->image, 'http') || str_starts_with($project->image, '/') ? $project->image : asset('storage/' . $project->image)) : null,
                        'client' => $project->client,
                        'url' => $project->url,
                        'tags' => $project->tags ?? [],
                    ];
                });
                return ['type' => 'flat', 'data' => $projects];
            }

            if ($page) {
                $projectsPaginated = $query->paginate($perPage);
                $transformedItems = collect($projectsPaginated->items())->map(function ($project) use ($lang) {
                    return [
                        'id' => $project->id,
                        'name' => $this->trans($project->name, $lang),
                        'category' => $project->category,
                        'description' => $this->trans($project->description, $lang),
                        'image' => $project->image ? (str_starts_with($project->image, 'http') || str_starts_with($project->image, '/') ? $project->image : asset('storage/' . $project->image)) : null,
                        'client' => $project->client,
                        'url' => $project->url,
                        'tags' => $project->tags ?? [],
                    ];
                });

                return [
                    'type' => 'paginated',
                    'data' => [
                        'data' => $transformedItems,
                        'current_page' => $projectsPaginated->currentPage(),
                        'last_page' => $projectsPaginated->lastPage(),
                        'has_more' => $projectsPaginated->hasMorePages(),
                    ]
                ];
            }

            // Fallback: return all projects as flat array
            $projects = $query->get()->map(function ($project) use ($lang) {
                return [
                    'id' => $project->id,
                    'name' => $this->trans($project->name, $lang),
                    'category' => $project->category,
                    'description' => $this->trans($project->description, $lang),
                    'image' => $project->image ? (str_starts_with($project->image, 'http') || str_starts_with($project->image, '/') ? $project->image : asset('storage/' . $project->image)) : null,
                    'client' => $project->client,
                    'url' => $project->url,
                    'tags' => $project->tags ?? [],
                ];
            });
            return ['type' => 'flat', 'data' => $projects];
        });

        return response()->json($response['data']);
    }

    public function getProject($id)
    {
        $lang = $this->getLang();
        $cacheKey = "project_{$id}_{$lang}";

        $project = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($id, $lang) {
            $record = \App\Models\Project::findOrFail($id);
            return [
                'id' => $record->id,
                'name' => $this->trans($record->name, $lang),
                'category' => $record->category,
                'description' => $this->trans($record->description, $lang),
                'image' => $record->image ? (str_starts_with($record->image, 'http') || str_starts_with($record->image, '/') ? $record->image : asset('storage/' . $record->image)) : null,
                'client' => $record->client,
                'url' => $record->url,
                'tags' => $record->tags ?? [],
            ];
        });

        return response()->json($project);
    }

    public function getProjectCategories()
    {
        $lang = $this->getLang();
        $cacheKey = "project_categories_{$lang}";

        $categories = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang) {
            return \App\Models\ProjectCategory::all()->map(function ($cat) use ($lang) {
                return [
                    'id' => $cat->slug,
                    'label' => $this->trans($cat->name, $lang),
                ];
            });
        });
        return response()->json($categories);
    }

    private function transformPost($post, $lang)
    {
        $categoryName = '';
        $categoryRaw = '';
        if ($post->blogCategory) {
            $categoryName = $this->trans($post->blogCategory->name, $lang);
            $categoryRaw = strtoupper(trim($this->trans($post->blogCategory->name, 'en')));
        } else if ($post->category) {
            $categoryName = $this->translateCategory($post->category, $lang);
            $categoryRaw = strtoupper(trim($post->category));
        }

        return [
            'id' => $post->id,
            'title' => $this->trans($post->title, $lang),
            'slug' => $post->slug,
            'content' => $this->trans($post->content, $lang),
            'author' => $post->author?->name ?? 'Admin',
            'category' => $categoryName,
            'category_raw' => $categoryRaw,
            'image' => $post->image ? (str_starts_with($post->image, 'http') || str_starts_with($post->image, '/') ? $post->image : asset('storage/' . $post->image)) : null,
            'published_at' => $this->formatDate($post->created_at, $lang),
        ];
    }

    public function getPosts(\Illuminate\Http\Request $request)
    {
        $lang = $this->getLang();
        $limit = $request->query('limit');
        $categoryRaw = $request->query('category_raw');
        $page = $request->query('page');
        $perPage = $request->query('per_page', 9);

        // Generate cache key containing all query parameters
        $cacheKey = "posts_{$lang}_cat_" . ($categoryRaw ?: 'all') . "_limit_" . ($limit ?: 'none') . "_page_" . ($page ?: 'none') . "_per_" . $perPage;

        $response = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang, $limit, $categoryRaw, $page, $perPage) {
            $query = Post::with(['author', 'blogCategory'])
                ->where('is_published', true)
                ->latest();

            if ($categoryRaw && strtoupper($categoryRaw) !== 'ALL') {
                $query->where(function ($q) use ($categoryRaw) {
                    $q->whereHas('blogCategory', function ($sub) use ($categoryRaw) {
                        $sub->where('slug', strtolower($categoryRaw));
                    })->orWhere('category', strtolower($categoryRaw));
                });
            }

            if ($limit) {
                $posts = $query->take($limit)->get()->map(function ($post) use ($lang) {
                    return $this->transformPost($post, $lang);
                });
                return ['type' => 'flat', 'data' => $posts];
            }

            if ($page) {
                $postsPaginated = $query->paginate($perPage);
                $transformedItems = collect($postsPaginated->items())->map(function ($post) use ($lang) {
                    return $this->transformPost($post, $lang);
                });

                return [
                    'type' => 'paginated',
                    'data' => [
                        'data' => $transformedItems,
                        'current_page' => $postsPaginated->currentPage(),
                        'last_page' => $postsPaginated->lastPage(),
                        'has_more' => $postsPaginated->hasMorePages(),
                    ]
                ];
            }

            // Fallback: return all posts as flat array
            $posts = $query->get()->map(function ($post) use ($lang) {
                return $this->transformPost($post, $lang);
            });
            return ['type' => 'flat', 'data' => $posts];
        });

        return response()->json($response['data']);
    }

    public function getBlogCategories()
    {
        $lang = $this->getLang();
        $cacheKey = "blog_categories_{$lang}";
        
        $categories = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang) {
            return \App\Models\BlogCategory::all()->map(function ($cat) use ($lang) {
                return [
                    'id' => strtoupper(trim($cat->slug)),
                    'label' => $this->trans($cat->name, $lang),
                ];
            });
        });
        
        return response()->json($categories);
    }

    public function getTrendingPosts()
    {
        $lang = $this->getLang();
        $cacheKey = "trending_posts_{$lang}";

        $posts = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang) {
            return Post::with('author')
                ->where('is_published', true)
                ->where('is_trending', true)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($post) use ($lang) {
                    return [
                        'id' => $post->id,
                        'title' => $this->trans($post->title, $lang),
                        'slug' => $post->slug,
                        'author' => $post->author?->name ?? 'Admin',
                    ];
                });
        });
        return response()->json($posts);
    }

    public function getPost($idOrSlug)
    {
        $lang = $this->getLang();
        $post = Post::with(['author', 'blogCategory'])
            ->where('is_published', true)
            ->where(function ($query) use ($idOrSlug) {
                $query->where('slug', $idOrSlug)
                      ->orWhere('id', $idOrSlug);
            })
            ->firstOrFail();

        $categoryName = '';
        $categoryRaw = '';
        if ($post->blogCategory) {
            $categoryName = $this->trans($post->blogCategory->name, $lang);
            $categoryRaw = strtoupper(trim($this->trans($post->blogCategory->name, 'en')));
        } else if ($post->category) {
            $categoryName = $this->translateCategory($post->category, $lang);
            $categoryRaw = strtoupper(trim($post->category));
        }

        return response()->json([
            'id' => $post->id,
            'title' => $this->trans($post->title, $lang),
            'slug' => $post->slug,
            'category' => $categoryName,
            'category_raw' => $categoryRaw,
            'content' => $this->trans($post->content, $lang),
            'author' => $post->author?->name ?? 'Admin',
            'image' => $post->image ? (str_starts_with($post->image, 'http') || str_starts_with($post->image, '/') ? $post->image : asset('storage/' . $post->image)) : null,
            'published_at' => $this->formatDate($post->created_at, $lang),
            'seo' => [
                'title' => $this->trans($post->seo_title, $lang) ?: $this->trans($post->title, $lang),
                'description' => $this->trans($post->seo_description, $lang),
            ]
        ]);
    }

    public function getFaqs(\Illuminate\Http\Request $request)
    {
        $lang = $this->getLang();
        $search = $request->query('search');
        $page = $request->query('page');
        $perPage = $request->query('per_page', 6);

        $query = Faq::where('is_active', true)->orderBy('id', 'asc');

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->where('question->en', 'like', "%{$search}%")
                  ->orWhere('question->ar', 'like', "%{$search}%")
                  ->orWhere('answer->en', 'like', "%{$search}%")
                  ->orWhere('answer->ar', 'like', "%{$search}%");
            });
        }

        $transformer = function ($faq) use ($lang) {
            return [
                'id' => $faq->id,
                'question' => $this->trans($faq->question, $lang),
                'answer' => $this->trans($faq->answer, $lang),
            ];
        };

        if ($page) {
            $faqsPaginated = $query->paginate($perPage);
            $transformedItems = collect($faqsPaginated->items())->map($transformer);

            return response()->json([
                'data' => $transformedItems,
                'current_page' => $faqsPaginated->currentPage(),
                'last_page' => $faqsPaginated->lastPage(),
                'has_more' => $faqsPaginated->hasMorePages(),
            ]);
        }

        $faqs = $query->get()->map($transformer);
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
                    ->url(fn () => \App\Filament\Resources\ContactMessageResource::getUrl('view', ['record' => $message]))
                    ->markAsRead()
                    ->extraAttributes([
                        'x-on:click' => '$dispatch(\'close-database-notifications\')',
                    ]),
            ])
            ->sendToDatabase($admins);

        return response()->json(['message' => 'Message sent successfully', 'data' => $message], 201);
    }

    public function submitQuotation(Request $request)
    {
        $validated = $request->validate([
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
        ]);

        $validated['user_id'] = auth()->id();

        $filePaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                try {
                    $path = \App\Services\ImageService::convertToWebp($file, 'quotations');
                } catch (\Exception $e) {
                    $path = $file->store('quotations', 'public');
                }
                $filePaths[] = $path;
            }
        }
        $validated['files'] = $filePaths;

        $quotation = \App\Models\Quotation::create($validated);

        $admins = User::role('super_admin')->get();
        
        Notification::make()
            ->title('New Quotation Request')
            ->body("A new quotation request has been submitted by {$validated['name']}.")
            ->icon('heroicon-o-document-text')
            ->success()
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->link()
                    ->url(fn () => \App\Filament\Resources\QuotationResource::getUrl('view', ['record' => $quotation]))
                    ->markAsRead()
                    ->extraAttributes([
                        'x-on:click' => '$dispatch(\'close-database-notifications\')',
                    ]),
            ])
            ->sendToDatabase($admins);

        return response()->json(['message' => 'Quotation request submitted successfully', 'data' => $quotation], 201);
    }

    public function getSettings()
    {
        $lang = $this->getLang();
        $cacheKey = "settings_{$lang}";

        $settings = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang) {
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

    public function getTestimonials()
    {
        $lang = $this->getLang();
        $cacheKey = "testimonials_{$lang}";

        $testimonials = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang) {
            return Testimonial::where('is_active', true)->get()->map(function ($t) use ($lang) {
                return [
                    'id' => $t->id,
                    'client_name' => $this->trans($t->client_name, $lang),
                    'position' => $this->trans($t->position, $lang),
                    'review' => $this->trans($t->review, $lang),
                    'image' => $t->image ? (str_starts_with($t->image, 'http') || str_starts_with($t->image, '/') ? $t->image : asset('storage/' . $t->image)) : null,
                    'rating' => $t->rating,
                    'video_url' => $t->video_url,
                    'audio_url' => $t->audio_url ? (str_starts_with($t->audio_url, 'http') || str_starts_with($t->audio_url, '/') ? $t->audio_url : asset('storage/' . $t->audio_url)) : null,
                ];
            });
        });
        return response()->json($testimonials);
    }

    public function getTeamMembers()
    {
        $lang = $this->getLang();
        $cacheKey = "team_members_{$lang}";

        $team = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang) {
            return TeamMember::where('is_active', true)->get()->map(function ($member) use ($lang) {
                return [
                    'id' => $member->id,
                    'name' => $this->trans($member->name, $lang),
                    'role' => $this->trans($member->role, $lang),
                    'bio' => $this->trans($member->bio, $lang),
                    'image' => $member->image ? asset('storage/' . $member->image) : null,
                    'facebook_url' => $member->facebook_url,
                    'linkedin_url' => $member->linkedin_url,
                ];
            });
        });
        return response()->json($team);
    }

    public function getFeatures()
    {
        $lang = $this->getLang();
        $cacheKey = "features_{$lang}";

        $features = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang) {
            return Feature::where('is_active', true)->get()->map(function ($f) use ($lang) {
                return [
                    'id' => $f->id,
                    'title' => $this->trans($f->title, $lang),
                    'description' => $this->trans($f->description, $lang),
                    'icon' => $f->icon,
                ];
            });
        });
        return response()->json($features);
    }

    public function getFooterData()
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

        $links = \App\Models\FooterLink::orderBy('sort_order')->get()->groupBy('column_name')->map(function ($group) use ($lang) {
            return $group->map(function ($link) use ($lang) {
                return [
                    'id' => $link->id,
                    'label' => $this->trans($link->label, $lang),
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
        
        // Fetch all active services to inject into the Services dropdown
        $servicesDropdown = \App\Models\Service::where('is_active', true)->get()->map(function ($service) use ($lang) {
            return [
                'name' => $this->trans($service->name, $lang),
                'href' => '/services/services-' . $service->slug,
            ];
        })->toArray();

        $links = \App\Models\NavLink::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($link) use ($lang, $servicesDropdown) {
                $data = [
                    'name' => $this->trans($link->label, $lang),
                    'href' => $link->url,
                ];

                $dropdown = [];
                if ($link->children->count() > 0) {
                    $dropdown = $link->children->map(function ($child) use ($lang) {
                        return [
                            'name' => $this->trans($child->label, $lang),
                            'href' => $child->url,
                        ];
                    })->toArray();
                }

                // If this is the Services link, inject the dynamic services from the database
                $enName = strtolower($this->trans($link->label, 'en'));
                if (str_contains($enName, 'service')) {
                    $data['dropdown'] = $servicesDropdown;
                } elseif (!empty($dropdown)) {
                    $data['dropdown'] = $dropdown;
                }

                return $data;
            });

        return response()->json($links);
    }

    public function getPageContent($slug)
    {
        $lang = $this->getLang();
        $cacheKey = "page_{$slug}_{$lang}";

        $pageData = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($slug, $lang) {
            $page = \App\Models\Page::where('slug', $slug)->first();

            if (!$page) {
                return null;
            }

            return [
                'slug' => $page->slug,
                'title' => $this->trans($page->title, $lang),
                'subtitle' => $this->trans($page->subtitle, $lang),
                'hero_image' => $page->hero_image ? asset('storage/' . $page->hero_image) : null,
                'content' => $this->trans($page->content, $lang),
                'seo' => [
                    'title' => $this->trans($page->seo_title, $lang) ?: $this->trans($page->title, $lang),
                    'description' => $this->trans($page->seo_description, $lang) ?: $this->trans($page->subtitle, $lang),
                ],
                'sections' => [
                    'template' => $page->sections['template'] ?? null,
                    'blocks' => $page->sections[$lang] ?? []
                ],
            ];
        });

        if (!$pageData) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return response()->json($pageData);
    }

    public function getPricingPlans()
    {
        $lang = $this->getLang();
        $serviceId = request('service_id');
        $cacheKey = "pricing_plans_{$lang}_service_" . ($serviceId ?: 'all');

        $plans = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang, $serviceId) {
            $query = \App\Models\PricingPlan::where('is_active', true);

            if ($serviceId) {
                if (!is_numeric($serviceId)) {
                    $service = \App\Models\Service::where('slug', $serviceId)->first();
                    $serviceId = $service ? $service->id : 999999;
                }
                $query->where('service_id', $serviceId);
            }

            return $query->orderBy('sort_order')
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
        });

        return response()->json($plans);
    }

    public function getProducts(\Illuminate\Http\Request $request)
    {
        $lang = $this->getLang();
        $page = $request->query('page');
        $perPage = $request->query('per_page', 9);
        $category = $request->query('category');

        $cacheKey = "products_{$lang}_cat_" . ($category ?: 'all') . "_page_" . ($page ?: 'none') . "_per_" . $perPage;

        $response = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang, $page, $perPage, $category) {
            $query = \App\Models\Product::where('is_active', true)->orderBy('id', 'asc');

            if ($category && strtolower($category) !== 'all') {
                $query->where(function ($q) use ($category) {
                    $q->where('category->en', $category)
                      ->orWhere('category->ar', $category)
                      ->orWhere('category', $category);
                });
            }

            if ($page) {
                $productsPaginated = $query->paginate($perPage);
                $transformedItems = collect($productsPaginated->items())->map(function ($product) use ($lang) {
                    return [
                        'id' => $product->slug,
                        'name' => $this->trans($product->name, $lang),
                        'category' => $this->trans($product->category, $lang),
                        'title' => $this->trans($product->title, $lang),
                        'description' => $this->trans($product->description, $lang),
                        'image' => $product->image ? (str_starts_with($product->image, 'http') || str_starts_with($product->image, '/') ? $product->image : asset('storage/' . $product->image)) : null,
                        'demoLink' => $product->demo_link ?? '#',
                        'orderLink' => $product->order_link ?? '#',
                        'tags' => $product->tech_specs ?? [],
                    ];
                });

                return [
                    'type' => 'paginated',
                    'data' => [
                        'data' => $transformedItems,
                        'current_page' => $productsPaginated->currentPage(),
                        'last_page' => $productsPaginated->lastPage(),
                        'has_more' => $productsPaginated->hasMorePages(),
                    ]
                ];
            }

            $products = $query->get()->map(function ($product) use ($lang) {
                return [
                    'id' => $product->slug,
                    'name' => $this->trans($product->name, $lang),
                    'category' => $this->trans($product->category, $lang),
                    'title' => $this->trans($product->title, $lang),
                    'description' => $this->trans($product->description, $lang),
                    'image' => $product->image ? (str_starts_with($product->image, 'http') || str_starts_with($product->image, '/') ? $product->image : asset('storage/' . $product->image)) : null,
                    'demoLink' => $product->demo_link ?? '#',
                    'orderLink' => $product->order_link ?? '#',
                    'tags' => $product->tech_specs ?? [],
                ];
            });
            return ['type' => 'flat', 'data' => $products];
        });

        return response()->json($response['data']);
    }

    public function getProductCategories()
    {
        $lang = $this->getLang();
        $cacheKey = "product_categories_{$lang}";

        $categories = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang) {
            $products = \App\Models\Product::where('is_active', true)->get();
            $cats = $products->map(function ($p) use ($lang) {
                return $this->trans($p->category, $lang);
            })->filter()->unique()->values();

            return $cats->map(function ($cat) {
                return [
                    'id' => $cat,
                    'label' => $cat,
                ];
            })->toArray();
        });

        return response()->json($categories);
    }

    public function getProduct($slug)
    {
        $lang = $this->getLang();
        $product = \App\Models\Product::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $features = [];
        if (is_array($product->features)) {
            $langFeatures = $product->features[$lang] ?? $product->features['en'] ?? [];
            if (is_array($langFeatures)) {
                foreach ($langFeatures as $f) {
                    if (isset($f['title']) && isset($f['desc'])) {
                        $features[] = [
                            'title' => $f['title'],
                            'desc' => $f['desc'],
                        ];
                    }
                }
            }
        }

        $samples = collect($product->samples ?? [])->map(function ($sample) {
            if (str_starts_with($sample, 'http') || str_starts_with($sample, '/')) {
                return $sample;
            }
            return asset('storage/' . $sample);
        })->toArray();

        return response()->json([
            'id' => $product->slug,
            'name' => $this->trans($product->name, $lang),
            'category' => $this->trans($product->category, $lang),
            'title' => $this->trans($product->title, $lang),
            'description' => $this->trans($product->description, $lang),
            'image' => $product->image ? (str_starts_with($product->image, 'http') || str_starts_with($product->image, '/') ? $product->image : asset('storage/' . $product->image)) : null,
            'demoLink' => $product->demo_link ?? '#',
            'orderLink' => $product->order_link ?? '#',
            'features' => $features,
            'techSpecs' => $product->tech_specs ?? [],
            'whyChooseUs' => $this->trans($product->why_choose_us, $lang),
            'samples' => $samples,
        ]);
    }

    public function getCareers(\Illuminate\Http\Request $request)
    {
        $lang = $this->getLang();
        $department = $request->query('department');
        $search = $request->query('search');
        $page = $request->query('page');
        $perPage = $request->query('per_page', 9);

        $query = \App\Models\Career::where('is_active', true)->orderBy('id', 'desc');

        if ($department && strtolower($department) !== 'all') {
            $query->where('department', $department);
        }

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $uniqueDepts = \App\Models\Career::where('is_active', true)->distinct()->pluck('department')->filter()->values();

        $transformer = function ($career) {
            $responsibilities = ['en' => [], 'ar' => []];
            if (is_array($career->responsibilities)) {
                foreach (['en', 'ar'] as $l) {
                    $list = $career->responsibilities[$l] ?? [];
                    if (is_array($list)) {
                        foreach ($list as $item) {
                            $val = $item['responsibility'] ?? (is_string($item) ? $item : '');
                            if ($val) $responsibilities[$l][] = $val;
                        }
                    }
                }
            }

            $requirements = ['en' => [], 'ar' => []];
            if (is_array($career->requirements)) {
                foreach (['en', 'ar'] as $l) {
                    $list = $career->requirements[$l] ?? [];
                    if (is_array($list)) {
                        foreach ($list as $item) {
                            $val = $item['requirement'] ?? (is_string($item) ? $item : '');
                            if ($val) $requirements[$l][] = $val;
                        }
                    }
                }
            }

            return [
                'id' => $career->id,
                'department' => $career->department,
                'title' => $career->title,
                'location' => $career->location,
                'salary' => $career->salary,
                'description' => $career->description,
                'responsibilities' => $responsibilities,
                'requirements' => $requirements,
            ];
        };

        if ($page) {
            $careersPaginated = $query->paginate($perPage);
            $transformedItems = collect($careersPaginated->items())->map($transformer);

            return response()->json([
                'data' => $transformedItems,
                'current_page' => $careersPaginated->currentPage(),
                'last_page' => $careersPaginated->lastPage(),
                'has_more' => $careersPaginated->hasMorePages(),
                'departments' => $uniqueDepts,
            ]);
        }

        $careers = $query->get()->map($transformer);
        return response()->json($careers);
    }

    public function submitCareerApplication(Request $request)
    {
        $validated = $request->validate([
            'career_id' => 'required|exists:careers,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'portfolio' => 'required|string',
            'message' => 'nullable|string',
        ]);

        $app = \App\Models\CareerApplication::create($validated);

        $admins = User::role('super_admin')->get();
        
        Notification::make()
            ->title('New Career Application')
            ->body("You have received a new application from {$validated['name']} for the position: " . ($app->career?->title['en'] ?? 'Job'))
            ->icon('heroicon-o-briefcase')
            ->success()
            ->sendToDatabase($admins);

        return response()->json(['message' => 'Application submitted successfully', 'data' => $app], 201);
    }

    public function getBankAccounts(\Illuminate\Http\Request $request)
    {
        $lang = $this->getLang();
        $country = $request->query('country');
        $page = $request->query('page');
        $perPage = $request->query('per_page', 9);

        $cacheKey = "bank_accounts_{$lang}_country_" . ($country ?: 'all') . "_page_" . ($page ?: 'none') . "_per_" . $perPage;

        $response = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($lang, $country, $page, $perPage) {
            $query = \App\Models\BankAccount::orderBy('sort_order', 'asc');

            if ($country) {
                $query->where('country', $country);
            }

            $transformer = function ($acc) use ($lang) {
                return [
                    'id' => (string) $acc->id,
                    'name' => [
                        'en' => $this->trans($acc->name, 'en'),
                        'ar' => $this->trans($acc->name, 'ar'),
                    ],
                    'owner' => [
                        'en' => $this->trans($acc->owner, 'en'),
                        'ar' => $this->trans($acc->owner, 'ar'),
                    ],
                    'account' => [
                        'en' => $this->trans($acc->account, 'en'),
                        'ar' => $this->trans($acc->account, 'ar'),
                    ],
                    'image' => $acc->image ? (str_starts_with($acc->image, 'http') || str_starts_with($acc->image, '/') ? $acc->image : asset('storage/' . $acc->image)) : null,
                    'type' => $acc->type,
                    'country' => $acc->country,
                    'brandColor' => $acc->brand_color ?: 'from-[#05050f] to-[#101025]',
                    'shadow' => $acc->shadow ?: 'rgba(0,0,0,0)',
                ];
            };

            if ($page) {
                $accountsPaginated = $query->paginate($perPage);
                $transformedItems = collect($accountsPaginated->items())->map($transformer);

                return [
                    'type' => 'paginated',
                    'data' => [
                        'data' => $transformedItems,
                        'current_page' => $accountsPaginated->currentPage(),
                        'last_page' => $accountsPaginated->lastPage(),
                        'has_more' => $accountsPaginated->hasMorePages(),
                    ]
                ];
            }

            $accounts = $query->get()->map($transformer);
            return ['type' => 'flat', 'data' => $accounts];
        });

        return response()->json($response['data']);
    }
}

