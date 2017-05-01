<?php

namespace App\Controllers\Auth;

use \Respect\Validation\Validator as v;
use App\Controllers\Controller;

class AuthController extends Controller {
        public function getSignUp($rst,$resp){
                return $this->view->render($resp,'template/auth/login.html');
        }
        public function postSignUp($rst,$resp,$args){
            $data  = $rst->getParseBody();
            v::noWhitespace()->notEmpty()->validate($data['username']) || $message[] = 'username can\'t emtpy';
            v::noWhitespace()->notEmpty()->validate($data['passwd']) || $message[] ='password at lease 6 words';
            return $resp;
        }
}