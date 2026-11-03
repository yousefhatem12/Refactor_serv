<?php

namespace App\Services\Http;

use Illuminate\Support\Facades\Http;

class OutboundHttpClientConfigurator
{
    /**
     * Configure Laravel's global HTTP client to negotiate HTTP/3 (or HTTP/2 over TLS)
     * with persistent keep-alive connections instead of defaulting to HTTP/1.1.
     */
    public static function configure(): void
    {
        if (! extension_loaded('curl')) {
            return;
        }

        $curlOptions = [];

        if (config('http_protocol.tcp_keepalive', true) && defined('CURLOPT_TCP_KEEPALIVE')) {
            $curlOptions[CURLOPT_TCP_KEEPALIVE] = 1;
            if (defined('CURLOPT_TCP_KEEPIDLE')) {
                $curlOptions[CURLOPT_TCP_KEEPIDLE] = 60;
            }
            if (defined('CURLOPT_TCP_KEEPINTVL')) {
                $curlOptions[CURLOPT_TCP_KEEPINTVL] = 30;
            }
        }

        if (defined('CURLOPT_HTTP_VERSION')) {
            $curlOptions[CURLOPT_HTTP_VERSION] = self::resolveCurlHttpVersion();
        }

        if ($curlOptions !== []) {
            Http::globalOptions([
                'curl' => $curlOptions,
            ]);
        }
    }

    public static function resolveCurlHttpVersion(): int
    {
        $preferred = strtolower((string) config('http_protocol.outbound_preferred_version', 'http3'));

        if ($preferred === 'http3' && defined('CURL_HTTP_VERSION_3')) {
            $versionInfo = function_exists('curl_version') ? curl_version() : [];
            $features = is_array($versionInfo) ? (int) ($versionInfo['features'] ?? 0) : 0;

            if (defined('CURL_VERSION_HTTP3') && ($features & CURL_VERSION_HTTP3) === CURL_VERSION_HTTP3) {
                return CURL_HTTP_VERSION_3;
            }
        }

        if (in_array($preferred, ['http3', 'http2'], true) && defined('CURL_HTTP_VERSION_2TLS')) {
            return CURL_HTTP_VERSION_2TLS;
        }

        return defined('CURL_HTTP_VERSION_1_1') ? CURL_HTTP_VERSION_1_1 : 2;
    }
}
