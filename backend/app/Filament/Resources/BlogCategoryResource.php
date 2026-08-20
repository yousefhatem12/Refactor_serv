<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Models\BlogCategory;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;
    protected static bool $shouldRegisterNavigation = false;
    public static function getNavigationGroup(): ?string { return __('Content'); }
    protected static ?int $navigationSort = 5;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    public static function getModelLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return $isAr ? 'تصنيف مقال' : 'Blog Category';
    }

    public static function getPluralModelLabel(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return $isAr ? 'تصنيفات المقالات' : 'Blog Categories';
    }

    public static function form(Schema $schema): Schema
    {
        $isAr = app()->getLocale() === 'ar';
        return $schema
            ->components([
                Section::make($isAr ? 'تفاصيل التصنيف' : 'Category Details')
                    ->schema([
                        TextInput::make('slug')
                            ->label($isAr ? 'الرابط الفرعي (Slug)' : 'Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g. technology, hardware, marketing')
                            ->helperText($isAr ? 'يُستخدم هذا الرابط لفلترة المقالات في واجهة الموقع.' : 'This slug is used by the frontend to identify the category.'),
                        TextInput::make('name.en')
                            ->label($isAr ? 'الاسم (إنجليزي)' : 'Name (English)')
                            ->required(),
                        TextInput::make('name.ar')
                            ->label($isAr ? 'الاسم (عربي)' : 'Name (Arabic)')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        $isAr = app()->getLocale() === 'ar';
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->label($isAr ? 'الرابط (Slug)' : 'Slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name.en')
                    ->label($isAr ? 'الاسم (إنجليزي)' : 'Name (English)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name.ar')
                    ->label($isAr ? 'الاسم (عربي)' : 'Name (Arabic)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label($isAr ? 'تاريخ الإنشاء' : 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
