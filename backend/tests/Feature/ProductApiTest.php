<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_active_products()
    {
        Product::create([
            'slug' => 'test-product',
            'name' => ['en' => 'Test Product', 'ar' => 'منتج التجربة'],
            'category' => ['en' => 'Software', 'ar' => 'برمجيات'],
            'title' => ['en' => 'Test Title', 'ar' => 'عنوان التجربة'],
            'description' => ['en' => 'Test Description', 'ar' => 'وصف التجربة'],
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/products?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => 'test-product',
                'name' => 'Test Product',
            ]);
    }

    /** @test */
    public function it_does_not_list_inactive_products()
    {
        Product::create([
            'slug' => 'inactive-product',
            'name' => ['en' => 'Inactive Product', 'ar' => 'غير نشط'],
            'category' => ['en' => 'Software', 'ar' => 'برمجيات'],
            'title' => ['en' => 'Title', 'ar' => 'عنوان'],
            'description' => ['en' => 'Description', 'ar' => 'وصف'],
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/products?lang=en');

        $response->assertStatus(200);
        $this->assertEmpty($response->json());
    }

    /** @test */
    public function it_can_list_categories()
    {
        Product::create([
            'slug' => 'p1',
            'name' => ['en' => 'P1', 'ar' => 'P1'],
            'category' => ['en' => 'E-Commerce', 'ar' => 'تجارة إلكترونية'],
            'title' => ['en' => 'Title', 'ar' => 'عنوان'],
            'description' => ['en' => 'Description', 'ar' => 'وصف'],
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/products/categories?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => 'E-Commerce',
                'label' => 'E-Commerce',
            ]);
    }

    /** @test */
    public function it_can_show_a_single_product()
    {
        Product::create([
            'slug' => 'single-product',
            'name' => ['en' => 'Single Product', 'ar' => 'منتج واحد'],
            'category' => ['en' => 'Software', 'ar' => 'برمجيات'],
            'title' => ['en' => 'Title', 'ar' => 'عنوان'],
            'description' => ['en' => 'Description', 'ar' => 'وصف'],
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/products/single-product?lang=en');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', 'single-product')
            ->assertJsonPath('data.name', 'Single Product');
    }

    /** @test */
    public function it_can_show_a_single_product_by_id()
    {
        $product = Product::create([
            'slug' => 'single-product-id',
            'name' => ['en' => 'Single Product ID', 'ar' => 'منتج واحد بالمعرف'],
            'category' => ['en' => 'Software', 'ar' => 'برمجيات'],
            'title' => ['en' => 'Title', 'ar' => 'عنوان'],
            'description' => ['en' => 'Description', 'ar' => 'وصف'],
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/products/' . $product->id . '?lang=en');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', 'single-product-id')
            ->assertJsonPath('data.name', 'Single Product ID');
    }
}
