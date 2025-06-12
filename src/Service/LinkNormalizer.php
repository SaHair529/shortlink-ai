<?php

namespace App\Service;

class LinkNormalizer
{
    public static function normalizeLink(string $url): ?string
    {
        $url = trim($url);

        if (!filter_var($url, FILTER_VALIDATE_URL))
            return null;

        $parts = parse_url($url);

        $queryParams = [];
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $queryParams);

            unset($queryParams['utm_source'], $queryParams['utm_medium'], $queryParams['session_id']);

            ksort($queryParams);
        }

        $query = http_build_query($queryParams);

        $normalizedUrl = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $normalizedUrl .= ':' . $parts['port'];
        }

        $path = $parts['path'] ?? '/';
        if ($path === '/' || $path === '') {
            $path = '';
        }
        $normalizedUrl .= $path;

        if ($query) {
            $normalizedUrl .= '?' . $query;
        }

        return $normalizedUrl;
    }
}
