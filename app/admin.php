<?php

$app->group('/admin',function()use($app){
        $app->get('/',function($rst,$resp,$args){
            return $resp;
        });
        $app->get('/login',function($rst,$resp,$args){
           
            return $this->view->render($resp,'login.html');
        });
        $app->post('/login',function($rst,$resp,$args){
            return $resp;
        });
        $app->get('/group/id',function($rst,$resp,$args){

              $list = $this->db->select("user_stock",'*',[
                  'AND' =>[
                  'status[>]' => 0,
                  'uid' => 1 
                ]
            ]);
        $resp->getBody()->write(var_export($list));
        return $resp;
    });
    $app->post('/group/public',function($rst,$resp,$args){
         $data = $rst->getParsedBody();

         $err_code =200;
         $resp->getBody()->write(json_encode([
             'status' => $err_code,
             'msg' => $msg
         ]));
         return $resp;
    });
    $app->get('/group/{uid:[0-9]+}/uid',function($rst,$resp,$args){
        $list = $this->db->select('stockGroup','*',[
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
    $app->get('/group/{gid:[0-9]+}/detail',function($rst,$resp,$args){
            $list = $this->db->select('user_stock','*',[
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

    $app->get('/tickets',function($rst,$resp,$args){
        $this->logger->addInfo('Ticket list');
        $resp->getBody()->write(var_export($rst->getQueryParams()));
        return $resp;
    });
    $app->get('/user/login',function($rst,$resp,$args){
        $data = $rst->getQueryParams();
        $user['email'] = filter_var($data['email'],FILTER_VALIDATE_EMAIL);
    });
    $app->post('/tickets',function($rst,$resp,$args){
        $data = $rst->getParsedBody();
        $ticket_data = [];

    });
});
