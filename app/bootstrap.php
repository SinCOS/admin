<?php

$app->get('/',"IndexController:index");

$app->group("/stock",function()use($app){
    $app->get('/public',function($rst,$resp,$args){
        
        return $resp;
    });
});
$app->group('/user',function()use($app){
    $app->get("/{uid}",function($rst,$resp,$args){
        return $resp->getBody()->write('hello world');
    });
});