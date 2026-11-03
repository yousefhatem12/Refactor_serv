<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class RecentLeadsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Recent Submissions'))
            ->description(__('Last 10 messages, quotations and consultations — sorted by newest'))
            ->query(
                ContactMessage::query()
                    ->select([
                        'id',
                        'name',
                        'email',
                        'status',
                        'created_at',
                        DB::raw("'contact' as type"),
                    ])
                    ->orderByDesc('created_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'contact'      => 'info',
                        'quotation'    => 'warning',
                        'consultation' => 'primary',
                        'career'       => 'success',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'contact'      => '✉ ' . __('Message'),
                        'quotation'    => '📄 ' . __('Quotation'),
                        'consultation' => '💬 ' . __('Consultation'),
                        'career'       => '💼 ' . __('Application'),
                        default        => $state,
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->copyable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread', 'pending' => 'danger',
                        'read'              => 'success',
                        'in_progress'       => 'warning',
                        'completed'         => 'success',
                        default             => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Received'))
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
