<?php

    $app->group('/admin', function () use ($app) {
        $app->get('', 'IndexController:index');
        $app->get('/login', "AuthController:getSignUp")->setName('auth.signup');
        $app->post('/login', 'AuthController:postSignUp');
        $app->get('/group/id', function ($rst, $resp, $args) {
                $list = $this->db->select("user_stock", '*', [
                  'AND' =>[
                  'status[>]' => 0,
                  'uid' => 1
                  ]
              ]);
            $resp->getBody()->write(var_export($list));
            return $resp;
        });
        $app->get('/member/list', "MemberController:getMembers");
        $app->get('/stock/public', function ($rst, $resp, $args) {
            if (!$rst->isXhr()) {
                $list = $this->db->select('stockGroup', '*', [
                    'AND' => [
                        'uid' => 0,
                        'status' => 1,
                        'public' => 1,
                    ],
                  'ORDER' => ['id' => 'ASC']
                ]);

                
                return $this->view->render($resp, 'template/stock/public.html', ['group_list'=> $list ??[]]);
            }
        });
        $app->get('/stock/vip', function ($rst, $resp, $args) {
            if (!$rst->isXhr()) {
                $list = $this->db->select('stockGroup', '*', [
                    'AND' => [
                        'uid' => 0,
                        'public' => 0,
                        'status' => 1
                    ],
                    'ORDER' => ['id'=>'ASC']
                    
                ]);
                return $this->view->render($resp,'template/stock/public.html',[
                    'group_list' => $list ?? [],
                    'public' => 0,
                    ]);
            }
        });
        $app->get('/stock/info', function ($rst, $resp) {
              $data = file_get_contents('http://120.24.184.121/public/cpy_info.json');
              echo $data;
        });
        $app->delete('/stock/group/{group_id:[0-9]+}', "StockController:delStockGroup");
        $app->post('/stock/group', "StockController:postStockGroup");
        $app->post('/stock/{group_id:[0-9]+}', "StockController:postStock");
        $app->delete('/stock/{group_id:[0-9]+}/{cpy_id:[0-9]+}', "StockController:delGroupStock");
        $app->get('/stock/group/{group_id:[0-9]+}', "StockController:getGroupStock");
        $app->get('/group/{uid:[0-9]+}/uid', function ($rst, $resp, $args) {
            $list = $this->db->select('stockGroup', '*', [
            "AND" => [
                'status[>]' => 0,
                'uid' => intval($args['uid'])
            ]
            ]);
            $resp->getBody()->write(json_encode([
             'status' => $err_code ?? 200,
             'msg' => $msg ?? 'ok',
             'result' => $list
             ]));
            return $resp;
        });
        $app->get('/group/{gid:[0-9]+}/detail', function ($rst, $resp, $args) {
            $list = $this->db->select('user_stock', '*', [
                'AND' => [
                    'sg_id' => intval($args['gid']),
                    'status[>]' => 0
                ]
            ]);
            return $resp->getBody()->write(json_encode(
                [
                    'status' => 200,
                    'msg' => '',
                    'result' => $list ?? []
                ]
            ));
        });
        
        $app->group("/system",function()use($app){
            $app->get('/config','\App\Controllers\SystemController:getConfig');
            $app->get('/reset-passwd','\App\Controllers\SystemController:getResetpasswd');
        });

        $app->get('/verify_code','\App\Libs\VerifyCode');
        $app->get('/shutdown',function($rst,$resp,$args){
            session_destroy();
            return $resp->withStatus(302)->withHeader('location','/admin');
        });
    })->add(function($request,$response,$next){
        $uri = $request->getUri()->getPath();
        $uri_rule[] = '/admin/login';
        $uri_rule[] = '/admin/verify_code';
        if(!isset($_SESSION['adm.usr.id']) && !in_array($uri,$uri_rule)){
            if($request->isXhr()){
                return $response->withJson([
                    'status' => 404,
                    'message' => '请登录',
                    'result' => []
                ],404);
            }
            return $response->withStatus(302)->withHeader('location','/admin/login');
        }
        return $next($request,$response);
    });
