<?php


namespace App\Controllers;

use Respect\Validation\Validator as v;

class StockController extends Controller
{
        // admin/user/1/grp
    public function getUserGroup($rst, $resp, $args)
    {
        $list = $this->db->select("stockGroup", ['id','name'], [
            'AND' => [
                'uid' => intval($args['uid']),
                'status[>]' => 0
            ]
        ]);
    }
    private function get_GroupKey($group_info)
    {
        return ($group_info && $group_info['public'] == 1) ? "group:{$group_info['id']}" : "vipgroup:{$group_info['id']}";
    }
    public function postStock($rst, $resp, $args)
    {
        $group_id = intval($args['group_id']);
        $data = $rst->getParams();
        $stock = $data['stock'];
        if (!$stock) {
            return $this->json($resp, '', 400, '');
        }
        $group_info = $this->get_GroupInfo($group_id);
        $redis = $this->redis;
        $redis->select(2);
        $key = $this->get_GroupKey($group_info);
        $result = $redis->pipeline(function ($pipe) use ($group_id, $stock) {
            $pipe->del($key);
            $pipe->sadd($key, $stock);
        });
        return !empty($result) && $result[1] > 0 ?$this->json($resp, '', 200, $result) : $resp;
    }
    public function delStockGroup($rst, $resp, $args)
    {
       
        $group_id = intval($args['group_id']);
        $group_info =  $this->get_GroupInfo($group_id);
         
        $res = $this->db->update('stockGroup', ['status' => 0], [
            'AND' => [
                'uid' => 0,
                'id' => $group_id
            ]
        ]);
        if ($res) {
            if ($this->update_StockGroup($group_id, $group_info)) {
                 return $this->json($resp, $this->db->log(), 200, $res);
            }
        }
         return $this->json($resp, '', 400, $res);
    }
    private function get_GroupInfo($group_id)
    {
        return  $this->db->get('stockGroup', [
            'id' =>$group_id
        ]);
    }
    public function getGroupStock($rst, $resp, $args)
    {
        $group_id = intval($args['group_id']);
        $group_info = $this->get_GroupInfo($group_id);
        
        $this->redis->select(2);
        $key = $this->get_GroupKey($group_info);
        $list = $this->redis->smembers($key);
       
        return $resp->getBody()->write(json_encode([
            'status' => 200,
            'message' => '',
            'result' => $list ?? [],
        ]));
    }
    private function update_StockGroup($group_id, $group_info)
    {
            $this->redis->select(2);
            $key = $this->get_GroupKey($group_info);
            $this->redis->del($key);
            $public = isset($group_info['public']) ? $group_info['public'] : 1;
            $res = $this->db->select('stockGroup', ['id','name'], [
                'AND' => [
                     'uid' => 0,
                     'public' => $public,
                     'status[>]' => 0
                ]
               
             ]);
        if ($res) {
            if ($public == 1) {
                 $this->redis->set("publicGroup", json_encode($res));
            } else {
                $this->redis->set('vipGroup', json_encode($res));
            }
            return true;
        }
            return false;
    }
    public function postStockGroup($rst, $resp, $args)
    {
        $data = $rst->getParseBody();
        // if (!v::noWhitespace()->vaildator($data['name'])) {

        // }
        $redis = $this->redis;
        $redis->select(2);
        $id =  $this->db->insert('stockGroup', [
                'uid' => 0,
                'status' => 1,
                'created_at' => date("Y-m-d h:i:s"),
                'name' => $data['name'],
                'public' => isset($data['public']) ? (int)$data['public'] : 1,
            ]);
        $group_info = $this->get_GroupInfo($group_id);
        if ($id >0) {
            if ($this->update_StockGroup($id, $group_info)) {
                return $this->json($resp, '', 200, []);
            }
        }
        return $this->json($resp, '', 400, []);
    }

    public function delGroupStock($rst, $resp, $args)
    {
        $id = intval($args['cpy_id']);
        $group_id = intval($args['group_id']);
        $group_info = $this->get_GroupInfo($group_id);
        $this->redis->select(2);
        $key = $this->get_GroupKey($group_info);
        $result = $this->redis->srem( $key, $args['cpy_id']);
        return $this->json($resp, '', $result === 1 ? 200  : 400, $result);
    }
}
