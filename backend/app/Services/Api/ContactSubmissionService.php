<?php

namespace App\Services\Api;

use App\Contracts\Notifications\AdminNotificationServiceInterface;
use App\Filament\Resources\ContactMessageResource;
use App\Filament\Resources\ProjectConsultationResource;
use App\Filament\Resources\QuotationResource;
use App\Models\ContactMessage;
use App\Models\ProjectConsultation;
use App\Models\Quotation;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;

class ContactSubmissionService
{
    public function __construct(
        private readonly AdminNotificationServiceInterface $notifications
    ) {}

    public function createContactMessage(array $validated, ?int $userId): ContactMessage
    {
        $validated['user_id'] = $userId;
        if (empty($validated['type'])) {
            $validated['type'] = 'inquiry';
        }

        $message = ContactMessage::create($validated);

        $this->notifications->notifySuperAdminsAfterResponse(
            'New Contact Message',
            "You have received a new {$validated['type']} from {$validated['name']}.",
            'heroicon-o-envelope',
            ContactMessageResource::getUrl('view', ['record' => $message])
        );

        return $message;
    }

    /**
     * @param array<int, UploadedFile>|null $files
     */
    public function createQuotation(array $validated, ?array $files, ?int $userId): Quotation
    {
        $validated['user_id'] = $userId;

        $filePaths = [];
        if (is_array($files)) {
            foreach ($files as $file) {
                try {
                    $path = ImageService::convertToWebp($file, 'quotations', 'local');
                } catch (\Exception $e) {
                    $path = $file->store('quotations', 'local');
                }
                $filePaths[] = $path;
            }
        }
        $validated['files'] = $filePaths;

        $quotation = Quotation::create($validated);

        $this->notifications->notifySuperAdminsAfterResponse(
            'New Quotation Request',
            "A new quotation request has been submitted by {$validated['name']}.",
            'heroicon-o-document-text',
            QuotationResource::getUrl('view', ['record' => $quotation])
        );

        return $quotation;
    }

    public function createConsultation(array $validated, ?int $userId): ProjectConsultation
    {
        $validated['user_id'] = $userId;

        $consultation = ProjectConsultation::create($validated);

        $this->notifications->notifySuperAdminsAfterResponse(
            'New Project Consultation',
            "A new project consultation request has been submitted by {$validated['name']}.",
            'heroicon-o-chat-bubble-left-right',
            ProjectConsultationResource::getUrl('view', ['record' => $consultation])
        );

        return $consultation;
    }
}
