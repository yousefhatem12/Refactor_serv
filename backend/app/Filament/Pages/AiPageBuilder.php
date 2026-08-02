<?php

namespace App\Filament\Pages;

use App\Filament\Pages\AiGenerationHistory;
use App\Filament\Resources\PageBuilderResource;
use App\Jobs\GenerateAiPageJob;
use App\Models\AiCredential;
use App\Models\AiGenerationRequest;
use App\Models\NavLink;
use App\Models\Page as DynamicPage;
use App\Models\Service;
use App\Services\Ai\ArabicDialectCatalog;
use App\Services\Ai\AiPageContentService;
use App\Services\Ai\ModelCatalog;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AiPageBuilder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?int $navigationSort = 3;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    protected string $view = 'filament.pages.ai-page-builder';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return config('ai.enabled', false);
    }

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'أدوات الذكاء الاصطناعي' : 'AI Content Tools';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'منشئ الصفحات بالذكاء الاصطناعي' : 'AI Page Builder';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'منشئ الصفحات بالذكاء الاصطناعي' : 'AI Page Builder';
    }

    public function mount(): void
    {
        $credential = AiCredential::query()->where('is_active', true)->whereNotNull('token_hint')->first();
        $this->form->fill([
            'page_type' => 'landing_page',
            'page_template' => 'custom',
            'page_depth' => 'standard',
            'tone' => 'confident',
            'arabic_dialect' => 'egyptian',
            'cta_url' => null,
            'provider' => $credential?->provider,
            'model' => $credential?->default_model,
            'creativity' => 'balanced',
            'auto_publish' => false,
            'nav_placement' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $isAr = app()->getLocale() === 'ar';

        return $schema->components([
            Section::make($isAr ? 'ما الصفحة التي تريدها؟' : 'What should this page achieve?')
                ->description($isAr
                    ? 'اكتب النتيجة المطلوبة والجمهور والعرض بوضوح. سيحوّلها النظام إلى صفحة عربية وإنجليزية كاملة.'
                    : 'Describe the outcome, audience, and offer. The system will build a complete Arabic and English page.')
                ->schema([
                    Textarea::make('page_brief')
                        ->label($isAr ? 'وصف الصفحة والهدف منها' : 'Page brief and objective')
                        ->placeholder($isAr
                            ? 'مثال: صفحة هبوط لخدمة تصميم المتاجر، تستهدف أصحاب الشركات في السعودية، وتوضح خطوات التنفيذ ولماذا Serv5 وتدفع الزائر لطلب استشارة.'
                            : 'Example: A landing page for ecommerce design targeting Saudi business owners, explaining delivery steps, why Serv5, and driving consultation requests.')
                        ->helperText($isAr
                            ? 'يكفي أن تشرح فكرتك بطريقتك. سيبحث النظام تلقائيًا في خدمات وصفحات ومقالات Serv5 ويستنتج الجمهور والكلمات والهيكل المناسب.'
                            : 'Describe the idea naturally. The system automatically uses relevant Serv5 services, pages, and articles and infers the audience, keywords, and page structure.')
                        ->rows(5)
                        ->minLength(30)
                        ->maxLength(3000)
                        ->required()
                        ->columnSpanFull(),
                    Select::make('page_type')
                        ->label($isAr ? 'هدف الصفحة' : 'Page goal')
                        ->helperText($isAr ? 'هدف التحويل.' : 'Conversion objective.')
                        ->options([
                            'landing_page' => $isAr ? 'صفحة هبوط وتحويل' : 'Conversion Landing Page',
                            'service_page' => $isAr ? 'صفحة خدمة' : 'Service Page',
                            'product_page' => $isAr ? 'صفحة منتج أو حل' : 'Product / Solution Page',
                            'campaign_page' => $isAr ? 'صفحة حملة' : 'Campaign Page',
                            'lead_generation' => $isAr ? 'جمع عملاء محتملين' : 'Lead Generation Page',
                            'informational' => $isAr ? 'صفحة تعريفية متعمقة' : 'In-depth Informational Page',
                        ])->required(),
                    Select::make('page_template')
                        ->label($isAr ? 'قالب الصفحة' : 'Page template')
                        ->options([
                            'about' => $isAr ? 'قالب من نحن' : 'About Us Template',
                            'contact' => $isAr ? 'قالب اتصل بنا' : 'Contact Us Template',
                            'portfolio' => $isAr ? 'قالب الأعمال' : 'Portfolio Template',
                            'blog' => $isAr ? 'قالب المقالات' : 'Blog Template',
                            'faq' => $isAr ? 'قالب الأسئلة' : 'FAQ Template',
                            'custom' => $isAr ? 'بناء ذكي مخصص (الموصى به)' : 'AI Smart Builder (Recommended)',
                        ])
                        ->default('custom')
                        ->helperText($isAr
                            ? 'سيبني AI الـblocks تلقائيًا حسب الوصف. الـCustom Builder هو الذي يعرض blocks المولدة بالكامل.'
                            : 'AI creates the needed blocks automatically. Custom Builder is the template that renders all generated blocks.')
                        ->required(),
                    Select::make('service_id')
                        ->label($isAr ? 'خدمة Serv5 المرتبطة' : 'Related Serv5 service')
                        ->options(fn (): array => Service::query()->where('is_active', true)->get()->mapWithKeys(fn (Service $service): array => [
                            $service->id => self::localized($service->name, $isAr ? 'ar' : 'en'),
                        ])->filter()->all())
                        ->searchable()
                        ->placeholder($isAr ? 'اختياري — لتحسين دقة الحقائق' : 'Optional — improves factual grounding'),
                    Select::make('page_depth')
                        ->label($isAr ? 'عمق الصفحة' : 'Page depth')
                        ->options([
                            'compact' => $isAr ? 'مركزة (6–7 أقسام)' : 'Focused (6-7 sections)',
                            'standard' => $isAr ? 'متكاملة (7–9 أقسام)' : 'Complete (7-9 sections)',
                            'extended' => $isAr ? 'موسعة (9–10 أقسام)' : 'Extended (9-10 sections)',
                        ])
                        ->default('standard')
                        ->required(),
                    TextInput::make('cta_url')
                        ->label($isAr ? 'رابط الإجراء' : 'Page CTA URL')
                        ->placeholder('/contact-us')
                        ->helperText($isAr ? 'اكتب رابطًا داخليًا أساسيًا مثل المثال الموضح أو /services.' : 'Enter a required internal URL, for example /contact-us or /services.')
                        ->columnSpanFull()
                        ->rule(fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail): void {
                            if (AiPageContentService::sanitizeInternalUrl((string) $value) !== trim((string) $value)) {
                                $fail('The CTA URL must be an internal Serv5 URL.');
                            }
                        })
                        ->required(),
                ])->columns(2),

            Section::make($isAr ? 'تخصيص اختياري' : 'Optional customization')
                ->description($isAr
                    ? 'اترك هذه الحقول كما هي وسيختار الذكاء الاصطناعي الأنسب من وصفك ومحتوى الموقع.'
                    : 'Leave these fields unchanged and AI will choose what fits your brief and the existing website content.')
                ->collapsed()
                ->schema([
                    TextInput::make('primary_keyword')
                        ->extraInputAttributes(['style' => 'height: 62px'])
                        ->label($isAr ? 'الكلمة المفتاحية الرئيسية' : 'Primary SEO keyword')
                        ->helperText($isAr ? 'الكلمة التي تريد الظهور بها في البحث؛ اتركها فارغة ليقترحها AI.' : 'The search phrase to target; leave blank and AI will suggest it.')
                        ->maxLength(160),
                    Textarea::make('target_audience')
                        ->extraInputAttributes(['style' => 'height: 62px'])
                        ->label($isAr ? 'الجمهور المستهدف ومشكلته' : 'Target audience and pain point')
                        ->helperText($isAr ? 'لمن نكتب وما المشكلة التي نساعده فيها.' : 'Who the page is for and which problem it should solve.')
                        ->rows(2)
                        ->maxLength(1000),
                    Textarea::make('value_proposition')
                        ->extraInputAttributes(['style' => 'height: 62px'])
                        ->label($isAr ? 'العرض والقيمة الأساسية' : 'Core offer and value proposition')
                        ->helperText($isAr ? 'الميزة أو النتيجة الأساسية التي يحصل عليها العميل.' : 'The main benefit or outcome the customer receives.')
                        ->rows(2)
                        ->maxLength(1000),
                    Select::make('tone')
                        ->extraInputAttributes(['style' => 'height: 62px'])
                        ->label($isAr ? 'نبرة الكتابة' : 'Writing tone')
                        ->helperText($isAr ? 'أسلوب صياغة النص النهائي.' : 'The voice and style of the generated copy.')
                        ->options([
                            'confident' => $isAr ? 'احترافية وواثقة' : 'Professional and confident',
                            'premium' => $isAr ? 'راقية ومميزة' : 'Premium and refined',
                            'direct' => $isAr ? 'مباشرة وبيعية' : 'Direct and conversion-led',
                            'consultative' => $isAr ? 'استشارية وتعليمية' : 'Consultative and educational',
                            'friendly' => $isAr ? 'ودية وبسيطة' : 'Friendly and approachable',
                        ])->required(),
                    Select::make('arabic_dialect')
                        ->extraInputAttributes(['style' => 'height: 62px'])
                        ->label($isAr ? 'اللهجة العربية' : 'Arabic dialect')
                        ->helperText($isAr ? 'اللهجة المستخدمة في النسخة العربية.' : 'The dialect used in the Arabic version.')
                        ->options(ArabicDialectCatalog::options($isAr))
                        ->default('egyptian')
                        ->required(),
                    Textarea::make('must_include')
                        ->extraInputAttributes(['style' => 'height: 62px'])
                        ->label($isAr ? 'نقاط أو أقسام يجب تضمينها' : 'Must-include points or sections')
                        ->helperText($isAr ? 'أي تفاصيل أو أقسام محددة تريد ظهورها.' : 'Specific details or sections that must appear.')
                        ->rows(2)
                        ->maxLength(1500),
                    Textarea::make('verified_facts')
                        ->extraInputAttributes(['style' => 'height: 62px'])
                        ->label($isAr ? 'حقائق وأرقام مؤكدة فقط' : 'Verified facts and numbers only')
                        ->helperText($isAr ? 'لن ينشئ النظام أرقامًا أو إحصائيات من عنده. اتركه فارغًا إذا لم توجد أرقام مؤكدة.' : 'The system will not invent statistics. Leave blank unless you have verified values.')
                        ->rows(2)
                        ->maxLength(1500),
                    TextInput::make('slug_hint')
                        ->extraInputAttributes(['style' => 'height: 62px'])
                        ->label($isAr ? 'اقتراح الرابط المختصر' : 'Preferred slug')
                        ->helperText($isAr ? 'اختياري؛ جزء الرابط الظاهر في عنوان الصفحة.' : 'Optional; the English part used in the page URL.')
                        ->alphaDash()
                        ->maxLength(120),
                ])->columns(2),

            Section::make($isAr ? 'إعدادات التوليد' : 'Generation settings')
                ->collapsed()
                ->schema([
                    Select::make('provider')
                        ->label($isAr ? 'مزود الذكاء الاصطناعي' : 'AI provider')
                        ->options(fn (): array => AiCredential::query()->where('is_active', true)->whereNotNull('token_hint')->pluck('provider', 'provider')->mapWithKeys(fn (string $provider): array => [$provider => strtoupper($provider)])->all())
                        ->live()
                        ->afterStateUpdated(function ($state, $set): void {
                            $set('model', AiCredential::query()->where('provider', $state)->where('is_active', true)->value('default_model'));
                        })
                        ->required(),
                    Select::make('model')
                        ->label($isAr ? 'الموديل' : 'Model')
                        ->options(fn ($get): array => ModelCatalog::optionsForProvider((string) $get('provider')))
                        ->required(),
                    Select::make('creativity')
                        ->label($isAr ? 'درجة الإبداع' : 'Creativity')
                        ->options([
                            'accurate' => $isAr ? 'دقيقة ومحافظة' : 'Accurate',
                            'balanced' => $isAr ? 'متوازنة' : 'Balanced',
                            'creative' => $isAr ? 'إبداعية' : 'Creative',
                        ])->required(),
                ])->columns(2),

            Section::make($isAr ? 'النشر بعد التوليد' : 'After generation')
                ->icon('heroicon-o-rocket-launch')
                ->description($isAr
                    ? 'اختر بوضوح هل تريد مراجعة الصفحة أولًا أم نشرها فور نجاح التوليد.'
                    : 'Choose whether to review the generated page first or publish it immediately after successful generation.')
                ->schema([
                    Toggle::make('auto_publish')
                        ->label($isAr ? 'نشر الصفحة تلقائيًا بعد نجاحها' : 'Auto publish after successful generation')
                        ->helperText($isAr ? 'مغلق افتراضيًا. الأفضل مراجعة الصفحة أولًا من Dynamic Pages.' : 'Disabled by default. Reviewing the draft in Dynamic Pages is recommended.')
                        ->default(false)
                        ->live()
                        ->columnSpanFull(),

                    Select::make('nav_placement')
                        ->label($isAr ? 'إضافة الصفحة إلى القائمة تحت' : 'Add page to navigation under')
                        ->helperText($isAr
                            ? 'اختر العنصر الأب في القائمة الذي ستظهر الصفحة تحته. يُطبَّق فقط عند التفعيل.'
                            : 'Choose the parent nav item this page should appear under. Only applied when auto-publish is on.')
                        ->options(function () use ($isAr): array {
                            $locale = $isAr ? 'ar' : 'en';
                            $none = [$isAr ? '— لا تضيف للقائمة —' : '— Do not add to nav —'];
                            $parents = NavLink::whereNull('parent_id')
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->get()
                                ->mapWithKeys(fn (NavLink $link): array => [
                                    $link->id => ($link->label[$locale] ?? $link->label['en'] ?? $link->label['ar'] ?? '#' . $link->id),
                                ])
                                ->all();
                            return ['' => ($isAr ? '— لا تضيف للقائمة —' : '— Do not add to nav —')] + $parents;
                        })
                        ->placeholder($isAr ? 'اختر موضع الصفحة في القائمة' : 'Select nav placement')
                        ->visible(fn ($get) => (bool) $get('auto_publish'))
                        ->columnSpanFull(),
                ]),
        ])->statePath('data');
    }

    public function generate(): void
    {
        $isAr = app()->getLocale() === 'ar';
        $state = $this->form->getState();
        $credential = AiCredential::query()
            ->where('provider', $state['provider'])
            ->where('is_active', true)
            ->whereNotNull('token_hint')
            ->first();
        if (!$credential) {
            Notification::make()->title($isAr ? 'بيانات المزود غير متاحة' : 'AI provider is not configured')->danger()->send();
            return;
        }

        $temperature = match ($state['creativity'] ?? 'balanced') {
            'accurate' => 0.2,
            'creative' => 0.9,
            default => 0.6,
        };
        $serviceName = '';
        if (!empty($state['service_id'])) {
            $selectedService = Service::query()->where('is_active', true)->find($state['service_id']);
            $serviceName = $selectedService ? self::localized($selectedService->name, $isAr ? 'ar' : 'en') : '';
        }
        $primaryKeyword = trim((string) ($state['primary_keyword'] ?? ''));
        if ($primaryKeyword === '') {
            $primaryKeyword = $serviceName !== ''
                ? $serviceName
                : self::briefLabel((string) $state['page_brief']);
        }

        $brief = [
            'page_brief' => trim((string) $state['page_brief']),
            'page_type' => $state['page_type'],
            'page_template' => $state['page_template'] ?? 'custom',
            'service_id' => $state['service_id'] ?? null,
            'primary_keyword' => $primaryKeyword,
            'target_audience' => trim((string) ($state['target_audience'] ?? '')),
            'value_proposition' => trim((string) ($state['value_proposition'] ?? '')),
            'page_depth' => $state['page_depth'],
            'tone' => $state['tone'],
            'arabic_dialect' => $state['arabic_dialect'],
            'cta_url' => AiPageContentService::sanitizeInternalUrl((string) $state['cta_url']),
            'must_include' => trim((string) ($state['must_include'] ?? '')),
            'verified_facts' => trim((string) ($state['verified_facts'] ?? '')),
            'auto_publish' => (bool) ($state['auto_publish'] ?? false),
            'nav_placement_parent_id' => !empty($state['nav_placement']) ? (int) $state['nav_placement'] : null,
            'temperature' => $temperature,
        ];
        $baseSlug = Str::slug((string) ($state['slug_hint'] ?? ''))
            ?: Str::slug($brief['primary_keyword'])
            ?: 'ai-page-' . Str::lower(Str::random(8));
        $placeholderSlug = self::uniqueSlug($baseSlug);
        $placeholderTitle = mb_substr($brief['primary_keyword'], 0, 150);

        $page = DynamicPage::create([
            'slug' => $placeholderSlug,
            'title' => ['en' => $placeholderTitle, 'ar' => $placeholderTitle],
            'subtitle' => ['en' => 'AI generation in progress', 'ar' => 'جاري إنشاء الصفحة بالذكاء الاصطناعي'],
            'content' => ['en' => null, 'ar' => null],
            'seo_title' => ['en' => null, 'ar' => null],
            'seo_description' => ['en' => null, 'ar' => null],
            'sections' => ['template' => 'custom', 'blocks' => [], 'en' => [], 'ar' => []],
            'is_published' => false,
            'ai_generated' => true,
            'ai_status' => 'queued',
            'ai_metadata' => [
                'provider' => $state['provider'],
                'model' => $state['model'],
                'brief' => $brief,
                'created_by' => auth()->id(),
                'queued_at' => now()->toIso8601String(),
            ],
        ]);

        $generationRequest = AiGenerationRequest::create([
            'created_by' => auth()->id(),
            'provider' => $state['provider'],
            'model' => $state['model'],
            'generation_mode' => 'ai_page',
            'idempotency_key' => (string) Str::uuid(),
            'request_hash' => hash('sha256', json_encode([
                'page_id' => $page->id,
                'brief' => $brief,
                'provider' => $state['provider'],
                'model' => $state['model'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
            'settings_snapshot' => [
                'page_id' => $page->id,
                'brief' => $brief,
            ],
            'main_keyword' => $primaryKeyword,
            'secondary_keywords' => [],
            'language' => 'both',
            'article_count' => 1,
            'page_id' => $page->id,
            'status' => 'pending',
            'total_items' => 1,
            'completed_items' => 0,
            'failed_items' => 0,
            'started_at' => now(),
        ]);

        $page->update([
            'ai_metadata' => array_merge($page->ai_metadata ?? [], [
                'generation_request_id' => $generationRequest->id,
            ]),
        ]);

        GenerateAiPageJob::dispatch($page->id);

        Notification::make()
            ->title($isAr ? 'بدأ إنشاء الصفحة' : 'AI page generation started')
            ->body($isAr ? 'تمت إضافة الصفحة للطابور. تابع حالتها من Dynamic Pages.' : 'The page was queued. Track its status in Dynamic Pages.')
            ->success()
            ->send();

        $this->redirect(AiGenerationHistory::getUrl());
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('generate')
                ->label(app()->getLocale() === 'ar' ? 'إنشاء صفحة AI' : 'Generate AI Page')
                ->icon('heroicon-o-sparkles')
                ->submit('generate'),
        ];
    }

    private static function uniqueSlug(string $base): string
    {
        $slug = $base;
        $suffix = 2;
        while (DynamicPage::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix++;
        }
        return $slug;
    }

    private static function localized(mixed $value, string $language): string
    {
        if (is_array($value)) {
            return trim((string) ($value[$language] ?? $value['en'] ?? $value['ar'] ?? ''));
        }
        return trim((string) $value);
    }

    private static function briefLabel(string $brief): string
    {
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags($brief)) ?? '');
        return mb_substr($plain, 0, 120);
    }
}
