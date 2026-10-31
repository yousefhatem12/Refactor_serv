<?php

namespace App\Http\Controllers\Api;

use App\Models\Faq;
use App\Http\Resources\FaqResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FaqController extends BaseApiController
{
    public function index(Request $request)
    {
        $lang = $this->getLang();
        $search = $request->query('search');
        $page = $this->pageNumber($request, 1);
        $perPage = $this->perPage($request, 12);
        $ver = $this->cacheVersion('faqs');

        $searchClean = is_string($search) ? trim(mb_substr($search, 0, 100)) : '';

        $buildResponse = function () use ($searchClean, $page, $perPage, $request) {
            $query = Faq::where('is_active', true)
                ->select(['id', 'question', 'answer', 'is_active'])
                ->orderBy('id', 'asc');

            if ($searchClean !== '') {
                $escaped = addcslashes(strtolower($searchClean), '%_\\');
                $query->where(function ($q) use ($escaped) {
                    $q->where('question->en', 'like', "%{$escaped}%")
                      ->orWhere('question->ar', 'like', "%{$escaped}%")
                      ->orWhere('answer->en', 'like', "%{$escaped}%")
                      ->orWhere('answer->ar', 'like', "%{$escaped}%");
                });
            }

            $faqsPaginated = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'data' => FaqResource::collection($faqsPaginated->items())->resolve($request),
                'current_page' => $faqsPaginated->currentPage(),
                'last_page' => $faqsPaginated->lastPage(),
                'total' => $faqsPaginated->total(),
                'has_more' => $faqsPaginated->hasMorePages(),
            ];
        };

        if ($searchClean !== '') {
            return response()->json($buildResponse());
        }

        $cacheKey = "faqs:v{$ver}:{$lang}:page_{$page}:per_{$perPage}";
        $data = Cache::remember($cacheKey, 3600, $buildResponse);

        return response()->json($data);
    }
}
