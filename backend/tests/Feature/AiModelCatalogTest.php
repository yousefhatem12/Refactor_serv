<?php

namespace Tests\Feature;

use App\Services\Ai\AiProviderManager;
use App\Services\Ai\ModelCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiModelCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[Test]
    public function deepseek_lists_models_from_the_api()
    {
        Http::fake([
            'api.deepseek.com/models' => Http::response([
                'data' => [
                    ['id' => 'deepseek-v4-pro'],
                    ['id' => 'deepseek-v4-flash'],
                ],
            ], 200),
        ]);

        $models = AiProviderManager::getProvider('deepseek')->listModels('fake-key');

        $this->assertSame(['deepseek-v4-flash', 'deepseek-v4-pro'], array_keys($models));
    }

    #[Test]
    public function openai_filters_out_non_chat_models()
    {
        Http::fake([
            'api.openai.com/v1/models' => Http::response([
                'data' => [
                    ['id' => 'gpt-4o'],
                    ['id' => 'text-embedding-3-large'],
                    ['id' => 'whisper-1'],
                    ['id' => 'gpt-4o-realtime-preview'],
                    ['id' => 'o3-mini'],
                ],
            ], 200),
        ]);

        $models = AiProviderManager::getProvider('openai')->listModels('fake-key');

        $this->assertArrayHasKey('gpt-4o', $models);
        $this->assertArrayHasKey('o3-mini', $models);
        $this->assertArrayNotHasKey('text-embedding-3-large', $models);
        $this->assertArrayNotHasKey('whisper-1', $models);
        $this->assertArrayNotHasKey('gpt-4o-realtime-preview', $models);
    }

    #[Test]
    public function gemini_keeps_only_generate_content_models()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'models' => [
                    [
                        'name' => 'models/gemini-2.0-flash',
                        'displayName' => 'Gemini 2.0 Flash',
                        'supportedGenerationMethods' => ['generateContent'],
                    ],
                    [
                        'name' => 'models/text-embedding-004',
                        'displayName' => 'Embedding 004',
                        'supportedGenerationMethods' => ['embedContent'],
                    ],
                ],
            ], 200),
        ]);

        $models = AiProviderManager::getProvider('gemini')->listModels('fake-key');

        $this->assertSame(['gemini-2.0-flash' => 'Gemini 2.0 Flash'], $models);
    }

    #[Test]
    public function catalog_caches_live_results_and_reports_the_source()
    {
        Http::fake([
            'api.deepseek.com/models' => Http::response(['data' => [['id' => 'deepseek-v4-pro']]], 200),
        ]);

        $first = ModelCatalog::fetch('deepseek', 'fake-key');
        $second = ModelCatalog::fetch('deepseek', 'fake-key');

        $this->assertEquals('live', $first['source']);
        $this->assertEquals(['deepseek-v4-pro' => 'deepseek-v4-pro'], $second['models']);
        Http::assertSentCount(1);
    }

    #[Test]
    public function catalog_falls_back_to_config_when_the_provider_rejects_the_key()
    {
        Http::fake([
            'api.deepseek.com/models' => Http::response('Unauthorized', 401),
        ]);

        $result = ModelCatalog::fetch('deepseek', 'bad-key');

        $this->assertEquals('fallback', $result['source']);
        $this->assertEquals(config('ai.models.deepseek'), $result['models']);
        $this->assertStringContainsString('401', $result['error']);
    }

    #[Test]
    public function catalog_uses_fallback_without_calling_the_api_when_no_token_is_set()
    {
        Http::fake();

        $result = ModelCatalog::fetch('deepseek', null);

        $this->assertEquals('fallback', $result['source']);
        $this->assertEquals(config('ai.models.deepseek'), $result['models']);
        Http::assertNothingSent();
    }
}
