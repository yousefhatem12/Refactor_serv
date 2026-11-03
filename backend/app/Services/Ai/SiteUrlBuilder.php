<?php

namespace App\Services\Ai;

class SiteUrlBuilder
{
    private const BASE_URL = 'https://serv5.com.eg';

    public static function home(string $language): string
    {
        return self::localizedBase($language) . '/';
    }

    public static function blogIndex(string $language): string
    {
        return self::localizedBase($language) . '/blog';
    }

    public static function blogPost(string $language, string $slug): string
    {
        return self::blogIndex($language) . '/' . self::cleanSlug($slug);
    }

    public static function servicesIndex(string $language): string
    {
        // Production canonicalizes /services/ to /services with a 308.
        return self::localizedBase($language) . '/services';
    }

    public static function service(string $language, string $slug): string
    {
        $slug = self::cleanSlug($slug);
        while (str_starts_with($slug, 'services-')) {
            $slug = substr($slug, strlen('services-'));
        }
        if ($slug === '') {
            throw new \InvalidArgumentException('Service slug cannot be empty.');
        }

        return self::servicesIndex($language) . '/services-' . $slug;
    }

    public static function page(string $language, string $path): string
    {
        $path = trim($path, '/');
        return match ($path) {
            '' => self::home($language),
            'blog' => self::blogIndex($language),
            'services' => self::servicesIndex($language),
            default => self::localizedBase($language) . '/' . $path,
        };
    }

    private static function localizedBase(string $language): string
    {
        if (!in_array($language, ['ar', 'en'], true)) {
            throw new \InvalidArgumentException("Unsupported site language: {$language}");
        }
        return self::BASE_URL . '/' . $language;
    }

    private static function cleanSlug(string $slug): string
    {
        $slug = trim($slug, " /\t\n\r\0\x0B");
        if ($slug === '' || str_contains($slug, '/') || str_contains($slug, '..')) {
            throw new \InvalidArgumentException('Invalid empty or nested slug.');
        }
        return $slug;
    }
}
