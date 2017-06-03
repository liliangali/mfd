<?php

/**
 * 样衣加入购物车
 *
 * @author Ruesin
 */

class CustomGoods extends BaseGoods
{
    function __construct()
    {
        $param['_name']         = 'custom';
        $param['_order_type']   = 'custom';
        parent::__construct($param);
    }

    /**
     * 格式化POST数据
     *
     * @author Ruesin
     */
    function _format_post($post = array()){
        $res = parent::_format_post($post);
        $res['type'] = $this->_name;
        $res['size'] = isset($post['size']) ? $post['size'] : 'diy';  //未按品类进行验证
        return $res;
    }
    
    /**
     * 加入购物车(目前是按照商品加入的，定制项都是默认的，不存在post定制项)
     *
     * @author Ruesin
     */
    function add($post)
    {
        $this->_user_id = $post['user_id'];
    	$_Bdata = $this->_base_info(intval($post["goods_id"]));
    	if(!$_Bdata){
    	    $this->_error('商品参数错误!'.$post["goods_id"].$this->get_error());
    		return false;
    	}

    	$_Gdata = $this->_group_info($post["goods_id"],$post["product_id"]);

    	 if(empty($_Gdata['oProducts'])){
    	 $this->_error('该商品没有匹配到货品信息!');
    	 return false;
    	 }
    	$_Cgoods = $this->_cart_info($post);
    	if($_Cgoods){
    	    $post['quantity'] += $_Cgoods['quantity'];
    	    $post['ident']    = $_Cgoods['ident'];
    	    return $this->update($post);
    	}

        $stock = $this->_check_stock($post);
        if($stock < 0 || empty($stock)){
            $this->_error("该商品已卖完！");
            return false;
        }
    	
//     	var_dump($_Bdata);exit();
    	//$fabricNum = $_Gdata['consumption']['fabric_m'];
        unset($_Gdata['oGoods']['intro']);
    	/* 生成入库数据 */
    	$cData = array(
    	        'user_id'    => $post['user_id'],
    	        'ident'      => $this->gen_ident($post['user_id']),
    		    'goods_id'   => $_Gdata['oProducts']["product_id"],
    	        'price'      => $_Gdata['price']["price"],
    	        'type'       => $this->_name,
    	        'cloth'      => $_Bdata["cat_id"],
    	        //'dis_ident'  => '',
//     	        'fabric'     => $_Gdata['oFabric']['CODE'],
    	        //...
    	        'params'      => serialize($_Gdata),
    	        //'items'      => json_encode($_Gdata['process'],JSON_UNESCAPED_UNICODE),
    	        'quantity'   => $post['quantity'],
    	        'size'       => $post['size'],
    	        'goods_name' => $_Bdata["name"],
    	        'image'      => $_Bdata["thumbnail_pic"],
    	        'time'       => time(),
    	        'session_id' => SESS_ID,
    	        'source_from'   => 'pc',
        		//'is_diy'     => 1,
        		//'type_name'    => $_Bdata["oClothing"]['name'],
    	);

    	$res = $this->_cart->add($cData);


        if(!$res){
    		$this->_error("add_to_cart_error");
    	}
    	return $res;
    }
    
    /**
     * 选择购物车项
     *
     * @date 2015-8-25 下午1:36:53
     * @author Ruesin
     */
    function choice($post = array()){
    
        $carts = $this->_cart->find(array('conditions'=> " user_id = '{$post['user_id']}' AND ident = '{$post['ident']}'" , 'index_key'=>'ident'));
    
        if(!$carts){
            $this->_error('选择失败!');
            return false;
        }
    
        foreach ($carts as $key=>$val){
            $res[$key] = $key;
        }
    
        return $res;
    }
    
    /**
     * 删除购物车项
     *
     * @author Ruesin
     */
    function drop($post){
    
        $droped_rows = $this->_cart->drop("ident = '".$post['ident']."' AND user_id = '".$post['user_id']."' ");
         
        if (!$droped_rows)
        {
            $this->_error("drop_error");
            return false;
        }
         
        return true;
    }
   
   function gen_ident($id = 0){
       do{
           $str='abcdefghigklmnopqrstuvwxyz0123456789';
           for($i=0;$i<5; $i++){
               $code .= $str[mt_rand(0, strlen($str)-1)];
           }
           $il = strlen($id);
           for ($i=$il;$i<10;$i++){
               $id = '0'.$id;
           }
           $ident =  $code.$id;
       } while($this->_cart->get("ident = '{$ident}'"));
       return $ident;
   }
}
