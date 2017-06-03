<?php

!defined('ROOT_PATH') && exit('Forbidden');

/**
 *    商品类型基类
 *
 *    @author    Garbin
 *    @usage    none
 */
class BaseGoods extends Object
{
    var $_is_material;  // 是否实体商品，支付接口可能需要用到
    var $_name;         // 商品类型的名称
    var $_order_type;   // 对应的订单类型
	var $_error = '';
	var $_visitor;
	var $_cart;
	var $_cart_global;
	var $_custom;
	public $_mod_custom;
	var $_mod_product;
	public $_mod_cusItem;
	public $_mod_cusFab;
    function __construct($params)
    {
    	$this->_visitor =& env('visitor', new UserVisitor());
    	$this->_mod_product = &m('products');
    	$this->_cart = &m("cart");
    	$this->_cart_global = &m("cart_global");
    	$this->_mod_custom=&m('custom');
    	$this->_custom = &m("customs");
    	$this->_mod_cusFab = &m("cusFab");
    	$this->_mod_cusItem = &m("cusItem");
        $this->BaseGoods($params);
    }
    function BaseGoods($params)
    {
        if (!empty($params))
        {
            foreach ($params as $key => $value)
            {
                $this->$key = $value;
            }
        }
    }
    /**
     *    获取对应订单类型实例
     *
     *    @author    Garbin
     *    @param     array $params
     *    @return    void
     */
    function get_order_type()
    {
        return $this->_order_type;
    }

    /**
     *    获取类型名称
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function get_name()
    {
        return $this->_name;
    }


    /**
     * 是否为定制商品
     * @return void
     * @access public
     * @see _is_diy
     * @version 1.0.0 (2014-11-19)
     * @author Ruesin
     */
    function _is_diy()
    {
        return $this->_is_diy;
    }
    /**
     * 购物车商品参数
     * @param array $aData 商品信息
     * @return string $return 序列化商品参数
     * @access public
     * @see _generateParams
     * @version 1.0.0 (2014-11-19)
     * @author Ruesin
     */
    function _generateParams($post) {
        
        $return = array(
                'spec_params' => $post['spec'],
                'diy_params'  => $post['items'],
                'try_params'  => $post['try_items'],
        );
        return serialize($return);
    }
    /**
     * 获取货品信息
     * @param array $post Post数据
     * @return array $return 货品信息
     * @access public
     * @see _product_info
     * @version 1.0.0 (2014-11-19)
     * @author Ruesin
     */
    function _product_info ($post = array())
    {
        if(empty($post))return;
        $cond = " goods_id = '".$post['goods_id']."'";
        //if($post['btype'] != 'diy'){
            $cond .=" AND goods_spec = '".$post['spec']."'";
        //}
        $return = $this->_mod_product->get(array(
                'conditions' => $cond,
        ));
        return $return;
    }
    
    /**
     * 删除定制商品购物车项
     * 
     * @author Ruesin
     */
    function drop($post){
    
        $droped_rows = $this->_cart->drop("ident='".$post['ident']."' AND user_id = '".$post['user_id']."' ");
        
        if (!$droped_rows)
        {
            $this->_error("drop_error");
            return false;
        }
        
        return true;
    }
    /**
     * 更新购物车数据
     * 
     * @author Ruesin
     */
    function update($post){
    
//         $stock = $this->_check_stock($post);
//         if($stock < 0){
//             $this->_error("goods_stock_error");
//         }
        
        if(intval($post['quantity']) <= 0){
            $this->_error("update_error");
            return ;
        } 
        
        $where = " ident = '".$post['ident']."' AND user_id = '".$post['user_id']."'";
        $datas=array('quantity' => $post["quantity"]);
        $datas['favorable_id']=$post['fav_id']?$post['fav_id']:'0';
        $res   = $this->_cart->edit($where, array('quantity' => $post["quantity"]));
        
        if(!$res)
        {
            $this->_error("update_error");
        }
         
        return $res;
    }
     
    /**
     * 校验面料库存(beta)
     *
     * @author Ruesin
     */
    function _check_stock($data = array()){
    	return true;
        $ck = $this->_cart->_check_stock($data);
        if($ck !== true){
            $this->_error($ck.' - 面料库存不足!');
            return false;
        }
        //总共的数量(添加/更新要预计算)
        //品类 → 单耗
        //面料
        
        /* $data = [
                'DBM739A' => [
                        '0003' => [
                                'cloth' => '0003',
                                'fabric' => 'DBM739A',
                                'quantity' => 6
                        ],
                        '0004' => [
                                'cloth' => 0004,
                                'fabric' => 'DBM739A',
                                'quantity' => 2
                        ]
                ],
                'DBP707A' => [
                        '0004' => [
                                'cloth' => 0004,
                                'fabric' => 'DBP707A',
                                'quantity' => 8
                        ],
                        '0003' => [
                                'cloth' => 0003,
                                'fabric' => 'DBP707A',
                                'quantity' => 3
                        ]
                ]
        ]; */
        
        /* $_cart = $this->_cart;
        foreach ($data as $key => $row){
            $fbs[$key] = $key;
            foreach ($row as $k=>$v){
                $stc[$key] += $v['quantity']*($_cart::$_CUSTOMS[$k]['fabric_m']);
            }
        }
        $mF = &m('fabric');
        $fabrics = $mF->find(array('conditions'=>db_create_in($fbs,'CODE'),'index_key'=>'CODE'));
        
        foreach ($stc as $key=>$val){
            if($fabrics[$key]['STOCK'] < $val){
                //$this->_error('面料库存不足!');
                $this->_error($key.' - 面料库存不足!');
                return false;
            }
        } */
        
        return true;
        
    }
    /**
     * 获取购物车项小计
     * 
     * @version 1.0.0 (Jan 8, 2015)
     * @author Ruesin
     */
    function _get_subtotal(&$item){
        $item['subtotal'] = $item['goods']['price'] * $item['quantity'];
    }
    
    /**
     * 格式化POST数据
     * 
     * @version 1.0.0 (Jan 8, 2015)
     * @author Ruesin
     */
    function _format_post($post = array()){
        $res['goods_id'] = isset($post['goods_id']) ? trim($post['goods_id']) : 0;
        $res['quantity'] = isset($post['quantity']) ? intval($post['quantity']) : 1;
        $res['rec_id']   = isset($post['rec_id']) ? intval($post['rec_id']) : 0;
        return $res;
    }
    
    ////************************************************////
    function _group_info($goods_id, $items){
    	/* 获取组件数据 */
    	
    	$res = array(
    			'oFabric'     => array(), //面料 
    			'iFabric'     => array(), //里料
    			'oData'       => array(), //其它组件
    			'process'     => array(), //工艺
    			'consumption' => array(), //单耗
    			'oCode'       => $items,
    		);
    	
    	$cs =& cs();
    	$groups = $cs->parsing_code($goods_id, $items);

    	if($groups['error']){
    		$this->_error($groups['msg']);
    		return false;
    	}
    	
    	foreach($groups['data'] as $key => $val){
    		// 取里料
    		if(in_array($val['t_id'], Constants::$materialParent)){
    			//unset($groups[$key]);
    			$res['iFabric'] = $val;
    		}
    		
    		// 取面料
    		if(in_array($val['t_id'], Constants::$fabricsParent)){
    			//unset($groups[$key]);
    			$res['oFabric'] = $val;
    		}
    	}
    	
    	$res['oData']       = $groups['data'];
    	$res['process']     = $groups['process'];
    	$res['consumption'] = $groups['consumption'];
    	
    	return $res;
    }
    
    /**
     * 获取组件信息
     *
     * @author Ruesin
     */
    function _group_info_format($data = array()){
    	 
        $arr = array('0011');
    	/* 获取组件数据 */
    	$res = array(
    			//'oFabric'     => array(), //面料
    			//'oMenus'       => array(), //品类工艺＋深度
    			//'process'     => array(), //工艺信息
    			//'oClothing'     => array(), //品类信息
    			//'oGoods'     => array(), //品类信息
    	);
    	 
    	//import("dict.lib");
    	//$dict = new Dict($this->init[$data["category"]]['items']);
    
    	/* design：款式设计｜deep：深度设计 */
    	//$res['oMenus'] = $dict->getMenu();  //所有的工艺项 暂时用不到 先注释 -- sin
    
    	foreach ($data as $row){
    		$ids[] = $row['id'];
    		$res[$row['id']]['oGoods']    = $row;
    		$res[$row['id']]['oClothing'] = $this->init[$row["category"]];
    	}
    
    	$fbs = $this->_mod_cusFab->find(array(
    			"conditions" => db_create_in($ids,'custom_id'), //"custom_id = '{$data["id"]}'",
    			'join'       => "belongs_to_fab",
    			'fields'     => "fab.*,f.CODE,f.ID"
    	));
    	foreach ($fbs as $row){
    		$res[$row['custom_id']]['oFabric'] = $row;
    	}
    
    	/* 全部工艺信息 */
    	$process = $this->_mod_cusItem->find(array(
    			'conditions' => db_create_in($ids,'custom_id')." AND im.is_default = '1' ", //custom_id = '{$data["id"]}'
    			'join'       => "belongs_to_dict",
    			'fields'     => "im.*,c.name"
    	));
    
    	//剔除刺绣
    	foreach ($process as $key => $row){
    		if($this->_embP[$row['menu_id']]){
    			unset($process[$key]);
    		}
            
    		//如果是女西服 工艺组装去掉menu_id 16-03-21
    		if (in_array($res[$row['custom_id']]['oGoods']['category'], $arr))
    		{
    		    if (!$row['assign'])
    		    {
    		        $res[$row['custom_id']]['process'][$row['item_id']] = $row['item_id'];
    		    }
    		    else
    		    {
    		        $res[$row['custom_id']]['process'][$row['item_id']] = $row['item_id']."|sin|".$row['assign'];
    		    }
    		}
    		else
    		{
    		    if (!$row['assign'])
    		    {
    		        $res[$row['custom_id']]['process'][$row['menu_id']] = $row['item_id'];
    		    }
    		    else
    		    {
    		        $res[$row['custom_id']]['process'][$row['menu_id']] = $row['item_id']."|sin|".$row['assign'];
    		    }
    		}
    		
    	}
    	return $res;
    }
    
    function _figure_body($height, $weight){
    	$m = 0;
    	if($height >= 191 || ($weight >= 101 && $weight <= 120)){
    		$m = 1.5;
    	}
    	
    	if($height > 200 || $weight >= 121){
    		$m = 2;
    	}
    	
    	return $m;
    }
    
    function _base_info($goods_id){
    	
    	/* 获取基本款数据 */
    	$data = $this->_custom->get_info($goods_id);
    	
    	 
    	/* 基本款不存在 */
    	if(!$data){
    		$this->_error('diy_goods_not_exist');
    		return false;
    	}
    	 
    	/* 检查基本款是否上架销售 */
    	if(!$data['is_active']){
    		$this->_error('diy_goods_not_sale');
    		return false;
    	}

    	return $data;
    }
    
    function _check_store($store, $buyNum){
    	
    	if($store < $buyNum){
    		$this->_error('商品库存不足！');
    		return false;
    	}
    	return true;
    }
    
    
    function _cart_info($goods_id, $rec_id = 0){
    	 
    	$conditions = "goods_id = '{$goods_id}' AND session_id='".SESS_ID."' AND user_id = '".$this->_visitor->get("user_id")."'";
    	 
    	if($rec_id){
    		$conditions .= " AND rec_id = '{$rec_id}'";
    	}
    	$item = $this->_cart->find(array(
    				'conditions' => $conditions,
    			));
    	
    	return $item;
    }
    
    
    function _check_fabric_store($params){
    	
    	$list = $this->_cart_fabric($params['fabric']);
    	$buyNum = $params['buy_num']*$params['fabric_m'];
    	
    	foreach($list as $key => $val){
    		if($params['rec_id'] != $val['rec_id']){
    			$item = unserialize($val['items']);
    			$buyNum += $val['quantity']*$item['consumption']['fabric_m'];
    		}
    	}
    	
    	if($params['store'] < $buyNum){
    		$this->_error('面料库存不足!');
    		return false;
    	}
    	
    	return true;
    }
    
    /**
     * 
     * @param str $fabric 面料
     * @return array
     */
    function _cart_fabric($fabric){
    	
    	$conditions = "fabric = '{$fabric}' AND session_id='".SESS_ID."' AND user_id = '".$this->_visitor->get("user_id")."'";
    	
    	$item = $this->_cart->find(array(
    			'conditions' => $conditions,
    	));
    	 
    	return $item;
    }
    
    //检查尺码是否合法
    function checkSpec($spec, $cate=0){
    	$size  = Constants::$sizelParent;
    	if(!isset($size[$cate])){
    		return false;
    	}else{
    		if(!in_array($spec, $size[$cate])){
    			return false;
    		}
    	}
    	
    	return true;
    }
    
    function _error($msg, $obj=""){
    	$this->error = $msg;
    }
    
    function get_error(){
    	return $this->error;
    }
    
    
    
    
    
}


?>