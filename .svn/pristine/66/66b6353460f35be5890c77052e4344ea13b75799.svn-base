<?php

!defined('ROOT_PATH') && exit('Forbidden');

class BaseGoods extends Object
{
    var $_name; 
	var $_error = '';
	var $_cart;
	public $_mod_custom;
	public $_mod_cusItem;
	public $_mod_cusFab;
	
    function __construct($params)
    {
    	$this->_cart = &m("cart");
    	$this->_mod_custom = &m("custom");
    	$this->_mod_cusItem = &m("cusItem");
    	$this->_mod_cusFab = &m("cusFab");
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
        );
        return serialize($return);
    }

    /**
     * 删除购物车项
     * 
     * @version 1.0.0 (2014-11-19)
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
     * @version 1.0.0 (2014-11-19)
     * @author Ruesin
     */
    function update($post){
    
        $stock = $this->_check_stock($post);
        if($stock < 0){
            $this->_error("goods_stock_error");
            return false;
        }
        
        if(intval($post['quantity']) <= 0){
            $this->_error("update_error");
            return false;
        } 
        
        $where = " ident = '".$post['ident']."' AND user_id = '".$post['user_id']."'";
        $res   = $this->_cart->edit($where, array('quantity' => $post["quantity"]));
         
        if(!$res)
        {
            $this->_error("update_error");
        }
         
        return $res;
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
    	$res['type'] = $this->_name;
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
        return true;
    }
    
    /**
     * 套装标识
     * 
     * @date 2015-10-15 下午3:28:22
     * @author Ruesin
     */
    function dis_ident($id = 0){
        do{
            $str='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            for($i=0;$i<8; $i++){
                $code .= $str[mt_rand(0, strlen($str)-1)];
            }
            $il = strlen($id);
            for ($i=$il;$i<10;$i++){
                $id = 'D'.$id;
            }
            $ident =  $code.$id;
        } while($this->_cart->get(" dis_ident = '{$ident}'"));
        return $ident;
    }
    
    
    
    
    
    
    
    /**
     * 获取组件信息
     *
     * @author Ruesin
     */
    function _group_info($goods_id){
    	
    	/* 获取组件数据 */
    	$res = array(
    			'oFabric'     => array(), //面料
    			//'oMenus'       => array(), //品类工艺＋深度
    			'process'     => array(), //工艺信息
    			'oClothing'     => array(), //品类信息
    			//'oGoods'     => array(), //品类信息
    	);
    	
    	//import("dict.lib");
    	$data = $this->_mod_custom->get($goods_id);
    	//$res['oGoods'] = $data;
    	$res['oClothing'] = $this->init[$data["category"]];   //既然写死了  就先按照写死的走吧
    	//$dict = new Dict($this->init[$data["category"]]['items']);
    	$res['oFabric'] = current($this->_mod_cusFab->find(array(
    			"conditions" => "custom_id = '{$data["id"]}'",
    			'join'       => "belongs_to_fab",
    			'fields'     => "fab.*,f.CODE,f.ID"
    	)));
    	
    	/* design：款式设计｜deep：深度设计 */
    	//$res['oMenus'] = $dict->getMenu();  //所有的工艺项 暂时用不到 先注释 -- sin
    	
    	/* 全部工艺信息 */
    	$process = $this->_mod_cusItem->find(array(
    			'conditions' => "custom_id = '{$data["id"]}' AND im.is_default = '1' ",  //CTO说后台选的刺绣等于无! 其他地方又不能选择工艺，所以就取默认，并把刺绣剔除。将来如果存在定制，就需要再改了。 -- sin
    			'join'       => "belongs_to_dict",
    			'fields'     => "im.*,c.name"
    	));

    	//剔除刺绣
    	foreach ($process as $key => $row){
    	    if($this->_embP[$row['menu_id']]){
    	        unset($process[$key]);
    	    }
    	    $res['process'][$row['menu_id']] = $row['item_id'];
    	}
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
    	
    	$data = $this->_mod_custom->get($goods_id);

    	/* 基本款不存在 */
    	if(!$data){
    		$this->_error('diy_goods_not_exist');
    		return false;
    	}
    	 
    	/* 检查基本款是否上架销售 */
    	if(!$data['is_sale']){
    		$this->_error('diy_goods_not_sale');
    		return false;
    	}

    	return $data;
    }
    
    function _check_store($buyNum){
        return true;
    	$store = 100;
    	if($store < $buyNum){
    		$this->_error('商品库存不足！');
    		return false;
    	}
    	return true;
    }
    
    
    function _cart_info($post = array()){
        
    	$conditions = "goods_id = '{$post['goods_id']}' AND user_id = '{$post['user_id']}' AND type='{$this->_name}' AND size='{$post['size']}'";
    	 
    	if($post['ident']){
    		$conditions .= " AND ident = '{$post['ident']}'";
    	}
    	
    	$item = $this->_cart->get(array(
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