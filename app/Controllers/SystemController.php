<?php

namespace App\Controllers;


class SystemController extends Controller {
    public function getConfig($rst,$resp,$args)
    {
        $cache = $this->redis;
        $cache->select(2);
        return $this->view->render($resp,'template/system/config.html',[]);
    }
    public function putConfig(){
        $cache = $this->redis;
        $cache->select(3);
        
    }
}