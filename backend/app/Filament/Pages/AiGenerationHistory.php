<?php

namespace App\Filament\Pages;

use App\Models\AiGenerationRequest;
use App\Models\AiGenerationItem;
use App\Jobs\GenerateArticleJob;
use App\Jobs\PlanTopicClusterJob;
use App\Jobs\RepairArticleQualityJob;
use App\Jobs\FinalizeTopicClusterLinksJob;
use App\Services\Ai\ArticleContentService;
use App\Services\Ai\ArabicDialectCatalog;
use App\Services\Ai\GeneratedHtmlSanitizer;
use App\Models\AiCredential;
use App\Models\BlogCategory;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class AiGenerationHistory extends Page implements HasTable
{
    use InteractsWithTable;

    public static function getNavigationGroup(): ?string 
    { 
        return app()->getLocale() === 'ar' ? 'أدوات الذكاء الاصطناعي' : 'AI Content Tools'; 
    }
    
    protected static ?int $navigationSort = 4;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    public static function getNavigationLabel(): string 
    { 
        return app()->getLocale() === 'ar' ? 'سجل طلبات التوليد' : 'Generation History'; 
    }

    protected string $view = 'filament.pages.ai-generation-history';

    public static function shouldRegisterNavigation(): bool
    {
        return config('ai.enabled', false);
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'ar' ? 'سجل عمليات التوليد' : 'AI Generation History';
    }

    public function table(Table $table): Table
    {
        $isAr = app()->getLocale() === 'ar';
        return $table
            ->query(AiGenerationRequest::query()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('generation_mode')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'ai_page'
                        ? ($isAr ? 'طµظپط­ط©' : 'Page')
                        : ($isAr ? 'ظ…ظ‚ط§ظ„ط§طھ' : 'Articles'))
                    ->color(fn (string $state): string => $state === 'ai_page' ? 'info' : 'gray'),
                Tables\Columns\TextColumn::make('main_keyword')
                    ->label($isAr ? 'الموضوع الرئيسي' : 'Topic')
                    ->searchable(),

                Tables\Columns\TextColumn::make('provider')
                    ->label($isAr ? 'المزود' : 'Provider')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                Tables\Columns\TextColumn::make('article_count')
                    ->label($isAr ? 'عدد العناصر' : 'Items'),

                Tables\Columns\TextColumn::make('status')
                    ->label($isAr ? 'الحالة' : 'Status')
                    ->badge()
                    ->getStateUsing(function ($record) use ($isAr) {
                        if ($record->status === 'completed') {
                            if ($record->generation_mode === 'ai_page') {
                                // Check if the linked page is published
                                if ($record->page_id && \App\Models\Page::where('id', $record->page_id)->where('is_published', true)->exists()) {
                                    return $isAr ? 'منشور' : 'Published';
                                }
                            } else {
                                $allPublished = $record->items()
                                    ->where('status', 'completed')
                                    ->whereHas('post', fn ($q) => $q->where('is_published', false))
                                    ->doesntExist();
                                if ($allPublished && $record->items()->where('status', 'completed')->exists()) {
                                    return $isAr ? 'منشور' : 'Published';
                                }
                            }
                        }
                        return match ($record->status) {
                            'pending' => $isAr ? 'قيد الانتظار' : 'Queued',
                            'planning' => $isAr ? 'جاري تخطيط العنقود' : 'Planning Cluster',
                            'generating' => $isAr ? 'جاري التوليد' : 'Generating',
                            'finalizing' => $isAr ? 'جاري ربط مقالات العنقود' : 'Linking Cluster Articles',
                            'completed' => $isAr ? 'جاهز للمراجعة' : 'Ready for Review',
                            'partially_completed' => $isAr ? 'مكتمل جزئياً' : 'Partially Completed',
                            'failed' => $isAr ? 'فشل' : 'Failed',
                            'cancelled' => $isAr ? 'ملغى' : 'Cancelled',
                            'repairing' => 'Fixing warnings',
                            default => $record->status
                        };
                    })
                    ->color(function ($record) {
                        if ($record->status === 'completed') {
                            if ($record->generation_mode === 'ai_page') {
                                if ($record->page_id && \App\Models\Page::where('id', $record->page_id)->where('is_published', true)->exists()) {
                                    return 'primary';
                                }
                            } else {
                                $allPublished = $record->items()
                                    ->where('status', 'completed')
                                    ->whereHas('post', fn ($q) => $q->where('is_published', false))
                                    ->doesntExist();
                                if ($allPublished && $record->items()->where('status', 'completed')->exists()) {
                                    return 'primary';
                                }
                            }
                        }
                        return match ($record->status) {
                            'completed' => 'success',
                            'generating' => 'warning',
                            'finalizing' => 'info',
                            'pending' => 'gray',
                            'planning' => 'info',
                            'failed' => 'danger',
                            'partially_completed' => 'info',
                            'cancelled' => 'danger',
                            'repairing' => 'warning',
                            default => 'gray'
                        };
                    }),

                Tables\Columns\TextColumn::make('progress')
                    ->label($isAr ? 'نسبة التقدم' : 'Progress')
                    ->getStateUsing(function ($record) use ($isAr) {
                        if ($record->generation_mode === 'ai_page') {
                            return $record->completed_items
                                ? ($isAr ? 'طھظ… ط¥ظ†ط´ط§ط، ط§ظ„طµظپط­ط©' : 'Page completed')
                                : ($isAr ? 'طµظپط­ط© ظ‚ظٹط¯ ط§ظ„طھظˆظ„ظٹط¯' : 'Page in progress');
                        }
                        return $isAr 
                            ? "تم إنشاء {$record->completed_items} من {$record->total_items} مقال"
                            : "{$record->completed_items} of {$record->total_items} articles";
                    }),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label($isAr ? 'بواسطة' : 'Created By')
                    ->placeholder('System'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label($isAr ? 'تاريخ الإنشاء' : 'Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('content_type')
                    ->label($isAr ? 'طھطµظ†ظٹظپ ط§ظ„ظ…ط­طھظˆظ‰' : 'Content type')
                    ->options([
                        'all' => $isAr ? 'ط§ظ„ظƒظ„' : 'All',
                        'articles' => $isAr ? 'ط§ظ„ظ…ظ‚ط§ظ„ط§طھ' : 'Articles',
                        'pages' => $isAr ? 'ط§ظ„طµظپط­ط§طھ' : 'Pages',
                    ])
                    ->default('all')
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return match ($data['value'] ?? 'all') {
                            'articles' => $query->whereIn('generation_mode', ['one_per_keyword', 'topic_cluster']),
                            'pages' => $query->where('generation_mode', 'ai_page'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                \Filament\Actions\Action::make('publishAll')
                    ->label(fn (AiGenerationRequest $record) => $record->total_items === 1
                        ? ($isAr ? 'نشر' : 'Publish')
                        : ($isAr ? 'نشر الكل' : 'Publish All'))
                    ->icon('heroicon-o-globe-alt')
                    ->color('success')
                    ->visible(fn (AiGenerationRequest $record) => ($record->generation_mode !== 'topic_cluster' || $record->status === 'completed')
                        && $record->generation_mode !== 'ai_page'
                        && $record->items()
                            ->where('status', 'completed')
                            ->whereHas('post', fn ($query) => $query->where('is_published', false))
                            ->exists())
                    ->requiresConfirmation()
                    ->modalHeading(fn (AiGenerationRequest $record) => $record->total_items === 1
                        ? ($isAr ? 'نشر المقال' : 'Publish article')
                        : ($isAr ? 'نشر المقالات' : 'Publish articles'))
                    ->modalDescription(fn (AiGenerationRequest $record) => $record->total_items === 1
                        ? ($isAr ? 'سيتم نشر المقال المكتمل في هذا الطلب.' : 'This will publish the completed article in this request.')
                        : ($isAr ? 'سيتم نشر كل المقالات المكتملة غير المنشورة في هذا الطلب.' : 'This will publish all completed, unpublished articles in this request.'))
                    ->action(fn (AiGenerationRequest $record) => $this->publishAll($record->id)),

                \Filament\Actions\Action::make('viewItems')
                    ->label($isAr ? 'عرض المقالات' : 'View Articles')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (AiGenerationRequest $record): bool => $record->generation_mode !== 'ai_page')
                    ->modalContent(fn (AiGenerationRequest $record) => view('filament.pages.actions.view-items', [
                        'request' => $record,
                        'isAr' => $isAr
                    ]))
                    ->modalHeading($isAr ? 'تفاصيل التوليد' : 'Generation details')
                    ->modalWidth('7xl')
                    ->extraModalFooterActions([
                        \Filament\Actions\Action::make('fixWarnings')
                            ->label($isAr ? 'إصلاح التحذيرات' : 'Fix warnings')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->color('warning')
                            ->visible(fn () => false)
                            ->visible(fn (AiGenerationRequest $record) => $record->items()->where('status', 'completed')->get()->contains(fn ($item) => !empty($item->quality_report['warnings'] ?? [])))
                            ->requiresConfirmation()
                            ->modalHeading($isAr ? 'إصلاح مشاكل الجودة' : 'Fix quality warnings')
                            ->modalDescription($isAr ? 'سيتم تعديل التحذيرات فقط مع الحفاظ على محتوى المقال.' : 'Only the listed warnings will be repaired while preserving the article content.')
                            ->action(function (AiGenerationRequest $record) use ($isAr) {
                                $items = $record->items()->where('status', 'completed')->get()->filter(fn ($item) => !empty($item->quality_report['warnings'] ?? []));
                                foreach ($items as $item) {
                                    $item->update(['status' => 'repairing', 'error_message' => null]);
                                    dispatch(new RepairArticleQualityJob($item->id));
                                }
                                $record->update(['status' => 'repairing']);
                                Notification::make()
                                    ->title($isAr ? 'بدأ إصلاح التحذيرات' : 'Warning repair started')
                                    ->body($isAr ? 'تم إرسال المقالات التي تحتوي على تحذيرات للإصلاح.' : 'Articles with warnings were queued for targeted repair.')
                                    ->success()
                                    ->send();
                            }),
                        \Filament\Actions\Action::make('editDraft')
                            ->label($isAr ? 'تعديل المسودة' : 'Edit draft')
                            ->icon('heroicon-o-pencil-square')
                            ->color('primary')
                            ->visible(fn () => false)
                            ->visible(fn (AiGenerationRequest $record) => $record->items()->whereNotNull('post_id')->exists())
                            ->url(fn (AiGenerationRequest $record) => url('/admin/posts/' . $record->items()->whereNotNull('post_id')->value('post_id') . '/edit')),
                    ])
                    ->extraModalFooterActions([])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel($isAr ? 'إغلاق' : 'Close'),

                \Filament\Actions\Action::make('viewPage')
                    ->label($isAr ? 'ط¹ط±ط¶ ط§ظ„طµظپط­ط©' : 'View Page')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (AiGenerationRequest $record): bool => $record->generation_mode === 'ai_page' && (bool) $record->page_id)
                    ->url(fn (AiGenerationRequest $record): string => \App\Filament\Resources\PageBuilderResource::getUrl('edit', ['record' => $record->page_id])),

                \Filament\Actions\Action::make('cancel')
                    ->label($isAr ? 'إلغاء الطلب' : 'Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn ($record) => $record->generation_mode !== 'ai_page' && in_array($record->status, ['pending', 'planning', 'generating', 'finalizing', 'repairing']))
                    ->requiresConfirmation()
                    ->action(function (AiGenerationRequest $record) use ($isAr) {
                        $record->update(['status' => 'cancelled']);
                        // Set pending items as failed/cancelled
                        $record->items()->whereIn('status', ['pending', 'generating', 'repairing'])->update([
                            'status' => 'failed',
                            'error_message' => 'Cancelled by Administrator.'
                        ]);
                        Notification::make()
                            ->title($isAr ? 'تم إلغاء الطلب' : 'Cancelled')
                            ->body($isAr ? 'تم إلغاء عملية التوليد بنجاح.' : 'Generation request cancelled successfully.')
                            ->success()
                            ->send();
                    }),

                \Filament\Actions\Action::make('retry')
                    ->label($isAr ? 'إعادة محاولة الكل' : 'Retry All')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn ($record) => $record->generation_mode !== 'ai_page' && in_array($record->status, ['failed', 'partially_completed']))
                    ->action(function (AiGenerationRequest $record) use ($isAr) {
                        $failedItems = $record->items()->where('status', 'failed')->get();
                        if ($record->generation_mode === 'topic_cluster' && $record->items()->doesntExist()) {
                            $record->update([
                                'status' => 'pending',
                                'failed_items' => 0,
                                'error_message' => null,
                                'completed_at' => null,
                            ]);
                            dispatch(new PlanTopicClusterJob($record->id));

                            Notification::make()
                                ->title($isAr ? 'بدأت إعادة التخطيط' : 'Cluster Planning Restarted')
                                ->body($isAr ? 'تمت إعادة طلب تخطيط العنقود إلى طابور المعالجة.' : 'The topic cluster plan was queued again.')
                                ->success()
                                ->send();
                            return;
                        }
                        if ($record->generation_mode === 'topic_cluster'
                            && $record->items()->exists()
                            && !$record->items()->where('status', '!=', 'completed')->exists()) {
                            $record->update(['status' => 'finalizing', 'error_message' => null, 'completed_at' => null]);
                            dispatch(new FinalizeTopicClusterLinksJob($record->id));
                            Notification::make()
                                ->title($isAr ? 'بدأت إعادة ربط العنقود' : 'Cluster Linking Restarted')
                                ->body($isAr ? 'تمت إعادة مرحلة ربط المقال الرئيسي بالمقالات الفرعية.' : 'The final cluster-linking stage was queued again.')
                                ->success()
                                ->send();
                            return;
                        }
                        if ($failedItems->isEmpty()) {
                            return;
                        }
                        
                        $record->update([
                            'status' => 'generating',
                            'failed_items' => 0
                        ]);
                        
                        foreach ($failedItems as $item) {
                            $item->update([
                                'status' => 'pending',
                                'error_message' => null
                            ]);
                            dispatch(new GenerateArticleJob($item->id));
                        }
                        
                        Notification::make()
                            ->title($isAr ? 'بدأت إعادة المحاولة' : 'Retry Started')
                            ->body($isAr ? 'تمت إعادة المقالات الفاشلة إلى طابور المعالجة.' : 'Failed articles queued for regeneration.')
                            ->success()
                            ->send();
                    }),

                \Filament\Actions\Action::make('editRequest')
                    ->label($isAr ? 'تعديل المعلمات' : 'Edit Parameters')
                    ->icon('heroicon-o-pencil')
                    ->color('gray')
                    ->visible(fn ($record) => $record->generation_mode !== 'ai_page' && in_array($record->status, ['failed', 'partially_completed', 'pending']))
                    ->form(fn ($record) => [
                        Select::make('provider')
                            ->label($isAr ? 'مزود الخدمة' : 'Provider')
                            ->options(AiCredential::where('is_active', true)->whereNotNull('token_hint')->pluck('provider', 'provider')->mapWithKeys(fn ($p) => [$p => strtoupper($p)]))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                $credential = AiCredential::where('provider', $state)->first();
                                $set('model', $credential?->default_model);
                            }),

                        Select::make('model')
                            ->label($isAr ? 'الموديل' : 'Model')
                            ->options(fn ($get) => \App\Services\Ai\ModelCatalog::optionsForProvider($get('provider')))
                            ->required(),

                        TextInput::make('main_keyword')
                            ->label($isAr ? 'الكلمة المفتاحية الرئيسية' : 'Primary Keyword')
                            ->required(),

                        Textarea::make('secondary_keywords_input')
                            ->label($isAr ? 'الكلمات الفرعية (مفصولة بفواصل)' : 'Secondary Keywords (comma separated)')
                            ->placeholder('e.g. SEO, design')
                            ->default(fn ($record) => implode(', ', $record->secondary_keywords ?? [])),

                        Select::make('category_id')
                            ->label($isAr ? 'التصنيف' : 'Category')
                            ->options(fn () => BlogCategory::all()->mapWithKeys(fn ($item) => [
                                $item->id => $item->name[app()->getLocale()] ?? $item->name['en'] ?? ''
                            ]))
                            ->required(),

                        Select::make('language')
                            ->label($isAr ? 'اللغة' : 'Language')
                            ->options([
                                'ar' => $isAr ? 'العربية' : 'Arabic',
                                'en' => $isAr ? 'الإنجليزية' : 'English',
                                'both' => $isAr ? 'العربية والإنجليزية معاً' : 'Both (Arabic & English)',
                            ])
                            ->reactive()
                            ->required(),

                        Select::make('arabic_dialect')
                            ->label($isAr ? 'اللهجة العربية' : 'Arabic Dialect')
                            ->options(ArabicDialectCatalog::options($isAr))
                            ->visible(fn ($get) => in_array($get('language'), ['ar', 'both'], true)),
                    ])
                    ->fillForm(fn (AiGenerationRequest $record) => [
                        'provider' => $record->provider,
                        'model' => $record->model,
                        'main_keyword' => $record->main_keyword,
                        'category_id' => $record->category_id,
                        'language' => $record->language,
                        'arabic_dialect' => $record->settings_snapshot['arabic_dialect'] ?? 'egyptian',
                    ])
                    ->action(function (AiGenerationRequest $record, array $data) use ($isAr) {
                        $secRaw = explode(',', $data['secondary_keywords_input'] ?? '');
                        $secondaryKeywords = array_filter(array_map('trim', $secRaw));

                        $settings = $record->settings_snapshot ?? [];
                        $settings['arabic_dialect'] = $data['arabic_dialect'] ?? 'egyptian';

                        $record->update([
                            'provider' => $data['provider'],
                            'model' => $data['model'],
                            'main_keyword' => $data['main_keyword'],
                            'secondary_keywords' => $secondaryKeywords,
                            'category_id' => $data['category_id'],
                            'language' => $data['language'],
                            'settings_snapshot' => $settings,
                        ]);

                        Notification::make()
                            ->title($isAr ? 'تم حفظ التعديلات' : 'Parameters Saved')
                            ->body($isAr ? 'تم تعديل معلمات طلب التوليد بنجاح.' : 'Generation request parameters updated successfully.')
                            ->success()
                            ->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->label($isAr ? 'حذف السجل' : 'Delete')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (AiGenerationRequest $record) use ($isAr) {
                        $record->items()->delete();
                        $record->delete();
                        
                        Notification::make()
                            ->title($isAr ? 'تم الحذف' : 'Deleted')
                            ->body($isAr ? 'تم حذف السجل بنجاح.' : 'Generation record deleted successfully.')
                            ->success()
                            ->send();
                    })
            ]);
    }

    public function publishArticle(int $itemId): void
    {
        $item = AiGenerationItem::with(['post', 'request'])->findOrFail($itemId);
        $isAr = app()->getLocale() === 'ar';

        if ($item->status !== 'completed' || !$item->post) {
            Notification::make()
                ->title($isAr ? 'المقال غير جاهز للنشر' : 'Article is not ready to publish')
                ->warning()
                ->send();
            return;
        }
        if ($item->request?->generation_mode === 'topic_cluster' && $item->request->status !== 'completed') {
            Notification::make()
                ->title($isAr ? 'ربط العنقود لم يكتمل بعد' : 'Cluster linking is not complete')
                ->body($isAr ? 'انتظر حتى يتم ربط المقال الرئيسي والمقالات الفرعية قبل النشر.' : 'Wait until the pillar and supporting articles are linked before publishing.')
                ->warning()
                ->send();
            return;
        }

        $item->post->update([
            'content' => $this->sanitizePostContent($item->post->content ?? []),
            'is_published' => true,
            'ai_status' => 'approved',
        ]);

        Notification::make()
            ->title($isAr ? 'تم نشر المقال' : 'Article published')
            ->body($isAr ? 'تم نشر هذا المقال فقط.' : 'Only this article was published.')
            ->success()
            ->send();
    }

    public function publishAll(int $requestId): void
    {
        $request = AiGenerationRequest::findOrFail($requestId);
        if ($request->generation_mode === 'topic_cluster' && $request->status !== 'completed') {
            Notification::make()
                ->title(app()->getLocale() === 'ar' ? 'ربط العنقود لم يكتمل بعد' : 'Cluster linking is not complete')
                ->warning()
                ->send();
            return;
        }
        $items = $request->items()
            ->where('status', 'completed')
            ->whereHas('post', fn ($query) => $query->where('is_published', false))
            ->with('post')
            ->get();

        foreach ($items as $item) {
            $item->post?->update([
                'content' => $this->sanitizePostContent($item->post->content ?? []),
                'is_published' => true,
                'ai_status' => 'approved',
            ]);
        }

        $isAr = app()->getLocale() === 'ar';
        Notification::make()
            ->title($isAr ? 'تم نشر المقالات' : 'Articles published')
            ->body($isAr ? "تم نشر {$items->count()} مقال." : "{$items->count()} articles were published.")
            ->success()
            ->send();
    }

    public function fixWarnings(int $itemId): void
    {
        $item = AiGenerationItem::with('request')->findOrFail($itemId);
        $isAr = app()->getLocale() === 'ar';

        if ($item->status !== 'completed' || empty($item->quality_report['warnings'] ?? [])) {
            Notification::make()
                ->title($isAr ? 'لا توجد تحذيرات قابلة للإصلاح' : 'No repairable warnings')
                ->warning()
                ->send();
            return;
        }

        $item->update(['status' => 'repairing', 'error_message' => null]);
        $item->request?->update(['status' => 'repairing']);
        dispatch(new RepairArticleQualityJob($item->id));

        Notification::make()
            ->title($isAr ? 'بدأ إصلاح المقال' : 'Article repair started')
            ->body($isAr ? 'تم إرسال هذا المقال فقط إلى طابور الإصلاح.' : 'Only this article was queued for targeted repair.')
            ->success()
            ->send();
    }

    private function sanitizePostContent(array $content): array
    {
        $content = ArticleContentService::sanitizeGeneratedArticle($content);
        foreach (['ar', 'en'] as $language) {
            if (isset($content[$language]) && is_string($content[$language])) {
                $content[$language] = GeneratedHtmlSanitizer::sanitize($content[$language]);
            }
        }

        return $content;
    }

    public function retryItem(int $itemId)
    {
        $item = AiGenerationItem::findOrFail($itemId);
        $isAr = app()->getLocale() === 'ar';

        if ($item->status === 'completed') {
            return null;
        }

        $request = $item->request;
        if ($item->status === 'failed') {
            if ($request->failed_items > 0) {
                $request->decrement('failed_items');
            }
        }

        $item->update([
            'status' => 'pending',
            'error_message' => null,
        ]);

        $request->update(['status' => 'generating']);

        // Dispatch back to queue
        dispatch(new GenerateArticleJob($item->id));

        Notification::make()
            ->title($isAr ? 'إعادة المحاولة' : 'Retrying')
            ->body($isAr ? 'تمت إعادة المقال إلى طابور المعالجة.' : 'Article queued for regeneration.')
            ->success()
            ->send();

        return redirect(request()->header('Referer') ?? '/admin/ai-generation-history');
    }
}
