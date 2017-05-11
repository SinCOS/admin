<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    define('APP_PATH',__DIR__);
    date_default_timezone_set('Asia/Shanghai');
    ini_set('display_errors','On');
    require  "../vendor/autoload.php";
    session_start();


    $app = new \Slim\App([
        'settings' => [
            'displayErrorDetails' => true,
            'db' => [
                'database_type' => 'mysql',
                'database_name' => 'stock',
                'server' => '10.28.185.254' ,
                'port' => 3306,
                'prefix' => 'cc_',
                'username' => 'root',
                'password' => '5gSy4eHOEPCdDXOb',
                'charset' => 'utf8'
            ]
        ],
        'debug' => true,
        'mode' => 'development'
    ]);
    require '../app/bootstrap.php';
    require '../app/admin.php';
    
    
    $app->run();

