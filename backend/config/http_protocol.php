<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HTTP/3 (QUIC) & HTTP/2 Protocol Configuration
    |--------------------------------------------------------------------------
    |
    | Controls inbound Alt-Svc protocol upgrade advertisements (allowing
    | browsers and clients to upgrade from HTTP/1.1 or HTTP/2 over TCP to
    | HTTP/3 over QUIC UDP:443) and outbound cURL HTTP version negotiation.
    |
    */

    'alt_svc_enabled' => filter_var(env('HTTP_ALT_SVC_ENABLED', true), FILTER_VALIDATE_BOOL),

    'alt_svc_header' => env(
        'HTTP_ALT_SVC_HEADER',
        'h3=":443"; ma=86400, h3-29=":443"; ma=86400, h2=":443"; ma=86400'
    ),

    /*
    |--------------------------------------------------------------------------
    | Outbound Guzzle / cURL Protocol Negotiation
    |--------------------------------------------------------------------------
    |
    | Supported values: 'http3', 'http2', 'http1.1'.
    | When set to 'http3', OutboundHttpClientConfigurator uses CURL_HTTP_VERSION_3
    | if available in the PHP cURL extension, falling back to CURL_HTTP_VERSION_2TLS.
    |
    */

    'outbound_preferred_version' => env('HTTP_OUTBOUND_VERSION', 'http3'),

    'tcp_keepalive' => filter_var(env('HTTP_TCP_KEEPALIVE', true), FILTER_VALIDATE_BOOL),

];
