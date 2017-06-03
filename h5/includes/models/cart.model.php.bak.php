<?php
/**
 * 购物车数据模型
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: cart.model.php 6993 2015-07-28 08:50:55Z liugx $
 * @copyright Copyright 2014 mfd.com
 * @package cart.model.php
 */
class CartModel extends BaseModel
{
    var $table  = 'cart';
    var $prikey = 'rec_id';
    var $_name  = 'cart';
    var $_mod_sample;
    var $_mod_shirt;
    var $_mod_customs;
    var $_diy_custom;
    
    public $_types = array("diy", "normal","dis","try",'ticket'/* ,'suit' */);
    var $_relation = array(
        'belongs_to_store'  => array(
            'type'      =>  BELONGS_TO,
            'model'     =>  'store',
            'reverse'   =>  'has_cart',
        ),
        'belongs_to_goodsspec'  => array(
            'type'      =>  BELONGS_TO,
            'model'     =>  'goodsspec',
            'foreign_key' => 'spec_id',
            'reverse'   =>  'has_cart_items',
        ),
    );
    
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
        $this->_mod_shirt = &m('shirt');
        $this->_mod_suitlist = &m("suitlist");
        
        $gtypes = $this->_new_types();
        
        $carts = array();
        
        //===== 价格
        //防止在循环中sql查询，事先把各项面料和里料价格查询出来 // 面料 + 里料、工艺
        $this->_get_discount($user_id);
        
        $this->_format_all_price($cart_items);
        //$this->_get_order_first($user_id);
        $gDatas = $this->_total_info($cart_items);
        
        
        $figure_fee = $discount = $goods_amount = $order_amount = $final_amount = $amount = $goods_num = $weight = $has_diy = 0;
        
        //产品上下架过滤暂时未做
        foreach ((array)$cart_items as $item)
        {
            if(!$item['ident']) continue;
            //品类存的数据都不一致，在其他代码做更小更改的前提下在这里format一下
            if ($this->_format_cloth($item['cloth'])){
                $item['cloth'] = $this->_format_cloth($item['cloth']);
            }
            
            if($check[$item['ident']]){
                $item['check'] = 'yes';
            }
            
            $item['params'] = json_decode($item['params'],true);
            
            //刺绣
            $item['embs']   = json_decode($item['embs'],true); 
            
            $item['has_embs'] = $item['embs'] ? 'yes' : 'no';
            
            $item['items']  = json_decode($item['items'],true);

            
            //因为其他类型是在app 先不做分类型处理  稍后优化
            //商品信息
            $item['goods'] = $this->_base_info($item,$gDatas);
            
            if($item['type'] == 'suit'){
                $iTems = array();
                $iList = $item;
                
                $iList['subtotal'] = $item['goods']['price'] * $item['quantity'];
                
                if(!$carts[$item['dis_ident']]){
                    
                    //$prc = $this->_calc_price($gDatas['suit'][$item['suit_id']]['cost_price'],$this->_memDisCount);
                    
                    $iTems['goods']              = $gDatas['suit'][$item['suit_id']];
                    $iTems['goods']['name']      = $gDatas['suit'][$item['suit_id']]['suit_name'];
                    
                    /* $iTems['goods']['basePrice'] = $prc['basePrice'];
                    $iTems['goods']['price']     = $prc['price']; */
                    
                    //if($item['first'] && $item['first'] == '0016'){
                    if($item['first']){
                        $iTems['goods']['basePrice'] = $this->_format_price_int($item['goods']['basePrice']);
                        $iTems['goods']['price']     = $this->_format_price_int($item['goods']['price']);
                        $iTems['goods']['markprice'] = $this->_format_price_int($item['goods']['markprice']); //$prc['markprice'];  //销售价oneMarkprice
                        $iTems['goods']['oneMarkprice'] = $this->_format_price_int($item['goods']['oneMarkprice']); //$prc['markprice'];  //销售价
                    }else{
                        $iTems['goods']['basePrice'] = $this->_format_price_int($gDatas['suit'][$item['suit_id']]['cost_price']);
                        $iTems['goods']['price']     = $this->_format_price_int($gDatas['suit'][$item['suit_id']]['price'] * $this->_memDisCount);
                        $iTems['goods']['markprice'] = $this->_format_price_int($gDatas['suit'][$item['suit_id']]['price']); //$prc['markprice'];  //销售价
                    }
                    
                    $iTems['goods']['image']     = $gDatas['suit'][$item['suit_id']]['image'];
                    
                    $iTems['quantity'] = $item['quantity'];
                    $iTems['type']     = $item['type'];
                    $iTems['ident']    = $item['dis_ident'];
                    //小计
                    $iTems['subtotal']   = $iTems['goods']['price'] * $iTems['quantity'];
                    $iTems['subtotal_m'] = $iTems['goods']['markprice'] * $item['quantity'];
                    
                    $iTems['check'] = $item['check'];
                    
                    //统计
                    $goods_amount   += $iTems['subtotal'];
                
                    $goods_amount_m += $iTems['subtotal_m'];
                    
                    if($item['check'] == 'yes'){
                        $check_amount += $iTems['subtotal'];
                        $check_amount_m += $iTems['subtotal_m'];
                        
                    }
        
                    $iTems['first'] = $item['first'];
                    $carts[$item['dis_ident']] = $iTems;
                    unset($iTems);
                    unset($prc);
                }
                 
                $goods_num    += $item['quantity'];
                 
                if($item['has_embs'] == 'no'){
                    $carts[$item['dis_ident']]['has_embs'] = 'no';
                }
                if($item['size'] == 'diy'){
                    $carts[$item['dis_ident']]['sizes'] = 'diy';
                }else{
                    //$carts[$item['dis_ident']]['sizes'] .= self::$_CUSTOMS[$item['cloth']]['cate_name'].' '.$item['size'].' ';
                    $carts[$item['dis_ident']]['sizes'][$item['cloth']]['name'] = self::$_CUSTOMS[$item['cloth']]['cate_name'];
                    $carts[$item['dis_ident']]['sizes'][$item['cloth']]['size'] = $item['size'];
                    //$carts[$item['dis_ident']]['sizes'] = $item['size'];
                }
                
                $carts[$item['dis_ident']]['list'][$item['ident']] = $iList;
                unset($iList);
            
            }else{
                
                //可以放在各类中  先不做
                if($item['type'] == 'diy' && $item['first'] && $item['dis_ident']){
                    
                    $iList = $item;
                    
                    $iList['subtotal'] = $item['goods']['price'] * $item['quantity'];
                    
                    if($item['cloth'] == '0003'){  //!$carts[$item['dis_ident']] && 
                    
                        $iTems['goods']         = $item['goods'];
                        $iTems['goods']['name'] = '首推套装';
                    
                        $iTems['quantity'] = $item['quantity'];
                        $iTems['type']     = $item['type'];
                        $iTems['ident']    = $item['dis_ident'];
                        $iTems['first']    = $item['first'];
                        
                        
                        //小计
                        $iTems['subtotal']  = $iTems['goods']['price'] * $iTems['quantity'];   //实际是不用乘的  因为只能为1
                        $item['subtotal_m'] = $item['goods']['markprice'] * $item['quantity'];
                        
                        $iTems['check'] = $item['check'];
                        
                        //统计
                        $goods_amount   += $iTems['subtotal'];
                    
                        $goods_amount_m += $iTems['subtotal_m'];
                        
                        if($item['check'] == 'yes'){
                            $check_amount += $iTems['subtotal'];
                        }
                        
                        $carts[$item['dis_ident']] = array_merge($iTems,$carts[$item['dis_ident']]);
                        
                        unset($iTems);
                    }
                     
                    $goods_num    += $item['quantity'];
                    
                    if($item['has_embs'] == 'no'){
                        $carts[$item['dis_ident']]['has_embs'] = 'no';
                    }
                    
                    $carts[$item['dis_ident']]['list'][$item['ident']] = $iList;
                    unset($iList);
                    
                } else {

                    if($item['size'] == 'diy'){
                        $item['sizes'] = 'diy';
                    }else{
                        $item['sizes'][$item['cloth']]['size'] = $item['size'];
                        $item['sizes'][$item['cloth']]['name'] = self::$_CUSTOMS[$item['cloth']]['cate_name'];
                    }
                    
                    $item['subtotal_m']   = $item['goods']['markprice'] * $item['quantity'];
                    
                    $item['subtotal']     = $item['goods']['price'] * $item['quantity'];
                    
                    $goods_amount   += $item['subtotal'];
                    
                    $goods_amount_m += $item['subtotal_m'];
                    
                    if($item['check'] == 'yes'){
                        $check_amount += $item['subtotal'];
                        $check_amount_m += $item['subtotal_m'];
                    }
                    
                    $goods_num    += $item['quantity'];
                    //$weight       += $item['quantity']*$item['goods']['weight'];
                    
                    $carts[$item['ident']] = $item;
                    
                }
            }
            
//             if($item['check'] == 'yes'){
                if($item['first']){
                    $hasFrist = 'yes';
                }
                if($item['size'] == 'diy'){
                    $sizehasdiy = 'yes';
                }
//             }
        }

        $amount = $goods_amount;
    	$order_amount = $final_amount = $goods_amount + $discount;
    	
    	$amount_m = $goods_amount_m;
    	$order_amount_m = $final_amount_m = $goods_amount_m + $discount;
    	
    	$return = array(
    	        "object" => $carts,                   //商品信息
    	        
    	        "goods_amount_m"   => $goods_amount_m,    //商品价格(购物车展示)
    	        "order_amount_m"   => $order_amount_m,    //订单最终价格(购物车展示)
    	        "final_amount_m"   => $final_amount_m,    //订单最终价格(购物车展示)
    	        
    	        "goods_amount"   => $goods_amount,    //商品价格
    	        "order_amount"   => $order_amount,    //订单最终价格
    	        "final_amount"   => $final_amount,    //订单最终价格
    	        
    	        //'point_g'        => $final_amount*1,  //积分获取
    	        'discount'       => $discount,
    	        'goods_num'      => $goods_num,       //商品总数
    	        //'weight'         => $weight,          //商品重量
    	        'sizehasdiy'     => $sizehasdiy,      //是否有量体定制
    	        'has_first'      => $hasFrist,
    	        'check_amount'   => $check_amount,
    	        'check_amount_m' => $check_amount_m,
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
        	    
        	    case 'chen':
        	       $chenIds[$row['goods_id']] = $row['goods_id'];
                break;
        	        
                case 'dis':
                    $disIds[$row['goods_id']] = $row['goods_id'];
                break;
                
                case 'suit':
                    $suitIds[$row['suit_id']] = $row['suit_id'];
                    $disIds[$row['goods_id']] = $row['goods_id'];
                break;
                
            }
        }
        $shirts = $this->_mod_shirt->find(db_create_in($chenIds,'v_id'));  //衬衣
        
        $customs = $this->_diy_custom->find(db_create_in($disIds,'id'));  //样衣  专题  作品单品
        
        $suits   = $this->_mod_suitlist->find(db_create_in($suitIds, "id"));  //套装
        
        $return = array(
        	    'chen' => $shirts,
                'dis'  => $customs,
                'suit' => $suits,
        );
        return $return;
    }
    
    /**
     * 获取购物车商品基础数据
     * 
     * @author Ruesin
     */
    function _base_info ($item = array(),$gDatas = array())
    {
        $firstCloth =  $this->firstCloth();
        if($item['type'] == 'diy'){

            $customs = self::$_CUSTOMS[$item['cloth']];
 
            //单价
            $item['process_p'] = $item['crafts_p'] = $item['fabric_p'] = $item['lining_p'] = 0;
            
            $item['fabric_p'] = $this->Prices['f'][$item['fabric']];
            
            foreach ((array)$item['params'] as $key=>$row){
                if(in_array($key, self::$_lining_parents)){
                    $item['lining_p'] = $this->Prices['c'][$item['params'][$key]]['price'];   //里料
                }elseif (in_array($key, self::$_process_parents)){
                    $item['crafts_p'] += $this->Prices['c'][$item['params'][$key]]['price'];
                }
            }
            
            $item['process_p'] = self::$_CUSTOMS[$item['cloth']]['process_fee'];   //加工费
            
            $fp = $customs['fabric_m'] * $item['fabric_p']; //面料
            $lp = $customs['lining_m'] * $item['lining_p']; //里料
            $cp = $item['crafts_p'];  //工艺
            $pp = $item['process_p'];
            
            
            if($item['first'] && $firstCloth[$item['first']]){
                $res = array(
                        'id'        => 0,
                        'name'      => $customs['cate_name'],
                        'basePrice' => $firstCloth[$item['first']]['price'],
                        'markprice' => $firstCloth[$item['first']]['price'],
                        'price'     => $firstCloth[$item['first']]['price'],
                        'oneBasePrice'  => $this->_format_price_int($fp+$lp+$cp+$pp),    //面料 + 里料 + 工艺 + 加工费
                        'weight'    => 0,
                        'image'     => $item['image'],
                );
                return $res;
            }
                        
            $res = array(
                    'id' => 0,
            	    'name'      => $customs['cate_name'],
                    'basePrice' => $this->_format_price_int($fp+$lp+$cp+$pp),    //面料 + 里料 + 工艺 + 加工费
                    'weight' => 0,
                    'image' => $item['image'],
            );
            
            
            
        }elseif($item['type'] == 'chen'){
            
            $shirt = $gDatas['chen'][$item['goods_id']];
            $res = array(
                    'id'    => $shirt['v_id'],
                    'name'  => '衬衣',//$sample['v_cate'], //v_name
                    'basePrice' => $shirt['v_cprice'], // *5.2 = $shirt['v_price'],
                    'markprice' => $shirt['v_price'],
                    'weight' => 0,
                    'image' => $shirt['v_image'],
            );
        }elseif($item['type'] == 'dis'){
            
            $cst = $gDatas['dis'][$item['goods_id']];
            
            $res = array(
                    'id' => $cst['id'],
                    'name' => self::$_CUSTOMS[$item['cloth']]['cate_name'],//$cst['name'],
                    'basePrice' => $cst['base_price'], // *5.2 = $cst['price'],
                    'weight' => $cst['weight'], //无
                    'image' => $cst['small_img'],//source_img
            );
            
        }elseif($item['type'] == 'suit'){
            
            $cst = $gDatas['dis'][$item['goods_id']];
            
            if($item['first'] && $firstCloth[$item['first']]){
                
                $res = array(
                        'id'        => $cst['id'],
                        'name'      => self::$_CUSTOMS[$item['cloth']]['cate_name'],
                        /* 'basePrice' => $firstCloth['0016']['price'],
                        'markprice' => $firstCloth['0016']['price'],
                        'price'     => $firstCloth['0016']['price'], */
                        
                        'basePrice' => $firstCloth[$item['first']]['price'],
                        'markprice' => $firstCloth[$item['first']]['price'],
                        'price'     => $firstCloth[$item['first']]['price'],
                        
                        'weight'    => $cst['weight'], //无
                        'image'     => $cst['small_img'],
                        'oneMarkprice' => $cst['price'],
                );
                
                return $res;
            }
            
            $res = array(
                    'id' => $cst['id'],
                    'name' => self::$_CUSTOMS[$item['cloth']]['cate_name'],//$cst['name'],
                    'basePrice' => $cst['base_price'], // *5.2 = $cst['price'],
                    'markprice' => $cst['price'],
                    'price'     => $this->_format_price_int($cst['price']*$this->_memDisCount),
                    'weight'    => $cst['weight'], //无
                    'image'     => $cst['small_img'],
            );
            return $res;
        }
        
        $prc = $this->_calc_price($res['basePrice'],$this->_memDisCount);
        
        if(!$res['markprice']){
            $res['markprice'] = $prc['markprice'];
        }
        
        $res['price'] = $prc['price'];
        
        return $res;
    }
    
    function _format_price_int($price = 0.00){
        return sprintf('%.2f',intval($price));
    }
    
    /**
     * 价格计算公式
     *
     * @author Ruesin
     */
    function _calc_price($price = 0 , $disCount = 1){
        $res['basePrice'] = $price;
        $res['markprice'] = $this->_format_price_int($price * $this->coefficient);   //销售价
        $res['price'] = $this->_format_price_int($price * $this->coefficient * $disCount);  //折扣价
        return $res;
    }

    /**
     * 获取会员等级折扣
     *
     * @author Ruesin
     */
    function _get_discount($user_id = 0){
        /*等级折扣 如果8.00，表示80%，可有两位小数点*/
        $member_lv_mod =& m('memberlv');
        $mMem = &m('member');
        $mem = $mMem->get($user_id);
        $member_lv_id = $mem['member_lv_id'] ? $mem['member_lv_id'] : 1;
        $dis = $member_lv_mod->get_leve_dis_count($member_lv_id);
        
        //$dic = $dis > 0 ? $dis : 0;
        //$this->_memDisCount = $disCount = 1+($dic/10);
        $dic = $dis > 0 ? $dis : 1;
        $this->_memDisCount = $disCount = ($dic/10);
        
        return $disCount;
    }
    
    /**
     * 获取当前购物车项下的各项价格
     * 
     * @author Ruesin
     */
    function _format_all_price($cart_items){
        
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
                    'conditions' => " 1 = 1 AND AREAID = '20151' AND ".db_create_in($fbs,'FABRICCODE'),
            ));
            
            foreach ((array)$FP as $row){
                $f[$row['FABRICCODE']] = $row['RMBPRICE'];
            }
        }
        
        if($ps){
            $mDt = &m('dict');
            $l = $mDt->find(array(
                    'conditions' => " 1 = 1 AND ".db_create_in($ps,'id'),
            ));
        }
        
        $this->Prices = array(
        	   'f' => $f,
               'c' => $l
        );
    }
    
    /**
     * 获取已享受的首推
     *
     * @author Ruesin
     */
    function _get_order_first($user_id = 0 , $cloth = ''){
        if (!$user_id) return false;
        if($cloth){
            $cond = " AND cloth = '{$cloth}'";
        }
        
        $mOfl = &m('orderfirstlog');
        
        $logs = $mOfl->find("user_id = '{$user_id}' " . $cond);
        
        foreach ((array)$logs as $row){
            $res[$row['cloth']] = $row;
        }
        //$this->_firsts = $res;
        return $res;
    }
    
    /**
     * 获取所有品类的刺绣信息
     * 
     * @author Ruesin
     */
    function _format_embs($type = 'code'){
        $mEp  = &m('dict_embs_parent');
        ///$mEmb = &m('dict_embs');
        //$p = $mEp->find(" is_show = '1'");
        /// 这个目前是对的 2015-06-27 17:20:51 sin
        $p = $mEp->find(array(
        	   'conditions' => " is_show = '1' ",
               'order'      => ' rank ASC',
        ));
        
        ///$e = $mEmb->find(" notshowonfront = '0' ");
        
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
    
    function get_price($item = array()){
        if($item['type'] == 'diy'){
            $customs = self::$_CUSTOMS[$item['cloth']];
            $fp = $customs['fabric_m'] * $item['fabric_p']; //面料
            $lp = $customs['lining_m'] * $item['lining_p']; //里料
            $cp = 0;  //工艺
            $sub = ($fp+$lp+$cp)*1.3*$item['quantity'];
        }elseif($item['type'] == 'chen'){
            $sub = $item['goods']['price'] * $item['quantity'];
        }elseif($item['type'] == 'dis'){
            $sub = $item['goods']['price'] * $item['quantity'];
        }
        return $sub;
    }
    
    /**
     * 校验面料库存(beta)
     *
     * @author Ruesin
     */
    function _check_stock($data = array()){
            
        // 总共的数量(添加/更新要预计算)
        // 品类 → 单耗
        // 面料
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

        foreach ($data as $key => $row){
            $fbs[$key] = $key;
            foreach ($row as $k=>$v){
                $stc[$key] += $v['quantity']*(self::$_CUSTOMS[$k]['fabric_m']);
            }
        }
        
        $mF = &m('fabric');
        $fabrics = $mF->find(array('conditions'=>db_create_in($fbs,'CODE'),'index_key'=>'CODE'));
    
        foreach ($stc as $key=>$val){
            if($fabrics[$key]['STOCK'] < $val){
                //$this->_error('面料库存不足!');
                //$this->_error($key.' - 面料库存不足!');
                //return false;
                return $key;
            }
        }
    
        return true;
    
    }
    
    function _customs(){
        return self::$_CUSTOMS;
    }
    public function firstCloth(){
        //$fCate = ['8001','8030','8050'];
        //$cates = ['8001','8030'];
        $cates = array('8001','8030');
        $fCate = array(
                '8001' => array(
                        'price' => 999,
                        'cloth' => '0003'
                ),
                '8030' => array(
                        'price' => 199,
                        'cloth' => '0006'
                )
        );
       /*  $fCate = [
                '8001' => [
                        'price' => 999,
                        'cloth' => '0003'
                ],
                '8030' => [
                        'price' => 199,
                        'cloth' => '0006'
                ],
        ]; */
        $mF = &m('fabric');
        $fabrics = $mF->find(array('conditions'=>db_create_in($cates,'CATEGORYID')."  AND is_first = '1' ",'index_key'=>'CODE'));
        foreach ($fabrics as $row){
            $res[$fCate[$row['CATEGORYID']]['cloth']]['fabric'][$row['CODE']] = $row;
            $res[$fCate[$row['CATEGORYID']]['cloth']]['price'] = $fCate[$row['CATEGORYID']]['price'];
        }
        $res['0016']['price'] = 199;
        return $res;
    }

    public $coefficient = 5.5;
    public static $_lining_parents = array(313,6291,103136);   //里料父ID  //不确定....
    public static $_process_parents = array(1230,431,2224,4992); //工艺父ID
    public static $_CUSTOMS = array(
            //lining 313 6291 103136
            /* 套装：单耗 3 ，    加工费 420 ， 300
            西服：单耗 1.95 ， 加工费 275 ， 230
            西裤：单耗 1.45 ， 加工费 145 ， 70
            大衣：单耗 2.58 ， 加工费 378 ， 270
            马夹：单耗 0.78 ， 加工费 145 ， 70
            衬衣：单耗 1.6 ，  加工费 60  ， 51
            溢价系数 5.5
            公式
            1.正常
            （面料费 * 单耗 + 加工费）* 系数 * 折扣
            2.买一赠一
            diy：（面料费 * 单耗 + 加工费）
            dis：（套装零售价/5.5）-（加工费 - 买一赠一加工费）
            3.首单
            按照 1 2 的正常计算  买一赠一 优先于 首单 */
            '0003' => array(
                    'cate_id' => '3',
                    'cate_name' => '西服',
                    'fabric_m' => '1.95', // 面料单号基数
                    'lining_m' => '2', // 里料单号基数
                    'craft' => array(
                            'id' => '435',
                            'name' => '工艺类型',
                            'son' => array(
                                    '1230' => '手工艺',
                                    '431' => '衬类型'
                            )
                    ),
                    'gender' => 10040,
                    'process_fee' => '275',   //加工费
                    'one_fee' => '230',       //买一赠一加工费
            ),
            '0004' => array(
                    'cate_id' => '2000',
                    'cate_name' => '西裤',
                    'fabric_m' => '1.45',
                    'lining_m' => '0',
                    'craft' => array(
                            'id' => '2224',
                            'name' => '工艺选择'
                    ),
                    'gender' => 10040,
                    'process_fee' => '145',   //加工费
                    'one_fee' => '70',       //买一赠一加工费
            ),
            '0005' => array(
                    'cate_id' => '4000',
                    'cate_name' => '马夹',
                    'fabric_m' => '0.78',
                    'lining_m' => '0',
                    'gender' => 10040,

                    'process_fee' => '145',   //加工费
                    'one_fee' => '70',       //买一赠一加工费
            ),
            '0006' => array(
                    'cate_id' => '3000',
                    'cate_name' => '衬衣',
                    'fabric_m' => '1.6',
                    'lining_m' => '0',
                    'gender' => 10040,

                    'process_fee' => '60',   //加工费
                    'one_fee' => '51',       //买一赠一加工费
            ),
            '0007' => array(
                    'cate_id' => '6000',
                    'cate_name' => '大衣',
                    'fabric_m' => '2.58',
                    'lining_m' => '2',
                    'craft' => array(
                            'id' => '6409',
                            'name' => '工艺类别'
                    ),
                    'gender' => 10040,
                    'process_fee' => '378',   //加工费
                    'one_fee' => '270',       //买一赠一加工费
            ),
            '0017' => array(
                    'cate_id' => '15000',
                    'cate_name' => '男短裤',
                    'fabric_m' => '0.85',
                    //'lining_m' => '2',
                    'gender' => 10040,
                    'process_fee' => '145',   //加工费
                    'one_fee' => '70',       //买一赠一加工费
            ),
            '0011' => array(
                    'cate_id' => '95000',
                    'cate_name' => '女西服',
                    'fabric_m' => '1.95',
                    'lining_m' => '2',
                    'gender' => 10041,

                    'process_fee' => '275',   //加工费
                    'one_fee' => '230',       //买一赠一加工费
            ),
            '0012' => array(
                    'cate_id' => '98000',
                    'cate_name' => '女西裤',
                    'fabric_m' => '1.45',
                    'lining_m' => '0',
                    'gender' => 10041,

                    'process_fee' => '145',   //加工费
                    'one_fee' => '70',       //买一赠一加工费
            ),
            '0016' => array(
                    'cate_id' => '11000',
                    'cate_name' => '女衬衣',
                    'fabric_m' => '1.6',
                    'lining_m' => '0',
                    'gender' => 10041,

                    'process_fee' => '60',   //加工费
                    'one_fee' => '51',       //买一赠一加工费
            ),
            '0021' => array(
                    'cate_id' => '103000',
                    'cate_name' => '女大衣',
                    'fabric_m' => '2.58',
                    'lining_m' => '2',
                    'gender' => 10041,

                    'process_fee' => '378',   //加工费
                    'one_fee' => '270',       //买一赠一加工费
            ),
            
    );
    
    public static $foCloth = array(
            '0006' => array(
                    'fabric' => array(
                            'SKP032A' => 'SKP032A',
                            'SKP031A' => 'SKP031A',
                            'SKP037A' => 'SKP037A',
                            'SKP022A' => 'SKP022A',
                            'SKP025A' => 'SKP025A',
                            'SKP020A' => 'SKP020A',
                    ),
                    'price' => 199,
            ),
            /* '0001' => array(
                    'fabric' => array(
                            'SAI303A' => 'SAI303A',  //两件套只匹配西服
                            'DBM739A' => 'DBM739A',
                    ),
                    'price' => 1999,
            ), */
            '0003' => array(
                    'fabric' => array(
                            'DBP659A' => 'DBP659A',
                            'DBK053A' => 'DBK053A',
                            'DBM601A' => 'DBM601A',
                            'DBP707A' => 'DBP707A',
                    ),
                    'price' => 999,
            ),
            '0016' => array(
                    'fabric' => array(
                            //'SBN075A' => 'SBN075A'
                    ),
                    'price' => 199,
            ),
    );
    
}