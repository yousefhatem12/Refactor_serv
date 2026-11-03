<?php

namespace App\Services\Api;

use App\Contracts\Api\ApiCacheServiceInterface;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqApiService
{
    public function __construct(
        private readonly ApiCacheServiceInterface $cache
    ) {}

    public function listFaqs(
        Request $request,
        string $lang,
        string $searchClean,
        int $page,
        int $perPage
    ): array {
        $ver = $this->cache->version('faqs');

        $buildResponse = static function () use ($searchClean, $page, $perPage, $request): array {
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
            return $buildResponse();
        }

        $cacheKey = "faqs:v{$ver}:{$lang}:page_{$page}:per_{$perPage}";

        return $this->cache->remember($cacheKey, $buildResponse);
    }
}
