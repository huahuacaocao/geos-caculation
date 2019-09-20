<?php

namespace Lib;

use Lib\Exception\RouteException;

class Router
{
    private $class = '';
    private $action = '';

    public function __construct($uri)
    {
        if (empty($uri)) {
            throw new RouteException('', 10001);
        }
        $this->parseClassAndAction($uri);
    }

    public function run(array $data)
    {
        if (empty($this->class || empty($this->action))) {
            throw new RouteException('', 10001);
        }
        $object = new $this->class();
        if (false === method_exists($object, $this->action)) {
            throw new RouteException('', 10001);
        }
        return $object->{$this->action}($data);
    }

    private function parseClassAndAction($uri)
    {
        $uris = explode('/', trim($uri, '/'));
        if (count($uris) != 3) {
            throw new RouteException('', 10001);
        }
        $class = '\\App\\' . ucfirst($uris[0]) . '\\' . ucfirst($uris[1]) . 'Controller';
        $this->class = $class;
        $this->action = lcfirst($uris[2]);
    }
}