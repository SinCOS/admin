<?php

    $app->group('/admin', function () use ($app) {
        $app->get('/', function ($rst, $resp, $args) {
            return $resp;
        });
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
                  'uid' => 0,
                  'ORDER' => ['id' => 'ASC']
                ]);

                
                 return $this->view->render($resp, 'template/stock/public.html', ['group_list'=> $list ??[]]);
            }
        });
        $app->get('/stock/info', function ($rst, $resp) {
              $data = file_get_contents('http://120.24.184.121/public/cpy_info.json');
              echo $data;
        });
        $app->post('/stock/{group_id:[0-9]+}',"StockController:postStock");
        $app->delete('/stock/{group_id:[0-9]+}/{cpy_id:[0-9]+}',"StockController:delGroupStock");
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

        $app->get('/tickets', function ($rst, $resp, $args) {
            $this->logger->addInfo('Ticket list');
            $resp->getBody()->write(var_export($rst->getQueryParams()));
            return $resp;
        });
        $app->get('/user/login', function ($rst, $resp, $args) {
            $data = $rst->getQueryParams();
            $user['email'] = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        });
        $app->post('/tickets', function ($rst, $resp, $args) {
            $data = $rst->getParsedBody();
            $ticket_data = [];
        });
    });
