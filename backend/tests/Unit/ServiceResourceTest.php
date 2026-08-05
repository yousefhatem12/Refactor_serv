<?php

namespace Tests\Unit;

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use PHPUnit\Framework\TestCase;

class ServiceResourceTest extends TestCase
{
    public function test_planet_style_is_returned_by_list_and_detail_representations(): void
    {
        $service = new Service([
            'slug' => 'planet-service',
            'name' => ['en' => 'Planet Service', 'ar' => 'خدمة الكوكب'],
            'description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'icon' => '/Services/planet5.webp',
            'is_active' => true,
            'sections' => [],
        ]);

        $expected = [
            'img' => '/Services/planet5.webp',
            'dotColor' => '#783B55',
            'gradient' => 'linear-gradient(135deg, #ec4899, #500724)',
        ];

        $listData = (new ServiceResource($service))->toArray(
            $this->requestForRoute('api.services.index'),
        );
        $detailData = (new ServiceResource($service))->toArray(
            $this->requestForRoute('api.services.show'),
        );

        foreach ($expected as $key => $value) {
            $this->assertSame($value, $listData[$key]);
            $this->assertSame($value, $detailData[$key]);
        }
    }

    private function requestForRoute(string $routeName): Request
    {
        $request = Request::create('/api/v1/services/planet-service?lang=en');
        $route = (new Route('GET', '/api/v1/services/planet-service', []))->name($routeName);
        $request->setRouteResolver(fn () => $route);

        return $request;
    }
}
