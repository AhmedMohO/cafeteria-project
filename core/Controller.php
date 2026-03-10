<?php

namespace Core;

class Controller
{
    public function view($path, $data = [])
    {
        extract($data);

        require "../views/$path.php";
    }
}
