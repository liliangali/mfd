<?php

!defined('ROOT_PATH') && exit('Forbidden');

class BaseGoods extends Object
{
    var $_name; 
	var $_error = '';
	var $_cart;
	public $_mod_goods;
	public $_mod_cusItem;
	public $_mod_product;
	
    function __construct($params)
    { 
    	$this->_cart = &m("cart");
    	$this->_mod_goods = &m("goods");
    	$this->_mod_cusItem = &m("cusItem");
    	$this->_mod_product = &m("products");
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
        /**     * 格式化POST数据     *     * @version 1.0.0 (Jan 8, 2015)     * @author Ruesin     */    function _format_post($post = array()){
         	$res['goods_id'] = isset($post['gid']) ? trim($post['gid']) : 0;    	$res['product_id'] = isset($post['pid']) ? trim($post['pid']) : 0;    	$res['quantity'] = isset($post['num']) ? intval($post['num']) : 1;    	$res['rec_id']   = isset($post['rec_id']) ? intval($post['rec_id']) : 0;
    	$res['type'] = $this->_name;
    	if (isset($post['fpic'])){
    	    $imgstr = str_replace("data:image/jpeg;base64,", "", $post['fpic']);
//     	    $res['fpic'] = isset($post['fpic']) ? '/diy/images/cptu.jpg' : '';
    	    $res['fpic'] = $this->save_img($imgstr,$post['cid']);
    	}    	return $res;    }
    
    //base64格式图片 存储 路径定义死
    function save_img($imgstr,$clothingID){
        $path = ROOT_PATH.'/upload/images/diy/';
        $img = base64_decode($imgstr);
        /* 品类id＋时间戳 md5 */
        $t = md5($clothingID.time()).'.jpg';
        $save = file_put_contents($path.$t,$img);
        if ($save){
            return '/upload/images/diy/'.$t;
        }else{
            return false;
        }
    }
    /**
     * 校验面料库存
     *
     * @date 2015年12月9日 下午3:11:23v
     *
     * @author Ruesin
     */
    
    function _check_stock($data){
        import('shopCommon');
        $stock = ShopCommon::checkFabricStock(array($data));
        if ($stock !== true){
            $this->_error("面料".$stock."库存不足");
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
    function _group_info($goods_id,$product_id=0){
    	
    	/* 获取组件数据 */    	$res = array(    			'oProducts'     => array(), //货品    			'oGoods'     => array(), //商品信息    	);
    	
    	//import("dict.lib");
    	$data = $this->_mod_goods->get($goods_id);
    	
    	$res['oGoods'] = $data;
    	$wherep = !empty($product_id) ? " and product_id = '{$product_id}' " : '';
    	$res['oProducts'] = current($this->_mod_product->find(array(    			"conditions" => "goods_id = '{$data["goods_id"]}' $wherep",    	)));

    	return $res;
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
        if(!isset($post['fav_id']) && !isset($post['zhek_id'])){
        if(intval($post['quantity']) <= 0){
            $this->_error("update_error");
            return ;
        }
        }
       
        $where = " ident = '".$post['ident']."' AND user_id = '".$post['user_id']."'";
     
        if(isset($post['fav_id'])){
            $datas=array('favorable_id' => $post["fav_id"]);
          
        }elseif(isset($post['zhek_id'])){
            $datas=array('zhek_id' => $post["zhek_id"]);
        }else{
            $datas=array('quantity' => $post["quantity"]);
        }
       
        $res   = $this->_cart->edit($where, $datas);
    
        if(!$res)
        {
            $this->_error("update_error");
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
        
        $fbs = $this->_mod_product->find(array(
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
    	
    	$data = $this->_mod_goods->get($goods_id);
    
    	/* 基本款不存在 */
    	if(!$data){
    		$this->_error('diy_goods_not_exist');
    		return false;
    	}
    	 
    	/* 商品扩展分类 */
    	$mCgoods = & m('categorygoods');
    	$cgs = $mCgoods->find(array(
    	    'conditions' => " goods_id = '".$goods_id."'",
    	));
    	if ($cgs)
    	{
    	   $data['cat_id'] = implode(',', array_unique(i_array_column($cgs, 'cate_id')));
    	}
    	
    	/* 检查基本款是否上架销售 */
    	if(!$data['marketable']){
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
        
    	$conditions = "goods_id = '{$post['product_id']}' AND user_id = '{$post['user_id']}' AND type='{$this->_name}' AND size='{$post['size']}'";
    	
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