<?php

namespace App\Admin\Controllers;

use Respect\Validation\Validator as v;

class MembersController extends Controller
{

    public function getMembers($rst, $resp, $args)
    {
        $page = intval($rst->getQueryParams('p')) ?? 1;
        $_start = ($page -1) * 20;
        $list = $this->db->select("users", "*", [

                "LIMIT" => [$_start,20]
            
        ]);
    }
    public function getUserInfo($rst, $resp, $args)
    {
        $uid = intval($args['uid']);
        if ($uid > 0) {
            $user_info = $this->db->get('users', ['*'], [
                'id' => $uid
            ]);
        }
    }
    public function putUser($rst, $resp, $args)
    {
    }
}
