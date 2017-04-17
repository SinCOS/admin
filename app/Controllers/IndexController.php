<?php

namespace  App\Controllers;

class IndexController extends Controller
{
    public function index($rst, $resp, $args)
    {
        return  $this->view->render($resp, 'template/frame.html');
    }
    public function left($rst, $resp, $args)
    {
        
    }
}
