<?php
namespace Cyteam\Shop\Cart;
use Cyteam\Member\Member;
use Cyteam\Cookie\Cookie;

use Cyteam\Db\Led;
use Cyteam\Shop\Shop;

class Carts
{
//     var $table  = 'cart';
//     var $prikey = 'rec_id';
//     var $_name  = 'cart';
    
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
//     public $coeff_fabric  = 5;
//     public $coeff_fabric_than  = 3; //面料费 ≥ 100元时，面料系数 = 3
//     public $coeff_fabric_less  = 1; //面料费 ＜100元时，面料系数=1
    /*
     * 加工费系数
     */
//     public $coeff_process = 4;
    /*
     * 里料父ID
     * 通过ID进行查里料以及价格(目前废弃)
     */
//     public static $_lining_parents = array(313,6291,103136);
    /*
     * 工艺父ID
     * 通过ID进行查工艺以及价格(目前废弃)
    */
//     public static $_process_parents = array(1230,431,2224,4992);
    /**
     * 商品类型
     */
    public $types = array('custom','fdiy');
    
    function __construct($param = []){
    
    }
    
    /**
     * 空购物车
     *
     * @author Ruesin
     */
    public static function noData(){
        return ['object' => [], 'amount' => 0 , 'check_amount_m' => 0 , 'goods_num' => 0 , 'check_num' => 0];
    }

    /**
     * 获取购物车数据
     *
     * @author Ruesin
     */
    public function main ($post = array())
    {  
//        $user_id = $_SESSION['user_info']['user_id'];
//        if ($user_id){
//            $sourceFrom = isset($post['source_from']) ? $post['source_from'] : 'pc';
//
//
//            $cModel = Led::table('cart')->where(['user_id'=>$user_id]);
//            if (isset($post['check'])){
//                $cModel = $cModel->whereIn('ident', $post['check']);
//            }
//            $cart_items = $cModel->orderBy('time','DESC')->get();
////echo '<pre>';var_dump($cart_items);exit;
//
//            if (empty($cart_items))
//                return self::noData();
//            return $this->format($cart_items, $user_id);
//        }
//        return self::noData();

        #####更新DB之后出错,坑爹的玩意,决定不在用led
        $user_id = $_SESSION['user_info']['user_id'];
        $mod = m('cart');
        if ($user_id){
            $sourceFrom = isset($post['source_from']) ? $post['source_from'] : 'pc';

            $where = "user_id = $user_id";
            if (isset($post['check'])){
                $where = " AND ident = ".$post['check'];
            }
            $cart_items = $mod->find(array(
               'conditions' => $where,
                'order' => 'time DESC',
            ));

            if (empty($cart_items))
                return self::noData();
           
            return $this->format($cart_items, $user_id);
        }
        return self::noData();


    }
    /**
     * 获取会员等级折扣
     *
     * @author Ruesin
     */
   public function _get_discount($user_id = 0){
    
//         $mMem = &m('member');
    
//         $mem = $mMem->get($user_id);
    
//         $member_lv_mod =& m('memberlv');
    
//         $memberLv = $member_lv_mod->get($mem['member_lv_id']);
    
//         if ($mem['member_lv_id'] == '1'){
//             //普通会员
//         }else{
//             //创业者
//             $this->_memDisCount = ($memberLv['dis_count']/10);
//             $this->_actDisCount = 1;  // 0.9
//             $this->_memType = 1;
//         }
    
//         $this->_bdDiscount = $memberLv['bd_dis_count'];
    
    
        return $this->_memDisCount;
    }

    /**
     * 统一查出商品数据
     *
     * @author Ruesin
     */
    function _total_info($carts = array()){
        return $carts;
    }
    
    function _normal_price(&$item,$gDatas = array() , $custom = array()) {
		
        $res = array();
        if($item['type'] == 'fdiy'){
            import("diys.lib");
            $_Diys = new \Diys();
            $diyRes = $_Diys->calcDiyPrice($item['items'], $item);   //价格计算
            $res = array(
                'id'        => 0,
                'goods_url'  =>'/fdiy-'.str_replace(',', '-', $item['cloth']).'.html',
                'name'      => $_Diys->get_cname($item['cloth'],'/','fdiy_management'),
                'dname'      => $_Diys->get_cname($item['items'],'-'),
                'dname_arr'  => $_Diys->get_cname_arr($item['items'],'-'),
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
        
           import("promotion.lib");
            $promotionLib = new \Promotion();
                $promotionLib->getGoodsPromotion($item);
           
           
            $res = array(
                'id'        => $item['params']['oProducts']['product_id'],
                'goods_id'  => $item['params']['oProducts']['goods_id'],
                'name'      => $item['params']['oGoods']['name'],
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
    
    function _format_price_int($price = 0.00)
    {
      //  return intval($price);
        return sprintf('%.2f',($price));
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
    /*
     * 获取商品优惠价格
     * 
     * */

    function get_favoreprice($oreder_goods){
     // var_dump($oreder_goods);exit;
    
        $newpromotion_mod =& m('newpromotion');
        if($oreder_goods['favorable_id']){
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
                   $new_price= $oreder_goods['goods']['price']*$oreder_goods['goods']['quantity']-$manjain*$newpros_fav['yhcase_value2'];
               }
           }elseif($newpros_fav['yhcase']=='7'){
               //买送

               $maisong=floor($oreder_goods['goods']['quantity']/($newpros_fav['yhcase_value']+1));
 
               if($maisong > 0){
				  
                   $new_price= $oreder_goods['goods']['price']*$oreder_goods['goods']['quantity']-$maisong*$oreder_goods['goods']['price'];
               }
           }
        }
        if($new_price){
            if($newpros_zk && $newpros_zk['yhcase_value'] <= $new_price && $newpros_zk['yhcase']=='1'){
            
                $final_price= $new_price*$newpros_zk['yhcase_value2']/100;
                 
            }else{
                $final_price=$new_price;
            }
        }else{
            $nprice=$oreder_goods['goods']['price']*$oreder_goods['goods']['quantity'];
            if($newpros_zk && $newpros_zk['yhcase_value'] <= $nprice && $newpros_zk['yhcase']=='1'){
            
                $final_price= $nprice*$newpros_zk['yhcase_value2']/100;
                 
            }else{
                $final_price=$nprice;
            } 
        }
     
        
        return $final_price;
        
    }
    /**
     * 格式化购物车数据
     *
     * @author Ruesin
     */
    function format ($cart_items = array(), $user_id = 0, $check = array())
    {
        if(!$check)
        {
            $check = $_SESSION['_cart']['check'];
        }
        if ($check) asort($check);
        $newCheck = array();
        // # 商品类型
        //$gtypes = $this->_new_types();
        // # 品类信息

        // # 参与首单的品类面料
        //$firstCloth = $this->firstCloth();
        // # 会员等级
        $this->_get_discount($user_id);
        
        
        // # 各类型商品总体数据
        $gDatas = $this->_total_info($cart_items);
        $is_try = 0;
        $carts = array();
        $check_num = $figure_fee = $discount = $goods_amount = $goods_amount_m = $order_amount = $final_amount = $amount = $check_amount = $check_amount_m = $goods_num = $weight = $has_diy = $diy_price = $custom_price = 0;
        $hasFrist = $hasActivity = $hasNoSpecial = $hasMeasure = $hasNoSale = $hasNoFabric = $freeShipping = 0;
        $cloth_all = $cloth_check = $cloth_normal = $cloth_activity = $cloth_quan = $cloth_ka = $cloth_bi = $stock = array();
//      echo '<pre>';print_r($cart_items);exit;

        foreach ((array) $cart_items as  $item) {
            if (! $item['ident'])
                continue;
            $item['params'] = unserialize($item['params']);
//            $item['check']  = $check[$item['ident']] ? 'yes' : '';
            $item['check']  = $item['is_check'] ? 'yes' : '';
            // # 获取商品的基本价格信息
            $item['goods'] = $this->_normal_price($item, $gDatas);
            // # 商品如果符合活动条件 将用活动价格重置基本价格
            $this->_base_info($item, $gDatas);
            //获得优惠价格
            $price=0;
            if($item['favorable_id'] or $item['zhek_id']){
                $price=$this->get_favoreprice($item);
            }
          
            // 通用前置
                $item['dis_ident'] = $item['dis_ident'] ? $item['dis_ident'] : $item['ident'];
                $iTems = array();
                if (! isset($carts[$item['dis_ident']])) {
                    $iTems['type'] = $item['type'];
                    $iTems['check'] = $item['check'];
                    $iTems['quantity'] = $item['quantity'];
                    $iTems['ident'] = $item['ident'];
                    $iTems['dog_name'] = $item['dog_name'];
                    $iTems['activity'] = isset($item['activity']) ? $item['activity'] : 0;
                }

                if (! isset($carts[$item['dis_ident']])) {
                    $iTems['goods'] = $item['goods'];
                    $iTems['goods']['image'] = $item['goods']['image'];
                    
                    $iTems['goods']['nBasePrice'] = $item['goods']['nBasePrice'];
                    $iTems['goods']['nPrice'] = $item['goods']['nPrice'];
                    $iTems['goods']['nMarkprice'] = $item['goods']['nMarkprice'];
                    $iTems['goods']['basePrice'] = $item['goods']['basePrice'];
                    $iTems['goods']['price'] = $item['goods']['price'];
                    $iTems['goods']['markprice'] = $item['goods']['markprice'];
                    $iTems['goods']['favorable_id'] = $item['favorable_id'];
                    $iTems['goods']['zhek_id'] = $item['zhek_id'];
                    $carts[$item['dis_ident']] = $iTems;
                } else {
                    $carts[$item['dis_ident']]['ident'] = $item['dis_ident'];
                    $carts[$item['dis_ident']]['type'] =  $item['type'];
                    
                    $carts[$item['dis_ident']]['goods']['nBasePrice'] += $item['goods']['nBasePrice'];
                    $carts[$item['dis_ident']]['goods']['nPrice'] += $item['goods']['nPrice'];
                    $carts[$item['dis_ident']]['goods']['nMarkprice'] += $item['goods']['nMarkprice'];
                    $carts[$item['dis_ident']]['goods']['basePrice'] += $item['goods']['basePrice'];
                    $carts[$item['dis_ident']]['goods']['price'] += $item['goods']['price'];
                    $carts[$item['dis_ident']]['goods']['markprice'] += $item['goods']['markprice'];
                }

                $carts[$item['dis_ident']]['subtotal'] = $carts[$item['dis_ident']]['goods']['price'] * $carts[$item['dis_ident']]['quantity'];
                
                $carts[$item['dis_ident']]['subtotal_m'] = $carts[$item['dis_ident']]['goods']['markprice'] * $carts[$item['dis_ident']]['quantity'];
                
                if (! $carts[$item['dis_ident']]['goods']['marketable'] || ! $item['goods']['marketable']) {
                    $hasNoSale = 1; // 是否有未上架
                    $carts[$item['dis_ident']]['check'] = $item['check'] = 0; // 重置成未选择
                    $carts[$item['dis_ident']]['goods']['is_sale'] = 0;
                }
                //是否免邮
                if (isset($item['promotion']['tid']) &&  5 == $item['promotion']['tid'] )
                {
                    $freeShipping = 1;
                }
                
                $item['subtotal_m'] = $item['goods']['price'] * $item['quantity'];
                $item['subtotal'] = $item['goods']['price'] * $item['quantity'];
            
                if($price){
                    $carts[$item['dis_ident']]['subtotal'] =$price;
                    $item['subtotal_m'] ==$price;
                   
                    $item['subtotal'] =$price;
                }
			
                //总重量


				
                if ($item['check']) {


                    if($item['is_try'] == 1)//=====  如果是试吃商品  =====
                    {
                        $is_try = 1;
                    }
                    else
                    {
                        $weight += $item['goods']['weight']*$item['quantity'];
                    }
					
					 if($price){
                    $goods_amount += $price;
					 $check_amount += $price;
                }else{
					 $goods_amount += $item['subtotal'];
					  $check_amount += $item['subtotal'];
				}
                 
                    $goods_amount_m += $item['subtotal_m'];

                    $check_amount_m += $item['subtotal_m'];
                   
                    if (isset($item['activity']) && $item['activity'] > 0) {
                        $hasActivity = 1;
                        $cloth_activity[$item['cloth']] = $item['cloth'];
                    } else {
                        $cloth_normal[$item['cloth']] = $item['cloth'];

                        //优惠券品类
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

                        //$cloth_quan[$item['cloth']] = $item['cloth'];
                    }
                    $cloth_ka[$item['cloth']] = $item['cloth'];
                    $cloth_bi[$item['cloth']] = $item['cloth'];
                }else{
					 if($price){
                    $goods_amount += $price;
                 
                }
				}
            // 通用后置
                $carts[$item['dis_ident']]['cate'] += 1;
                $carts[$item['dis_ident']]['sizes'][$item['cloth']]['size'] = $item['size'];
                $carts[$item['dis_ident']]['list'][$item['ident']] = $item;
                // 无差别统计
                $goods_num += $item['quantity'];
                $cloth_all[$item['cloth']] = $item['cloth'];
                // 选择统计
                if ($item['check']) {
                    //$check_num += $item['quantity'];
                    $check_num += 1;// 10.12 edit by liang.li  修复数量问题
                    $cloth_check[$item['cloth']] = $item['cloth'];
                    
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
                }
        }
       
        asort($newCheck);
        // 不存在的check 无库存/下架的check 重置掉
//        if ($check !== $newCheck) {
//            Shop::cartCookieSet('check', $newCheck);
//        }
     
        $amount = $goods_amount;
        $order_amount = $final_amount = $goods_amount + $discount;
        	
        $amount_m = $goods_amount_m;
        $order_amount_m = $final_amount_m = $goods_amount_m + $discount;
        $return = array(
                "object" => $carts, // 商品信息
                
                "goods_amount_m" => $goods_amount_m,
                "order_amount_m" => $order_amount_m,
                "final_amount_m" => $final_amount_m,
                
                "goods_amount" => $goods_amount, // 商品价格
                "order_amount" => $order_amount, // 订单最终价格
                "final_amount" => $final_amount, // 订单最终价格

                "diy_price"      => $diy_price,    //商品价格
                "custom_price"   => $custom_price,    //订单最终价格
                                                   
                // 'point_g' => $final_amount*1,
                'discount' => $discount,
                'goods_num' => $goods_num,
                'check_num' => $check_num,
                'weight' => $weight,
                'check_amount' => $check_amount,
                'check_amount_m' => $check_amount_m,
                'has_first' => $hasFrist, // 有首单商品
                'has_activity' => $hasActivity, // 有活动商品
                'memDiscount' => $this->_memDisCount,
                'actDiscount' => $this->_actDisCount,
                'memberType' => $this->_memType,
                'has_measure' => $hasMeasure, // 需要量体
                'has_nospecial' => $hasNoSpecial, // 是否有不可使用卡券币的商品 ##
                'has_nosale' => $hasNoSale, // 是否有未上架的商品
                'has_nofabric' => $hasNoFabric, // 有 不存在面料的商品
                'free_shipping' =>  $freeShipping,   //活动免邮
                'cloth_all' => $cloth_all,
                'cloth_check' => $cloth_check,
                'cloth_activity' => $cloth_activity,
                'cloth_normal' => $cloth_normal,
                'cloth_ka' => $cloth_ka,
                'cloth_quan' => $cloth_quan,
                'cloth_bi' => $cloth_bi,
                'stock' => $stock,
                'money_amount' => 0,
                'coin' => 0,
                'is_try' => $is_try,
                'bd_dis_count' => $this->_bdDiscount
        );
     
        return $return;
    }
}