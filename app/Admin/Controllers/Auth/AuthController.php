<?php

namespace App\Admin\Controllers\Auth;

use \Respect\Validation\Validator as v;
use App\Admin\Controllers\Controller;

class AuthController extends Controller {
        public function getSignUp($rst,$resp){
                return $this->view->render($resp,'template/auth/login.html');
        }
        public function postSignUp($rst,$resp){
            $data  = $rst->getParams();
       
            return $resp;
        }
}