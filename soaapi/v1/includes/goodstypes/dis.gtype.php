<?php

/**
 *    定制商品
 */
class DisGoods extends BaseGoods
{
	
    function __construct($param)
    {
        /* 初始化 */
        $param['_is_material']  = true;
        $param['_name']         = 'dis';
        $param['_order_type']   = 'normal';
        
        parent::__construct($param);
    }
    
    
    function add($post)
    {
    	//简单过滤传过来的基体款ID
    	$goodsids = explode(",", $post["goods_id"]);
    	$spec    = explode(",", $post['spec']);
    	$specs = array();
    	foreach($goodsids as $key => $val){
    		$goodsids[$key] = intval($val);
    		$specs[$val] = $spec[$key];
    	}
    	
    	if(count($goodsids) != count($specs)){
    		$this->_error('参数错误!');
    		return false;
    	}
    	
    	$this->_link_mod =& m("links");
    	$_dis_mod = &m("dissertation");
    	$links = $this->_link_mod->find(array(
    			"conditions" => "d_id = '{$post["disid"]}' AND ". db_create_in($goodsids, "c_id"),
    			'fields'     => 'c_id',
    	));
    	
    	if (!$links){
			$this->_error('未匹配到相关的基本款');
			return false;
    	}
    	
    	$disinfo = $_dis_mod->get_info($post['disid']);
    	
    	if(empty($disinfo)){
    		$this->_error('参数错误!');
    		return false;
    	}
    	
    	$cs =& cs();
    	$gcategory = $cs->_get_gcategory();
    	 
    	
    	/* 事件开始 */
    	$transaction = $this->_cart->beginTransaction();
    	

    	foreach($links as $key => $val){
    		
    		$_Bdata = $this->_base_info(intval($val["c_id"]));
    		
    		$_Cdata = $this->_cart_info($val['c_id']);
    		
    		$parsCode = $cs->parsing_code_base($val["c_id"]);
    		
    		if($parsCode['error']){
    			$this->_error($parsCode['msg']);
    			 
    			return false;
    		}
    		
    		$items = current($parsCode['data']);
    		
    		$_Gdata = $this->_group_info($val['c_id'], $items);
    		
    		if(empty($_Gdata['oFabric'])){
    			$this->_error('该商品没有匹配到面料信息!');
    			return false;
    		}
    		
    		/********************************检查库存 ************************************/
    		$buyNum = 1;
    		
    		if($_Cdata){
    			foreach($_Cdata as $_ck => $_cv){
    				$buyNum += $_cv['quantity'];
    			}
    		}
    		
    		$_checkStore = $this->_check_store($_Bdata['cst_store'], $buyNum);
    		
    		if(!$_checkStore){
    			return false;
    		}

    		$_checkFabricStore = $this->_check_fabric_store(array(
    				'fabric'   => $_Gdata['oFabric']['part_name'],
    				'fabric_m' => $_Gdata['consumption']['fabric_m'],
    				'rec_id'   => 0,
    				'buy_num'  => 1,
    				'store'    => $_Gdata['oFabric']['part_number'],
    		));
    		
    		if(!$_checkFabricStore){
    			return false;
    		}
    		
    		$price  = $_Bdata['service_fee']; //商品价格
    		
    		/********************************计算价格**********************************/
    		//面料价格
    		$price += $_Gdata['oFabric']['price'] * $_Gdata['consumption']['fabric_m'];
    		 
    		//里料价格
    		$price += $_Gdata['iFabric']['price'] * $_Gdata['consumption']['lining_m'];
    		 
    		 
    		//工艺费
    		foreach((array)$_Gdata['process'] as $_pk => $_pv){
    			$price += $_pv['price'];
    		}
    		
    		if(!$this->checkSpec($specs[$val["c_id"]], $_Bdata['cst_cate'])){
    			$this->_cart->rollback();
    			$this->_error('尺码错误!');
    			return false;
    		}
    		
    		/* 用G+新数据 与Gd进行对比库存*/
    		  
    		/* 生成入库数据 */
    		$cData = array(
    				'goods_id'   => $_Bdata["cst_id"],
    				'goods_name' => $_Bdata["cst_name"],
    				'goods_sn'   => $_Bdata["cst_sn"],
    				'source_id'  => $post["disid"],
    				'cst_author' => $_Bdata['cst_author'],
    				'cst_source' => $_Bdata['cst_source'],
    				'cst_source_id' => $_Bdata['cst_source_id'],
    				'goods_image'  => $_Bdata["cst_dis_image"],
    				'cst_cate'  => $_Bdata["cst_cate"],
    				'items'      => serialize($_Gdata),
    				'price'      => round($price,0),
    				'session_id' => SESS_ID,
    				'user_id'    => $this->_visitor->get('user_id'),
    				'quantity'   => 1,
    				'is_diy'     => 0,
    				'specification' => $specs[$val["c_id"]],
    				'type'       => $this->_name,
    				'source_title' => $disinfo['title'],
    				'type_name'    => $_Bdata["cst_cate"] ? $gcategory[$_Bdata["cst_cate"]]['cate_name'] : '',
    				'goods_weight' => 10,
    				'fabric'     => $_Gdata['oFabric']['part_name'],
    		);
    		
    		$res = $this->_cart->add($cData);
    		
    		if(!$res){
	    		$this->_cart->rollback();
				$this->_error('加入购物车，失败请重试！');
				return false;
    		}
    	}
    	
    	$this->_cart->commit($transaction);
    	
    	return true;
    }
    
    
    function update($post){
    	 
 		$_Bdata = $this->_base_info(intval($post["goods_id"]));
    	
    	if(!$_Bdata){
    		return false;
    	}

        /* 购物车商品 */
    	$_Cgoods = $this->_cart_info($post["goods_id"]);
    	
    	$item = array();
    	foreach($_Cgoods as $key => $val){
    		if($val['rec_id'] == $post['id']){
    			  $item = unserialize($val['items']);
    		}
    	}
    	
    	/* 组件信息 */
    	$_Gdata = $this->_group_info($post["goods_id"], $item['oCode']);
    	
    	if(empty($_Gdata['oFabric'])){
    		$this->_error('该商品没有匹配到面料信息!');
    		return false;
    	}
    	
    	/********************************检查库存 ************************************/
    	$buyNum = 1;
    	if($_Cgoods){
    		foreach($_Cgoods as $key => $val){
    			if($val['rec_id'] == $post['id']){
    				$buyNum  += $post["num"];
    			}else{
    				$buyNum += $val['quantity'];
    			}
    		}
    	}
    	
    	$_checkStore = $this->_check_store($_Bdata['cst_store'], $buyNum);

    	if(!$_checkStore){
    		return false;
    	}

        $_checkFabricStore = $this->_check_fabric_store(array(
    				'fabric'   => $_Gdata['oFabric']['part_name'],
    				'fabric_m' => $_Gdata['consumption']['fabric_m'],
    				'rec_id'   => $post['id'],
    				'buy_num'  => $post['num'],
    				'store'    => $_Gdata['oFabric']['part_number'],
    			));

    	if(!$_checkFabricStore){
    		return false;
    	}

    	/********************************检查End ************************************/
    	
    	$where = "session_id = '".SESS_ID."' AND rec_id = '{$post['id']}'";
    	$where .= $this->_visitor->get("user_id") ? " AND user_id = '".$this->_visitor->get("user_id")."'" : '';
    	
    	$res = $this->_cart->edit($where, array('quantity' => $post["num"]));
    	if(!$res)
    	{
    		$this->_error("update_error");
    	}
    	return $res;
    }
    
    
    function drop($post){
    	$droped_rows = $this->_cart->drop("rec_id='{$post['id']}' AND type='{$this->_name}' AND session_id='" . SESS_ID ."'");
    	if (!$droped_rows)
    	{
    		$this->_error("drop_error");
    		return false;
    	}
    	return true;
    }
    
    function reset(){
    	return true;
    }
}

?>