<?php

namespace Core;

class Controller
{
    protected function appUrl(string $path): string
    {
        $base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';
        $normalizedPath = '/' . ltrim($path, '/');

        if ($base === '') {
            return $normalizedPath;
        }

        return $base . $normalizedPath;
    }

    public function view($path, $data = [])
    {
        extract($data);

        require (__DIR__) . "/../views/$path.php";
        
    }
}
