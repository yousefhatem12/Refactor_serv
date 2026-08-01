<?php

namespace App\Filament\Pages;

use App\Models\BlogCategory;
use App\Models\User;
use App\Models\AiCredential;
use App\Models\AiGenerationRequest;
use App\Models\AiGenerationItem;
use App\Jobs\GenerateArticleJob;
use App\Jobs\PlanTopicClusterJob;
use App\Filament\Resources\AiCredentialResource;
use App\Services\Ai\GenerationRequestNormalizer;
use App\Services\Ai\ArabicDialectCatalog;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class AiGenerationDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationGroup(): ?string 
    { 
        return app()->getLocale() === 'ar' ? 'أدوات الذكاء الاصطناعي' : 'AI Content Tools'; 
    }
    
    protected static ?int $navigationSort = 2;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    public static function getNavigationLabel(): string 
    { 
        return app()->getLocale() === 'ar' ? 'إنشاء مقال بالذكاء الاصطناعي' : 'AI Article Generator'; 
    }

    protected string $view = 'filament.pages.ai-generation-dashboard';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return config('ai.enabled', false);
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'مولد المقالات الذكي' : 'AI Article Generator';
    }

    public function mount(): void
    {
        $defaultProvider = AiCredential::where('is_active', true)->whereNotNull('token_hint')->first();
        
        $this->form->fill([
            'generation_type' => 'one_article',
            'language' => 'ar',
            'arabic_dialect' => 'egyptian',
            'article_count' => 1,
            'category_id' => BlogCategory::first()?->id,
            'author_id' => auth()->id(),
            'provider' => $defaultProvider?->provider,
            'model' => $defaultProvider?->default_model,
            'article_length_preset' => 'standard',
            'creativity_preset' => 'balanced',
            'suggest_keywords' => true,
            'auto_publish' => false,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $isAr = app()->getLocale() === 'ar';
        return $schema
            ->components([
                Placeholder::make('no_credentials_warning')
                    ->hidden(fn () => AiCredential::where('is_active', true)->whereNotNull('token_hint')->exists())
                    ->content(new \Illuminate\Support\HtmlString('
                        <div class="p-4 bg-red-50 text-red-700 rounded-md dark:bg-red-950/20 dark:text-red-400">
                            ' . ($isAr ? 'لا يوجد أي مزود خدمة ذكاء اصطناعي نشط ومُهيأ بالكامل. يرجى إعداد مفاتيح الـ API أولاً من ' : 'No active AI credentials configured. Please configure API keys first at ') . '
                            <a href="' . AiCredentialResource::getUrl() . '" class="underline font-bold text-red-800 dark:text-red-300 hover:text-red-600">' . ($isAr ? 'صفحة الصلاحيات ومفاتيح الـ API' : 'AI API Credentials') . '</a>.
                        </div>
                    ')),

                \Filament\Schemas\Components\Section::make($isAr ? 'إعدادات المقال الأساسية' : 'General Article Info')
                    ->schema([
                        Select::make('generation_type')
                            ->label($isAr ? 'طريقة الإنشاء' : 'Generation Mode')
                            ->options([
                                'one_article' => $isAr ? 'مقال واحد' : 'One Article',
                                'one_per_keyword' => $isAr ? 'مقال لكل كلمة مفتاحية' : 'One Article Per Keyword',
                                'topic_cluster' => $isAr ? 'عنقود مواضيع (موضوع رئيسي ومقالات فرعية)' : 'Topic Cluster',
                            ])
                            ->required()
                            ->reactive()
                            ->helperText(function ($state) use ($isAr) {
                                return match ($state) {
                                    'one_article' => $isAr ? 'كتابة مقال واحد مخصص لموضوع محدد بدقة.' : 'Generates a single comprehensive article from your primary topic.',
                                    'one_per_keyword' => $isAr ? 'كتابة مقال مستقل لكل كلمة مفتاحية تدخلها في القائمة (مقال لكل سطر).' : 'Generates one independent article for each keyword listed (one per line).',
                                    'topic_cluster' => $isAr ? 'كتابة موضوع رئيسي محوري بالإضافة لعدة مقالات فرعية تدور في نفس الفلك.' : 'Generates a central topic article plus related sub-topic articles.',
                                    default => null
                                };
                            }),

                        // Visible for single keyword modes (One Article, Topic Cluster)
                        TextInput::make('main_keyword')
                            ->label($isAr ? 'الموضوع / الكلمة المفتاحية الرئيسية' : 'Primary Keyword / Topic')
                            ->required()
                            ->visible(fn ($get) => in_array($get('generation_type'), ['one_article', 'topic_cluster']))
                            ->placeholder($isAr ? 'أدخل الموضوع مثل: أهمية السيو للشركات الناشئة' : 'e.g. Importance of SEO for Startups'),

                        // Visible only for multi-keyword generation
                        Textarea::make('keywords_input')
                            ->label($isAr ? 'قائمة الكلمات المفتاحية (كلمة في كل سطر)' : 'Keywords List (One per line)')
                            ->placeholder($isAr ? "تصميم المواقع\nاستضافة سيرفرات" : "Web Design\nServer Hosting")
                            ->required()
                            ->visible(fn ($get) => $get('generation_type') === 'one_per_keyword')
                            ->rows(5),

                        // Visible only for topic cluster
                        TextInput::make('article_count')
                            ->label($isAr ? 'عدد المقالات الفرعية المطلوبة' : 'Number of Sub-articles')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->default(3)
                            ->required()
                            ->visible(fn ($get) => $get('generation_type') === 'topic_cluster'),

                        Select::make('category_id')
                            ->label($isAr ? 'التصنيف' : 'Category')
                            ->options(fn () => BlogCategory::all()->mapWithKeys(fn ($item) => [
                                $item->id => $item->name[app()->getLocale()] ?? $item->name['en'] ?? ''
                            ]))
                            ->required()
                            ->searchable(),

                        Select::make('language')
                            ->label($isAr ? 'اللغة' : 'Language')
                            ->options([
                                'ar' => $isAr ? 'العربية (افتراضي)' : 'Arabic (Default)',
                                'en' => $isAr ? 'الإنجليزية' : 'English',
                                'both' => $isAr ? 'العربية والإنجليزية معاً' : 'Both (Arabic & English)',
                            ])
                            ->default('ar')
                            ->reactive()
                            ->required(),

                        Select::make('arabic_dialect')
                            ->label($isAr ? 'اللهجة العربية' : 'Arabic Dialect')
                            ->options(ArabicDialectCatalog::options($isAr))
                            ->default('egyptian')
                            ->visible(fn ($get) => in_array($get('language'), ['ar', 'both'], true))
                            ->required(fn ($get) => in_array($get('language'), ['ar', 'both'], true)),

                        Toggle::make('auto_publish')
                            ->label($isAr ? 'النشر التلقائي' : 'Auto Publish')
                            ->helperText($isAr
                                ? 'ينشر كل مقال تلقائيًا بعد اكتمال التوليد والتحقق بنجاح. عند إيقافه تظل المقالات مسودات حتى تضغط نشر أو نشر الكل.'
                                : 'Publishes every article automatically after successful generation and validation. When disabled, articles remain drafts until you use Publish or Publish All.')
                            ->default(false),
                    ]),

                \Filament\Schemas\Components\Section::make($isAr ? 'خيارات متقدمة' : 'Advanced Options')
                    ->collapsed()
                    ->schema([
                        Select::make('provider')
                            ->label($isAr ? 'مزود الخدمة الذكية' : 'AI Provider')
                            ->options(fn () => AiCredential::where('is_active', true)->whereNotNull('token_hint')->pluck('provider', 'provider')->mapWithKeys(fn ($p) => [$p => strtoupper($p)]))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                $credential = AiCredential::where('provider', $state)->first();
                                $set('model', $credential?->default_model);
                            }),

                        Select::make('model')
                            ->label($isAr ? 'الموديل (Model)' : 'Model')
                            ->options(fn ($get) => \App\Services\Ai\ModelCatalog::optionsForProvider($get('provider')))
                            ->required(),

                        Textarea::make('secondary_keywords_input')
                            ->label($isAr ? 'الكلمات الفرعية / LSI (مفصولة بفواصل)' : 'Secondary / LSI Keywords')
                            ->placeholder('e.g. SEO, performance, branding')
                            ->rows(2),

                        Select::make('author_id')
                            ->label($isAr ? 'الكاتب' : 'Author')
                            ->options(fn () => User::all()->pluck('name', 'id'))
                            ->default(auth()->id())
                            ->required()
                            ->searchable(),

                        Select::make('article_length_preset')
                            ->label($isAr ? 'طول المقال المستهدف' : 'Target Article Length')
                            ->options([
                                'short' => $isAr ? 'قصير (700–900 كلمة)' : 'Short (700-900 words)',
                                'standard' => $isAr ? 'قياسي (1000–1300 كلمة)' : 'Standard (1000-1300 words)',
                                'long' => $isAr ? 'طويل (1500–2000 كلمة)' : 'Long (1500-2000 words)',
                            ])
                            ->default('standard')
                            ->required(),

                        Select::make('creativity_preset')
                            ->label($isAr ? 'درجة الإبداع والحرية' : 'Creativity Level')
                            ->options([
                                'accurate' => $isAr ? 'دقيق وحقائق مركزة' : 'Accurate (Factual)',
                                'balanced' => $isAr ? 'متوازن' : 'Balanced',
                                'creative' => $isAr ? 'إبداعي وحر' : 'Creative (Open)',
                            ])
                            ->default('balanced')
                            ->required(),

                        Toggle::make('suggest_keywords')
                            ->label($isAr ? 'السماح للذكاء الاصطناعي باقتراح كلمات فرعية' : 'Let AI suggest related keywords')
                            ->default(true),
                    ])
            ])
            ->statePath('data');
    }

    public function generate(): void
    {
        $formData = $this->form->getState();
        $isAr = app()->getLocale() === 'ar';

        // Gating active credential check
        $credential = AiCredential::where('provider', $formData['provider'] ?? '')
            ->where('is_active', true)
            ->whereNotNull('token_hint')
            ->first();

        if (!$credential) {
            Notification::make()
                ->title($isAr ? 'خطأ' : 'Error')
                ->body($isAr ? 'يرجى اختيار مزود خدمة نشط ومُهيأ بالكامل.' : 'Please select an active, configured provider.')
                ->danger()
                ->send();
            return;
        }

        // 1. Process Keywords Input depending on Generation Type selected
        $keywords = [];
        $type = $formData['generation_type'];

        if ($type === 'one_article') {
            $keywords = [trim($formData['main_keyword'])];
        } elseif ($type === 'topic_cluster') {
            $keywords = [trim($formData['main_keyword'])];
            // Topic cluster count validation against max limits configured
            $subCount = (int) ($formData['article_count'] ?? 3);
            $clusterTotal = $subCount + 1;
            if ($clusterTotal > $credential->max_articles_per_request) {
                Notification::make()
                    ->title($isAr ? 'تجاوز الحد الأقصى' : 'Max Limit Exceeded')
                    ->body($isAr 
                        ? "المقال الرئيسي مع المقالات الفرعية يتجاوز الحد الأقصى للمزود وهو: {$credential->max_articles_per_request}."
                        : "The pillar plus supporting articles exceed this provider's limit of {$credential->max_articles_per_request}.")
                    ->danger()
                    ->send();
                return;
            }
        } elseif ($type === 'one_per_keyword') {
            $rawKeywords = preg_split('/\r\n|\r|\n/', $formData['keywords_input'] ?? '');
            $keywords = array_filter(array_map('trim', $rawKeywords));
        }

        $keywords = array_values(array_unique(array_filter($keywords)));

        if (count($keywords) === 0) {
            Notification::make()
                ->title($isAr ? 'خطأ' : 'Error')
                ->body($isAr ? 'يرجى إدخال كلمة مفتاحية واحدة على الأقل.' : 'Please enter at least one keyword.')
                ->danger()
                ->send();
            return;
        }

        if ($type === 'one_per_keyword' && count($keywords) > $credential->max_articles_per_request) {
            Notification::make()
                ->title($isAr ? 'تجاوز الحد الأقصى' : 'Max Limit Exceeded')
                ->body($isAr
                    ? "الحد الأقصى لهذا المزود هو {$credential->max_articles_per_request} مقال في الطلب الواحد."
                    : "This provider allows at most {$credential->max_articles_per_request} articles per request.")
                ->danger()
                ->send();
            return;
        }

        // 2. Mappings for presets
        $lengthMap = [
            'short' => 800,
            'standard' => 1100,
            'long' => 1700,
        ];
        $tempMap = [
            'accurate' => 0.2,
            'balanced' => 0.7,
            'creative' => 1.0,
        ];

        $targetLength = $lengthMap[$formData['article_length_preset'] ?? 'standard'];
        $targetTemp = $tempMap[$formData['creativity_preset'] ?? 'balanced'];

        // Parse secondary keywords
        $secRaw = explode(',', $formData['secondary_keywords_input'] ?? '');
        $secondaryKeywords = array_filter(array_map('trim', $secRaw));

        // Snapshot config
        $snapshot = [
            'provider' => $formData['provider'],
            'model' => $formData['model'],
            'length' => $targetLength,
            'temperature' => $targetTemp,
            'suggest_keywords' => (bool) $formData['suggest_keywords'],
            'arabic_dialect' => $formData['arabic_dialect'] ?? 'egyptian',
            'auto_publish' => (bool) ($formData['auto_publish'] ?? false),
        ];

        // 3. Normalize parameters and calculate hash
        $hashParams = [
            'provider' => $formData['provider'],
            'model' => $formData['model'],
            'generation_mode' => $type === 'topic_cluster' ? 'topic_cluster' : 'one_per_keyword',
            'language' => $formData['language'],
            'main_keyword' => $keywords[0],
            'keywords' => $keywords,
            'secondary_keywords' => $secondaryKeywords,
            'settings_snapshot' => $snapshot,
            'article_count' => $type === 'topic_cluster' ? ((int) $formData['article_count'] + 1) : count($keywords),
        ];
        $requestHash = GenerationRequestNormalizer::calculateHash($hashParams);

        // Check if matching pending/generating request already exists to avoid redundant queue
        $existing = AiGenerationRequest::where('request_hash', $requestHash)
            ->whereIn('status', ['pending', 'generating'])
            ->exists();

        if ($existing) {
            Notification::make()
                ->title($isAr ? 'طلب مكرر' : 'Duplicate Request')
                ->body($isAr ? 'هناك طلب توليد مطابق قيد التنفيذ بالفعل حالياً.' : 'An identical generation request is already in progress.')
                ->warning()
                ->send();
            return;
        }

        // 4. Create Generation Request record
        $generationRequest = AiGenerationRequest::create([
            'created_by' => auth()->id(),
            'provider' => $formData['provider'],
            'model' => $formData['model'],
            'generation_mode' => $type === 'topic_cluster' ? 'topic_cluster' : 'one_per_keyword',
            'idempotency_key' => (string) Str::uuid(),
            'request_hash' => $requestHash,
            'settings_snapshot' => $snapshot,
            'main_keyword' => $keywords[0],
            'secondary_keywords' => $secondaryKeywords,
            'language' => $formData['language'],
            'article_count' => $type === 'topic_cluster' ? ((int) $formData['article_count'] + 1) : count($keywords),
            'category_id' => $formData['category_id'],
            'author_id' => $formData['author_id'],
            'status' => 'pending',
            'total_items' => $type === 'topic_cluster' ? ((int) $formData['article_count'] + 1) : count($keywords),
            'completed_items' => 0,
            'failed_items' => 0,
            'started_at' => now(),
        ]);

        // 5. Topic clusters need a dedicated planning pass before article jobs.
        if ($type === 'topic_cluster') {
            dispatch(new PlanTopicClusterJob($generationRequest->id));
        } else {
            foreach ($keywords as $keyword) {
                $item = AiGenerationItem::create([
                    'request_id' => $generationRequest->id,
                    'keyword' => $keyword,
                    'status' => 'pending',
                    'attempts' => 0,
                ]);

                dispatch(new GenerateArticleJob($item->id));
            }
        }

        Notification::make()
            ->title($isAr ? 'بدء التوليد' : 'Generation Started')
            ->body($isAr ? 'تمت إضافة طلب التوليد إلى طابور المعالجة بنجاح.' : 'Generation request queued successfully.')
            ->success()
            ->send();

        // Redirect to History/Progress Dashboard page
        $this->redirect(AiGenerationHistory::getUrl());
    }

    protected function getFormActions(): array
    {
        $isAr = app()->getLocale() === 'ar';
        return [
            \Filament\Actions\Action::make('generate')
                ->label($isAr ? 'إنشاء المسودة الذكية' : 'Generate Draft')
                ->submit('generate')
                ->color('primary')
        ];
    }
}
