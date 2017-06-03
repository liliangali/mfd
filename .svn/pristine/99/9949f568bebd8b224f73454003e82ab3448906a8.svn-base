<?php

class FdiyGoods extends BaseGoods
{
    function __construct(){
        $param['_name']         = 'fdiy';
        parent::__construct($param);
    }
    
    /**
     * 格式化POST数据
     * 
     * @date 2015-10-15 下午1:57:18
     * @author Ruesin
     */
    function _format_post($post = array()){
        $res = parent::_format_post($post);
        $res['btype']    = $this->_name;
        //$res['params']   = isset($post['params']) ? json_decode( str_replace('\\"', '"', str_replace('\\\\"', '"', str_replace('\\\\\\"', '"', str_replace('|||', '|sin|', $post['params'])))) ,true) : array();
        $fcode = $post['fcode'];
        $fpic = $post['fpic'];
        $params   = isset($post['params']) ? json_decode( stripslashes_deep($post['params']) ,true) : array();  //由于入口文件做了字符转义  所以需要替换一下
// print_exit($params);
        $param = array();
        if ($params) 
        {   $itmes = array();
            foreach ($params as $key => $value) //=====  格式化参数于diy保持一致  =====
            {
                $pa = array();
               
                if ($value) 
                {
                    $value2_arr = explode("|", $value);
                    foreach ($value2_arr as $key1 => $value1) 
                    {
         
                            if (count($value2_arr) == 2) //=====   客户指定  ===== 
                            {
                                $str = $key.":".$value2_arr[0].",".$value2_arr[1];
                                
                            }
                            else 
                            {
                                $str = $key.":".$value2_arr[0];
                               
                            }
                            if ($value1)
                            {
                                $value1 = str_replace('"', '',$value1);
                                $itmes[] = intval($value1);
                            }
                            
                    }
                    
                }
                
                $param[$key] = $str;
              
            }
        }

        $res['params'] = $param;
        $res['items'] = $itmes;
        $res['image'] = $fpic;
        $res['cloth'] = $post['cid'];
        $res['size']   = isset($post['params']) ? json_decode( stripslashes_deep($post['size']) ,true) : array();

        return $res;
    }
    
    /**
     * 校验并格式化量体信息
     *
     * @date 2015-10-6 下午3:52:21
     * @author Ruesin
     */
    function _format_figure($sizes){
        
        $figure = array();
        if(!isset($sizes['figuretype'])){
            $this->_error('请选择量体方式');
            return false;
        }

        ## 2 5 6
        if($sizes['figuretype'] == 2 || $sizes['figuretype'] == 6){
            ##地区
            $region_mod = &m('region');
            $region = $region_mod->get(" region_id = '{$sizes['region']}' AND is_serve = 1");
            if(!$region){
                $this->_error('地区错误!');
                return false;
            }
            $sizes['region_name'] = $region['region_name'];
            ##手机号
            if(!preg_match("/^1[34578]\d{9}$/", $sizes['phone'])){
                $this->_error('手机号错误!');
                return false;
            }
            ##预约时间 简易匹配
            if(!preg_match("/^2\d{3}-(1[0-2])|(0[0-9])-([012][0-9])|(3[01])$/", $sizes['dateline'])){
                $this->_error('预约时间错误!');
                return false;
            }
        }
    
        if ($sizes['figuretype'] == 2){
    
            ##门店
            $msv = m('serve');
            $server = $msv->get("region_id = '{$sizes['region']}' AND idserve = '{$sizes['server_id']}'");
            if(!$server){
                $this->_error('服务门店错误!');
                return false;
            }

            $figure = array(
                    'type_name' => '去附近门店量体',
                    'region_id' => $sizes['region'], // 服务地区
                    'region_name' => $sizes['region_name'], // 服务地区
                    'realname'  => $sizes['realname'], // 真实姓名
                    'gender'    => $sizes['gender'] ? (in_array($sizes['gender'], array(10040,10041)) ? $sizes['gender'] : 10040 ): 10040, // 性别 10040男 10041女
                    'phone'     => $sizes['phone'], // 手机号码
                    'time'      => $sizes['dateline'], // 预约时间
                    'time_noon' => $sizes['timepart'] ? (in_array($sizes['timepart'], array('am','pm')) ? $sizes['timepart'] : 'am') : 'am', // 预约时间段
                    'server_id' => $sizes['server_id'], // 门店ID
                    
                    'customer_mobile'    => $sizes['phone'],
                    'customer_name'      => $sizes['realname'],
                    
            );
            
        } elseif ($sizes['figuretype'] == 5){
            ## 已有量体
            $mCf  = &m('customer_figure');
            $customer = $mCf->get(" figure_sn = '{$sizes['figureid']}' AND figure_state = 1"); //storeid = '{$this->_user_id}' AND customer_mobile = '{$_POST['data']['phone']}'
    
            if(!$customer){
                $this->_error('现有量体错误!');
                return false;
            }
            
            $figure = array(
                    'customer_mobile'      => $customer['customer_mobile'],
                    'customer_name'      => $customer['customer_name'],
                    'history_id' => $sizes['figureid'],  //历史量体数据ID
                    'type_name'  => '现有量体数据'
            );
            
        } elseif ($sizes['figuretype'] == 6){
    
            ##量体师  门店/地区
            $ltId = isset($sizes['figurerid']) ? intval($sizes['figurerid']) : 0;

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
            $server = $mServe->get("userid = '{$dId}' AND region_id = '{$sizes['region']}' AND shop_type IN (1,2)"); // AND virtual = '0'
            if(!$server){
                $this->_error('量体师所在门店错误!');
                return false;
            }
    
            $users = $mMember->get(" user_id = '{$ltId}'");
            if(!$users){
                $this->_error('该量体师会员信息缺失!');
                return false;
            }

            $figure = array(
                    'region_id' => $sizes['region'], // 服务地区
                    'region_name' => $sizes['region_name'], // 服务地区
                    'liangti'   => $sizes['figurerid'], // 指定量体师ID
                    'realname'  => $sizes['realname'], // 真实姓名
                    'phone'     => $sizes['phone'], // 联系电话
                    'time'      => $sizes['dateline'], // 预约时间
                    'time_noon' => $sizes['timepart'] ? (in_array($sizes['timepart'], array('am','pm')) ? $sizes['timepart'] : 'am') : 'am', // 预约时间段
                    'liangti_name' => $users['real_name'], // 指定量体师姓名
                    'server_id' => $server['idserve'], // 指定量体师所在门店
                    'type_name' => '指定量体师', // 量体方式类型
                    
                    'customer_mobile'    => $sizes['phone'],
                    'customer_name'      => $sizes['realname'],
                    
                    //$sizes['address']
            );
        }
        if (isset($figure['phone']) && $figure['phone'] != ''){
            $figure['phone'] = $figure['phone'].'';
        }
        $figure['type_id'] = $sizes['figuretype'];
        if(!$sizes['history']){
            $cki = $sizes;
            unset($cki['goods_id']);
            unset($cki['quantity']);
            $this->_cki = $cki;
        }
        
        return $figure;
    }
    
        
    function add($post = array())
    {
        $time = gmtime();
        $sizes = array();
        $dis_ident = $first = $ident = '';
        
        if(!$post['params'] || count($post['params']) <1){
            $this->_error("没有商品!");
            return false;
        }

        
        $dis_id = 0;
        if(count($post['params']) > 1 ){
            $dis_ident = $this->dis_ident($post['user_id']);
        }else{
            $ident = $this->gen_ident($post['user_id']);
            $dis_ident = $ident;
            $fCloth = $this->_cart->firstCloth();
            $cid = key($post['params']);
            if($fCloth[$cid]){
                $has = $this->_cart->get("user_id = '{$post['user_id']}' AND first = '{$cid}'");
                if(!$has){  //购物车中目前没有
                    $mOfl = &m('orderfirstlog');
                    $has  = $mOfl->find("user_id = '{$post['user_id']}'  AND cloth = '{$cid}' AND is_active = '1'");
                    if (!$has){  //之前订单也没有享受过
                        if($fCloth[$cid]['fabric'][$post['params'][$cid]['fabric']]){  //验证是首推面料
                            $first = $cid;
                        }
                    }
                }
            }
        }

        $data = array(
                'user_id'      => $post['user_id'],
                'dis_ident'    => $dis_ident ? $dis_ident : '',
                'suit_id'      => 0,
                'goods_id'     => 0,
                'type'         => $this->_name,
                'quantity'     => $post['quantity'],
                'items'     => implode(',', array_unique($post['items'])),
                'time'         => $time,
                'first'        => $first,
                'figure'       => '',
                'source_from'  => 'pc',
                'session_id' => SESS_ID,

            'dog_name' => isset($post['dog_name']) ? $post['dog_name'] : '',
            'dog_date' => isset($post['dog_date']) ? $post['dog_date'] : '',
            'dog_desc' => isset($post['dog_desc']) ? $post['dog_desc'] : '',
        );
      
            
            $rData = array(
                    'ident'  => $ident ? $ident : $this->gen_ident($post['user_id']),
                    'dis_ident' => $dis_ident,
                    'cloth'  => $post['cloth'],
                    'fabric' => '',
                    'lining' => '',
                    'button' => '',
                    'style'  => '',
                    'embs'   => '',
                    'syline' => '',
                    'params' => serialize($post['params']),
                    'size'   => 'diy',
                    'image'  => '/diy/images/cptu.jpg',
                    'style'  => $post['fpic'],      //包装图片
            );
            
            $aSave[] = array_merge($data,$rData);
//  var_dump($post);
  print_exit($aSave);
        $res = $this->_cart->add($aSave);
        
        if(!$res)
        {
            $this->_error("add_to_cart_error");
            return false;
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
    

    
    /**
     * 更新购物车数据
     *
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
            if($row['ident'] == $post['ident']){
                $idents[$row['ident']] = $row['ident'];
            }
        }
        
        if (empty($idents)){
            $this->_error('更新的商品不存在!');
            return false;
        }
        
           
        $where = " ident = '".$post['ident']."' AND user_id = '".$post['user_id']."' AND first = '' ";
        
        $res   = $this->_cart->edit($where, array('quantity' => $post["quantity"]));
    
        if(!$res){
            $this->_error("update_error");
        }
         
        return $res;
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
    * 选择购物车项
    * 
    * @date 2015-8-25 下午1:47:51
    * @author Ruesin
    */
   function choice($post){
   
       $carts = $this->_cart->find(array('conditions'=> " user_id = '{$post['user_id']}' AND ident = '{$post['ident']}'",'index_key'=>'ident'));
   
       if(!$carts){
           $this->_error('选择失败!');
           return false;
       }
   
       foreach ($carts as $key=>$val){
           $res[$key] = $key;
       }
   
       return $res;
   }
   
   
   
}