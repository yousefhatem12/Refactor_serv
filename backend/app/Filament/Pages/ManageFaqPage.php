<?php

namespace App\Filament\Pages;

use App\Models\Page as PageModel;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables;
use App\Models\Faq;

/**
 * @property \Filament\Schemas\Schema|\Filament\Forms\Form $form
 */
class ManageFaqPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;
    public static function getNavigationGroup(): ?string { return __('Content'); }
    protected static ?int $navigationSort = 5;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';
    public static function getNavigationLabel(): string { return __('FAQ Page'); }
    protected string $view = 'filament.pages.manage-faq-page';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('
            <span class="inline-flex items-center gap-3">
                <a href="' . SitePages::getUrl() . '" class="inline-flex items-center justify-center rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                    <svg class="rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <span>' . __('FAQ Page') . '</span>
            </span>
        ');
    }

    public ?array $data = [];
    public ?PageModel $record = null;

    public function mount(): void
    {
        $this->record = PageModel::firstOrCreate(
            ['slug' => 'faq'],
            [
                'title' => ['en' => 'Frequently Asked Questions', 'ar' => 'الأسئلة الشائعة'],
                'subtitle' => ['en' => '', 'ar' => ''],
                'content' => ['en' => '', 'ar' => ''],
                'sections' => ['en' => [], 'ar' => []],
            ]
        );

        $this->form->fill($this->record->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Translations')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('English')
                            ->schema([
                                TextInput::make('title.en')->label('Hero Title'),
                                Textarea::make('content.en')->label('Hero Description')->rows(3),
                                FileUpload::make('hero_image')
                                    ->label('Hero Planet Image')
                                    ->image()
                                    ->directory('pages')
                                    ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'pages')),
                                TextInput::make('seo_title.en')->label('SEO Title'),
                                Textarea::make('seo_description.en')->label('SEO Description'),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Arabic')
                            ->schema([
                                TextInput::make('title.ar')->label('العنوان الرئيسي'),
                                Textarea::make('content.ar')->label('الوصف الرئيسي')->rows(3),
                                TextInput::make('seo_title.ar')->label('عنوان SEO'),
                                Textarea::make('seo_description.ar')->label('وصف SEO'),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $this->record->update($data);

            Notification::make()
                ->title('FAQ page content saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving FAQ page content')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Faq::query())
            ->columns([
                Tables\Columns\TextColumn::make('question.en')
                    ->label('Question (EN)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('question.ar')
                    ->label('Question (AR)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->model(Faq::class)
                    ->form([
                        TextInput::make('question.en')
                            ->label('Question (English)')
                            ->required(),
                        TextInput::make('question.ar')
                            ->label('Question (Arabic)')
                            ->required(),
                        Textarea::make('answer.en')
                            ->label('Answer (English)')
                            ->required(),
                        Textarea::make('answer.ar')
                            ->label('Answer (Arabic)')
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true),
                    ])
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->form([
                        TextInput::make('question.en')
                            ->label('Question (English)')
                            ->required(),
                        TextInput::make('question.ar')
                            ->label('Question (Arabic)')
                            ->required(),
                        Textarea::make('answer.en')
                            ->label('Answer (English)')
                            ->required(),
                        Textarea::make('answer.ar')
                            ->label('Answer (Arabic)')
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true),
                    ]),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
