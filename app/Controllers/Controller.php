<?php

namespace  App\Controllers;

class Controller
{
    protected $container ;
    public function __construct($container)
    {
        $this->container = $container;
    }
    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }
    public function json($resp,$message,$status,$result)
    {
        return $resp->getBody()->write(json_encode([
            'status' => $status,
            'message' => $message,
            'result' => $result
        ]));
    }
}
