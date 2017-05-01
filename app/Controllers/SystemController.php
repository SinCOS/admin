<?php

namespace App\Controllers;


class SystemController extends Controller {
    public function getConfig($rst,$resp,$args)
    {
        $cache = $this->redis;
        $cache->select(2);
        $config = $cache->get('');
    }
    public function updateConfig(){
        
    }
}