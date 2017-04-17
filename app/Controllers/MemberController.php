<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;

class MemberController extends Controller
{

    public function getMembers($rst, $resp, $args)
    {
        
        if(!$rst->isXhr()){
             return $this->view->render($resp,"template/members.html");
        }
        $total = $this->db->count('user');
        $page = intval($rst->getQueryParams('p')) ;
        $page = $page === 0 ?1:$page;
        $_start = ($page -1) * 20;
        

        $list = $this->db->select("user", "*", [
                "LIMIT" => [$_start,20],
                'ORDER' => ['total_login' => "DESC"]
        ]);
        return $resp->getBody()->write(json_encode([
            'status' => 200,
            'msg' => '',
            'result' => [
                'data' =>$list ?? [],
                'total' => $total,
            ]
        ]));
       
    }
    public function getUserInfo($rst, $resp, $args)
    {
        $uid = intval($args['uid']);
        if ($uid > 0) {
            $user_info = $this->db->get('users', '*', [
                'id' => $uid
            ]);
            if(!$user_info){
                 $rst->redirect("/");
            }
           return $this->view->render($resp,"template/user_info.html",[
               user_info => $userinfo,
           ]);
        }
    }
    public function putUser($rst, $resp, $args)
    {

    }
}
