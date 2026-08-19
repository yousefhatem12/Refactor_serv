<?php

namespace App\Http\Controllers\Api;

use App\Models\ContactMessage;
use App\Models\Quotation;
use App\Models\ProjectConsultation;
use App\Models\User;
use App\Http\Requests\StoreContactMessageRequest;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\StoreProjectConsultationRequest;
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
        
        // Notify admins after the response is sent — decouples notification delivery from form success.
        app()->terminating(static function () use ($message, $admins, $validated): void {
            $actionClass = 'Filament\\Actions\\Action';
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
        });

        return response()->json(['message' => 'Message sent successfully', 'reference_id' => $message->id], 201);
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
                    $path = $file->store('quotations', 'local');
                }
                $filePaths[] = $path;
            }
        }
        $validated['files'] = $filePaths;

        $quotation = Quotation::create($validated);
        $admins = User::role('super_admin')->get();
        
        // Notify admins after the response is sent.
        app()->terminating(static function () use ($quotation, $admins, $validated): void {
            $actionClass = 'Filament\\Actions\\Action';
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
        });

        return response()->json(['message' => 'Quotation request submitted successfully', 'reference_id' => $quotation->id], 201);
    }

    public function submitConsultation(StoreProjectConsultationRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $consultation = ProjectConsultation::create($validated);
        $admins = User::role('super_admin')->get();
        
        // Notify admins after the response is sent.
        app()->terminating(static function () use ($consultation, $admins, $validated): void {
            $actionClass = 'Filament\\Actions\\Action';
            Notification::make()
                ->title('New Project Consultation')
                ->body("A new project consultation request has been submitted by {$validated['name']}.")
                ->icon('heroicon-o-chat-bubble-left-right')
                ->success()
                ->actions([
                    $actionClass::make('view')
                        ->label('View')
                        ->link()
                        ->url(fn () => \App\Filament\Resources\ProjectConsultationResource::getUrl('view', ['record' => $consultation]))
                        ->markAsRead()
                        ->extraAttributes([
                            'x-on:click' => '$dispatch(\'close-database-notifications\')',
                        ]),
                ])
                ->sendToDatabase($admins);
        });

        return response()->json(['message' => 'Consultation request submitted successfully', 'reference_id' => $consultation->id], 201);
    }
}
