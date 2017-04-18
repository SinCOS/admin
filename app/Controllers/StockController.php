<?php


namespace App\Controllers;

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
    public function postStock($rst,$resp,$args){
        $group_id = intval($args['group_id']);
        $data = $rst->getParams();
        $stock = json_encode($data['stock']);
        if(!$stock){
            return $this->json($resp,400,'','');
        }

    }
    public function getGroupStock($rst, $resp, $args)
    {
        $group_id = intval($args['group_id']);
        $list = $this->db->select("user_stock", ['cpy_id','sg_id'], [
            'AND' => [
                'sg_id' => intval($args['group_id']),
                'status[>]' => 0
            ]
        ]);
        return $resp->getBody()->write(json_encode([
            'status' => 200,
            'message' => '',
            'result' => $list ?? [],
        ]));
    }
    public function postStockGroup($rst, $resp, $args)
    {
        $data = $rst->getParams();
        if (!v::noWhitespace()->vaildate($data['name'])) {

        }
           $id =  $this->db->insert('stockGroup', [
                'uid' => 0,
                'status' => 1,
                'created_at' => date("Y-m-d h:i:s"),
                'name' => $data['name'],
                'public' => 1
            ]);
    }
    public function delStockGroup($rst, $resp, $args)
    {
        $id = intval($args['group_id']);
        if ($id >0) {
            $this->db->update('stock_group', [
                'status'=>0
                ], [
                    'id' => $id
                ]);
        }
    }
    public function delGroupStock($rst, $resp, $args)
    {
        $id = intval($args['id']);
        if ($id >0) {
            $res = $this->db->update('user_stock', [
                'status' => 0
            ], [
                'id' => $id
            ]);
        }
    }
}
