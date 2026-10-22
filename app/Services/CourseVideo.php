<?php

namespace App\Services;

class CourseVideo
{
    // Only supported player URLs become iframe sources; never embed arbitrary HTML.
    public static function embed(?string $url): ?string
    {
        if (!$url || filter_var($url, FILTER_VALIDATE_URL) === false) return null;
        $parts = parse_url($url);
        if (($parts['scheme'] ?? '') !== 'https' || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])) return null;
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        $id = null;
        if ($host === 'youtu.be') $id = trim($path, '/');
        if (in_array($host, ['youtube.com', 'www.youtube.com', 'www.youtube-nocookie.com'], true)) {
            if ($path === '/watch') { parse_str($parts['query'] ?? '', $query); $id = $query['v'] ?? null; }
            elseif (preg_match('~^/(?:embed|shorts)/([A-Za-z0-9_-]{11})/?$~D', $path, $matches)) $id = $matches[1];
        }
        return is_string($id) && preg_match('/^[A-Za-z0-9_-]{11}$/D', $id)
            ? 'https://www.youtube-nocookie.com/embed/'.$id.'?rel=0' : null;
    }

    public static function local(?string $path): ?string
    {
        return $path && preg_match('~^videos/courses/[a-f0-9]{32}\.mp4$~D', $path) && is_file(public_path($path)) ? $path : null;
    }
}
