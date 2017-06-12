<?php
use Cyteam\Shop\Cart;
use Cyteam\Shop\Cart\Carts;
use Cyteam\Shop\Shop;
use Cyteam\Shop\Type;
use Cyteam\Shop\Type\Types;
use Cyteam\Goods\Goods;
class CartApp extends ShoppingbaseApp
{
	public $types = array("suit",'custom','diy','fdiy');
	var $_mod_cart;
    var $_class_carts;
	var $_template_file = 'cart/';
	function __construct()
	{
		$this->_mod_cart   =& m('cart');
        $this->_class_carts = new Carts();
		
		parent::__construct();
		 $this->_newpromotion_mod =& m('newpromotion');
		 $this->_youhuiquan_mod =& m('youhuiquan');
        $this->_goodstype_mod =& m('goodstype'); //商品类型
        $this->_gcategory_mod =& bm('gcategory', array('_store_id' => 0));//商品分类
        $this->_link_mod =& m('goods_prorel');
        $this->_goodslink_mod = &m('goods_prolink');
        $this->_user_mod =& m('member');
		$this->_goods_mod =& m('goods');
        $this->_lv_mod =& m('memberlv');
	}

    /**
     * 购物车列表
     *
     * @author Ruesin
     */
    function index2()
    {

        $main = $this->_cart_main();
        $this->_curlocal( LANG::get('cart'));
        $this->_config_seo('title', Lang::get('confirm_goods') . ' - ' . Conf::get('site_title'));
        if (empty($main['object'])){
            $this->_cart_empty();
            return;
        }
        $this->cartCookieSet('check',array());
        if (isset($_GET['v5']) && !empty($_GET['v5']))
        {
            print_exit($main['object']);
        }
        $this->assign('cart', $main);
        $this->display('cart/index.html');
    }

    /**
     * 购物车列表
     *
     * @author Ruesin
     */
    function index()
    {
       
        $main = $this->_class_carts->main(['source_from'=>$this->_source_from]);
//        echo '<pre>';print_r($main);exit;
      
        $this->_curlocal( LANG::get('cart'));
        $this->_config_seo('title', Lang::get('confirm_goods') . ' - ' . Conf::get('site_title'));
        if (empty($main['object'])){
            $this->_cart_empty();
            return;
        }
		
		//商品的优惠方案
		if($main['object']){
			foreach($main['object'] as $key=>$val){
				if($val['goods']['goods_id']){
					/*$favorables_mj=$this->get_goods_favorable_mj($val['goods']['goods_id'],$_SESSION['user_info']['member_lv_id']);
					$favorables_zk=$this->get_goods_favorable_zk($val['goods']['goods_id'],$_SESSION['user_info']['member_lv_id']);*/
				    //获取商品当前优先级最高活动
				    $goodsLib =  new Goods();
				    //获取商品的优惠活动
				     $main['object'][$key]['favorables'] = $goodsLib->getfavourable($id);
				// var_dump($main['object'][$key]['favorables']);exit;
				}
				
				/*if($favorables_mj){
				   $main['object'][$key]['favorable_mj']=$favorables_mj;
				  
			    }else{
					$main['object'][$key]['favorable_mj']='';
				
				}
				if($favorables_zk){
				    $main['object'][$key]['favorable_zk']=$favorables_zk;
				}else{
				    $main['object'][$key]['favorable_zk']='';
				}*/
				
			}
			
		}
        if($_SESSION['_cart']['choiceAll'] && !empty($_SESSION['_cart']['check']))
            $this->assign('choiceAll',$_SESSION['_cart']['choiceAll']);
		
	//var_dump($main['object']['ggyqy0000000048']['goods']);exit;
        $this->assign('cart', $main);
        $this->assign('tk', $_REQUEST['token']);
        $this->display('cart/index.html');
    }
    //优惠券优惠（可用）
    function get_goods_favorable_yh($member){
        $member_lv_id=$member['member_lv_id'];
        //优惠券
        $youhuiquans=$this->_youhuiquan_mod->find(array(
            'conditions'=>"uid='{$member['user_id']}' AND status = 0",
            //'index_key'=>'quan_id',
        ));
       
        if($youhuiquans){
            $quanids=array_column($youhuiquans,'quan_id');
            if($quanids){
                $quanid=db_create_in($quanids,'id');
            }
        }
       
     
        if($quanid){
            $newpromotions= $this->_newpromotion_mod->find(array(
                'conditions'=>$quanid.' AND yhcase=9 AND is_open=1  AND starttime <= '.time().' AND endtime >= '.time().' AND FIND_IN_SET('.$member_lv_id.',member_lv_id) ',
                'order'     =>'id desc',
            ));
        }else{
            $newpromotions='';
        }
       
             return $newpromotions;
    }
    
//满减买送优惠
   function get_goods_favorable_mj($goods_id,$member_lv_id='1'){
	   //商品信息
	   $goods_info=$this->_goods_mod->get(array(
	          'conditions'=>'goods_id='.$goods_id,
		   ));
	   if($member_lv_id){
	       //可用优惠
	       $newpromotions= $this->_newpromotion_mod->find(array(
	           'conditions'=>'(yhcase=6 or yhcase=7) AND is_open=1  AND starttime <= '.time().' AND endtime >= '.time().' AND FIND_IN_SET('.$member_lv_id.',member_lv_id) ',
	           'order'     =>'id desc',
	       ));
	 
	   }
	 
		//判断商品的优惠
		if($newpromotions){
			foreach($newpromotions as $key=>$val){
				
				if($val['favorable']=='1'){
				
					//商品类型
					$goodstypes=$this->_goodslink_mod->get(array(
				          'conditions'=>"rules_id='{$val['id']}' AND favorable_value='{$goods_info['type_id']}' AND favorable_id ='1'",
					));

					if($goodstypes){
						$newfavore[$val['id']]=$val;
					}
				}elseif($val['favorable']=='2'){
					//指定商品
				 
					$link=$this->_link_mod->get(array(
					      'conditions'=>'d_id='.$val['id'].' AND c_id='.$goods_id,
					));
				
					if($link){
					$newfavore[$val['id']]=$val;	
					}
					
				}elseif($val['favorable']=='3'){
					
					//商品分类
					$goodslinks=$this->_goodslink_mod->get(array(
				          'conditions'=>"rules_id='{$val['id']}' AND favorable_value='{$goods_info['cat_id']}' AND favorable_id ='3'",
					));

					if($goodslinks){
						$newfavore[$val['id']]=$val;
					}
					
				}elseif($val['favorable']=='4'){
					//全部商品
					$newfavore[$val['id']]=$val;
				}
			}
			return $newfavore;
		}
		
      }
      //折扣优惠
      function get_goods_favorable_zk($goods_id,$member_lv_id='1'){
          //商品信息
          $goods_info=$this->_goods_mod->get(array(
              'conditions'=>'goods_id='.$goods_id,
          ));
          if($member_lv_id){
              //可用优惠
              $newpromotions= $this->_newpromotion_mod->find(array(
                  'conditions'=>'is_open=1 AND yhcase=1 AND starttime <= '.time().' AND endtime >= '.time().' AND FIND_IN_SET('.$member_lv_id.',member_lv_id) ',
                  'order'     =>'id desc',
              ));
          }

          //判断商品的优惠
          if($newpromotions){
              foreach($newpromotions as $key=>$val){
      
                  if($val['favorable']=='1'){
      
                      //商品类型
                      $goodstypes=$this->_goodslink_mod->get(array(
                          'conditions'=>"rules_id='{$val['id']}' AND favorable_value='{$goods_info['type_id']}' AND favorable_id ='1'",
                      ));
      
                      if($goodstypes){
                          $newfavore[$val['id']]=$val;
                      }
                  }elseif($val['favorable']=='2'){
                      //指定商品
                      	
                      $link=$this->_link_mod->get(array(
                          'conditions'=>'d_id='.$val['id'].' AND c_id='.$goods_id,
                      ));
      
                      if($link){
                          $newfavore[$val['id']]=$val;
                      }
                      	
                  }elseif($val['favorable']=='3'){
                      	
                      //商品分类
                      $goodslinks=$this->_goodslink_mod->get(array(
                          'conditions'=>"rules_id='{$val['id']}' AND favorable_value='{$goods_info['cat_id']}' AND favorable_id ='3'",
                      ));
      
                      if($goodslinks){
                          $newfavore[$val['id']]=$val;
                      }
                      	
                  }elseif($val['favorable']=='4'){
                      //全部商品
                      $newfavore[$val['id']]=$val;
                  }
              }
              return $newfavore;
          }
      
      }
    /**
     * 加入购物车
     *
     * @author Ruesin
     */
    function add()
    {

        $type = $_POST['type'] ? $_POST['type'] :"custom";
        if(!in_array($type,  $this->types)){
            $this->json_error('type_error');
            return;
        }
        $goods = Types::createObj($type);
        $post = $goods->_formatPost($_REQUEST);
        if(!$post){
            $this->json_error($goods->get_error());
            return;
        };
        $post['user_id'] = $this->_user_id;
        $post['source_from'] = $this->_source_from;
        $res = $goods->add($post);
        if($res){
            $main = $this->_class_carts->main($post);
            $last_arr = array_values($main['object']);
            $this->json_result(array(
                'goods_num'   => $main['goods_num'],
                'amount'      => price_format($main['goods_amount']),
                'lastkey'     => $last_arr[0]['ident'],
            ), 'addto_cart_successed');

        }else{
            $this->json_error($goods->get_error());
            return;
        }




//        $type = $_POST['type'] ? $_POST['type'] :"custom";
//
//		if(!in_array($type, $this->types)){
//			$this->json_error('type_error');
//			return;
//		}
//		$goods = &gt($type);
//		$post = $goods->_format_post($_POST);
//
//        if(!$post){
//            $this->json_error($goods->get_error());
//            return;
//        }
//		$post['user_id'] = $this->_user_id;
////print_exit($post);
//		$res = $goods->add($post);
//
//		if($res){
//
//			$main = $this->_cart_main();
//            $last_arr = array_values($main['object']);
//			$this->json_result(array(
//					'goods_num'   => $main['goods_num'],
//			        'amount'      => price_format($main['goods_amount']),
//                    'lastkey'     => $last_arr[0]['ident'],
//			), 'addto_cart_successed');
//		}else{
//   			$this->json_error($goods->get_error());
//   			return;
//   		}
    }
    function sin(){
        $str = file_get_contents(ROOT_PATH.'/upload/sin.txt');
        echo '<pre>';
        print_r(unserialize($str));
        exit;
    }
    
    /**
     * 删除购物车
     * 
     * @date 2015-8-21 下午2:43:10
     * @author Ruesin
     */
    function drop()
    {
        $ident = isset($_POST['id']) ? trim($_POST['id']) : '';
        $type  = isset($_POST['tp'])? trim($_POST['tp']): '';
        
        if(!$ident){
            $this->json_error('update_params_error');
            return;
        }
        
        if(!in_array($type, $this->types)){
            $this->json_error('type_error');
            return;
        }
        
        $goods = &gt($type);

        $choice = $goods->choice(array('ident' => $ident, "user_id" => $this->_user_id));
        if(!$choice){
            $this->json_error($goods->get_error());
            return;
        }

        if(!$goods->drop(array('ident' => $ident,'user_id'=>$this->_user_id))){
            $this->json_error($goods->get_error());
            return;
        }
        $oldChoice = $this->cartCookieGet('check');
        foreach ((array)$choice as $val){
            unset($oldChoice[$val]);
        }
        $this->cartCookieSet('check', $oldChoice);
        
        $this->json_result(array(),'drop_item_successed');
    }
    
    /**
     * 清空购物车
     * 
     * @date 2015-8-21 下午2:43:35
     * @author Ruesin
     */
    function clear(){
        $oldChoice = $_SESSION['_cart']['check'];
        if(!$oldChoice){
            $this->json_error('请选择要删除的商品');
            die();
        }
    	$droped_rows = $this->_mod_cart->drop(db_create_in($oldChoice , 'ident') . " AND user_id = '".$this->_user_id."'");
    	if (!$droped_rows)
    	{
    		$this->json_error("drop_error");
    		return;
    	}
    	$this->json_result(array(
    		),'clear_cart_successed');
    }
    
    /**
     * 更新购物车数量
     *
     * @author Ruesin
     */
    function update()
    {
        $ident = isset($_POST['id']) ? trim($_POST['id']) : '';
        $type  = isset($_POST['type'])? trim($_POST['type']): '';
        $num   = isset($_POST['num']) ? intval($_POST['num']) : 1;
        
        if(!$ident || !$num ){
            $this->json_error('update_params_error');
            return;
        }
        
        if(!in_array($type, $this->types)){
            $this->json_error('type_error');
            return;
        }
        
        $goods = &gt($type);
       
        if(!$goods->update(array('ident' => $ident, "quantity" => $num,"user_id" => $this->_user_id))){
            $this->json_error($goods->get_error());
            return;
        }
        
        $this->json_result(array(),'update_item_successed');
        
    }
	
    /*更新优惠方案
	*/
	function updata_favore(){
	
		 $fav_id = isset($_POST['fav_id']) ? trim($_POST['fav_id']) : '';
		
         $goods_id  = isset($_POST['goods_id'])? trim($_POST['goods_id']): '';
         $ident = isset($_POST['ident']) ? trim($_POST['ident']) : '';
       //  $num = isset($_POST['num']) ? trim($_POST['num']) : '';
		   $goods = &gt('custom');
		 
         $res=$goods->update(array('ident' => $ident,"user_id" => $this->_user_id,'fav_id' => $fav_id));
        if(!$res){
            $this->json_error($goods->get_error());
            return;
        }
		//更新优惠价格
		 $mod = m('cart');
		 $item = $mod ->get(array(
               'conditions' => "ident='{$ident}'",
            ));
	
		
		$price=$this->favoreprice($item);
        $this->json_result(array('favorprice'=>$price,),'更新优惠方案成功');
	}
	/*更新折扣方案
	 */
	function updata_zhek(){
	  
	    $zk_id = isset($_POST['zk_id']) ? trim($_POST['zk_id']) : '';
	    $goods_id  = isset($_POST['goods_id'])? trim($_POST['goods_id']): '';
	    $ident = isset($_POST['ident']) ? trim($_POST['ident']) : '';
	    //  $num = isset($_POST['num']) ? trim($_POST['num']) : '';
	    $goods = &gt('custom');
	   
	    $res=$goods->update(array('ident' => $ident,"user_id" => $this->_user_id,'zhek_id' => $zk_id));
	    if(!$res){
	        $this->json_error($goods->get_error());
	        return;
	    }
	    //更新优惠价格
	    $mod = m('cart');
	    $item = $mod ->get(array(
	        'conditions' => "ident='{$ident}'",
	    ));
	
	
	    $price=$this->zhekouprice($item);
	
	    $this->json_result(array('zhekprice'=>$price,),'更新优惠方案成功');
	}
	/*更新加优惠券后金额
	 */
	function updatequan(){
	     
	    $quan_id = isset($_POST['quan_id']) ? trim($_POST['quan_id']) : '';
	    $final_money = isset($_POST['final_money']) ? trim($_POST['final_money']) : '';
	    $newpromotion_mod =& m('newpromotion');
	    //更新优惠价格
	    if($quan_id && $final_money){
	        $promotions = $newpromotion_mod ->get(array(
	            'conditions' => "id='{$quan_id}'",
	        ));
	        if($final_money >= $promotions['yhcase_value']){
	            $price=$final_money-$promotions['yhcase_value2'];
	        }else{
	            $price=$final_money;
	        }
	    }else{
	        $price=$final_money;
	    }
	   
	   

	    $this->json_result(array('quanprice'=>number_format($price, 2).'元'));
	}
	//获取商品折扣方案价格
	function zhekouprice($cart){
	     
	    if($cart['type']=='diy'){
	        import("diys.lib");
	        $_Diys = new \Diys();
	        $diyRes = $_Diys->calcDiyPrice($cart['items'], $cart);   //价格计算
	
	        $goodsprice=$diyRes['baseprice'];
	        $num=$cart['quantity'];
	    }else{
	        $params=unserialize($cart['params']);
	        $goodsprice=$params["oProducts"]['price'];
	        $num=$cart['quantity'];
	    }
	     
	    $newpromotion_mod =& m('newpromotion');
	   
	    $newpros=$newpromotion_mod->get(array(
	        'conditions'=>"id='{$cart['zhek_id']}' AND is_open=1 AND starttime <= ".time()." AND endtime >= ".time(),
	    ));
	   
	   
	        $finprice=$goodsprice*$num;
	       
	    if($newpros){
	        
	        if($newpros['yhcase']=='1' &&  $newpros['yhcase_value'] <= $finprice ){
	            //折扣
	                return $finprice*$newpros['yhcase_value2']/100;
	        }else{
	            return $finprice;
	        }
	    }else{
	            return $finprice;
	        }
	}
	
	
	   /*
     * 获取商品优惠价格
     * 
     * */

    function favoreprice($cart){
       
        if($cart['type']=='diy'){
            import("diys.lib");
            $_Diys = new \Diys();
            $diyRes = $_Diys->calcDiyPrice($cart['items'], $cart);   //价格计算
		
			$goodsprice=$diyRes['baseprice'];
			$num=$cart['quantity'];
        }else{
            $params=unserialize($cart['params']);
			$goodsprice=$params["oProducts"]['price'];
			$num=$cart['quantity'];
        }
   
        $newpromotion_mod =& m('newpromotion');
        $newpros=$newpromotion_mod->get(array(
            'conditions'=>"id='{$cart['favorable_id']}' AND is_open=1 AND starttime <= ".time()." AND endtime >= ".time(),
        ));
      
        if($newpros){
           if($newpros['yhcase']=='6'){
               //满减
              
               $manjain=floor($goodsprice*$num/$newpros['yhcase_value']);
         
               if($manjain > 0){
                   return $goodsprice*$num-$manjain*$newpros['yhcase_value2'];
               }
           }elseif($newpros['yhcase']=='7'){
               //买送

               $maisong=floor($num/($newpros['yhcase_value']+1));
 
               if($maisong > 0){
                   return $goodsprice*$num-$maisong*$goodsprice;
               }
           }
        }
    }
    /**
     * 选择购物车项
     * 
     * @date 2015-8-25 下午1:33:02
     * @author Ruesin
     */
    function choice(){

        $ident = isset($_POST['id']) ? trim($_POST['id']) : '';
        $type  = isset($_POST['tp']) ? trim($_POST['tp']) : '';
        $ck    = isset($_POST['ck']) ? trim($_POST['ck']) : '';
     
        if(!$ident || !$ck ){
            $this->json_error('update_params_error');
            return;
        }

        if(!in_array($type, $this->_class_carts->types)){
            $this->json_error('type_error');
            return;
        }
//print_exit(11);
        $goods = Types::createObj($type);
      
        $choice = $goods->choice(array('ident' => $ident, "user_id" => $this->_user_id,'source_from'=>$this->_source_from),$ck);
        
//print_exit($choice);
        if(!$choice){
            $this->json_error($goods->get_error());
            return;
        }
        /* 在购物车选择商品时，将对应的ident存到$_SESSION['_cart']['check']中，如果全选$_SESSION['_cart']['choiceAll']=true*/
        if($ck == 'y'){
            $_SESSION['_cart']['check'] = array_merge((array)$_SESSION['_cart']['check'],$choice);
//            print_exit($_SESSION);
        }else{
            foreach ($choice as $val){
                unset($_SESSION['_cart']['check'][$val]);
            }
            if($_SESSION['_cart']['choiceAll']) unset($_SESSION['_cart']['choiceAll']);
        }

        $main = $this->_class_carts->main(['source_from'=>$this->_source_from]);
//        print_exit($_SESSION);
        $this->json_result(array(
            'goods_num'  => (isset($main['check_num']) && $main['check_num'] ) ? $main['check_num'] : 0,
            'goods_amount' => price_format($main['check_amount_m']),
            'order_amount' => price_format($main['check_amount']),
//                'amount'     => price_format($main['check_amount_m']),
        ),'操作成功!');

    }

    /**
     * 全选
     *
     * @date 2015-8-25 下午1:57:31
     * @author Ruesin
     */
    function choiceAll(){

        $ck    = isset($_POST['ck']) ? trim($_POST['ck']) : '';

        if(!$ck ){
            $this->json_error('update_params_error');
            return;
        }
        $is_check = 1;
        if($ck !== 'y'){
            $is_check = 0;
            unset($_SESSION['_cart']['check']);
            unset($_SESSION['_cart']['choiceAll']);
            $this->json_result(array(
                'goods_num'  => 0,
                'amount'     => price_format(0),
            ),'操作成功!');
            die();
        }
        $carts = $this->_mod_cart->find(array('conditions'=> " user_id = '{$this->_user_id}' and source_from = '{$this->_source_from}' ",'index_key'=>'ident'));

        if(!$carts){
            $this->json_error('全选失败!');
            die();
        }

        foreach ($carts as $key=>$val){
            $choice[$key] = $key;
            $this->_mod_cart->edit($val['rec_id'],['is_check'=>$is_check]);
        }

        $_SESSION['_cart']['check'] = $choice;
        $_SESSION['_cart']['choiceAll'] = true;
        $main = $this->_class_carts->main(['source_from'=>$this->_source_from]);

        $this->json_result(array(
            'goods_num'  => $main['goods_num'],
            'amount'     => price_format($main['goods_amount']),
        ),'操作成功!');
    }
    /**
     *    购物车为空
     *
     *    @return    void
     */
    function _cart_empty()
    {
        $this->display($this->_template_file.'empty.html');
    }
    
    /**
     * 刺绣
     * 
     * @date 2015-8-21 下午2:44:04
     * @author Ruesin
     */
    public function embs(){
        
        $id   = isset($_POST['id']) ? trim($_POST['id']) : 0;
        $type = isset($_POST['tp']) ? trim($_POST['tp']): '';
        
        if(!in_array($type, $this->types)){
            $this->json_error('type_error');
            return;
        }
        
        if(!$id){
            $this->json_error('update_params_error');
            return;
        }
        
        //$condition = $type == 'suit' ? " dis_ident = '{$id}' " : " ident = '{$id}' " ;
        $condition = " dis_ident = '{$id}' "  ;
        
        $carts = $this->_mod_cart->find(array(
                'conditions' => $condition . " AND user_id = '{$this->_user_id}'",
                'fields' => 'ident,dis_ident,cloth,embs,f_cloth',
                'index_key'  => 'ident',
        ));
        
        foreach ($carts as &$row){
            $row['embs'] = json_decode($row['embs'],1);
        }

        $customs = $this->_mod_cart->_customs();
        if ($type == 'fdiy'){
            import('shopCommon');
            $embs = ShopCommon::embsNew();
            $result = array();
            foreach ($carts as $key=>$val){
                if ($val['cloth'] == '0004' || $val['cloth'] == '0017') continue;
                
                $val['embitems'] = $embs[$val['f_cloth']][$val['cloth']]['list'];
                $val['cate_name'] = $customs[$val['cloth']]['cate_name'];
                $result[$key] = $val;
            }
            
            $this->assign('info',$result);
            $content = $this->_view->fetch('cart/embs_fdiy.html');
        }else{

            $this->assign('list',$carts);
            $this->assign('cloths',$customs);
            $this->assign('embs',$this->_mod_cart->_format_embs());
            $content = $this->_view->fetch('cart/embs.html');
        }
        
        
        $this->json_result(array('content' => $content));
    }
    
    /**
     * 保存刺绣信息
     * 
     * @date 2015-8-24 下午4:24:52
     * @author Ruesin
     */
    function embsSave(){
        if(!IS_POST){
            $this->json_error("params_error");
            return;
        }
        $ident = isset($_POST['id']) ? trim($_POST['id']) : '';
        
        if( !$_POST['embs']){
            $this->json_error('请录入正确的数据!');
            die();
        }
        foreach ($_POST['embs'] as $key=>$val){
            $idents[$key] = $key;
        }
        
        $cart = $this->_mod_cart->find(array(
                "conditions" => db_create_in($idents,'ident') ." AND user_id = '{$this->_user_id}'",
                "index_key"  => 'ident', 
        ));
        
        if(!$cart || count($idents) !== count($cart)){
            $this->json_error('请录入正确的数据!');
            die();
        }
        
        if (isset($_POST['type']) && $_POST['type'] == 'fdiy'){
            return $this->saveEmbs($_POST, $cart);
        }
                
        $embs = $this->_mod_cart->_format_embs();
        
        // 保存刺绣信息
        foreach ((array)$_POST['embs'] as $key => $row) {
            
            $er = 0;
            
            if (count($row) !== 4){
                $er = 1;
            }
            
            foreach ($row as $k => $v) {
                
                if($er) break;
                
                if ($v) {
                    
                    // 不存在父级 //三类通用
                    if (!$embs[$cart[$key]['cloth']][$k]) {
                        $er = 1;
                        break;
                    }
                    // 有父级 && 没有list || 有list并且有值
                    if (is_array($v)) { // 是数组 校验位置
                        //刺绣位置单选
                        if(count($v) >1 ){
                            $er = 1;
                            break;
                        }
                        
                        if (!$embs[$cart[$key]['cloth']][$k]['list'] || $embs[$cart[$key]['cloth']][$k]['statusid'] != '10002') {
                            $er = 1;
                            break;
                        }
                        foreach ($v as $vv) {
                            if (!$embs[$cart[$key]['cloth']][$k]['list'][$vv]) {
                                $er = 1;
                                break;
                            }
                            $data[$k][$vv] = $vv;
                        }
                    } else {
    
                        // 有list但是没有值
                        if ($embs[$cart[$key]['cloth']][$k]['list'] && !$embs[$cart[$key]['cloth']][$k]['list'][$v]) {
                            $er = 1;
                            break;
                        }
    
                        if (!intval($v)){
    
                            if(strlen(iconv("UTF-8", "GB2312//IGNORE", $v)) > 14){
                                $this->json_error('刺绣文字长度过长!');
                                die();
                            }
                        }
                        $data[$k] = $v;
                    }
                } else {
                    $er = 1;
                    break;
                }
            }
    
            //if ($er) continue;
            if ($er) $data = array();
            if ($data)
                $res = $this->_mod_cart->edit(" ident = '{$key}' ", array('embs' => json_encode($data,JSON_UNESCAPED_UNICODE)));
            else 
                $res = 0;
            $data = array();
            if ($res < 0) break;
        }
        
        if ($res >= 0) {
            $this->json_result(array('res'=>$res), 'successed');
            return;
        } else {
            $this->json_error('check_error');
            return;
        }        
    }
    
    
    public function saveEmbs ($post, $cart )
    {
        import('shopCommon');
        $embs = ShopCommon::embsNew();
        if (count($cart) > 1){
            $fcloth = '0001';
        }else{
            $tCart = reset($cart);
            if ($tCart['dis_ident'] != $tCart['ident']){
                $fcloth = '0001';
            }else{
                $fcloth = $tCart['cloth'];
            }
        }
        
        if (!isset($embs[$fcloth])) {
            $this->json_result(array('res'=>$res), 'successed');
            return true;
        }
    
        $emb = $embs[$fcloth];

        $res = 1;
        foreach ($post['embs'] as $key => $row) {
            if (count($row) < 4)  continue;
            $er = 0;
            foreach ($row as $k => $v) {
                if ($k == -1){
                    // 内容
                    if (!isset($son['list'])) {
                        if (strlen(iconv("UTF-8", "GB2312//IGNORE", $v)) > 14) {
                            $this->error = '刺绣文字长度过长!';
                            return false;
                        }
                    }
                    
                    $data[$k] = $v;
                    continue;
                }
                if ($v) {
                    $son = array();
                    // 不存在父级 //三类通用
                    if (! isset($emb[$cart[$key]['cloth']]['list'][$k])) {
                        $er = 2;
                        break;
                    }
    
                    $son = $emb[$cart[$key]['cloth']]['list'][$k];  //颜色  || 字体 || 位置
    
                    // 有父级 && 没有list || 有list并且有值
    
                    if (is_array($v)) { // 是数组 校验位置
    
                        // 2015-07-08 15:09:13 刺绣位置单选!
                        if (count($v) > 1) {
                            $er = 3;
                            break;
                        }
    
                        if (! isset($son['list']) || $son['cate_name'] != '位置') {
                            $er = 4;
                            break;
                        }
    
                        foreach ($v as $vv) {
                            if (! $son['list'][$vv]) {
                                $er = 5;
                                break;
                            }
                            $data[$k][$vv] = $vv;
                        }
                    } else {
    
                        // 有list但是没有值 // 颜色 字体
                        if (isset($son['list']) && ! $son['list'][$v]) {
                            $er = 6;
                            break;
                        }
    
                        $data[$k] = $v;
                    }
                } else {
                    $er = 10;
                    break;
                }
            }
    
            if ($er > 0) continue;
        
            if ($data)
                $res = $this->_mod_cart->edit(" ident = '{$key}' AND user_id = '{$this->_user_id}'", array('embs' => json_encode($data,JSON_UNESCAPED_UNICODE)));
            else
                $res = 0;
            
            $data = array();
            if ($res < 0)break;
        }
        
        if ($res >= 0) {
            $this->json_result(array('res'=>$res), 'successed');
            return true;
        }
        
        $this->error = '保存刺绣失败!';
        return false;
    }
    
    
    
    
    /**
     * 校验提交确认页(还未做各种活动等的验证)
     * 
     * @date 2015-8-26 上午8:19:46
     * @author Ruesin
     */
    function check(){
	
        if(empty($_SESSION['_cart']['check']))
        {
            $this->json_error("请选择商品");
            return;
        }
        $this->json_result(array(),'successed');
        return ;

    	if(!IS_POST){
    		$this->json_error("params_error");
    		return;
    	}
    	$cart = $this->_cart_check(1);
    	
    	if($cart['goods_num'] <= 0){
    		$this->json_error("params_error");
    		return;
    	}
    	
	    $this->json_result(array(),'successed');
	    return ;
    }
    
    /**
     * 订单确认页
     *
     * @author Ruesin
     */
    function checkout(){
		
        $aCart = $this->_cart_check();
		//var_dump($aCart);exit;
        if(!$aCart['object']){
            $view = &v();
            $url = $view->build_url(array("app" => "cart"));
            header('Location:'.$url);
            $this->_cart_empty();
            return;
        }
        import('shopCommon');
        import('shopConf');
        if(empty($aCart['cloth_quan'])){
        }else{
//       echo '<pre>';print_r($aCart);exit;
            
            $debit = ShopCommon::debits($aCart);
            $this->assign('debits',array('count'=>count($debit),'data'=>$debit));
        }
        //商品优惠方案
       // $active_lists = ShopCommon::active_lists($goods_id);
        $favorables_yh=$this->get_goods_favorable_yh($_SESSION['user_info']);
        $this->assign('favorables_yh',$favorables_yh);
        //酷卡
        $member = ShopCommon::userInfo();
        $aCart = ShopCommon::totalData($order, $aCart);

        //会员信息
        $this->assign('member',$member);
        
        //物流公司
        $addRess = ShopCommon::addressList();
        
        if (isset($member['def_addr']) && isset($addRess[$member['def_addr']]))
        {
            $defAddRess = $addRess[$member['def_addr']];
        }else{
            $defAddRess = current($addRess);
        }
        imports("orders.lib");
        $orderLib = new Orders();
        $shipInit = $orderLib->getShipInit(['ship_area_id'=>$defAddRess['region_id'],'weight'=>$aCart['weight']]);
        $this->assign('ships',$shipInit['shippings']);
        $this->assign('defShips',$shipInit['defship']);

        $aCart['order_amount'] += $shipInit['defship']['post_fee'];
        $aCart['final_amount'] += $shipInit['defship']['post_fee'];

        //收货地址
        $this->assign('addressList',$addRess);
        //支付方式
        $this->assign("payments", ShopCommon::payments());
        
        imports("orders.lib");
        $orderLib = new Orders();
        $ship_init = $orderLib->getShipInit(['ship_area_id'=>'']);

		$this->_config_seo('title', Lang::get('confirm_order') . ' - ' . Conf::get('site_title'));
		$this->assign('cart', $aCart);
		
    	$this->display('cart/checkout.html');
    }
    
    /**
     * 抵扣券列表
     * 
     * @date 2015-8-26 上午9:57:16
     * @author Ruesin
     */
    function getDebits($cloth = array()){
        
        if(empty($cloth)) return false;
        
        $mDbt = &m('debit');
        $time = gmtime();
         
        $debits = $mDbt->find(" user_id = '{$this->_user_id}' AND is_used = '0' AND is_invalid = '0' AND expire_time >'{$time}' AND ".db_create_in($cloth,'cate'));
        
        foreach ($debits as $dKey=>$dRow){
            $dRow['cate'] = explode(',', $dRow['cate']);
            foreach ($cloth as $cKey=>$cVal){
                if (in_array($cVal, $dRow['cate'])){
                    $debit[$dKey] = $dRow;
                }
            }
        }
        
        $customs = $this->_mod_cart->_customs();
        foreach ((array)$debit as $row){
            //$data[$row['cate']][$row['id']] = $row;
            foreach ($row['cate'] as $cKey=>$cVal){
                if (in_array($cVal, $cloth)){
                    $data[$cVal]['cateName'] = $customs[$cVal]['cate_name'];
                    $data[$cVal]['list'][$row['id']] = $row;
                    break;
                }
            }
        }
        
        return array(
                'count' => count($debit),
                'data'  => $data,
        );
        
    }
    
    /**
     * 根据地区获取服务点
     * 
     * @date 2015-8-26 上午11:23:30
     * @author Ruesin
     */
    function getServer(){
        
    	$region_id = isset($_POST['id']) ? intval($_POST['id']) : '';
    	$server_id = isset($_POST['sv']) ? intval($_POST['sv']) : 0;
    	
    	$regions = $this->getServeRegion();
    	if (!$region_id || !array_key_exists($region_id, $regions)){
    		$this->json_error('所选地区有误!');
    		return;
    	}
   
    	$msv = &m('serve');
        $servers = $msv->find("region_id = '{$region_id}' AND virtual = 0 AND shop_type IN (1,2)");
        
        $this->assign('servers',$servers);
        $this->assign('server_id',$server_id);

    	$content = $this->_view->fetch($this->_template_file."figure/server.html");
    	$this->json_result(array(
    			'content'      =>  $content,
    	),'success');
    	die();
    }
    
    /**
     * 获取地址列表
     * 
     * @date 2015-10-23 下午3:59:58
     * @author Ruesin
     */
    function addressList(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        $mAddress = &m("address");
        $data = $mAddress->find(" user_id = '{$this->_user_id}' ");
        $this->assign('data',$data);
        if (!isset($data[$id])) $id = 0;
        $this->assign('id',$id);
        $content = $this->_view->fetch($this->_template_file."address/list.html");
        $this->json_result(array(
                'content'      =>  $content,
        ),'success');
        die();
    }
    
    /**
     * 收货地址
     * 
     * @date 2015-8-27 上午9:01:05
     * @author Ruesin
     */
    function address(){
    	$id = isset($_POST['id']) ? intval($_POST['id']) : 0 ;
    	if($id){
    	    $mAddress = &m("address");
	        $data = $mAddress->get("user_id = '{$this->_user_id}' AND addr_id = '{$id}'");
	        $this->assign('data',$data);
    	}
    	$content = $this->_view->fetch($this->_template_file."address.html");
    	$this->json_result(array(
    	        'content'      =>  $content,
    	),'success');
    	die();
        $this->display($this->_template_file."address.html");
    }
    
    /**
     * 保存收货地址
     * 
     * @date 2015-8-27 下午1:37:36
     * @author Ruesin
     */
    function addressSave(){
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if(strlen($_POST['user_name']) > 18){
            $this->json_error('收货人姓名超长!');
            die();
        }
        
        //验证手机号
        if(!preg_match("/^1[34578]\d{9}$/", $_POST['user_phone'])){
            $this->json_error('手机号错误!');
            die();
        }
        
        //验证地区
        if(!$_POST['area_id'] || !$_POST['area_name'] || count(explode(',', $_POST['area_id'])) != 3 ){
            $this->json_error('请正确选择地区信息!');
            die();
        }
        $model_region =& m('region');
        $_regions = $model_region->find(db_create_in($_POST['area_id'],"region_id"));
        
        $rNames = explode("\t", $_POST['area_name']);
        $rIds   = explode(",", $_POST['area_id']);
        $parent_id = 0;
        foreach ($rIds as $key=>$val){
            if($_regions[$val]['region_name'] != $rNames[$key]){
                $this->json_error('请正确选择地区信息!');
                die();
            }
            if($_regions[$val]['parent_id'] != $parent_id){
                $this->json_error('请正确选择地区信息!');
                die();
            }
            $parent_id = $val;
        }
        
        $data = array(
                'consignee'   => trim($_POST['user_name']),
                'phone_mob'   => trim($_POST['user_phone']),
                'region_id'   => trim($_POST['area_id']),
                'region_name' => trim($_POST['area_name']),
                'address'     => trim($_POST['user_adress']),
                'zipcode'     => trim($_POST['user_zipcode']),
        );
        
        $mAddress = &m("address");
        if($id){
            $where = " user_id = '{$this->_user_id}' AND addr_id = '{$id}' ";
            $res = $mAddress->edit($where,$data);
        }else{
            
            $data['al_name'] = '收货地址';
            $data['user_id'] = $this->_user_id;
            $id = $res = $mAddress->add($data);
        }
        if ($res < 0){
            $this->json_error('提交失败!');
            die();
        }
        
        if ($id > 0){
            $data = $mAddress->find(" user_id = '{$this->_user_id}' ");
            $this->assign('data',$data);
            if (!isset($data[$id])) $id = 0;
            $this->assign('id',$id);
            $content = $this->_view->fetch($this->_template_file."address/list.html");
            $this->json_result(array(
                    'content'      =>  $content,
            ),'success');
            die();
        }
        
        $this->json_error('未保存地址!');
        die();
    }
    
    /**
     * 删除收货地址
     * 
     * @date 2015-8-27 下午2:16:46
     * @author Ruesin
     */
    function addressDrop(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if(!$id){
            $this->json_error('请正确选择要删除的地址!');
            return;
        }
        $mAddress = &m("address");
        $res = $mAddress->drop(" user_id = '{$this->_user_id}' AND addr_id = '{$id}' ");
        
        if ($res <= 0){
            $this->json_error('删除收货地址失败!');
            die();
        }
        $this->json_result('成功删除收货地址!');
        die();
    }
    
    /**
     * 抵扣券使用规则
     * 
     * @date 2015-10-22 下午1:30:08
     * @author Ruesin
     */
    function debit_rules(){
        $mArticle = &m('article');
        $content = $mArticle->get("code = '优惠券使用说明'");
        $this->assign('data',$content);
        $this->display('cart/debit_rules.html');
    }
    

    ####################################################################
    ######################## Diy 量体前置 Bgn ###########################
    ####################################################################
    /**
     * 量体方式列表
     * 
     * @date 2015-10-13 上午9:50:46
     * @author Ruesin
     */
    function measureList(){
        $measure_way = $this->_measure_way;
        $measure_way['-1'] = '操作记录';
        $this->json_result($measure_way);
        die();
    }
    
    /**
     * 量体方式
     * 
     * @date 2015-10-13 上午9:57:51
     * @author Ruesin
     */
    
    function measure(){
        $params = $this->getParams();
        $id = isset($params['id']) ? intval($params['id']) : 0;
        if(!$id){
            $this->json_error('量体方式选择错误!');
            die();
        }
        //unset($_SESSION['_order']['measure']);
        /* $measure = $_SESSION['_order']['measure'];
        
        if($measure['data'][$id]){
            $this->assign('data',$measure['data'][$id]);
        } */
        
        if($id == 1 || $id == 2 || $id == 6){
            $regions = $this->getServeRegion();
            if($id == 6){
                foreach ($regions as $row){
                    $rIds[$row['region_id']] = $row['region_id'];
                }
                $mServe = &m('serve');
                $server = $mServe->find(array(
                        'conditions' => db_create_in($rIds,'region_id') . "  AND shop_type IN (1,2)",
                        'index_key'  => 'region_id'
                ));
                foreach ($regions as $key=>$row){
                    if(!$server[$key]){
                        unset($regions[$key]);
                    }
                }
            }
            $result['region'] = $regions;
        }elseif($id == -1){
            
            $history = json_decode(base64_decode($_COOKIE["suit_history"]),1);
            if(empty($history)){
                $this->json_error('咱无操作记录');
                die();
            }
            $his = $svs = $lts = array();
            foreach((array)$history as $key => $val){
                if($val['figuretype'] == 5){
                    $his[] = $val["figureid"];  //已有id
                }elseif($val["figuretype"] == 2){
                    $svs[] = $val["serveid"];    //门店id
                }elseif($val['figuretype'] == 6){
                    $lts[] = $val['figurerid'];  //量体师
                }
            }
            
            //已有量体
            $aData = array();
            
            if (!empty($his)){
                $mCf = &m("customer_figure");
                $cstFigure = $mCf->find(db_create_in($his,'figure_sn'));
                foreach((array)$cstFigure as $key => $val){
                    $svs[$val["id_serve"]]   = $val["id_serve"];
                }
            }
            
            $mServe = &m("serve");
            $servers = $mServe->find(db_create_in($svs,'idserve'));
            
            foreach((array)$cstFigure as $key=>$row){
                $cstFigure[$key]['serve_name'] = isset($servers[$row["id_serve"]]) ? $servers[$row["id_serve"]]['serve_name'] : '';
            }
            
            $mLiangti =& mr('figure_liangti');
            $liangti = $mLiangti->find(db_create_in($lts,'liangti_id') ." AND alone = '1' ");
            
            foreach ($liangti as $key=>$row){
                $mngs[$row['manager_id']] = $row['manager_id'];
                $liangtishi[$row['liangti_id']] = $row;
            }
            
            $mServe = &m('serve');
            $server = $mServe->find(db_create_in($mngs,'userid') . " AND shop_type IN (1,2)"); // AND virtual = '0' // AND region_id = '{$data['region_id']}'
            foreach ($server as $row){
                $serveByManage[$row['userid']] = $row;
            }
            foreach ($history as $key=>$row){
                if($row['figuretype'] == 5){
                    $result['history'][$key]['name']   = $cstFigure[$row['figureid']]['customer_name'];
                    $result['history'][$key]['phone']  = $cstFigure[$row['figureid']]['customer_mobile'];
                    $result['history'][$key]['region'] = $cstFigure[$row['figureid']]['serve_name'];
                }elseif($row["figuretype"] == 2){
                    $result['history'][$key]['name']   = $row['realname'];
                    $result['history'][$key]['phone']  = $row['phone'];
                    $result['history'][$key]['region'] =  $server[$row['serveid']]['serve_name'];
                }elseif($row['figuretype'] == 6){
                    $result['history'][$key]['name']   = $row['realname'];
                    $result['history'][$key]['phone']  = $row['phone'];
                    $result['history'][$key]['region'] =  $serveByManage[$liangtishi[$row['figurerid']]['manager_id']]['serve_name'];
                }
            }
        }
        $this->json_result($result);
        die();
    }
    
    /**
     * 门店
     * 
     * @date 2015-10-13 上午10:49:55
     * @author Ruesin
     */
    function ajaxServer(){
        
        $region_id = isset($_GET['id']) ? intval($_GET['id']) : '';
        $server_id = isset($_GET['sv']) ? intval($_GET['sv']) : 0;
         
        $regions = $this->getServeRegion();
        if (!$region_id || !array_key_exists($region_id, $regions)){
            $this->json_error('所选地区有误!');
            return;
        }
         
        $msv = &m('serve');
        $servers = $msv->find("region_id = '{$region_id}'");
        
        foreach ($servers as $key=>$row){
            $result['servers'][$key]['id'] = $row['idserve'];
            $result['servers'][$key]['name'] = $row['serve_name'];
            $result['servers'][$key]['address'] = $row['serve_address'];
            $result['servers'][$key]['phone'] = $row['store_mobile'];
//             $result['servers'][$key]['linkman'] = $row['linkman'];
//             $result['servers'][$key]['mobile'] = $row['mobile'];
//             $result['servers'][$key]['business_time'] = $row['business_time'];
        }
        
        $this->json_result($result,'success');
        die();
        
    }
    
    /**
     * 获取已有量体
     *
     * @date 2015-8-26 下午2:20:53
     * @author Ruesin
     */
    function ajaxFigured(){
        $val   = isset($_GET['val']) ? trim($_GET['val']) : '';
        $ckid  = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        $condition = " storeid = '{$this->_user_id}' ";
        
        if($val != ''){
            $condition = " (customer_mobile = '{$val}' OR customer_name = '{$val}') "; //(customer_mobile LIKE '%{$phone}%' OR customer_name LIKE '%{$phone}%')
        }
        $condition .= " AND figure_state = 1 ";
        
        $mCf  = &m('customer_figure');
        $list = $mCf->find($condition);
        
        foreach ($list as $key=>$row){
            $svs[$row['id_serve']] = $row['id_serve'];
        }
        $mServe = &m('serve');
        $serves = $mServe->find(db_create_in($svs,'idserve'));
        foreach ($list as $key=>$row){
            $result['list'][$key]['id'] = $key;
            $result['list'][$key]['name'] = $row['customer_name'];
            $result['list'][$key]['phone'] = $row['customer_mobile'];
            $result['list'][$key]['liangti_id'] = $row['liangti_id'];
            $result['list'][$key]['liangti_name'] = $row['liangti_name'];
            $result['list'][$key]['serve_name'] = $serves[$row['id_serve']]['serve_name'];
            $result['list'][$key]['serve_id'] = $row['id_serve'];
        }
        
        $this->json_result($result,'success');
        die();
    }
    /**
     * 根据地区获取可独立指定的量体师
     * 
     * @date 2015-10-13 下午1:34:28
     * @author Ruesin
     */
    function ajaxFigurer(){
        
        $region_id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if(!$region_id){
            $this->json_error('请选择正确的地区!');
            die();
        }
        
        $mRegion = &m('region');
        $result['region'] = $mRegion->get($region_id);
        
        $mServe = &m('serve');
        $server = $mServe->find(array(
                'conditions' => "region_id = '{$region_id}' AND shop_type IN (1,2)", //AND virtual = '0'
                'index_key'  => 'userid',
        ));
        if(!$server){
            $this->json_error('该地区下无门店!');
            die();
        }
        
        foreach ($server as $row){
            $dzIds[$row['userid']] = $row['userid'];
        }
        
        $mLiangti =& mr('figure_liangti');
        
        $liangti = $mLiangti->find(array(
                'conditions' => db_create_in($dzIds,'manager_id') . " AND alone = '1' ",
                'index_key'  => 'liangti_id',
        ));
        if(!$liangti){
            $this->json_error('该地区下无可指定量体师!');
            die();
        }
        
        foreach ($liangti as $row){
            $uIds[$row['liangti_id']] = $row['liangti_id'];
            $svIds[$row['manager_id']] = $row['manager_id'];
        }
        
        $mServe = &m('serve');
        $serve = $mServe->find(array(
                'conditions' => db_create_in($svIds,'userid') . "   AND shop_type IN (1,2)", //AND virtual = '0'
                'index_key'  => 'userid',
        ));
        
        $mUser = &m('member');
        $users = $mUser->find(array(
                'conditions' => db_create_in($uIds,'user_id'),
        ));
        
        foreach ($liangti as $rows){
            //$row['info'] = $users[$row['liangti_id']];
            //$row['seve'] = $serve[$row['manager_id']];
            if(isset($users[$rows['liangti_id']]) && isset($serve[$rows['manager_id']])){
                $liangtis[$rows['liangti_id']] = $rows;
                $liangtis[$rows['liangti_id']]['info'] = $users[$rows['liangti_id']];
                if ($users[$rows['liangti_id']]['avatar']){
                    $liangtis[$rows['liangti_id']]['info']['avatar'] = SITE_URL.$users[$rows['liangti_id']]['avatar'];
                }
                $liangtis[$rows['liangti_id']]['seve'] = $serve[$rows['manager_id']];
            }
        }
        foreach($liangtis as $key=>$row){
            $result['liangtis'][$key]['realname'] = $row['info']['real_name'];
            $result['liangtis'][$key]['nickname'] = $row['info']['nickname'];
            //$result['liangtis'][$key]['user_name'] = $row['info']['user_name'];
            $result['liangtis'][$key]['phone_mob'] = $row['info']['phone_mob'];
            //$result['liangtis'][$key]['phone_tel'] = $row['info']['phone_tel'];
            $result['liangtis'][$key]['avatar'] = $row['info']['avatar'];
            $result['liangtis'][$key]['serve_name'] = $row['seve']['serve_name'];
            $result['liangtis'][$key]['address'] = $row['seve']['serve_address'];
            //$result['liangtis'][$key]['store_mobile'] = $row['serve']['store_mobile'];
            //$result['liangtis'][$key]['linkman'] = $row['serve']['linkman'];
            $result['liangtis'][$key]['serve_id'] = $row['seve']['idserve'];
        }
        
        //$result['liangtis'] = $liangtis;
                        
        $this->json_result($result);
        die();
    }
    
    ####################################################################
    ######################## Diy 量体前置 End ###########################
    ####################################################################
}

