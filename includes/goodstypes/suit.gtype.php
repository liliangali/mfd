<?php

/**
 * 套装加入购物车
 *
 * @author Ruesin
 */

class SuitGoods extends BaseGoods
{
    function __construct()
    {
        $param['_name']         = 'suit';
        $param['_order_type']   = 'suit';
        parent::__construct($param);
    }

    /**
     * 格式化POST数据
     *
     * @author Ruesin
     */
    function _format_post($post = array()){
        $res = parent::_format_post($post);
        
        //=====  换面料  纽扣  =====
        if ($post['fabric_code']) 
        {
            $res['fabric_code'] = $post['fabric_code'];
        }
        if ($post['button_code'])
        {
            $res['button_code'] = $post['button_code'];
        }
        if($post['size'] == 'diy' || !$post['size']){
            $res['size'] = 'diy';
            $res['figure'] = $this->_format_figure($post);
            if(!$res['figure']) return false;
        }else{
            $sTemp = explode(',', $post['size']);
            foreach ((array)$sTemp as $row){
                $sArr = explode('|', $row);
                if(count($sArr) == 2){
                    $res['size'][$sArr[0]] = $sArr[1];
                }
            }
        }
        
        return $res;
    }
    
    /**
     * 校验并格式化量体信息
     * 
     * @date 2015-10-6 下午3:52:21
     * @author Ruesin
     */
    function _format_figure($post){
        $figure = array();
        if(!$post['figuretype']){
            $this->_error('请选择量体方式');
            return false;
        }
        
        if($post['figuretype'] == 2 || $post['figuretype'] == 6){
            ##地区
            $region_mod = &m('region');
            $region = $region_mod->get(" region_id = '{$post['region']}' AND is_serve = 1");
            if(!$region){
                $this->_error('地区错误!');
                return false;
            }
            $post['region_name'] = $region['region_name'];
            ##手机号
            if(!preg_match("/^1[34578]\d{9}$/", $post['phone'])){
                $this->_error('手机号错误!');
                return false;
            }
            ##预约时间 简易匹配
            if(!preg_match("/^2\d{3}-(1[0-2])|(0[0-9])-([012][0-9])|(3[01])$/", $post['dateline'])){
                $this->_error('预约时间错误!');
                return false;
            }
        }
        
        if ($post['figuretype'] == 2){
            
            ##门店
            $msv = m('serve');
            $server = $msv->get("region_id = '{$post['region']}' AND idserve = '{$post['serveid']}'");
            if(!$server){
                $this->_error('服务门店错误!');
                return false;
            }
            
            $figure = array(
                    'type_name' => '去附近门店量体',
                    'region_id' => $post['region'], // 服务地区
                    'region_name' => $post['region_name'], // 服务地区
                    'realname'  => $post['realname'], // 真实姓名
                    'gender'    => $post['gender'] ? (in_array($post['gender'], array(10040,10041)) ? $post['gender'] : 10040 ): 10040, // 性别 10040男 10041女
                    'phone'     => $post['phone'], // 手机号码
                    'time'      => $post['dateline'], // 预约时间
                    'time_noon' => $post['timepart'] ? (in_array($post['timepart'], array('am','pm')) ? $post['timepart'] : 'am') : 'am', // 预约时间段
                    'server_id' => $post['serveid'], // 门店ID
                    
                    'customer_mobile'    => $post['phone'],
                    'customer_name'      => $post['realname'],
                    
            );
        } elseif ($post['figuretype'] == 5){
            ##历史数据
            $mCf  = &m('customer_figure');
            $history = $mCf->get(" figure_sn = '{$post['figureid']}' AND figure_state = 1"); //storeid = '{$this->_user_id}' AND customer_mobile = '{$_POST['data']['phone']}'
            
            if(!$history){
                $this->_error('现有量体错误!');
                return false;
            }
            $figure = array(
                    //'phone' => 15936722475, //手机号
                    'customer_mobile'      => $history['customer_mobile'],
                    'customer_name'      => $history['customer_name'],
                    
                    'history_id' => $post['figureid'],  //历史量体数据ID
                    'type_name'  => '现有量体数据'
            );
        } elseif ($post['figuretype'] == 6){
            
            ##量体师  门店/地区
            $ltId = isset($post['figurerid']) ? intval($post['figurerid']) : 0;
            
            $mLiangti = &mr('figure_liangti');
            $liangti = $mLiangti->get("liangti_id = '{$ltId}' AND alone = '1' ");
            
            $mMember = &m('member');
            if (!$liangti){
                $dz = $mMember->get(" user_id = '{$ltId}' AND alone='1' AND figure_type = '1' AND serve_type = '2' ");
                $dId = $dz['user_id'];
            }else{
                $dId = $liangti['manager_id'];
            }
            
            if(!$dId){
                $this->_error('该量体师不可独立量体!');
                return false;
            }
            
            
            $mServe = &m('serve');
            $server = $mServe->get("userid = '{$dId}' AND region_id = '{$post['region']}' AND shop_type IN (1,2)"); // AND virtual = '0'
            if(!$server){
                $this->_error('量体师所在门店错误!');
                return false;
            }
            
            $mUser = &m('member');
            $users = $mUser->get(" user_id = '{$ltId}'");
            if(!$users){
                $this->_error('该量体师会员信息缺失!');
                return false;
            }
            
            $figure = array(
                    'region_id' => $post['region'], // 服务地区
                    'region_name' => $post['region_name'], // 服务地区
                    'liangti'   => $post['figurerid'], // 指定量体师ID
                    'realname'  => $post['realname'], // 真实姓名
                    'phone'     => $post['phone'], // 联系电话
                    'time'      => $post['dateline'], // 预约时间
                    'time_noon' => $post['timepart'] ? (in_array($post['timepart'], array('am','pm')) ? $post['timepart'] : 'am') : 'am', // 预约时间段
                    'liangti_name' => $users['real_name'], // 指定量体师姓名
                    'server_id' => $server['idserve'], // 指定量体师所在门店
                    'type_name' => '指定量体师', // 量体方式类型
                    
                    'customer_mobile'    => $post['phone'],
                    'customer_name'      => $post['realname'],
                    
                    //$post['address']
            );
        }
        if (isset($figure['phone']) && $figure['phone'] != ''){
            $figure['phone'] = $figure['phone'].'';
        }
        $figure['type_id'] = $post['figuretype'];
        if(!$post['history']){
            $cki = $post;
            unset($cki['goods_id']);
            unset($cki['quantity']);
            $this->_cki = $cki;
        }
        
        return $figure;
    }
    
    /**
     * 加入购物车
     *
     * @author Ruesin
     */
    function add($post)
    {
        $goods_id = intval($post["goods_id"]);
        $figures = $_SESSION['_cart']['figure'][$goods_id];
        
        if(!$post['size']){
            $this->_error('请选择尺码信息!');
            return false;
        }
        
    	$_Bdata = $this->_base_info($goods_id);
    	
    	if(!$_Bdata){
    	    $this->_error('商品参数错误!');
    		return false;
    	}
    	
    	$goods = $this->_base_goods($_Bdata);
    	 
    	if(!$goods){
    	    $this->_error('商品参数错误!');
    	    return false;
    	}
    	 
    	if($post['size'] != 'diy' && count($goods) != count($post['size'])){
    	    $this->_error('尺码参数错误!');
    	    return false;
    	}
       $fabric_code = $post['fabric_code'];
       $button_code	 = $post['button_code'];   	
    	//校验面料库存 Bgn
    	foreach ($goods as $gRow){
    	    
    	    if(empty($gRow['oFabric'])){
    	        $this->_error('该商品没有匹配到面料信息!');
    	        return false;
    	    }
    	    if (!$fabric_code) 
    	    {
    	        if (isset($stockData[$gRow['oFabric']['CODE']][$gRow['oGoods']['category']])){
    	            $stockData[$gRow['oFabric']['CODE']][$gRow['oGoods']['category']]['quantity'] += $post['quantity'];
    	        }else{
    	            $stockData[$gRow['oFabric']['CODE']][$gRow['oGoods']['category']] = array(
    	                'cloth'  => $gRow['oGoods']['category'],
    	                'fabric' => $gRow['oFabric']['CODE'],
    	                'quantity' => $post['quantity'],
    	            );
    	        }
    	        $fbs[$row['fabric']] = $row['fabric'];
    	    }
    	    else 
    	    {
    	        if (isset($stockData[$fabric_code][$gRow['oGoods']['category']])){
    	            $stockData[$fabric_code][$gRow['oGoods']['category']]['quantity'] += $post['quantity'];
    	        }else{
    	            $stockData[$fabric_code][$gRow['oGoods']['category']] = array(
    	                'cloth'  => $gRow['oGoods']['category'],
    	                'fabric' => $fabric_code,
    	                'quantity' => $post['quantity'],
    	            );
    	        }
//     	  print_exit($stockData);
    	    }
    	    
    	    
    	    
    	}
    	$fCart = $this->_cart->find("user_id = '{$post['user_id']}' AND " .db_create_in($fbs,'fabric'));
    	
    	foreach ((array)$fCart as $row){
    	    $stockData[$row['fabric']][$row['cloth']]['quantity'] += $row['quantity'];
    	}
    	if ($this->_check_stock($stockData) !== true) return false;
    	
    	// 校验面料库存 End
    	
    	
    	//=====  如果是换面料 检验库存Beg  liang.li  01.16  =====
    	
    	//=====  Em  =====
    	
    	$dis_ident = $this->dis_ident($post['user_id']);
        $pinlei = include_once ROOT_PATH.'/includes/arrayfiles/pinlei.php';
        
    	foreach ((array)$goods as $row){
    	    $category = $row['oGoods']['category'];
    	    if ($button_code) //=====   纽扣工艺  ===== 
    	    {
    	        $code = $pinlei[$category]['code'];
    	        $ecode = key($code);
    	        $ecode_id = $code[$ecode];
    	        $row['process'][$ecode_id] = $ecode_id."|sin|".$button_code;
    	    }
    	    if($post['size'] == 'diy'){
	            $row['size'] = 'diy';
    	    }else{
	            if(!$post['size'][$row['oGoods']['id']]){
	                $this->_error('商品参数错误!');
	                return false;
	            }
	            $row['size'] = $post['size'][$row['oGoods']['id']];
    	    }
    	    
    	    $f_cloth = $row['oGoods']["category"];
    	    if (count($goods) > 1) //=====  2件套  =====
    	    {
    	        $f_cloth = '0001';
    	    }
    	    $fabric = $row['oFabric']['CODE'];
    	    $is_change = 0;
    	    if ($fabric_code) 
    	    {
    	        $fabric = $fabric_code;
    	        $is_change = 1;
    	    }
    	    /* 生成入库数据 */
    	    $cData[] = array(
    	            'user_id'    => $post['user_id'],
    	            'ident'      => $this->gen_ident($post['user_id']),
    	            'goods_id'   => $row['oGoods']["id"],
    	            'price'      => $row['oGoods']['price'],
    	            'type'       => $this->_name,
    	            'cloth'      => $row['oGoods']["category"],
    	            'dis_ident'  => $dis_ident,
    	            'suit_id'    => $post['goods_id'],
    	            'fabric'     => $fabric,
    	            'params'     => json_encode($row['process'],JSON_UNESCAPED_UNICODE),
    	            //'items'      => json_encode($_Gdata['process'],JSON_UNESCAPED_UNICODE),
    	            'figure'     => json_encode($post['figure'],JSON_UNESCAPED_UNICODE),  //未做校验  默认正常
    	            'quantity'   => $post['quantity'],
    	            'size'       => $row['size'],
    	            'goods_name' => $row['oGoods']["name"],
    	            'image'      => $row['oGoods']["source_img"],
    	            'time'       => gmtime(),
    	            'session_id' => SESS_ID,
    	            'source_from'   => 'pc',
    	            'f_cloth' => $f_cloth,
    	             'is_change' => $is_change,
    	    );
    	}
    	$res = $this->_cart->add(addslashes_deep($cData));
    	if(!$res){
    		$this->_error("add_to_cart_error");
    		return false;
    	}
    	if(isset($this->_cki)){
    	    $cooked = isset($_COOKIE['suit_history']) ? json_decode(base64_decode($_COOKIE['suit_history']),1) : array();
    	    while (count($cooked)>=6){
    	        array_shift($cooked);
    	    }
    	    $cookie = array_merge($cooked,array($this->_cki));
    	    setcookie('suit_history',base64_encode(json_encode($cookie,JSON_UNESCAPED_UNICODE)),strtotime(date('Y-m-d',strtotime('+1 day'))));
    	}
    	return $res;
    }
    
    /**
     * 加入购物车 商品详细信息
     *
     * @author Ruesin
     */
    function _base_info($goods_id){
        
        $mSuit = &m('suitlist');
        
        $data = $mSuit->get(array(
                'conditions' => " suit_list.id = '{$goods_id}' AND suit_list.is_sale = 1 ", // AND suit_list.id = suit_relation.tz_id
                'join'       => 'has_one_suit_relation',
                'fields'     => 'suit_list.*,suit_relation.*,suit_relation.id relationid',
        ));
        
        if(!$data){
            $this->_error('diy_goods_not_exist');
            return false;
        }
    
        if(!$data['is_sale']){
            $this->_error('diy_goods_not_sale');
            return false;
        }
    
        return $data;
    }
    
    /**
     * 套装内的样衣数据
     *
     * @author Ruesin
     */
    function _base_goods($suit = array()){

        $data = $this->_mod_custom->find("id IN(".$suit['jbk_id'].")");
        
        if(!$data){
            $this->_error('商品参数错误!');
            return false;
        }
        
        $result = $this->_group_info_format($data);

        return $result;
        
    }
    
    /**
     * 购物车现有数据
     * 
     * @date 2015-8-20 下午2:10:31
     * @author Ruesin
     */
    function _cart_info($post = array()){
    
        $conditions = " suit_id = '{$post['goods_id']}' AND user_id = '{$post['user_id']}' AND type='{$this->_name}' ";
    
        if($post['size'] == 'diy'){
            $conditions .= " AND size='{$post['size']}' ";
        }elseif(is_array($post['size'])){
            $conditions .= " AND ( ";
            foreach (($post['size']) as $key=>$val){
                $conditions .= " (goods_id = '{$key}' AND size = '{$val}') OR ";
            }
            $conditions .= " ( 1 = 0) ) ";
        }else{
            return false;
        }

        if($post['ident']){
            $conditions .= " AND ident = '{$post['ident']}'";
        }
         
        $item = $this->_cart->get(array(
                'conditions' => $conditions,
        ));
                 
        return $item;
    }
    
    
    /**
     * 获取购物车商品基础数据(购物车列表)
     *
     * @author Ruesin
     */
    function _goods_info ($item = array())
    {
        return false;
        $cst = $this->_mod_custom->get($item['goods_id']);
        $res = array(
                'id' => $cst['id'],
                'name' => $cst['name'],
                'price' => $cst['price'],
                'weight' => $cst['weight'],
                'goods_img' => $cst['source_img'],
        );
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
     * 更新购物车数据
     *
     * @version 1.0.0 (2014-11-19)
     * @author Ruesin
     */
    function update($post){
        
        if(intval($post['quantity']) <= 0){
            $this->_error("update_error");
            return false;
        }
        
        $carts = $this->_cart->find(array('conditions'=> " user_id = '{$post['user_id']}'",'index_key'=>'ident')); // // dis_ident = '{$post['ident']}'
        if (empty($carts)) {
            $this->_error("update_error");
            return false;
        }
        
        $idents = $fbs = array();
        foreach ($carts as $row){
            if($row['dis_ident'] == $post['ident']){
                
                $idents[$row['ident']] = $row['ident'];
                $fbs[$row['fabric']]   = $row['fabric'];
        
                if($row['first'] != null || $row['first'] != 0 ){ //首单 只能买一件
                    $this->_error('首单只能买一件!');
                    return false;
                }
            }
        }
        if (empty($idents) || empty($fbs)){
            $this->_error('更新的商品不存在!');
            return false;
        }
        
        foreach ($carts as $row){
            if(isset($fbs[$row['fabric']])){
                if($row['dis_ident'] == $post['ident']){
                    $row['quantity'] = $post['quantity'];
                }
                
                if (isset($stk[$row['fabric']][$row['cloth']])){
                    $stk[$row['fabric']][$row['cloth']]['quantity'] += $row['quantity'];
                } else{
                    $stk[$row['fabric']][$row['cloth']]['cloth'] = $row['cloth'];
                    $stk[$row['fabric']][$row['cloth']]['fabric'] = $row['fabric'];
                    $stk[$row['fabric']][$row['cloth']]['quantity'] = $row['quantity'];
                }
            }
        }
        
        if ($this->_check_stock($stk) !== true) return false;
        
        $where = " dis_ident = '{$post['ident']}' AND user_id = '".$post['user_id']."'";
        
        $res   = $this->_cart->edit($where, array('quantity' => $post["quantity"]));
         
        if(!$res)
        {
            $this->_error("update_error");
        }
        return $res;
    }
    
    /**
     * 删除购物车项
     *
     * @author Ruesin
     */
    function drop($post){
        
        $droped_rows = $this->_cart->drop("dis_ident = '".$post['ident']."' AND user_id = '".$post['user_id']."' ");
         
        if (!$droped_rows)
        {
            $this->_error("drop_error");
            return false;
        }
         
        return true;
    }
    
    /**
     * 选择购物车项
     * 
     * @date 2015-8-25 下午1:36:53
     * @author Ruesin
     */
    function choice($post = array()){
        
        $carts = $this->_cart->find(array('conditions'=> " user_id = '{$post['user_id']}' AND dis_ident = '{$post['ident']}'" , 'index_key'=>'ident'));
        
        if(!$carts){
            $this->_error('选择失败!');
            return false;
        }
        
        foreach ($carts as $key=>$val){
            $res[$key] = $key;
        }
        
        return $res;
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
   
   function dis_ident($id = 0){
       do{
           $str='abcdefghigklmnopqrstuvwxyz0123456789';
           for($i=0;$i<8; $i++){
               $code .= $str[mt_rand(0, strlen($str)-1)];
           }
           $il = strlen($id);
           for ($i=$il;$i<10;$i++){
               $id = '0'.$id;
           }
           $ident =  $code.$id;
       } while($this->_cart->get(" dis_ident = '{$ident}'"));
       return $ident;
   }
   
}
