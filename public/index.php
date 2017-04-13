<?php



    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    date_default_timezone_set('Asia/Shanghai');
    ini_set('display_errors','On');
    require  "../vendor/autoload.php";




    $app = new \Slim\App([
        'settings' => [
            'displayErrorDetails' => true,
            'db' => [
                'database_type' => 'mysql',
                'database_name' => 'stock',
                'server' => '127.0.0.1',
                'port' => 3306,
                'prefix' => 'cc_',
                'username' => 'root',
                'password' => '123456',
                'charset' => 'utf8'
            ]
        ],
        'debug' => true,
        'mode' => 'development'
    ]);
    $container = $app->getContainer();
    

    $container['view'] = function($c)use($container){
        $view = new \Slim\Views\Twig('../app/views',[
            'cache' => false
        ]);
        $view->addExtension(new Slim\Views\TwigExtension(
            $container->router,
            $container->request->getUri()    
        ));
        //$basePath = rtrim()
        return $view;
    };

    $container['logger'] = function($c){
        $logger = new \Monolog\Logger('debug_logger');
        $file_handler = new \Monolog\Handler\StreamHandler('../logs/'.date('Y-m-d').'.log');
        $logger->pushHandler($file_handler);
        return $logger;
    };

    $container['db'] = function($c){
        try{

             $medoo = new \Medoo\Medoo($c['settings']['db']);
             
            
             return $medoo;
        } catch(Exception $e){

        }
       
    };
    $container['redis'] = function($c){
        try{
            $redis = new \Redis;
            $redis->pconnect('127.0.0.1',3679);
            return $redis;
        } catch (Exception $e){
            print $e->getMessage();
            $this->logger->addError($e->getMessage());
            exit();
        }
      
    };

    $container['IndexController'] = function($container){
        return new \App\Admin\Controllers\IndexController($container);
    };
    $container['AuthController'] = function($c){
    return new \App\Admin\Controllers\Auth\AuthController($c);
    };
    $container['Validate'] = function($c){
        
    };
    require '../app/admin.php';
    require '../app/bootstrap.php';
    
    $app->run();

