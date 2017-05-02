<?php

namespace App\Controllers\Auth;

use \Respect\Validation\Validator as v;
use App\Controllers\Controller;

class AuthController extends Controller {
        public function getSignUp($rst,$resp){
                return $this->view->render($resp,'template/auth/login.html');
        }
        public function postSignUp($rst,$resp,$args){
            $data  = $rst->getParsedBody();
            v::noWhitespace()->notEmpty()->validate($data['username']) || $message[] = 'username can\'t emtpy';
            v::noWhitespace()->notEmpty()->validate($data['password']) || $message[] ='password at lease 6 words';
            if(empty($message)){
                $user = $this->db->get('admin',['username','password'],[
                        'AND' => [
                                'username' => $data['username'],
                        'password' =>  md5($data['passwd']),
                        ]]);
                if($user){
                        $_SESSION['adm.usr.id'] = $user['id'];
                        $_SESSION['adm.user.name'] = $user['username'];
                }else{
                        $message[] = '用户名或密码错误';
                }

            }
            $status = empty($message) ? 200:400;
            return $resp->withJson([
                    'status' => empty($message) ? 200:400,
                    'message'=> 'error',
                    'result'=>  $message
                    ],$status);
        }
}