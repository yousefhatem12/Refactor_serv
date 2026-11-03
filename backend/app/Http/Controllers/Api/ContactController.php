<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreContactMessageRequest;
use App\Http\Requests\StoreProjectConsultationRequest;
use App\Http\Requests\StoreQuotationRequest;
use App\Services\Api\ContactSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContactController extends BaseApiController
{
    public function __construct(
        private readonly ContactSubmissionService $submissionService
    ) {}

    public function submitMessage(StoreContactMessageRequest $request): JsonResponse
    {
        $message = $this->submissionService->createContactMessage($request->validated(), Auth::id());

        return response()->json([
            'message' => 'Message sent successfully',
            'reference_id' => $message->id,
        ], 201);
    }

    public function submitQuotation(StoreQuotationRequest $request): JsonResponse
    {
        $quotation = $this->submissionService->createQuotation(
            validated: $request->validated(),
            files: $request->hasFile('files') ? $request->file('files') : null,
            userId: Auth::id(),
        );

        return response()->json([
            'message' => 'Quotation request submitted successfully',
            'reference_id' => $quotation->id,
        ], 201);
    }

    public function submitConsultation(StoreProjectConsultationRequest $request): JsonResponse
    {
        $consultation = $this->submissionService->createConsultation($request->validated(), Auth::id());

        return response()->json([
            'message' => 'Consultation request submitted successfully',
            'reference_id' => $consultation->id,
        ], 201);
    }
}
