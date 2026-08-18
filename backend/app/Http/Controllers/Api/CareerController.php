<?php

namespace App\Http\Controllers\Api;

use App\Models\Career;
use App\Models\CareerApplication;
use App\Models\User;
use App\Http\Resources\CareerResource;
use App\Http\Requests\StoreCareerApplicationRequest;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;

class CareerController extends BaseApiController
{
    public function index(Request $request)
    {
        $lang = $this->getLang();
        $department = $request->query('department');
        $search = $request->query('search');
        $page = $this->pageNumber($request);
        $perPage = $this->perPage($request, 9);

        $query = Career::where('is_active', true)->orderBy('id', 'desc');

        if ($department && strtolower($department) !== 'all') {
            $query->where('department', $department);
        }

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                  ->orWhere('title->ar', 'like', "%{$search}%")
                  ->orWhere('description->en', 'like', "%{$search}%")
                  ->orWhere('description->ar', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $uniqueDepts = Career::where('is_active', true)->distinct()->pluck('department')->filter()->values();

        if ($page) {
            $careersPaginated = $query->paginate($perPage);
            return response()->json([
                'data' => CareerResource::collection($careersPaginated->items()),
                'current_page' => $careersPaginated->currentPage(),
                'last_page' => $careersPaginated->lastPage(),
                'has_more' => $careersPaginated->hasMorePages(),
                'departments' => $uniqueDepts,
            ]);
        }

        $careers = $query->get();
        return response()->json(CareerResource::collection($careers));
    }

    public function apply(StoreCareerApplicationRequest $request)
    {
        $validated = $request->validated();
        $app = CareerApplication::create($validated);

        $admins = User::role('super_admin')->get();
        
        Notification::make()
            ->title('New Career Application')
            ->body("You have received a new application from {$validated['name']} for the position: " . ($app->career?->title['en'] ?? 'Job'))
            ->icon('heroicon-o-briefcase')
            ->success()
            ->sendToDatabase($admins);

        return response()->json(['message' => 'Application submitted successfully', 'data' => $app], 201);
    }
}
