<?php

namespace App\Services\Ai;

use App\Models\AiCredential;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Resolves the list of models a provider actually exposes for a given API key.
 *
 * Model names change on the provider side without warning (DeepSeek retired
 * `deepseek-chat`/`deepseek-reasoner` in favour of `deepseek-v4-*`), so the
 * lists are fetched live and only fall back to the curated config registry
 * when no token is available or the provider call fails.
 */
class ModelCatalog
{
    /** Successful lookups stay warm for an hour. */
    public const CACHE_TTL = 3600;

    /** Failed lookups are cached briefly so a broken key does not stall every form render. */
    public const FAILURE_CACHE_TTL = 60;

    /**
     * Full lookup result.
     *
     * @return array{models: array<string, string>, source: 'live'|'fallback', error: ?string}
     */
    public static function fetch(?string $provider, ?string $token, bool $fresh = false): array
    {
        $provider = strtolower(trim((string) $provider));

        if ($provider === '') {
            return ['models' => [], 'source' => 'fallback', 'error' => null];
        }

        if (blank($token)) {
            return [
                'models' => self::fallbackModels($provider),
                'source' => 'fallback',
                'error' => null,
            ];
        }

        $key = self::cacheKey($provider, $token);

        if ($fresh) {
            Cache::forget($key);
        }

        $cached = Cache::get($key);
        if (is_array($cached)) {
            return $cached;
        }

        try {
            $models = AiProviderManager::getProvider($provider)->listModels($token);

            if (empty($models)) {
                throw new \Exception("{$provider} returned an empty model list.");
            }

            $result = ['models' => $models, 'source' => 'live', 'error' => null];
            Cache::put($key, $result, self::CACHE_TTL);

            return $result;
        } catch (\Throwable $e) {
            Log::warning("Failed to fetch {$provider} models: " . $e->getMessage());

            $result = [
                'models' => self::fallbackModels($provider),
                'source' => 'fallback',
                'error' => $e->getMessage(),
            ];
            Cache::put($key, $result, self::FAILURE_CACHE_TTL);

            return $result;
        }
    }

    /**
     * Model options keyed by model id, for a raw provider/token pair.
     *
     * @return array<string, string>
     */
    public static function options(?string $provider, ?string $token, bool $fresh = false): array
    {
        return self::fetch($provider, $token, $fresh)['models'];
    }

    /**
     * Model options for a provider whose token lives on a stored credential.
     *
     * @return array<string, string>
     */
    public static function optionsForProvider(?string $provider, bool $fresh = false): array
    {
        return self::options($provider, self::tokenForProvider($provider), $fresh);
    }

    /**
     * Decrypted token of the active credential for a provider, if any.
     */
    public static function tokenForProvider(?string $provider): ?string
    {
        if (blank($provider)) {
            return null;
        }

        $credential = AiCredential::where('provider', $provider)
            ->whereNotNull('token_hint')
            ->orderByDesc('is_active')
            ->first();

        return self::decrypt($credential);
    }

    /**
     * Decrypts a credential token without letting a corrupt payload break the UI.
     */
    public static function decrypt(?AiCredential $credential): ?string
    {
        if (!$credential) {
            return null;
        }

        try {
            return $credential->getDecryptedToken();
        } catch (\Throwable $e) {
            Log::warning('Unable to decrypt AI credential token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Drops every cached model list for a provider/token pair.
     */
    public static function forget(?string $provider, ?string $token): void
    {
        if (blank($provider) || blank($token)) {
            return;
        }

        Cache::forget(self::cacheKey(strtolower(trim($provider)), $token));
    }

    /**
     * Curated fallback registry used when the provider cannot be queried.
     *
     * @return array<string, string>
     */
    public static function fallbackModels(string $provider): array
    {
        return config("ai.models.{$provider}", []);
    }

    private static function cacheKey(string $provider, string $token): string
    {
        return 'ai:models:' . $provider . ':' . substr(hash('sha256', $token), 0, 32);
    }
}
