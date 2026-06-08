<?php

namespace App\Http\Controllers\Api;

use App\Models\Faq;
use App\Http\Resources\FaqResource;
use Illuminate\Http\Request;

class FaqController extends BaseApiController
{
    public function index(Request $request)
    {
        $lang = $this->getLang();
        $search = $request->query('search');
        $page = $request->query('page');
        $perPage = $request->query('per_page', 6);

        $query = Faq::where('is_active', true)->orderBy('id', 'asc');

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->where('question->en', 'like', "%{$search}%")
                  ->orWhere('question->ar', 'like', "%{$search}%")
                  ->orWhere('answer->en', 'like', "%{$search}%")
                  ->orWhere('answer->ar', 'like', "%{$search}%");
            });
        }

        if ($page) {
            $faqsPaginated = $query->paginate($perPage);
            return response()->json([
                'data' => FaqResource::collection($faqsPaginated->items()),
                'current_page' => $faqsPaginated->currentPage(),
                'last_page' => $faqsPaginated->lastPage(),
                'has_more' => $faqsPaginated->hasMorePages(),
            ]);
        }

        $faqs = $query->get();
        return response()->json(FaqResource::collection($faqs));
    }
}
