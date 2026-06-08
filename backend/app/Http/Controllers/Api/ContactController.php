<?php

namespace App\Http\Controllers\Api;

use App\Models\ContactMessage;
use App\Models\Quotation;
use App\Models\User;
use App\Http\Requests\StoreContactMessageRequest;
use App\Http\Requests\StoreQuotationRequest;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ContactController extends BaseApiController
{
    public function submitMessage(StoreContactMessageRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        
        if (empty($validated['type'])) {
            $validated['type'] = 'inquiry';
        }
        
        $message = ContactMessage::create($validated);
        $admins = User::role('super_admin')->get();
        
        $actionClass = 'Filament\\Notifications\\Actions\\Action';
        Notification::make()
            ->title('New Contact Message')
            ->body("You have received a new {$validated['type']} from {$validated['name']}.")
            ->icon('heroicon-o-envelope')
            ->success()
            ->actions([
                $actionClass::make('view')
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

    public function submitQuotation(StoreQuotationRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

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

        $quotation = Quotation::create($validated);
        $admins = User::role('super_admin')->get();
        
        $actionClass = 'Filament\\Notifications\\Actions\\Action';
        Notification::make()
            ->title('New Quotation Request')
            ->body("A new quotation request has been submitted by {$validated['name']}.")
            ->icon('heroicon-o-document-text')
            ->success()
            ->actions([
                $actionClass::make('view')
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
}
