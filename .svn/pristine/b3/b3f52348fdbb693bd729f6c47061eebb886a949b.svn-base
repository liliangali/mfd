<?php
/**
 * 购物车数据模型
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: cart.model.php 14270 2016-03-18 07:07:36Z liugx $
 * @copyright Copyright 2014 mfd.com
 * @package cart.model.php
 */
class CartModel extends BaseModel
{
    var $table  = 'cart';
    var $prikey = 'rec_id';
    var $_name  = 'cart';
    
    /*
     * 用户等级折扣
     * 消费者不打折 创业者普通商品按会员折扣
     */
    public $_memDisCount = 1;
    /*
     * 用户类型
     * 0 : 消费者 1 : 创业者
     */
    public $_memType = 0;
    /*
     * 活动折扣
     * 消费者不打折 创业者活动商品在活动价上打九折 //后改成不打折
     */
    public $_actDisCount = 1;
    
    public $_bdDiscount = 0;
    /*
     * 商品类型
     */
    public $_types = array("diy", 'suit'); 

    /*
     * 面料系数
     */
    public $coeff_fabric  = 5;
    public $coeff_fabric_than  = 3; //面料费 ≥ 100元时，面料系数 = 3
    public $coeff_fabric_less  = 1; //面料费 ＜100元时，面料系数=1
    /*
     * 加工费系数
     */
    public $coeff_process = 4;
    /*
     * 里料父ID
     * 通过ID进行查里料以及价格(目前废弃)
     */
    public static $_lining_parents = array(313,6291,103136);
    /*
     * 工艺父ID
     * 通过ID进行查工艺以及价格(目前废弃)
     */
    public static $_process_parents = array(1230,431,2224,4992);

    
    
    public function __construct($params,$db)
    {
        if(isset($_REQUEST['mfd_cart_is_check']))
        {
            $this->table = 'cartc';
        }
        parent::__construct($params,$db);
    }
    /**
     * 空购物车
     *
     * @date 2015年12月1日 上午11:00:07 
     * @author Ruesin
     */
    public static function _cart_empty(){
        return array('object' => array(), 'amount' => 0 , 'check_amount_m' => 0 , 'goods_num' => 0 , 'check_num' => 0);
    }
    
    /**
     * 购物车已选择数据
     *
     * @author Ruesin
    */
    function _cart_check($user_id = 0 , $check = array())
    {
        if (!$check)
            return self::_cart_empty();
        
        $cart_items = $this->find(array(
                'conditions'    =>  " user_id = '{$user_id}' AND ident <> ''  AND ".db_create_in($check,'ident'),
                'order'         => 'rec_id DESC',
        ));

        if (empty($cart_items))
            return self::_cart_empty();
        
        return $this->_cart_format($cart_items,$user_id,$check);
    }

    /**
     * 获取购物车数据
     *
     * @author Ruesin
     */
    function _cart_main($user_id = 0 , $check = array(),$source_from="pc")
    {

//        echo  " user_id = '{$user_id}'  AND ident <> '' AND  source_from = '$source_from' ";exit;
        $cart_items = $this->find(array(
//                 'conditions'    =>  " user_id = '{$user_id}'  AND dis_ident <> '' ",
                'conditions'    =>  " user_id = '{$user_id}'  AND ident <> '' AND  source_from = '$source_from' ",
                'order'         => 'rec_id DESC',
        ));
//        echo '<pre>';print_r($cart_items);exit;
        

        if (empty($cart_items))
            return self::_cart_empty();

        return $this->_cart_format($cart_items,$user_id,$check);

    }
    /*
     * 获取商品优惠价格111
     *
     * */
    
    function get_favoreprice($oreder_goods){
    
      
        $newpromotion_mod =& m('newpromotion');
        if($oreder_goods['zhek_id']){
          $newpros_fav=$newpromotion_mod->get(array(
            'conditions'=>"id='{$oreder_goods['favorable_id']}' AND is_open=1 AND starttime <= ".time()." AND endtime >= ".time(),
        ));
        }
        if($oreder_goods['zhek_id']){
            $newpros_zk=$newpromotion_mod->get(array(
            'conditions'=>"id='{$oreder_goods['zhek_id']}' AND is_open=1 AND starttime <= ".time()." AND endtime >= ".time(),
            ));
        }
        if($newpros_fav){
            if($newpros_fav['yhcase']=='6'){
                //满减
    
                $manjain=floor($oreder_goods['goods']['price']*$oreder_goods['goods']['quantity']/$newpros_fav['yhcase_value']);
    
                if($manjain > 0){
                    $data['price']=$oreder_goods['goods']['price']*$oreder_goods['goods']['quantity']-$manjain*$newpros_fav['yhcase_value2'];
                    $data['favorable_name']=$newpros_fav['name'];
                
                }
            }elseif($newpros_fav['yhcase']=='7'){
                //买送
    
                $maisong=floor($oreder_goods['goods']['quantity']/($newpros_fav['yhcase_value']+1));
    
                if($maisong > 0){
                    $data['price']=$oreder_goods['goods']['price']*$oreder_goods['goods']['quantity']-$maisong*$oreder_goods['goods']['basePrice'];
                    $data['favorable_name']=$newpros_fav['name'];
                 
                }
            }
        }
     
        if($data){
          
            if($newpros_zk && $newpros_zk['yhcase_value'] <= $data['price'] && $newpros_zk['yhcase']=='1'){
              
                $data['price']= $data['price']*$newpros_zk['yhcase_value2']/100;
                $data['zhek_name']=$newpros_zk['name'];
            }
        }else{
            $newprice=$oreder_goods['goods']['price']*$oreder_goods['goods']['quantity'];
            if($newpros_zk && $newpros_zk['yhcase_value'] <= $newprice && $newpros_zk['yhcase']=='1'){
            
                $data['price']= $newprice*$newpros_zk['yhcase_value2']/100;
                $data['zhek_name']=$newpros_zk['name'];
            }else{
                $data['price']= $newprice;
            }
            
        }
       
        return $data;
        
    }
    /**
     * 格式化购物车数据
     *
     * @author Ruesin
     */
    function _cart_format($cart_items = array(),$user_id = 0,$check = array()){


        $newCheck = array();
        asort($check);
        
        ## 商品类型
        $gtypes = $this->_new_types();
        ## 品类信息
        imports("diys.lib");
        $this->_Diys = new Diys();
        $customs = $this->_Diys->_customs();
        
        imports("promotion.lib");
        $this->_Prot = new Promotion();
//         ## 参与首单的品类面料
//         $firstCloth =  $this->firstCloth();
        ## 会员等级
        $this->_get_discount($user_id);
        ## 各类型商品总体数据
        $gDatas = $this->_total_info($cart_items);
        
        $carts = array();
        
        $check_num = $figure_fee = $discount = $goods_amount = $goods_amount_m = $order_amount = $final_amount = $amount = $check_amount = $check_amount_m = $goods_num = $weight = $has_diy = $diy_price = $custom_price = 0;
        
        $hasFrist = $hasActivity = $hasNoSpecial = $hasMeasure = $hasNoSale = $hasNoFabric = $freeShipping = 0;
        
        $cloth_all = $cloth_check = $cloth_normal = $cloth_activity = $cloth_quan = $cloth_ka = $cloth_bi = $stock = array();

// var_dump($cart_items);
//        echo '<pre>';print_r($cart_items);exit;

        
        foreach ((array)$cart_items as $item)
        {
            if(!$item['ident']) continue;
       
            $item['params'] = unserialize($item['params']);
            
            $item['check']    = isset($check[$item['ident']]) ? 1 : 0;


            ## 获取商品的基本价格信息
            $item['goods'] = $this->_normal_price($item,$gDatas);
//            echo '<pre>';print_r($item);exit;
            ## 商品如果符合活动条件  将用活动价格重置基本价格
            $this->_base_info($item,$gDatas);
//             var_dump($item['goods']);exit();
            //获得优惠价格
            $price=0;
            if($item['favorable_id'] or $item['zhek_id']){
                $getfavore=$this->get_favoreprice($item);
                if($getfavore){
                    $price=$getfavore['price'];
                    $favorable_name=$getfavore['favorable_name'];
                    $zhek_name=$getfavore['zhek_name'];
                }
                
              ///  var_dump($price);exit;
            }
          
            

            
            //通用前置
                $item['dis_ident'] = $item['dis_ident'] ? $item['dis_ident'] : $item['ident'];

                $carts[$item['dis_ident']]['ident'] = $item['ident'];
                $carts[$item['dis_ident']]['type'] =  $item['type'];
                $carts[$item['dis_ident']]['favorable_name'] =$favorable_name;
                $carts[$item['dis_ident']]['zhek_name'] =$zhek_name;
                 $carts[$item['dis_ident']]['goods']['nBasePrice'] += $item['goods']['nBasePrice'];
                 $carts[$item['dis_ident']]['goods']['nPrice']     += $item['goods']['nPrice'];
                 $carts[$item['dis_ident']]['goods']['nMarkprice'] += $item['goods']['nMarkprice'];
                 $carts[$item['dis_ident']]['goods']['basePrice'] += $item['goods']['basePrice'];
                 $carts[$item['dis_ident']]['goods']['price']     += $item['goods']['price'];
                 $carts[$item['dis_ident']]['favprice']     += $price;
                 $carts[$item['dis_ident']]['goods']['markprice'] += $item['goods']['markprice'];
                 $carts[$item['dis_ident']]['goods'] = $item['goods'];
                
               
                $carts[$item['dis_ident']]['subtotal']   = $carts[$item['dis_ident']]['goods']['price'] * $carts[$item['dis_ident']]['goods']['quantity'];
                $carts[$item['dis_ident']]['subtotal_m'] = $carts[$item['dis_ident']]['goods']['markprice'] * $carts[$item['dis_ident']]['goods']['quantity'];
                
                if (!$carts[$item['dis_ident']]['goods']['marketable'] || !$item['goods']['marketable']){
                    $hasNoSale = 1;// 是否有未上架
                    $carts[$item['dis_ident']]['check'] = $item['check'] = 0;  //重置成未选择
                    $carts[$item['dis_ident']]['goods']['is_sale'] = 0;
                }
                
//                 if($item['params']['oGoods']['is_debit'] == 0){
//                     $hasNoSpecial = 1; // 是否有不可使用卡券币
//                 }
//       echo '<pre>';print_r($item);exit;
                
                //限制优惠券
//                if ($item['params']['oGoods']['is_debit'] == 1 && $item['type'] == 'custom'){
//                    $cloth_quan[] = $item['cloth'];
//                }
                //优惠券品类


                //是否免邮
                if (isset($item['promotion']['tid']) &&  5 == $item['promotion']['tid'] )
                {
                    $freeShipping = 1;
                }
            
                $item['subtotal_m']   = $item['goods']['price'] * $item['quantity'];
                $item['subtotal']     = $item['goods']['price'] * $item['quantity'];
//                 var_dump($item['goods']);exit();
               
                
                $goods_amount_m += $item['subtotal_m'];
                if($price){
                  
                    $goods_amount   += $price;
                }else{
                    $goods_amount   += $item['subtotal'];
                }
             
//echo '<pre>';print_r($item);exit;

            if($item['type'] == 'custom')
            {
                $cloth_quan[] = 2;
                $custom_price += $item['subtotal'];

            }
            else
            {
                $cloth_quan[] = 1;
                $diy_price += $item['subtotal'];
            }


                //总重量
                $weight += $item['goods']['weight'];
                
//                 if($item['check']){
                
//                     $check_amount   += $item['subtotal'];
//                     $check_amount_m += $item['subtotal_m'];
                
//                     if (isset($item['activity']) && $item['activity'] > 0 ){
//                         $hasActivity = 1;
//                         $cloth_activity[$item['cloth']] = $item['cloth'];
//                     }else{
//                         $cloth_normal[$item['cloth']] = $item['cloth'];
//                         $cloth_quan[$item['cloth']] = $item['cloth'];
//                     }
//                     $cloth_ka[$item['cloth']]   = $item['cloth'];
//                     $cloth_bi[$item['cloth']]   = $item['cloth'];
//                 }
            
            
            //通用后置
          
                $carts[$item['dis_ident']]['cate'] += 1;
                
               
                $carts[$item['dis_ident']]['sizes'][$item['cloth']]['size'] = $item['size'];
                
                $carts[$item['dis_ident']]['list'][$item['ident']] = $item;
                
                //无差别统计
                $goods_num    += $item['quantity'];
                $cloth_all[$item['cloth']] = $item['cloth'];
               
                //选择统计
//                 if($item['check']){
                    $check_num  += $item['quantity'];
                    $cloth_check[$item['cloth']] = $item['cloth'];
                
                    if($item['first']) $hasFrist = 1;
                
                    if($item['size'] == 'diy'){
                        if(!empty($item['figure']) && ($item['figure']['type_id'] == 1 || $item['figure']['type_id'] == 2 || $item['figure']['type_id'] == 6) ){
                            $hasMeasure = 1;
                        }
                    }
                
                    $newCheck[$item['ident']] = $item['ident'];
                    
                    if (isset($stock[$item['ident']][$item['cloth']])){
                        $stock[$item['ident']][$item['cloth']]['quantity'] += $item['quantity'];
                    }else{
                        $stock[$item['ident']][$item['cloth']]['cloth']  = $item['cloth'];
                        $stock[$item['ident']][$item['cloth']]['fabric'] = $item['fabric'];
                        $stock[$item['ident']][$item['cloth']]['quantity'] = $item['quantity'];
                        $stock[$item['ident']][$item['cloth']]['type'] = $item['type'];
                        
                        $stock[$item['ident']][$item['cloth']]['product_id'] = $item['params']['oProducts']['product_id'];
                    }
                    
//                 }
                
            
        }
      
//         var_dump($carts);
        asort($newCheck);
        //不存在的check  无库存/下架的check  重置掉
        if ($check !== $newCheck){
            import('shopCommon');
            ShopCommon::cartCookieSet('check', $newCheck);
        }
//         var_dump($goods_amount);
        $amount = $goods_amount;
        $order_amount = $final_amount = $goods_amount + $discount;
      
        $amount_m = $goods_amount_m;
        $order_amount_m = $final_amount_m = $goods_amount_m + $discount;
       // var_dump($order_amount);exit;
        $return = array(
                "object" => $carts,                   //商品信息
                 
                "goods_amount_m"   => $goods_amount_m,
                "order_amount_m"   => $order_amount_m,
                "final_amount_m"   => $final_amount_m,
                 
                "goods_amount"   => $goods_amount,    //商品价格
                "order_amount"   => $order_amount,    //订单最终价格
                "final_amount"   => $final_amount,    //订单最终价格

                "diy_price"      => $diy_price,    //商品价格
                "custom_price"   => $custom_price,    //订单最终价格

                //'point_g'        => $final_amount*1,
                'discount'       => $discount,
                'goods_num'      => $goods_num,
                'check_num'      => $check_num,
                'weight'         => $weight,
                'check_amount'   => $check_amount,
                'check_amount_m' => $check_amount_m,
               
                'has_first'      => $hasFrist,    // 有首单商品
                'has_activity'   => $hasActivity, // 有活动商品
                
                 
                'memDiscount'    => $this->_memDisCount,
                'actDiscount'    => $this->_actDisCount,
                'memberType'     => $this->_memType,
                
                'has_measure'    => $hasMeasure,   //需要量体
                'has_nospecial'  => $hasNoSpecial, //是否有不可使用卡券币的商品 ##
                'has_nosale'     => $hasNoSale,    //是否有未上架的商品
                'has_nofabric'   => $hasNoFabric,  //有 不存在面料的商品
                'free_shipping' =>  $freeShipping,   //活动免邮
//                 'free_shipping' =>  1,   //活动免邮
                'cloth_all'         => $cloth_all,
                'cloth_check'       => $cloth_check,
                'cloth_activity'    => $cloth_activity,
                'cloth_normal'      => $cloth_normal,
                'cloth_ka'      => $cloth_ka,
                'cloth_quan'      => $cloth_quan,
                'cloth_bi'      => $cloth_bi,
                
                'stock'         => $stock,
                
                'money_amount'  => 0,
                'coin'          => 0,
                
                'bd_dis_count' => $this->_bdDiscount,
        );
//         var_dump($return['goods_num']);exit();
        return $return;
    }

    /**
     * 统一查出商品数据
     *
     * @author Ruesin
     */
    function _total_info($carts = array()){
        $custom = $fDiys = $diyFabric = array();
        foreach ($carts as $row){
            switch ($row['type']){
            	case 'diy':
            	    $diyFabric[$row['fabric']] = $row['fabric'];
            	    break;
            	     
            	case 'suit':
            	    $suitIds[$row['suit_id']] = $row['suit_id'];
            	    $disIds[$row['goods_id']] = $row['goods_id'];
            	    if ($row['is_change']){
            	        $diyFabric[$row['fabric']] = $row['fabric'];
            	    }
            	    break;
            	case 'fdiy':
            	    $fDiys[$row['fabric']] = $row['fabric'];
            	    break;
            }
            
            if($row['f_id'] > 0){
                $fDiys[$row['fabric']] = $row['fabric'];
            }
            
        }
        $custom = $suit = $fabrics = $women = array();
        if (!empty($suitIds)){
            $mSuit = &m("suitlist");
            $suit = $mSuit->find(db_create_in($suitIds, "id"));
        }
        
        if (!empty($disIds)){
            $mCustom = &m('custom');
            $custom = $mCustom->find(db_create_in($disIds,'id'));
        }
        
        if (!empty($fDiys)){
        
            $mFabricInfo = &m('fabric_info');
            $fabric = $mFabricInfo->find(array(
                    'conditions' => db_create_in($fDiys,'fabric_sn'),
                    'index_key'  => 'fabric_sn',
            ));
            
            foreach ($fabric as $fVal){
                $fIds[$fVal['fabric_sn']] = $fVal['fabric_id'];
                $fCodes[$fVal['fabric_id']] = $fVal['fabric_sn'];
            }
            $mFabricPrice = &m('fabric_price');
            $fabricPrice = $mFabricPrice->find(db_create_in($fIds,'fabric_id'));

            foreach ($fabricPrice as $fpVal){
                $fabric[$fCodes[$fpVal['fabric_id']]]['fabricPrice'][$fpVal['category']] = $fpVal;
            }
            
        }
        
        /* if ($suit){
            foreach ($suit as $sVal){
                if ($sVal['theme'] == 16){
                    $women[$sVal['id']] = $sVal['id'];
                }
            }
        }*/
        
        if ($diyFabric){
            $dFabric = $this->_all_diy_info($diyFabric);
        }
        
        return array(
                'diy'  => $dFabric,  //diy 面料 + 工艺价格
                'dis'  => $custom, //样衣
                'suit' => $suit, //套装
                'fabric' => $fabric,
                'women'  => $women,
        );
    }
    
    /**
     * 获取当前购物车项下的各项价格
     *
     * @author Ruesin
     */
    function _all_diy_info($fbs){
    
        $FP = array();
        
        
        if(!empty($fbs)){
            
            $mFP = &m('fabricprice');
            $FP = $mFP->find(array(
                    'fields' => 'FABRICCODE code,RMBPRICE price',
                    'conditions' => " 1 = 1 AND AREAID = '20151' AND ".db_create_in($fbs,'FABRICCODE'),
                    'index_key' => 'code',
            ));
    
            $mod_fabric   = &m('fabric');
            $fabrics = $mod_fabric->find(array('conditions'=>db_create_in($fbs,'CODE') . " AND is_sale = 1",'index_key'=>'CODE'));
    
            
            foreach ($FP as &$row){
                if (!isset($fabrics[$row['code']])){
                    $row['tname'] = $row['code'];
                    continue;
                }
                $row['tname'] = $fabrics[$row['code']]['tname'];
            
                //活动面料价格
                if($fabrics[$row['code']]['activity'] == '1' && ($fabrics[$row['code']]['activity_price_0003']>0 || $fabrics[$row['code']]['activity_price_0004']>0 || $fabrics[$row['code']]['activity_price_0005']>0 || $fabrics[$row['code']]['activity_price_0006']>0 || $fabrics[$row['code']]['activity_price_0007']>0)){
                    $row['activity'] = $fabrics[$row['code']]['activity'];
                    $row['activity_price_0003'] = $fabrics[$row['code']]['activity_price_0003'];
                    $row['activity_price_0004'] = $fabrics[$row['code']]['activity_price_0004'];
                    $row['activity_price_0005'] = $fabrics[$row['code']]['activity_price_0005'];
                    $row['activity_price_0006'] = $fabrics[$row['code']]['activity_price_0006'];
                    $row['activity_price_0007'] = $fabrics[$row['code']]['activity_price_0007'];
                }
                //指定销售价
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
        return  $FP;
    }

    protected function _normal_price(&$item,$gDatas = array() , $custom = array()) {
        $res = array();

        if($item['type'] == 'fdiy'){

                $diyRes = $this->_Diys->calcDiyPrice($item['items'], $item);   //价格计算
//                 var_dump($item);
//                 var_dump($diyRes);
                $res = array(
                        'id'        => 0,
                        'goods_url'  =>'/fdiy-'.str_replace(',', '-', $item['cloth']).'.html',
                        'name'      => $this->_Diys->get_cname($item['cloth'],'/','fdiy_management'),
                        'dname'      => $this->_Diys->get_cname($item['items'],'-'),
                        'weight'    => $diyRes['weight'],
                        'image'     => $item['image'],
                        'basePrice' =>$this->_format_price_int($diyRes['baseprice']),
                    //这里需要和 普通商品做成统一  price 是销售总价  markprice是优惠价
                        'markprice' => $this->_format_price_int($diyRes['markprice']),
                        'price'     => $this->_format_price_int($diyRes['markprice'] * $this->_memDisCount),
                        'quantity'  => $item['quantity'],
                        'marketable'   => 1,
                );
            
        }elseif($item['type'] == 'custom'){

            //根据商品促销规则 处理价格
            $this->_Prot->getGoodsPromotion($item);

             
            $res = array(
                    'id'        => $item['params']['oProducts']['product_id'],
                    'goods_id'  => $item['params']['oProducts']['goods_id'],
                    'name'      =>  $item['params']['oGoods']['name'],
                    'weight'    => $item['params']['oProducts']['weight']*$item['quantity'],
                    'image'     => $item['params']['oGoods']['thumbnail_pic'],
                    'basePrice' => $item['params']['oProducts']['price'],    //打折后价格
                    'markprice' => $item['params']['oProducts']['price'],
                    'price'     => $item['params']['oProducts']['fprice'],   //商品原价
                    'quantity'     => $item['quantity'],
                    'marketable'     => $item['params']['oGoods']['marketable'],
                    'promotion' => isset($item['promotion']) ? 1 : 0,       //商品促销
                    'pdata' => $item['promotion'],                          //规则明细
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
        if($item['type'] == 'fdiy'){

            
        }elseif($item['type'] == 'custom'){

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
//        echo '<pre>';print_r(printf('%.1f',($price)));exit;
        
//        return intval($price);
        return sprintf('%.2f',($price));
    }

    /**
     * 价格计算公式
     *
     * @author Ruesin
     */
    function _calc_price($price = 0 , $disCount = 1){
        $res['basePrice'] = $price;
        $res['markprice'] = $this->_format_price_int($price * $this->coeff_fabric);   //销售价
        $res['price']     = $this->_format_price_int($price * $this->coeff_fabric * $disCount);  //折扣价
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

        $member_lv_mod =& m('memberlv');
        
        $memberLv = $member_lv_mod->get($mem['member_lv_id']);
        
        if ($mem['member_lv_id'] == '1'){
            //普通会员
        }else{
            //创业者
            $this->_memDisCount = ($memberLv['dis_count']/10);
            $this->_actDisCount = 1;  // 0.9
            $this->_memType = 1;
        }
        
        $this->_bdDiscount = $memberLv['bd_dis_count'];
        

        return $this->_memDisCount;
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
    function get_kinds( $user_id = 0,$source_from="pc" )
    {

        if($user_id == 0 ) return 0;
        $carts = $this->get(array(
                'conditions' => " user_id = '{$user_id}' ",
                'fields' => 'sum(quantity) as num'
        ));
//        echo '<pre>';print_r($carts);exit;
        
        return $carts['num'] ? $carts['num'] : 0;
    }
    
    /**
     * 获取购物车中没有库存的套装的IDS
     *
     * @date 2015-10-26 下午1:23:49
     *
     * @author Ruesin
     */
    function _getNoStoreSuit ($user_id = 0)
    {
        $res = array();
        $cart_items = $this->find(array(
                'conditions' => " user_id = '{$user_id}' AND type = 'suit'  GROUP BY suit_id",
                'fields' => 'suit_id',
        ));
        foreach ($cart_items as $row){
            $ids[$row['suit_id']] = $row['suit_id'];
        }
        if(isset($ids)){
            $mSuit = &m('suitlist');
            $suit = $mSuit->find(db_create_in($ids,'id')." AND is_sale = 1 AND ".db_create_in(array('0','pc'),'to_site'));
            if ($suit){
                foreach ($suit as $row){
                    $res[$row['id']] = $row['id'];
                }
            }
        }
    
        return $res;
    }

    /**
     * 校验面料库存(beta)
     * 面料号为键 DBM739A ，品类CODE为二维键 0003 ，三维  'cloth' => '0003', 'fabric' => 'DBM739A','quantity' => 6
     *
     * @author Ruesin
     */
    function _check_stock($data = array()){

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


}