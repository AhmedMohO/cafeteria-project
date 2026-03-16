<?php

namespace Core;

class Controller
{
    public function view($path, $data = [])
    {
        extract($data);
        //         require "../views/$path.php";
        $viewFile = dirname(__DIR__) . "/views/$path.php";

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: $path");
        }

        require $viewFile;
    }
}