<?php
/**
 * 定制商品订单
 * @see DiyOrder
 * @version 1.0.0 (2014-12-1)
 * @author Ruesin <ruesin@163.com>
 */
class DiyOrder extends BaseOrder
{
    var $_name = 'diy';
    var $_ms;
    function __construct(){
        $this->_ms = &ms();
		parent::__construct();
    }
    
    function submit($data){

    	extract($data);

    	/* 订单基本信息 */
    	$baseinfo = $this->_handle_order_info($_order, $_cart);
		if(!$baseinfo){
			return false;
		}
		
		
    	/* 保存订单信息 */
		$order_id = $this->_ms->order->create_order($baseinfo);
		
    	if (!$order_id){
    		$this->_error('create_order_failed');
    		return false;
    	}

    	/* 保存商品数据 */
    	foreach ($_cart['object'] as $key => $item)
    	{

    	    //量体信息
    	    if($item['need_measure'] == 1){
    	        if($item['measure_id'] == 1 || $item['measure_id'] == 4){
    	            $mea_data = unserialize($item['measure_data']);
    	            unset($mea_data['name']);
    	        }elseif ($item['measure_id'] == 2 || $item['measure_id'] == 3){
    	            $mea_data = array();
    	        }
    	        $mea_data['figure']   = $item['measure_id'];
    	        $mea_data['order_id'] = $order_id;
    	    }
    	    
    	    $params = unserialize($item['params']);
    		$goods_items[] = array(
    		        'son_sn'        => $baseinfo['order_sn'].(101+$key),
    				'order_id'      => $order_id,
    				'goods_id'      => $item['goods_id'],
    				'product_id'    => $item['product_id'],
    		        'product_sn'    => $item['goods']['product_sn'],
    		        'goods_name'    => $item['goods']['goods_name'],
    				'price'         =>  $item['goods']['price'],
    				'quantity'      =>  $item['quantity'],
    				'goods_image'   =>  $item['goods']['goods_img'],
    		        'is_valid' 		=> 	1,
    		        'is_diy' 		=> 	($item['type']=='diy')?1:0,
    		        'need_measure'  => $item['need_measure'],
    		        'emb_con'		=>  $item['emb'],
    		        'emb_color'     =>  $item['color'],
    		        'emb_font'      =>  $item['font'],
    				'items'         =>  serialize($params['diy_params']),
    		        'params'        =>  serialize($params['spec_params']),
    		        'measure_data'  =>  $mea_data,
    		        //字段需要对对.
    		);
    	}
//     	print_r($goods_items);exit;
    	$res = $this->_ms->order->add_order_goods($goods_items);
    	if (!$res){
    		$this->_error('create_order_failed');
    		return false;
    	}
    	
    	//保存订单日志
    	//.
    	//...
    	
    	return array('order_id' => $order_id, 'order_sn' => $baseinfo['order_sn']);
    
    }
    
    /**
     * 获取子订单对应量体数据
     * @access public
     * @see _get_order_figure
     * @version 1.0.0 (2014-12-6)
     * @author Ruesin
     */
    function _get_order_figure ($id,$data)
    {
        if($id == 1 || $id == 4){
            $res = unserialize($data);
            unset($res['name']);
        }elseif ($id == 2 || $id == 3){
            
            
        }elseif ($id == 4111){

        }
        
        return $res;
    }
}

?>