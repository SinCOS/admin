<?php

namespace  App\Admin\Controllers;

class IndexController extends Controller
{
    public function index($rst, $resp, $args)
    {
        return  $this->view->render($resp, 'template/auth/login.html');
    }
    public function left($rst, $resp, $args)
    {
        
    }
}
