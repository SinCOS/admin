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
    public function postStock($rst, $resp, $args)
    {
        $group_id = intval($args['group_id']);
        $data = $rst->getParams();
        $stock = $data['stock'];
        if (!$stock) {
            return $this->json($resp, '', 400, '');
        }
        
        $redis = $this->redis;
        $redis->select(2);
        $result = $redis->pipeline(function ($pipe) use ($group_id, $stock) {
            $key = "group:{$group_id}";
            $pipe->del($key);
            $pipe->sadd($key, $stock);
        });
        return !empty($result) && $result[1] > 0 ?$this->json($resp, '', 200, $result) : $resp;
    }
    public function delStockGroup($rst,$resp,$args){
       
        $group_id = intval($args['group_id']);
       
        $res = $this->db->delete('stockGroup',[
            'AND' => [
                'uid' => 0,
                'id' => $group_id
            ] 
        ]);
        if($res){
          
            if($this->update_StockGroup($group_id)){
                 return $this->json($resp, $this->db->log(), 200, $res);
            }
        }
         return $this->json($resp, '', 400, $res);
    }
    public function getGroupStock($rst, $resp, $args)
    {
        $group_id = intval($args['group_id']);
        // $list = $this->db->select("user_stock", ['cpy_id','sg_id'], [
        //     'AND' => [
        //         'sg_id' => intval($args['group_id']),
        //         'status[>]' => 0
        //     ]
        // ]);
        $this->redis->select(2);
        $list = $this->redis->smembers("group:{$group_id}");
        return $resp->getBody()->write(json_encode([
            'status' => 200,
            'message' => '',
            'result' => $list ?? [],
        ]));
    }
    private function update_StockGroup($group_id){
            $this->redis->select(2);
            $this->redis->del("group:{$group_id}");
             $res = $this->db->select('stockGroup', ['id','name'], [
                'uid' => 0
            ]);
            if ($res) {
                $this->redis->set("publicGroup", json_encode($res));
                return true;
            }
            return false;
    }
    public function postStockGroup($rst, $resp, $args)
    {
        $data = $rst->getParams();
        // if (!v::noWhitespace()->vaildator($data['name'])) {

        // }
        $redis = $this->redis;
        $redis->select(2);
        $id =  $this->db->insert('stockGroup', [
                'uid' => 0,
                'status' => 1,
                'created_at' => date("Y-m-d h:i:s"),
                'name' => $data['name'],
                'public' => 1
            ]);
        if ($id >0) {
           if($this->update_StockGroup($id)){
               return $this->json($resp, '', 200, []);
           }
        }
        return $this->json($resp, '', 400, []);
    }

    public function delGroupStock($rst, $resp, $args)
    {
        $id = intval($args['cpy_id']);
        $group_id = intval($args['group_id']);
        $this->redis->select(2);
      
        $result = $this->redis->srem("group:{$group_id}", $args['cpy_id']);
        return $this->json($resp, '', $result === 1 ? 200  : 400, $result);
    }
}
