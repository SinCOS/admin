<?php

namespace App\Admin\Controllers\Auth;

use App\Admin\Controllers\Controller;

class AuthController extends Controller {
        public function getSignUp($rst,$resp){
                return $this->view->render($resp,'template/auth/login.html');
        }
        public function postSignUp($rst,$resp){
        //       if(!$rst->isXhr){
        //               return $this->error('')
        //       }
            $this->success("xxx");
            $data  = $rst->getParams();
            
            return $resp;
        }
}