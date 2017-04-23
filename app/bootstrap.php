<?php
    
    $app->add(function ($req, $res, $next) {
        $response = $next($req, $res);
        return $response->withHeader('Access-Control-Allow-Origin', "*")
                        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    });
    
    $container = $app->getContainer();
    

    $container['view'] = function ($c) use ($container) {
        $view = new \Slim\Views\Twig('../app/views', [
            'cache' => false
        ]);
        $view->addExtension(new Slim\Views\TwigExtension(
            $container->router,
            $container->request->getUri()
        ));
        //$basePath = rtrim()
        return $view;
    };

    $container['logger'] = function ($c) {
        $logger = new \Monolog\Logger('debug_logger');
        $file_handler = new \Monolog\Handler\StreamHandler('../logs/'.date('Y-m-d').'.log');
        $logger->pushHandler($file_handler);
        return $logger;
    };

    $container['db'] = function ($c) {
        try {
             $medoo = new \Medoo\Medoo($c['settings']['db']);
             return $medoo;
        } catch (Exception $e) {
        }
    };
    $container['redis'] = function ($c) {
        try {
            $redis = new Predis\Client();
            return $redis;
        } catch (Exception $e) {
            $c['logger']->addInfo($e->getMessage());
            exit();
        }
    };

    $container['IndexController'] = function ($container) {
        return new \App\Controllers\IndexController($container);
    };
    $container['AuthController'] = function ($c) {
        return new \App\Controllers\Auth\AuthController($c);
    };
    $container['Validate'] = function ($c) {
    };
    $container['MemberController'] = function ($c) {
        return new \App\Controllers\MemberController($c);
    };
    $container['StockController'] = function ($c) {
        return new \App\Controllers\StockController($c);
    };
    $app->get('/', "IndexController:index");

    $app->group("/stock", function () use ($app) {
        $app->get('/public', function ($rst, $resp, $args) {
        
            return $resp;
        });
    });
    $app->group('/user', function () use ($app) {
        $app->get("/{uid}", function ($rst, $resp, $args) {
            return $resp->getBody()->write('hello world');
        });
    });
