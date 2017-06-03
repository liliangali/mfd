<?php
/**
 * 购物车数据模型
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: cart.model.php 11375 2015-11-09 06:50:40Z liugx $
 * @copyright Copyright 2014 mfd.com
 * @package cart.model.php
 */
class CartModel extends BaseModel
{
    var $table  = 'cart';
    var $prikey = 'rec_id';
    var $_name  = 'cart';
    var $_mod_sample;
    var $_mod_customs;
    var $_diy_custom;
    var $_actDisCount = 1;
    var $_memDisCount = 1;
    var $_memType = 0;
    
    public $_types = array("diy", /* "normal","dis","try",'ticket','suit' */);
    
    /**
     * 购物车已选择数据
     *
     * @author Ruesin
     */
    function _cart_check($user_id = 0 , $check = array())
    {
    	
        if (!$check){
            return array('goods_list' => array(), 'amount' => 0);
        }
        
        $cart_items = $this->find(array(
                'conditions'    =>  " user_id = '{$user_id}' AND ".db_create_in($check,'ident'),
                'order'         => 'rec_id DESC',
        ));
        
        if (empty($cart_items)){
            return array('goods_list' => array(), 'amount' => 0);
        }
        
        return $this->_cart_format($cart_items,$user_id,$check);
        
    }
    
   /**
    * 获取购物车数据
    * 
    * @author Ruesin
    */
    function _cart_main($user_id = 0 , $check = array())
    {
        $cart_items = $this->find(array(
                'conditions'    =>  " user_id = '{$user_id}' " ,
                'order'         => 'rec_id DESC',
        ));

        if (empty($cart_items)){
            return array('goods_list' => array(), 'amount' => 0);
        }
        
        return $this->_cart_format($cart_items,$user_id,$check);
        
    }
    /**
     * 格式化购物车数据
     *
     * @author Ruesin
     */
    function _cart_format($cart_items = array(),$user_id = 0,$check = array()){
        
        $this->_diy_custom = &m('custom');
        $this->_mod_suitlist = &m("suitlist");

        $gtypes = $this->_new_types();
        
        $customs = $this->_customs();
        
        $carts = array();
        
        $this->_get_discount($user_id);  //等级折扣
        
        $gDatas = $this->_total_info($cart_items); ## 各类型商品整体数据
        
        $check_num = $figure_fee = $discount = $goods_amount = $order_amount = $final_amount = $amount = $goods_num = $weight = $has_diy = 0;
        
        $normal_cloth = array();  //正常款
        
        $firstCloth =  $this->firstCloth();  ## 参与首单的品类面料
        
        foreach ((array)$cart_items as $item)
        {
            if(!$item['ident']) continue;
            
            if ($this->_format_cloth($item['cloth'])){
                $item['cloth'] = $this->_format_cloth($item['cloth']);
            }
            
            $item['params'] = json_decode($item['params'],true);
            $item['items']  = json_decode($item['items'],true);
            $item['embs']   = json_decode($item['embs'],true); 
            
            $item['has_embs'] = $item['embs'] ? 'yes' : 'no';
            
            $item['check']  = $check[$item['ident']] ? 'yes' : '';
            
            $item['goods'] = $this->_normal_price($item,$gDatas,$customs);

            $this->_base_info($item,$gDatas,$firstCloth);

            if($item['type'] == 'suit'){
                
                $iTems = $iList = array();
                
                $iList = $item;
                
                $iList['subtotal'] = $item['goods']['price'] * $item['quantity'];
                
                if(!$carts[$item['dis_ident']]){
                    
                    $iTems['type']     = $item['type'];
                    $iTems['check']    = $item['check'];
                    $iTems['first']    = $item['first'];
                    $iTems['quantity'] = $item['quantity'];
                    $iTems['ident']    = $item['dis_ident'];
                    $iTems['activity']  = $item['activity'];
                    
                    $iTems['goods']              = $gDatas['suit'][$item['suit_id']];
                    $iTems['goods']['name']      = $gDatas['suit'][$item['suit_id']]['suit_name'];
                    
                    if($iTems['goods']['is_special'] == 0){
                        $has_special = 'yes';
                    }
                    
                    $iTems['goods']['nBasePrice'] = $iTems['goods']['cost_price'];
                    $iTems['goods']['nPrice']     = $this->_format_price_int($iTems['goods']['price'] * $this->_memDisCount);
                    $iTems['goods']['nMarkprice'] = $this->_format_price_int($iTems['goods']['price']);
                    
                    if($iTems['activity']){
                        $iTems['goods']['basePrice'] = $item['goods']['basePrice'];
                        $iTems['goods']['price']     = $this->_format_price_int($item['goods']['price']);
                        $iTems['goods']['markprice'] = $this->_format_price_int($item['goods']['markprice']);
                    }else{
                        $iTems['goods']['basePrice'] = $iTems['goods']['nBasePrice'];
                        $iTems['goods']['price']     = $iTems['goods']['nPrice'];
                        $iTems['goods']['markprice'] = $iTems['goods']['nMarkprice'];
                    }
                    
                    
                    $iTems['goods']['image']     = $gDatas['suit'][$item['suit_id']]['image'];
                    
                    $iTems['subtotal']   = $iTems['goods']['price'] * $iTems['quantity'];
                    $iTems['subtotal_m'] = $iTems['goods']['markprice'] * $item['quantity'];
                    
                    $goods_amount   += $iTems['subtotal'];
                    $goods_amount_m += $iTems['subtotal_m'];
                    
                    if($item['check'] == 'yes'){
                        $check_amount += $iTems['subtotal'];
                        $check_amount_m += $iTems['subtotal_m'];
                    }
                    
                    $carts[$item['dis_ident']] = $iTems;
                }
                
                if($item['check'] == 'yes'){
                    if(!$item['activity'] && $carts[$item['dis_ident']]['goods']['is_special']){
                        $normal_cloth[$item['cloth']] = $item['cloth'];
                    }
                }
                
                 
                $goods_num    += $item['quantity'];
                 
                if($item['has_embs'] == 'no'){
                    $carts[$item['dis_ident']]['has_embs'] = 'no';
                }
                if($item['size'] == 'diy'){
                    $carts[$item['dis_ident']]['sizes'] = 'diy';
                }else{
                    $carts[$item['dis_ident']]['sizes'][$item['cloth']]['name'] = $customs[$item['cloth']]['cate_name'];
                    $carts[$item['dis_ident']]['sizes'][$item['cloth']]['size'] = $item['size'];
                }
                
                $carts[$item['dis_ident']]['list'][$item['ident']] = $iList;
            
            }else{

                    if($item['size'] == 'diy'){
                        $item['sizes'] = 'diy';
                    }else{
                        $item['sizes'][$item['cloth']]['size'] = $item['size'];
                        $item['sizes'][$item['cloth']]['name'] = $customs[$item['cloth']]['cate_name'];
                    }
                    
                    $item['subtotal_m']   = $item['goods']['markprice'] * $item['quantity'];
                    $item['subtotal']     = $item['goods']['price'] * $item['quantity'];
                    
                    $goods_amount   += $item['subtotal'];
                    $goods_amount_m += $item['subtotal_m'];
                    
                    if($item['check'] == 'yes'){
                        $check_amount += $item['subtotal'];
                        $check_amount_m += $item['subtotal_m'];
                        
                        if(!$item['activity']){
                            $normal_cloth[$item['cloth']] = $item['cloth'];
                        }
                    }
                    
                    $goods_num    += $item['quantity'];
                    
                    $carts[$item['ident']] = $item;

            }
            if($item['check'] == 'yes'){
                if($item['activity']){
                    $hasActivity = 'yes';
                }
                $check_num += $item['quantity'];
                $check_cloth[$item['cloth']] = $item['cloth'];
            }
             //if($item['check'] == 'yes'){
                if($item['first']){
                    $hasFrist = 'yes';
                }
                if($item['size'] == 'diy'){
                    $sizehasdiy = 'yes';
                }
                
                
             //}
        }
        
        $amount = $goods_amount;
    	$order_amount = $final_amount = $goods_amount + $discount;
    	
    	$amount_m = $goods_amount_m;
    	$order_amount_m = $final_amount_m = $goods_amount_m + $discount;

    	$return = array(
    	        "object" => $carts,                   //商品信息
    	        
    	        "goods_amount_m"   => $goods_amount_m,
    	        "order_amount_m"   => $order_amount_m,
    	        "final_amount_m"   => $final_amount_m,
    	        
    	        "goods_amount"   => $goods_amount,    //商品价格
    	        "order_amount"   => $order_amount,    //订单最终价格
    	        "final_amount"   => $final_amount,    //订单最终价格
    	        
    	        //'point_g'        => $final_amount*1,
    	        'discount'       => $discount,
    	        'goods_num'      => $goods_num,
    	        'check_num'      => $check_num,
    	        //'weight'         => $weight,
    	        'sizehasdiy'     => $sizehasdiy,
    	        'has_first'      => $hasFrist,
    	        'has_activity'   => $hasActivity,
    	        'has_special'    => $has_special,
    	        
    	        'check_amount'   => $check_amount,
    	        'check_amount_m' => $check_amount_m,
    	        
    	        'memDiscount'    => $this->_memDisCount,
    	        'actDiscount'    => $this->_actDisCount,
    	        'memberType'    => $this->_memType,
    	        
    	        'normal_cloth'   => $normal_cloth,
    	        'check_cloth'    => $check_cloth,
    	);
    	
        return $return;
    }
    
    /**
     * 统一查出商品数据
     *
     * @author Ruesin
     */
    function _total_info($carts = array()){
        foreach ($carts as $row){
            switch ($row['type']){
            	case 'diy':
                    
        	    break;
        	    
                case 'suit':
                    $suitIds[$row['suit_id']] = $row['suit_id'];
                    $disIds[$row['goods_id']] = $row['goods_id'];
                break;
                
            }
        }
        
        return array(
                'diy'  => $this->_all_diy_info($carts),  //diy 面料 + 工艺价格
                'dis'  => $this->_diy_custom->find(db_create_in($disIds,'id')), //样衣
                'suit' => $this->_mod_suitlist->find(db_create_in($suitIds, "id")), //套装
        );
    }
    
    protected function _normal_price(&$item,$gDatas = array() , $custom = array()) {
        
        $customs = $custom[$item['cloth']];
        
        if($item['type'] == 'diy'){
        
            $item['process_p'] = $item['crafts_p'] = $item['fabric_p'] = $item['lining_p'] = 0;
        
            $item['fabric_p'] = $gDatas['diy']['f'][$item['fabric']]['price']; //=====  工艺价格  =====
        
            foreach ((array)$item['params'] as $key=>$row){
                if(in_array($key, self::$_lining_parents)){
                    $item['lining_p'] = $gDatas['diy']['c'][$item['params'][$key]]['price'];   //里料
                }elseif (in_array($key, self::$_process_parents)){
                    $item['crafts_p'] += $gDatas['diy']['c'][$item['params'][$key]]['price'];  //工艺
                }
            }
        
            $item['process_p'] = $customs['process_fee'];   //加工费
            $mPrice = (($customs['fabric_m'] * $item['fabric_p']) + ($customs['lining_m'] * $item['lining_p']) + $item['crafts_p']) * $this->coefficient + $item['process_p'] * $this->coeff;  //(面料 + 里料 + 工艺) * 系数1 + 加工费 * 系数2
            $res = array(
                    'id' => 0,
                    'name'      => $customs['cate_name'],
                    'weight' => 0,
                    'image' => $item['image'],
                    'basePrice' => ($customs['fabric_m'] * $item['fabric_p']) + ($customs['lining_m'] * $item['lining_p']) + $item['crafts_p'] + $item['process_p'],  //面料 + 里料 + 工艺 + 加工费
                    'markprice' => $this->_format_price_int($mPrice),
                    'price'     => $this->_format_price_int($mPrice * $this->_memDisCount),
                    
            );
        
            //$prc = $this->_calc_price($res['basePrice'],$this->_memDisCount);
            //$res['markprice'] = $prc['markprice'];
            //$res['price']     = $prc['price'];
            
        }elseif($item['type'] == 'suit'){
        
            $cst = $gDatas['dis'][$item['goods_id']];

            $res = array(
                    'id'        => $cst['id'],
                    'name'      => $customs['cate_name'],//$cst['name'],
                    'weight'    => $cst['weight'], //无
                    'image'     => $cst['small_img'],
                    'basePrice' => $cst['base_price'], // *5.2 = $cst['price'],
                    'markprice' => $cst['price'],
                    'price'     => $this->_format_price_int($cst['price']*$this->_memDisCount),
            );
        }
        
        $res['nBasePrice'] = $res['basePrice'];
        $res['nMarkprice'] = $res['markprice'];
        $res['nPrice']     = $res['price'];
        
        return $res;
        
    }
    
    /**
     * 获取购物车商品基础数据
     * 
     * @author Ruesin
     */
    function _base_info ( &$item , $gDatas = array() , $firstCloth = array())
    {
        if($item['type'] == 'diy'){
            
            //首单优先级最高
            //活动价格优于销售价  活动价必须活动开启  
            if($item['first'] && $firstCloth[$item['first']]){
                    if($firstCloth[$item['first']]['price']){
                        
                        $item['goods']['basePrice'] = $firstCloth[$item['first']]['price'];
                        $item['goods']['markprice'] = $firstCloth[$item['first']]['price'];
                        $item['goods']['price']     = $firstCloth[$item['first']]['price'];
                        $item['activity'] = 3;
                        
                    }elseif($firstCloth[$item['first']]['fabric'][$item['fabric']] && $firstCloth[$item['first']]['fabric'][$item['fabric']]['price'] > 0){
                    
                        $item['goods']['basePrice'] = $firstCloth[$item['first']]['fabric'][$item['fabric']]['price'];
                        $item['goods']['markprice'] = $firstCloth[$item['first']]['fabric'][$item['fabric']]['price'];
                        $item['goods']['price']     = $firstCloth[$item['first']]['fabric'][$item['fabric']]['price'];
                        
                        $item['activity'] = 3;
                    }
                    
            }elseif($gDatas['diy']['f'][$item['fabric']]['activity'] && $gDatas['diy']['f'][$item['fabric']]['activity_price_'.$item['cloth']] > 0){
                
                $item['goods']['basePrice'] = $gDatas['diy']['f'][$item['fabric']]['activity_price_'.$item['cloth']];
                $item['goods']['markprice'] = $gDatas['diy']['f'][$item['fabric']]['activity_price_'.$item['cloth']];
                $item['goods']['price']     = $gDatas['diy']['f'][$item['fabric']]['activity_price_'.$item['cloth']];
                
                $item['activity'] = 2;
            }
            elseif ($gDatas['diy']['f'][$item['fabric']]['sales__price'] && $gDatas['diy']['f'][$item['fabric']]['sales__price_'.$item['cloth']] > 0) //=====  liang.li 单独面料价格  =====
            {
                
                $item['goods']['basePrice'] = $this->_format_price_int($gDatas['diy']['f'][$item['fabric']]['sales__price_'.$item['cloth']]);
                $item['goods']['markprice'] = $this->_format_price_int($gDatas['diy']['f'][$item['fabric']]['sales__price_'.$item['cloth']]);
                $item['goods']['price']     = $this->_format_price_int($gDatas['diy']['f'][$item['fabric']]['sales__price_'.$item['cloth']]*$this->_memDisCount);//=====  liang.li 固定面料价格乘以会员系数  =====
                $item['sales'] = 1; //=====  这个价格不乘以0.9  只需要乘以会员等级  =====
                $item['activity'] = 900;
            }
            
        }elseif($item['type'] == 'suit'){
            
            if($gDatas['suit'][$item['suit_id']]['is_promotion'] == '1' && $gDatas['suit'][$item['suit_id']]['promotion_price'] > 0){
        
                $item['goods']['basePrice'] = $gDatas['suit'][$item['suit_id']]['promotion_price'];
                $item['goods']['price']     = $this->_format_price_int($gDatas['suit'][$item['suit_id']]['promotion_price']);
                $item['goods']['markprice'] = $this->_format_price_int($gDatas['suit'][$item['suit_id']]['promotion_price']);
        
                $item['activity'] = 2;
            }
            
            if($item['first'] && $firstCloth[$item['first']]){
                if($firstCloth[$item['first']]['price']){
                    
                    $item['goods']['basePrice'] = $firstCloth[$item['first']]['price'];
                    $item['goods']['markprice'] = $firstCloth[$item['first']]['price'];
                    $item['goods']['price']     = $firstCloth[$item['first']]['price'];
                    $item['activity'] = 3;
                    
                }elseif($firstCloth[$item['first']]['fabric'][$item['fabric']] && $firstCloth[$item['first']]['fabric'][$item['fabric']]['price'] > 0){
                
                    $item['goods']['basePrice'] = $firstCloth[$item['first']]['fabric'][$item['fabric']]['price'];
                    $item['goods']['markprice'] = $firstCloth[$item['first']]['fabric'][$item['fabric']]['price'];
                    $item['goods']['price']     = $firstCloth[$item['first']]['fabric'][$item['fabric']]['price'];
                    
                    $item['activity'] = 3;
                }
                
            }
            
        }
        
        if($this->_memType == 1 && $item['activity'] && $item['activity'] != 900){
            $this->_activity_price($item);
        }
    }
    
    /**
     * 创业者活动款打九折
     * 
     * @date 2015-8-6 下午4:08:36
     * @author Ruesin
     */
    function _activity_price(&$item){
        //$item['goods']['basePrice'] = $this->_format_price_int($item['goods']['basePrice'] * $this->_actDisCount);
        //$item['goods']['markprice'] = $this->_format_price_int($item['goods']['markprice'] * $this->_actDisCount);
        $item['goods']['price']     = $this->_format_price_int($item['goods']['price'] * $this->_actDisCount);
    }
    
    
    function _format_price_int($price = 0.00){
        return sprintf('%.2f',intval($price));
    }
    
    /**
     * 价格计算公式
     * [2015-08-31 14:00:17] 价格公式已改 将废弃.
     * @deprecate 
     * @author Ruesin
     */
    function _calc_price($price = 0 , $disCount = 1){
        $res['basePrice'] = $price;
        $res['markprice'] = $this->_format_price_int($price * $this->coefficient);   //销售价
        $res['price']     = $this->_format_price_int($price * $this->coefficient * $disCount);  //折扣价
        return $res;
    }

    /**
     * 获取会员等级折扣
     *
     * @author Ruesin
     */
    function _get_discount($user_id = 0){
        
        $mMem = &m('member');
        
        $mem = $mMem->get($user_id);
        
        //if ($mem['serve_type'] == '1'){
	    if ($mem['member_lv_id'] == '1'){
	    //普通会员
		}else{
            //创业者
            $member_lv_mod =& m('memberlv');
            $member_lv_id = $mem['member_lv_id'] ? $mem['member_lv_id'] : 1;
            $dis = $member_lv_mod->get_leve_dis_count($member_lv_id);
            
            //$this->_actDisCount = 0.9;  //5）创业者和消费者购买活动款（限量款）时均按照售价购买，创业者不再享有九折优惠。(广信、李亮、小白)
            $this->_actDisCount = 1;
            
            $dic = $dis > 0 ? $dis : 10;
            $this->_memDisCount = ($dic/10);
            
            $this->_memType = 1;
        }
        
        return $this->_memDisCount;
    }
    
    /**
     * 获取当前购物车项下的各项价格
     * 
     * @author Ruesin
     */
    function _all_diy_info($cart_items){
        
        $parents = array_merge(self::$_lining_parents,self::$_process_parents);  //工艺父ID + 里料父ID  //影响价格 //里料设计不确定....
        
        foreach ($cart_items as $item){
            if($item['type'] == 'diy'){
                $fbs[] = $item['fabric'];
                $prm = json_decode($item['params'],true);
                foreach ($parents as $val){
                    if($prm[$val]){
                        $ps[] = $prm[$val];
                    }
                }
            }
        }
        
        if($fbs){
            //$fbs = array_column($cart_items, 'fabric');
            $mFP = &m('fabricprice');
            $FP = $mFP->find(array(
                    'fields' => 'FABRICCODE code,RMBPRICE price',
                    'conditions' => " 1 = 1 AND AREAID = '20151' AND ".db_create_in($fbs,'FABRICCODE'),
                    'index_key' => 'code',
            ));
            
            $mod_fabric   = &m('fabric');            
            $fabrics = $mod_fabric->find(array('conditions'=>db_create_in($fbs,'CODE') . " AND is_sale = 1",'index_key'=>'CODE'));
            
            foreach ($FP as &$row){
                if($fabrics[$row['code']]['activity'] == '1' && ($fabrics[$row['code']]['activity_price_0003']>0 || $fabrics[$row['code']]['activity_price_0004']>0 || $fabrics[$row['code']]['activity_price_0005']>0 || $fabrics[$row['code']]['activity_price_0006']>0 || $fabrics[$row['code']]['activity_price_0007']>0)){
                    
                    $row['activity'] = $fabrics[$row['code']]['activity'];
                    $row['activity_price_0003'] = $fabrics[$row['code']]['activity_price_0003'];
                    $row['activity_price_0004'] = $fabrics[$row['code']]['activity_price_0004'];
                    $row['activity_price_0005'] = $fabrics[$row['code']]['activity_price_0005'];
                    $row['activity_price_0006'] = $fabrics[$row['code']]['activity_price_0006'];
                    $row['activity_price_0007'] = $fabrics[$row['code']]['activity_price_0007'];
                }
                elseif ($fabrics[$row['code']]['sales__price_0003'] > 0 || $fabrics[$row['code']]['sales__price_0004'] > 0 || $fabrics[$row['code']]['sales__price_0004'] > 0 || $fabrics[$row['code']]['sales__price_0005'] > 0 || $fabrics[$row['code']]['sales__price_0006'] > 0 || $fabrics[$row['code']]['sales__price_0007'] > 0) //=====  单独销售的面料  =====
                {
                    $row['sales__price'] = 1;
                    $row['sales__price_0003'] = $fabrics[$row['code']]['sales__price_0003'];
                    $row['sales__price_0004'] = $fabrics[$row['code']]['sales__price_0004'];
                    $row['sales__price_0005'] = $fabrics[$row['code']]['sales__price_0005'];
                    $row['sales__price_0006'] = $fabrics[$row['code']]['sales__price_0006'];
                    $row['sales__price_0007'] = $fabrics[$row['code']]['sales__price_0007'];
                }
            }
        }
        
        if($ps){
            $mDt = &m('dict');
            $l = $mDt->find(array(
                    'conditions' => " 1 = 1 AND ".db_create_in($ps,'id'),
            ));
        }
        
        return array(
        	   'f' => $FP,
               'c' => $l
        );
        
    }
    
    /**
     * 获取所有品类的刺绣信息
     * 
     * @author Ruesin
     */
    function _format_embs($type = 'code'){
        $mEp  = &m('dict_embs_parent');
        $p = $mEp->find(array(
        	   'conditions' => " is_show = '1' ",
               'order'      => ' rank ASC',
        ));
        
        foreach ((array)$p as $row){
            $pIds[$row['id']] = $row['id'];
            $res[$row['cCode']][$row['id']] = $row;
            $resId[$row['cId']][$row['id']] = $row;
        }
        if($pIds){
            $mDict = &m('dict');
            $e = $mDict->find(array(
                    'conditions' => " is_display = '1' AND ".db_create_in($pIds,'parentid'),
            ));
        }
        
        foreach ((array)$e as $row){

            if($p[$row['parentid']]){
                $row['image'] = "http://img.diy.mfd.cn/process/{$row['parentid']}/{$row['id']}_S.png";
                //$row['parentid'];
                $res[$p[$row['parentid']]['cCode']][$row['parentid']]['list'][$row['id']] = $row;
                $resId[$p[$row['parentid']]['cId']][$row['parentid']]['list'][$row['id']] = $row;
            }
        }
        
        if($type == 'id'){
            return $resId;
        }
        return $res;
    }
    
    
    function _format_cloth($id){
        $arr = array(
    	        1 => '0001',
                2 => '0002',
                3 => '0003',
                2000 => '0004',
                4000 => '0005',
                3000 => '0006',
                6000 => '0007',
                5000 => '0008',
                4 => '0009',
                90000 => '0010',
                95000 => '0011',
                98000 => '0012',
                5 => '0013',
                6 => '0014',
                7 => '0015',
                11000 => '0016',
                15000 => '0017',
                18000 => '0018',
                19 => '0019',
                100000 => '0020',
                103000 => '0021',
                //110000 //小童西服
                //113000  //小童西裤
        );
        if (isset($arr[$id]))
        return $arr[$id];
        
        return false;
    }
    
    function _format_fabric($user_id){
        $cart = $this->find(array(
                'conditions' => "user_id = '$user_id'",
                'fields'     => 'fabric',
                'index_key'  => 'fabric',
        ));
        foreach ($cart as $row){
            $fbs[$row['fabric']] = $row['fabric'];
        }
        $mF = &m('fabric');
        $fabrics = $mF->find(array('conditions'=>db_create_in($fbs,'CODE'),'fields'     => 'CODE,tname' ,'index_key'=>'CODE'));
        return $fabrics;
    }
    

    /**
     * 获取所有类型的操作类
     * 
     * @version 1.0.0 (Jan 8, 2015)
     * @author Ruesin
     */
    function _new_types(){
        foreach ($this->_types as $val){
            $res[$val] = &gt($val);
        }
        return $res;
    }
    
    /**
     *    获取商品数量
     *
     *    @return    void
     */
    function get_kinds( $user_id = 0 , $city_id = 0)
    {
        if($user_id == 0 || $city_id == 0 ) return 0;
        
        $num = 0;
        $carts = $this->find(" user_id = '{$user_id}' AND city_id = '{$city_id}'");
        foreach ($carts as $row){
            $in[] = $row['goods_id'];
            $quan[$row['goods_id']][$row['quantity']] = $row['quantity']; 
        }
        $goods = $this->db->getall("select * from ".DB_PREFIX."goods where if_show = 1 AND ".db_create_in($in,'goods_id'));
        foreach ((array)$goods as $row){
            foreach((array)$quan[$row['goods_id']] as $gr){
                $num += $gr;
            }
        }
        return $num;
    }
        
    /**
     * 校验面料库存(beta)
     *
     * @author Ruesin
     */
    function _check_stock($data = array()){
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

        $customs = $this->_customs();
        foreach ($data as $key => $row){
            $fbs[$key] = $key;
            foreach ($row as $k=>$v){
                $stc[$key] += $v['quantity']*($customs[$k]['fabric_m']);
            }
        }
        
        $mF = &m('fabric');
        //$fabrics = $mF->find(array('conditions'=>db_create_in($fbs,'CODE') . " AND is_sale = '1' ",'index_key'=>'CODE'));
        $fabrics = $mF->find(array('conditions'=>db_create_in($fbs,'CODE') ,'index_key'=>'CODE'));
    
        foreach ($stc as $key=>$val){
            if($fabrics[$key]['STOCK'] < $val){
                return $key;
            }
        }
    
        return true;
    
    }
    
    function _customs( $cloth = ''){
        imports("diys.lib");
        $oLogs = new Diys();
        $res = $oLogs->_customs();
        return ($cloth && $res[$cloth]) ? $res[$cloth] : $res;
    }
    
    /**
     * 获取首单面料及价格
     *
     * @author Ruesin
     */
    public function firstCloth(){
        
        $cates = array('8001','8030');
        $fCate = array(
                '8001' => array(
                        'list' => array(
                                '0003' => array(
                                        'cloth' => '0003',
                                        'price' => 999,
                                ),
                                '0004' => array(
                                        'cloth' => '0004',
                                        'price' => 299,
                                )
                        )
                ),
                '8030' => array(
                        'list' => array(
                                '0006' => array(
                                        'cloth' => '0006',
                                        'price' => 199,
                                )
                        )
                )
        );
        $mF = &m('fabric');
        $fabrics = $mF->find(array('conditions'=>db_create_in($cates,'CATEGORYID')."  AND is_first = '1' ",'index_key'=>'CODE'));
        
        foreach ($fabrics as $row){
            foreach ($fCate[$row['CATEGORYID']]['list'] as $k=>$v){
                if(isset($row['activity_price_'.$k]) && $row['activity_price_'.$k] > 0) {
                    $res[$k]['fabric'][$row['CODE']] = $row;
                    $res[$k]['fabric'][$row['CODE']]['price'] = $row['activity_price_'.$k];
                    //$res[$k]['price']                = $v['price'];
                }
            }
        }
        $res['0016']['price'] = 199;
        return $res;
    }
    
    public function _getCartCloths($user_id){
        if (!isset($_SESSION['_cart']['check']) || empty($_SESSION['_cart']['check'])){
            return array();
        }
        $cart = $this->find(array(
        	'conditions' => "user_id = $user_id AND ".db_create_in($_SESSION['_cart']['check'],'ident'),
                'index_key' => 'cloth',
        ));
        foreach ((array)$cart as $row){
            $res[$row['cloth']] = $row['cloth'];
        }
        return $res;
    }

    public $coefficient = 5; // 溢价系数 2015-10-08 10.71版本
    public $coeff       = 5; // 溢价系数 2015-10-08 10.71版本
    public static $_lining_parents = array(313,6291,103136);   //里料父ID  //不确定....
    public static $_process_parents = array(1230,431,2224,4992); //工艺父ID

    
    
}