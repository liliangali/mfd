<?php

class OrderpccronModel extends BaseModel 
{
    var $table  = 'order_pc_cron';
    var $prikey = 'id';
    var $_name  = 'orderpccron';
    
    function _addToList($order_id = 0){
        if(!$order_id) return false;
        $mOg  = &m('ordergoods');
        $goods = $mOg->find(" order_id = '{$order_id}' ");
        
        foreach ($goods as $row){
            //if ($row['type'] == 'suit' && $row['activity'] == 4)continue;
            $cData[] = array(
                    'fabric_id' => $row['rec_id'], //其实是order_goods  id 
                    'son_sn'    => $row['son_sn'],
                    //'is_scales' => '0',
                    //'status' => '0'
                    //'err_msg' => '',
                    //'rcmtem_order_id' => '',
                    //'rcmtem_status' => '',
                    'add_time' => gmtime(),
                    //'up_time' => '',
                    'order_id' => $order_id,
            );
        }
        $res = $this->add(addslashes_deep($cData));
        return $res;
    }
}

