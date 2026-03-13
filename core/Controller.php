<?php

namespace Core;

class Controller
{
    public function view($path, $data = [])
    {
        extract($data);

        require __DIR__ . "/../views/$path.php";
    }
}
