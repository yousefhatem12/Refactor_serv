<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;
    protected static bool $shouldRegisterNavigation = true;
    public static function getNavigationGroup(): ?string { return __('Settings'); }
    protected static ?int $navigationSort = 10;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    public static function getModelLabel(): string
    {
        return __('Bank Account');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Bank Accounts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bank Account Details')
                    ->schema([
                        TextInput::make('name.en')
                            ->label('Bank Name (EN)')
                            ->required(),
                        TextInput::make('name.ar')
                            ->label('Bank Name (AR)')
                            ->required(),
                        TextInput::make('owner.en')
                            ->label('Account Owner (EN)')
                            ->required(),
                        TextInput::make('owner.ar')
                            ->label('Account Owner (AR)')
                            ->required(),
                        TextInput::make('account.en')
                            ->label('Account Number / Info (EN)')
                            ->required(),
                        TextInput::make('account.ar')
                            ->label('Account Number / Info (AR)')
                            ->required(),
                        Select::make('type')
                            ->options([
                                'bank' => 'Bank',
                                'wallet' => 'Wallet',
                                'international' => 'International Transfer',
                            ])
                            ->default('bank')
                            ->required(),
                        Select::make('country')
                            ->options([
                                'egypt' => 'Egypt',
                                'ksa' => 'KSA',
                            ])
                            ->default('egypt')
                            ->required(),
                        FileUpload::make('image')
                            ->label('Bank Logo / Icon')
                            ->image()
                            ->directory('banks'),
                        TextInput::make('brand_color')
                            ->label('CSS Gradient (from-X to-Y)')
                            ->placeholder('from-[#006A4E] to-[#128C7E]'),
                        TextInput::make('shadow')
                            ->label('Shadow Color')
                            ->placeholder('rgba(0, 106, 78, 0.25)'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name.en')
                    ->label('Bank Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
