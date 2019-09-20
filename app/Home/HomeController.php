<?php

namespace App\Home;

use Lib\Exception\RouteException;

class HomeController
{
    public function index($data)
    {
        throw new RouteException('', 10002);
        return ['a' => 1];
    }
}