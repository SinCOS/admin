<?php

namespace App\Controllers\Auth;

use \Respect\Validation\Validator as v;
use App\Controllers\Controller;

class AuthController extends Controller
{
    public function getSignUp($rst, $resp)
    {
        return $this->view->render($resp, 'template/auth/login.html');
    }
    public function postSignUp($rst, $resp, $args)
    {
        $data  = $rst->getParsedBody();
        $message = array();
        isset($data['verify_code']) && $data['verify_code'] == $_SESSION['authcode'] || $message[] = '验证码不对';
        isset($data['username']) &&  v::noWhitespace()->notEmpty()->validate($data['username']) || $message[] = '用户名太短';
        isset($data['password']) && v::noWhitespace()->notEmpty()->validate($data['password']) || $message[] ='您的密码必须是大于等于6个字符';
        if (empty($message)) {
            $user = $this->db->get('admin', ['id','username','password'], [
                    'AND' => [
                            'username' => $data['username'],
                            'password' =>  md5($data['password']),
                    ]]);
            if ($user) {
                $_SESSION['adm.usr.id'] = $user['id'];
                $_SESSION['adm.usr.name'] = $user['username'];
            } else {
                $message[] = '用户名或密码错误';
            }
        }
        $status = empty($message) ? 200:400;
        return $resp->withJson([
            'status' => empty($message) ? 200:400,
            'message'=> $status == 200 ? '' : $message[0],
            'result'=>  $message
            ], $status);
    }
}
