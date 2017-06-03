<?php
use Cyteam\Shop\Cart;
use Cyteam\Shop\Cart\Carts;
use Cyteam\Shop\Shop;
use Cyteam\Shop\Type;
use Cyteam\Shop\Type\Types;
class CartApp extends ShoppingbaseApp
{
	var $_mod_cart;
	var $_class_carts;
	var $_user_id;
	var $_template_file = 'cart/';
	function __construct()
	{  
	
		$this->_mod_cart   =& m('cart');
		$this->_class_carts = new Carts();
		 $this->_newpromotion_mod =& m('newpromotion');
        $this->_goodstype_mod =& m('goodstype'); //商品类型
        $this->_gcategory_mod =& bm('gcategory', array('_store_id' => 0));//商品分类
        $this->_link_mod =& m('goods_prorel');
        $this->_goodslink_mod = &m('goods_prolink');
        $this->_user_mod =& m('member');
		$this->_goods_mod =& m('goods');
        $this->_lv_mod =& m('memberlv');
		parent::__construct();
//		$user = $this->visitor->get();
        $user = $_SESSION['user_info'];

		$this->_user_id = $user['user_id'];

		if (!$this->_user_id)
		{
		    $this->json_error('_user_id不存在');
		    return;
		}
		

	}
    /**
     * 购物车列表
     *
     * @author Ruesin
     */
    function index()
    {
       $_mod_member = &m('member');
    	$mInfo = $_mod_member->get($this->_user_id);
        $main = $this->_class_carts->main(['source_from'=>$this->_source_from]);
       
        $this->_curlocal( LANG::get('cart'));
        $this->_config_seo('title', Lang::get('confirm_goods') . ' - ' . Conf::get('site_title'));
        if (empty($main['object'])){
            $this->_cart_empty();
            return;
        }
        $this->import_resource(array(
        		'script' => 'json.js,cart.js,art/artDialog.source.js,art/box.js',
        		'style'  => "art/default.css"
        ));
      //商品的优惠方案
		if($main['object']){
			
			foreach($main['object'] as $key=>$val){
					
				if($val['goods']['goods_id']){
				
					
					$favorables_mj=$this->get_goods_favorable_mj($val['goods']['goods_id'],$mInfo['member_lv_id']);
					$favorables_zk=$this->get_goods_favorable_zk($val['goods']['goods_id'],$mInfo['member_lv_id']);
				}
		
				if($favorables_mj){
				   $main['object'][$key]['favorable_mj']=$favorables_mj;
			    }else{
					$main['object'][$key]['favorable_mj']='';
				}
				if($favorables_zk){
				    $main['object'][$key]['favorable_zk']=$favorables_zk;
				}else{
				    $main['object'][$key]['favorable_zk']='';
				}
			}
			
		}

        if($_SESSION['_cart']['choiceAll'] && !empty($_SESSION['_cart']['check'])) $this->assign('choiceAll',$_SESSION['_cart']['choiceAll']);
        $this->assign('cart', $main);
        $this->assign('tk', $_REQUEST['token']);
        $this->assign('title','购物车');
        $this->display('cart/index.html');
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
                  
						'conditions' =>"d_id='{$val['id']}' AND c_id = '{$goods_id}'",
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
	 //商品满减的优惠方案
   function get_goods_favorable_mj($goods_id,$member_lv_id){
     
	   //商品信息
	   $goods_info=$this->_goods_mod->get(array(
	          'conditions'=>'goods_id='.$goods_id,
		   ));
	   //可用优惠
	   if($member_lv_id){
	   $newpromotions= $this->_newpromotion_mod->find(array(
		           'conditions'=>'(yhcase=6 or yhcase=7) AND is_open=1 AND starttime <= '.time().' AND endtime >= '.time().' AND FIND_IN_SET('.$member_lv_id.',member_lv_id) ',
				   'order'     =>'level asc,id desc',
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
					     
						  'conditions' =>"d_id='{$val['id']}' AND c_id = '{$goods_id}'",
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

	  
      /*更新折扣方案
       */
      function updata_zhek(){
        
         
          $type  = isset($_POST['tp'])? trim($_POST['tp']): 'custom';
            $zk_id = isset($_POST['zk_id']) ? trim($_POST['zk_id']) : '';

          $ident = isset($_POST['ident']) ? trim($_POST['ident']) : '';

          $goods = Types::createObj($type);
      
          if(!$goods->update(array('ident' => $ident,"user_id" =>$this->_user_id,'zhek_id' => $zk_id))){
      
              $this->json_error($goods->get_error());
              return;
          }
           
      
          	
           
          $main = $this->_class_carts->main(['source_from'=>$this->_source_from]);
      
          foreach ((array)$main['object'] as $row){
              if($row['ident'] == $ident){
                  $return['quantity'] = $row['quantity'];
                  $return['subtotal'] = $row['subtotal_m'];
                  break;
              }
          }
      
          $this->json_result(array(
              'goods_num'    => $main['goods_num'],
              'goods_amount' => price_format($main['goods_amount']),
              'order_amount' => price_format($main['goods_amount']),
              'quantity'     => $return['quantity'],
              'subtotal'     => price_format($return['final_amount']),
          ),'update_item_successed');
      
      
          //更新优惠价格
          /* $mod = m('cart');
           $item = $mod ->get(array(
           'conditions' => "ident='{$ident}'",
           ));
      
      
           $price=$this->favoreprice($item);
           $this->json_result(array('favorprice'=>$price,),'更新优惠方案成功');*/
      }
      
      
      
	     /*更新优惠方案
	*/
	function updata_favore(){

	     $type  = isset($_POST['tp'])? trim($_POST['tp']): 'custom';
		 $fav_id = isset($_POST['fav_id']) ? trim($_POST['fav_id']) : '';
         //$goods_id  = isset($_POST['goods_id'])? trim($_POST['goods_id']): '';
         $ident = isset($_POST['ident']) ? trim($_POST['ident']) : '';
         //$num = isset($_POST['num']) ? trim($_POST['num']) : '';
		
		     $goods = Types::createObj($type);
        if(!$goods->update(array('ident' => $ident,"user_id" =>$this->_user_id,'fav_id' => $fav_id))){
		
            $this->json_error($goods->get_error());
            return;
        }
     
		
			
       
        $main = $this->_class_carts->main(['source_from'=>$this->_source_from]);
  
        foreach ((array)$main['object'] as $row){
            if($row['ident'] == $ident){
                $return['quantity'] = $row['quantity'];
                $return['subtotal'] = $row['subtotal_m'];
                break;
            }
        }
    
        $this->json_result(array(
                'goods_num'    => $main['goods_num'],
                'goods_amount' => price_format($main['goods_amount']),
                'order_amount' => price_format($main['goods_amount']),
                'quantity'     => $return['quantity'],
                'subtotal'     => price_format($return['final_amount']),
        ),'update_item_successed');
		
		
		//更新优惠价格
		/* $mod = m('cart');
		 $item = $mod ->get(array(
               'conditions' => "ident='{$ident}'",
            ));
	
		
		$price=$this->favoreprice($item);
        $this->json_result(array('favorprice'=>$price,),'更新优惠方案成功');*/
	}
    /**
     * 加入购物车
     *
     * @author Ruesin
     */
    function add()
    {
        /* 普通商品加入购物车 */
    	$type = $_POST['type'] ? $_POST['type'] :"custom";
        $is_check = isset($_REQUEST['mfd_cart_is_check']) ? $_REQUEST['mfd_cart_is_check'] : 0;
		if(!in_array($type, $this->_class_carts->types)){
			$this->json_error('type_error');
			return;
		}
        if($is_check)
        {
            $this->_mod_cart->drop("user_id={$this->_user_id} ");
            $this->_mod_cart->drop("user_id={$this->_user_id} ");
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
		    $cat_info = $this->getCart($res);
            if($is_check && $cat_info)
            {
                $this->_mod_cart->edit("user_id={$this->_user_id} AND rec_id = ".$cat_info['rec_id'],['is_check'=>1]);
            }
			$main = $this->_class_carts->main($post);

			$this->json_result(array(
					'goods_num'   => $main['goods_num'],
			        'amount'      => price_format($main['goods_amount']),
			), 'addto_cart_successed'); 
			
		}else{
   			$this->json_error($goods->get_error());
   			return;
   		}
    }

    function getCart($id)
    {
        if(!$id)
        {
            return;
        }
        $cart = m('cart');
        $cart_info = $cart->get_info($id);
        return $cart_info;
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
        
        if(!in_array($type, $this->_class_carts->types)){
            $this->json_error('type_error');
            return;
        }
        
        $goods = Types::createObj($type);
        
        $choice = $goods->choice(array('ident' => $ident, "user_id" => $this->_user_id,'source_from'=>$this->_source_from));
        if(!$choice){
            $this->json_error($goods->get_error());
            return;
        }
        
        if(!$goods->drop(array('ident' => $ident, "user_id" => $this->_user_id,'source_from'=>$this->_source_from))){
            $this->json_error($goods->get_error());
            return;
        }
        
        foreach ((array)$choice as $val){
            if (isset($_SESSION['_cart']['check'][$val]))
                unset($_SESSION['_cart']['check'][$val]);
        }
        if(empty($_SESSION['_cart']['check']) && isset($_SESSION['_cart']['choiceAll'])){
            unset($_SESSION['_cart']['choiceAll']);
        }
        
        $main = $this->_class_carts->main(['source_from'=>$this->_source_from]);
        
        $this->json_result(array(
                'goods_num' => $main['check_num'] ? $main['check_num'] : 0,
                'amount'    => price_format($main['check_amount_m']),
        ),'drop_item_successed');
    }
    
    /**
     * 清空购物车
     * 
     * @date 2015-8-21 下午2:43:35
     * @author Ruesin
     */
    function clear(){
        
//        if(!$_SESSION['_cart']['check']){
//            $this->json_error('请选择要删除的商品');
//            die();
//        }
        
    	$droped_rows = $this->_mod_cart->drop(" is_check=1 AND user_id = '".$this->_user_id."'");

    	if (!$droped_rows)
    	{
    		$this->json_error("drop_error");
    		return;
    	}
    	$this->json_result(array(
    		),'clear_cart_successed');
    }
    
    /**
     * 更新购物车项数量
     *
     * @author Ruesin
     */
    function update()
    {
        $ident = isset($_POST['id']) ? trim($_POST['id']) : '';
        $type  = isset($_POST['tp'])? trim($_POST['tp']): '';
        $num   = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
        if(!$ident || !$num ){
            $this->json_error('update_params_error');
            return;
        }
    
        if(!in_array($type, $this->_class_carts->types)){
            $this->json_error('type_error');
            return;
        }

        $goods = Types::createObj($type);
	
        if(!$goods->update(array('ident' => $ident, "quantity" => $num,"user_id" =>$this->_user_id))){
            $this->json_error($goods->get_error());
            return;
        }
    
       
        $main = $this->_class_carts->main(['source_from'=>$this->_source_from]);
    
        foreach ((array)$main['object'] as $row){
            if($row['ident'] == $ident){
                $return['quantity'] = $row['quantity'];
                $return['subtotal'] = $row['subtotal_m'];
                break;
            }
        }
    
        $this->json_result(array(
                'goods_num'    => $main['goods_num'],
                'goods_amount' => price_format($main['goods_amount']),
                //'order_amount' => price_format($main['order_amount']),
                'order_amount' => price_format($main['goods_amount']),
                'quantity'     => $return['quantity'],
                'subtotal'     => price_format($return['final_amount']),
        ),'update_item_successed');
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
    
        $goods = Types::createObj($type);

        $choice = $goods->choice(array('ident' => $ident, "user_id" => $this->_user_id),$ck);
        if(!$choice){
            $this->json_error($goods->get_error());
            return;
        }
    	/* 在购物车选择商品时，将对应的ident存到$_SESSION['_cart']['check']中，如果全选$_SESSION['_cart']['choiceAll']=true*/
        if($ck == 'y'){
            $_SESSION['_cart']['check'] = array_merge((array)$_SESSION['_cart']['check'],$choice);
        }else{
            foreach ($choice as $val){
                unset($_SESSION['_cart']['check'][$val]);
            }
            if($_SESSION['_cart']['choiceAll']) unset($_SESSION['_cart']['choiceAll']);
        }

        $main = $this->_class_carts->main(['source_from'=>$this->_source_from]);
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
        $carts = $this->_mod_cart->find(array('conditions'=> " user_id = '{$this->_user_id}'  ",'index_key'=>'ident'));
        $ck    = isset($_POST['hd']) ? trim($_POST['hd']) : '';
        
        if(!$ck ){
            $this->json_error('update_params_error');
            return;
        }
        $is_check = 1;
        if($ck !== 'y'){
            $is_check = 0;
            unset($_SESSION['_cart']['check']);
            unset($_SESSION['_cart']['choiceAll']);

            foreach ($carts as $key=>$val){
                $choice[$key] = $key;
                $this->_mod_cart->edit($val['rec_id'],['is_check'=>$is_check]);
            }

            $this->json_result(array(
                    'goods_num'  => 0,
                    'amount'     => price_format(0),
            ),'操作成功!');
            die();
        }

        
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
        $this->display($this->_template_file.'app_error.html');
    }
    
    /**
     * 刺绣页面
     *
     * @author Ruesin
     */
//     function embs(){
        
//         $type  = isset($_GET['type']) ? trim($_GET['type']) : '';
//         $ident = isset($_GET['id']) ? trim($_GET['id']) : '';
//         if(!in_array($type, $this->_class_carts->types) || !$type){
//             $this->show_warning('类型错误!');
//             return;
//         }
//         if(!$ident){
//             $this->show_warning('要刺绣的商品已不存在，请返回重新选择!');
//             return;
//         }
//         $carts = $this->_cart_main();
        
//         $info = $carts['object'][$ident];
// //     dump($carts);    
//         if(!$info){
//             $this->show_warning('要刺绣的商品已不存在，请返回重新选择!');
//             return;
//         }
//         $this->assign('info',$info);
//         $this->assign('embs',$this->_mod_cart->_format_embs());
//         if ($info['list']){
//             $this->display('cart/embs/index.html');
//         }else{
//             $this->display('cart/embs/normal.html');
//         }
        
//     }
    
    
    /**
     * 保存刺绣信息
     *
     * @author Ruesin
     */
//     function embSave(){
//         if(!IS_POST){
//             $this->json_error('请正确提交数据');
//             die();
//         }
        
//         $cart = $this->_cart_main(1);
        
//         if($cart['goods_num'] <= 0){
//             $this->json_error("params_error");
//             return;
//         }
        
//         //直接将原代码copy过来了  实际是可以做优化的
//         if ($_POST['embs']) { //暂没有做严格验证
            
//             $embs = $this->_mod_cart->_format_embs();
//             // 保存刺绣信息
//             foreach ((array) $_POST['embs'] as $key => $row) {
//                 if (count($row) < 4)
//                     continue;
//                 $er = 0;
//                 foreach ($row as $k => $v) {
//                     if ($v) {
                        
//                         // 不存在父级 //三类通用
//                         if (!$embs[$cart['object'][$key]['cloth']][$k]) {
//                             $er = 1;
//                             break;
//                         }
                        
//                         // 有父级 && 没有list || 有list并且有值
                        
//                         if (is_array($v)) { // 是数组 校验位置
                            
//                             //2015-07-08 15:09:13  刺绣位置尼玛又不让多选了!
//                             if(count($v) >1 ){
//                                 $er = 1;
//                                 break;
//                             }
                            
//                             if (!$embs[$cart['object'][$key]['cloth']][$k]['list'] || $embs[$cart['object'][$key]['cloth']][$k]['statusid'] != '10002') {
//                                 $er = 1;
//                                 break;
//                             }
//                             foreach ($v as $vv) {
//                                 if (!$embs[$cart['object'][$key]['cloth']][$k]['list'][$vv]) {
//                                     $er = 1;
//                                     break;
//                                 }
//                                 $data[$k][$vv] = $vv;
//                             }
//                         } else {
                            
//                             // 有list但是没有值
//                             if ($embs[$cart['object'][$key]['cloth']][$k]['list'] && !$embs[$cart['object'][$key]['cloth']][$k]['list'][$v]) {
//                                 $er = 1;
//                                 break;
//                             }
                            
//                             if (!intval($v)){
                                
//                                 if(strlen(iconv("UTF-8", "GB2312//IGNORE", $v)) > 14){
//                                     $this->json_error('刺绣文字长度过长!');
//                                     die();
//                                 }
//                             }
                            
                            
//                             $data[$k] = $v;
                            
//                         }
//                     } else {
//                         $er = 1;
//                         break;
//                     }
//                 }
                
//                 if ($er) continue; 
                
//                 $res = $this->_mod_cart->edit(" ident = '{$key}' ", array('embs' => json_encode($data,JSON_UNESCAPED_UNICODE)));
//                 $data = array();
//                 if ($res < 0)break;
//             }
//         }
//         if ($res >= 0) {
//             $this->json_result(array(), 'successed');
//             return;
//         } else {
//             $this->json_error('check_error');
//             return;
//         }
        
//     }
    
    
    /**
     * 校验提交确认页(还未做各种活动等的验证)
     * 
     * @date 2015-8-26 上午8:19:46
     * @author Ruesin
     */
    function check(){

    	if(!IS_POST){
    		$this->json_error("请选择要购买的商品！");
    		return;
    	}
   
    	$cart = $this->_class_carts->main(['source_from'=>$this->_source_from]);
    	if($cart['check_num'] <= 0){
    		$this->json_error("请选择要购买的商品！");
    		return;
    	}
    	
    	
    	$this->_check_stock($cart['object']);
  
	    $this->json_result(array(),'successed');
	    return ;
    }
    
    /**
     * 订单确认页
     *
     * @author Ruesin
     */
    function checkout(){

        $aCart = $this->_class_carts->main(['source_from'=>$this->_source_from]);
        if($aCart['order_amount'] <= 0)
        {
            unset($_SESSION['_order']['debit']);
        }
        $_order = $this->_order_info($aCart);
    	$this->assign("order" , $_order);

    	if(!$aCart['object']){
    	    $view = &v(); 
    	    $url = $view->build_url(array("app" => "cart"));
    	    header('Location:'.$url);
    		$this->_cart_empty();
    		return;
    	}


    	//计算物流费用
        //if ($aCart['weight']){
    	    imports("orders.lib");
    	    $orderLib = new Orders();
    	    $_SESSION['_order']['ships']['defship']['post_fee'] = $orderLib->freightCnt($aCart['weight'],$_SESSION['_order']['ships']['defship'],$aCart['is_try']);
    	    $aCart['final_amount'] += $_SESSION['_order']['ships']['defship']['post_fee'];
        //}
    	$aCart['defship'] = $_SESSION['_order']['ships']['defship'];

    	//抵扣券
    	if (isset($_order['discount'])){
    	        $aCart['debit_fee'] = $_order['discount'];
    	        $aCart['final_amount'] -= $_order['discount'];
    	}
    	$_mod_pay = &m("payment");


        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) //=====  如果是微信打开  =====
        {
            $pay_name = 'wxpay';
        }
        else
        {
            $pay_name = 'malipay';
        }
    	$payments = $_mod_pay->find(array(
    	    'conditions' => "payment_code = '$pay_name' ",
    	    'order'      => "sort_order DESC"
    	));

    	$this->assign('payments',$payments);
    	
    	$_mod_member = &m('member');
    	
    	$mInfo = $_mod_member->get($this->_user_id);
    	$this->assign('member',$mInfo);
    	
    	foreach ($aCart['object'] as $row){
    	    
    	    if($row['list']){
    	        foreach ($row['list'] as $arr){
    	            foreach ((array)explode(',', $arr['cloth_quan']) as $val){
    	                $cls[] = $val;
    	            }
    	        }
    	    }else{
            	    foreach ((array)explode(',', $row['cloth']) as $val)
            	    {
            	                $cls[] = $val;
            	    }
            	    }
    	}
    	$cls = $aCart['cloth_quan'];
        $order_amount = $aCart['order_amount'];

        if($cls && $order_amount>0)
        {
            $mDbt = &m('voucher');
            $time = gmtime();
            $debits = $mDbt->find(" binding_user_id = '".$this->_user_id."' AND use_status = 0 AND order_money <= '{$order_amount}'   AND end_time >'{$time}' AND (category = '1,2' OR category in (".implode(',',$cls)."))");
        }

        if( $aCart['final_amount'] <= 0)
        {
            $aCart['final_amount']  = 0;
        }
        $this->assign('debits',count($debits));
    	$this->assign('cart', $aCart);
        $this->assign('title','订单确认');
    	$this->assign('payway',$this->_pay_way);
    	$this->assign('shipway',$this->_ship_way);
    	$this->assign('shipping',$this->_shipping_type);
    	$this->assign('apptk',$_REQUEST['token']);
    	$this->assign('one',$_SESSION['_cart']['one']);
    	$this->assign('one_id',$_SESSION['_cart']['one_id']);
    	$this->assign('num_one',$_SESSION['_cart']['num_one']);
        $this->assign('user_id',$this->_user_id);
    	
    	$this->display("cart/checkout.html");
    }
    
    /**
     * 抵扣券列表
     * 
     * @date 2015-8-26 上午9:57:16
     * @author Ruesin
     */
    function getDebits($aCart = array()){
        
        if(empty($aCart['object'])) return false;

        $cls = $aCart['cloth_quan'];
        $mDbt = &m('voucher');
        $time = gmtime();
        //$debits = $mDbt->find(" user_id = '{$this->_user_id}' AND is_used = '0' AND is_invalid = '0' AND expire_time >'{$time}' AND ".db_create_in($cls,'cate'));
        $debits = $mDbt->find(" binding_user_id = '".$this->_user_id."' AND use_status = 0  AND end_time >'{$time}' AND (category = '1,2' OR category in (".implode(',',$cls)."))");
        // $debits = $mDbt->find(" user_id = '{$this->_user_id}' AND is_used = '0' AND is_invalid = '0' AND expire_time >'{$time}' ");
        foreach ($debits as $dKey=>$dRow){
            $dRow['category'] = explode(',', $dRow['category']);
            foreach ($cls as $cKey=>$cVal){
                if (in_array($cVal, $dRow['category'])){
                    $debit[$dKey] = $dRow;
                }
            }
        }
        $customs = $this->_mod_cart->_customs();
        foreach ((array)$debit as $row){
            //$data[$row['cate']][$row['id']] = $row;
            foreach ($row['cate'] as $cKey=>$cVal){
                if (in_array($cVal, $cls)){
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
     * @author Ruesin
     */
    function address(){
        $mAddr = &m('address');
        $addrs = $mAddr->find(" user_id = '{$this->_user_id}' ");
    
        $regions = $this->getServeRegion();
        $this->assign('regions',$regions);
    
        $this->assign('address',$_SESSION['_order']['shipping']['address']);
        $this->assign('store',$_SESSION['_order']['shipping']['store']);
        $this->assign('type',$_SESSION['_order']['shipping']['type']);
        $this->assign('list',$addrs);
        $this->display('cart/shipping.html');
    }
    
    /**
     * 编辑/新增 收货地址
     *
     * @author Ruesin
     */
    function addressEdit(){
        $args = $this->get_params();
        $id = isset($args[0]) ? intval($args[0]) : 0;
        if($id){
            $mAddr = &m('address');
            $addrs = $mAddr->get(" addr_id = '{$id}' AND user_id = '{$this->_user_id}' ");
            if($addrs){
                $this->assign('data',$addrs);
                $this->assign('edit','1');
            }
        }
    
        $this->display('cart/address/edit.html');
    }
    
    /**
     * 保存收货地址
     *
     * @author Ruesin
     */
    function addressSave(){
    
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
        foreach ($_POST as $key=>$val){
            $errors = array(
                    'region_list' => '请选择城市',
                    'region_name' => '请选择城市',
                    'name' => '请填写收件人',
                    'address' => '请填写收货地址',
                    'phone' => '请填写手机号',
            );
            foreach ($_POST as $key => $val){
                if(!$val){
                    if($errors[$key]){
                        $this->json_error($errors[$key]);
                        die();
                    }
                }
            }
        }
    
        $data = array(
                'user_id'       => $this->_user_id,
                'consignee'     => $_POST['name'],
                'al_name'       => '收货地址',
                //'region_id'     => $_POST['region_id'],
                'region_id'     => $_POST['region_list'],
                'region_name'   => $_POST['region_name'],
                'address'       => $_POST['address'],
                'zipcode'       => $_POST['zipcode'],
                'phone_tel'     => $_POST['phone'],
                'phone_mob'     => $_POST['phone'],
                //'email'         => $_POST['email'],
        );
        if(!preg_match("/^1[34578]\d{9}$/", $data['phone_mob'])){
            $this->json_error('手机号错误!');
            die();
        }
        if(count(explode(',', $data['region_id'])) < 3){
            $this->json_error('请选择地区!');
            die();
        }
        $mAddr =& m('address');
        if($id){
            $res = $mAddr->edit(" user_id = '{$this->_user_id}' AND addr_id = '{$id}'",$data);
        }else{
            $id = $res = $mAddr->add($data);
        }
        if($res < 0){
            $this->json_error('提交失败!');
            die();
        }
        $addres_info = $mAddr->get($id);
        unset($_SESSION['_order']['shipping']);
        $_SESSION['_order']['shipping']['address'] = $addres_info;
        $_SESSION['_order']['shipping']['type'] = 'address';
        $_SESSION['_order']['address'] = $addres_info;
        $this->json_result('','提交成功!');
        die();
    
    }
    
    /**
     * 删除收货地址
     *
     * @author Ruesin
     */
    function addressDel(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $mAddr =& m('address');
        $res = $mAddr->drop(" user_id = '{$this->_user_id}' AND addr_id = '{$id}'");
        if(!$res){
            $this->json_error('删除失败!');
            die();
        }
        $address = $_SESSION['_order']['shipping']['address'];
    
    
    
        if($address['addr_id'] == $id){
            $addr = $mAddr->get("user_id = '{$this->_user_id}'");
            if($addr){
                unset($_SESSION['_order']['shipping']);
                $_SESSION['_order']['shipping']['type'] = 'address';
                $_SESSION['_order']['shipping']['address'] = $addr;
                $nid = $addr['addr_id'];
            }else{
                unset($_SESSION['_order']['shipping']);
            }
        }
        $this->json_result(
                $nid,'删除成功'
        );
    }
    
    /**
     * 选择收货地址
     *
     * @author Ruesin
     */
    function addressSet(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $mAddr =& m('address');
        $addr = $mAddr->get(" user_id = '{$this->_user_id}' AND addr_id = '{$id}'");
        if(!$addr){
            $this->json_error('设置失败!');
            die();
        }
        unset($_SESSION['_order']['shipping']);
        $_SESSION['_order']['shipping']['type'] = 'address';
        $_SESSION['_order']['shipping']['address'] = $addr;
        $this->json_result(
                $id,'设置成功'
        );
        die();
    
    }
    
    /**
     * 选择物流公司
     *
     * @author Ruesin
     */
    function shipsSet(){
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        imports("orders.lib");
        $orderLib = new Orders();
        $shipInit = $orderLib->getShipInit([],$id);

        if(!$shipInit['defship']){
            $this->json_error('设置失败!');
            die();
        }
        unset($_SESSION['_order']['ships']);
        $_SESSION['_order']['ships']['defship'] = $shipInit['defship'];
        $this->json_result(
            $id,'设置成功'
        );
        die();
    
    }

    /**
     * 物流公司
     *
     * @date 2015-10-13 上午9:57:51
     * @author Ruesin
     */
    
    function ships(){
        $args = $this->get_params();
        $id = isset($args[0]) ? intval($args[0]) : 0;
        $mAddr = &m('address');
        $defAddRess = $mAddr->find(" user_id = '{$this->_user_id}' and addr_id ='{$id}'");
   
        imports("orders.lib");
        $orderLib = new Orders();
        $shipInit = $orderLib->getShipInit(['ship_area_id'=>$defAddRess['region_id'],'weight'=>'']);
        
        $this->assign('list',$shipInit['shippings']);
        $this->assign('defShips',$shipInit['defship']);
        $this->assign('apptk',$_REQUEST['token']);
        $this->display('cart/ships.html');
    }
    
    /**
     * 保存物流  收货地址
     *
     * @author Ruesin
     */
    function shippingSave(){
    	$type = isset($_POST['type']) ? trim($_POST['type']) : '';
    	if(!$type){
    		$this->json_error('格式不正确!');
    		die();
    	}
    
    	$errors = array(
    			'region_id' => '请选择城市',
    			'ship_name' => '请填写自提人姓名',
    			'ship_mob' => '请填写自提人手机号',
    			'server_id' => '请选择门店',
    			'server_name' => '请选择门店',
    	);
    	$type = 'store'; //目前地址在set哪里  这里就先写死吧
    
    	foreach ($_POST[$type] as $key=>$val){
    		if(!$val){
    			if($errors[$key]){
    				$this->json_error($errors[$key]);
    			}else{
    				$this->json_error('数据填写不完整!');
    			}
    			die();
    		}
    	}
    
    	if($_POST[$type]['ship_mob']){
    		if(!preg_match("/^1[34578]\d{9}$/", $_POST[$type]['ship_mob'])){
    			$this->json_error('自提人手机号错误!');
    			die();
    		}
    	}
    
    	unset($_SESSION['_order']['shipping']);
    	$_SESSION['_order']['shipping']['type'] = $type;
    	$_SESSION['_order']['shipping'][$type] = $_POST[$type];
    	$this->json_result(array(),'保存成功');
    	die();
    }
    
    /**
     * 获取服务点
     *
     * @author Ruesin
     */
    function getServer(){
    	$region_id = isset($_POST['region_id']) ? intval($_POST['region_id']) : '';
    	$server_id = isset($_POST['server_id']) ? intval($_POST['server_id']) : 0;
    	$type      = isset($_POST['type']) ? trim($_POST['type']) : '';
    	if (!$region_id){
    		$this->json_error('error');
    		return;
    	}
    	$msv = m('serve');
    	$servers = $msv->find("region_id = '{$region_id}' AND virtual = '0' AND shop_type IN (1,2)");
    	$this->assign('servers',$servers);
    	$this->assign('server_id',$server_id);
    	if($type == 'ship'){
    		$content = $this->_view->fetch("cart/shipping/server.html");
    	}else{
    		$content = $this->_view->fetch("cart/measure/server.html");
    	}
    
    	$this->json_result(array(
    			'content'      =>  $content,
    	),'success');
    	die();
    }
    
    /**
     * 使用抵扣券列表
     *
     * @author Ruesin
     */
    function debit(){
    	$cart = $this->_class_carts->main(['source_from'=>$this->_source_from]);
        
        
        
    	if(!$cart['object']){
    		return ;
    	}
    	foreach ((array)$cart['object'] as $item){
    	    foreach ((array)$item['list'] as $row){
    	        foreach ((array)explode(',', $row['cloth_quan']) as $v){
    	            $cls[] = $v;
    	        }
    	    }
    	}
        $cls = $cart['cloth_quan'];
        if(!$cls) 
        {
            return ;
        }
    	$time = time();
        $mDbt = &m('voucher');
        $time = gmtime();
        $debits = $mDbt->find(" binding_user_id = '".$this->_user_id."' AND use_status = 0  AND end_time >'{$time}' AND (category = '1,2' OR category in (".implode(',',$cls)."))");
    	$this->assign('count',count($debits));
    	foreach ((array)$debits as $row){
    		$data[$row['category']][$row['id']] = $row;
    	}
//    echo '<pre>';print_r($_SESSION['_order']['debit']['data']);exit;
    
    	$this->assign('customs',$this->_mod_cart->_customs());

    	$this->assign('debit',$_SESSION['_order']['debit']['data']);
    
    	$this->assign('data',$data);
    	$this->display('cart/debit/index.html');
    }
    
    /**
     * 使用抵扣券
     *
     * @author Ruesin
     */
    function debitSave(){
    	$ids = isset($_POST['debit']) ? $_POST['debit'] : array();
        $aCart = $this->_class_carts->main(['source_from'=>$this->_source_from]);
//        echo '<pre>';print_r($aCart);exit;
        if(count($ids) > 1)
        {
            unset($_SESSION['_order']['debit']);
            $this->json_result('','券只能用一张');
            die();
        }
    	if(!$ids){
    		unset($_SESSION['_order']['debit']);
    		$this->json_result('','设置成功');
    		die();
    	}
    	$mDbt = &m('voucher');
    	$time = gmtime();
//    	$debits = $mDbt->find(" user_id = '".$this->_user_id."' AND is_used = '0' AND is_invalid = '0' AND expire_time >'{$time}' AND id in (".implode(',',$ids).")");
        $debits = $mDbt->find(" binding_user_id = '".$this->_user_id."' AND use_status = 0  AND end_time >'{$time}' AND (id in (".implode(',',$ids)."))");
    	if(count($ids) != count($debits)){
    		$this->json_error('数据有误!');
    		die();
    	}
    	$diy_price = $aCart['diy_price'];
        $custom_price = $aCart['custom_price'];
//echo '<pre>';print_r($debits);exit;
        foreach ((array)$debits as $index => &$d)
        {
            $money = $d['money'];
            if($d['category'] == '1')
            {
                if($diy_price < $money)
                {
                    $debits[$index]['money'] = $diy_price;
//        echo '<pre>';print_r($d);exit;
                    
                }
            }
            elseif ($d['category'] == '2')
            {
                if($custom_price < $money)
                {
                    $debits[$index]['money'] = $custom_price;
                }
            }
        }
//echo '<pre>';print_r($debits);exit;

    	unset($_SESSION['_order']['debit']);
    	$_SESSION['_order']['debit']['data'] = $debits;
    	$this->json_result(array_values($debits)[0]['id'],'设置成功');
    	die();
    }
    
    /**
     * 使用余额
     *
     * @author Ruesin
     */
    function useMoney(){
    	//$tp = isset($_POST['tp']) ? trim($_POST['tp']) : 'n';
    
    	unset($_SESSION['_order']['money']);
    	die();
    	if($tp == 'u'){
    		$_mod_member = &m('member');
    		$mInfo = $_mod_member->get($this->_user_id);
    		if($mInfo['money'] <= 0){
    			die();
    		}
    		$_order = $this->_order_info();
    		$aCart = $this->_total($_order);
    
    		if($mInfo['money'] >= $aCart['final_amount']){
    			$money = $aCart['final_amount'];
    		}else{
    			$money = $mInfo['money'];
    		}
    		$_SESSION['_order']['money'] = $money;
    	}
    }
    

    /**
     * 抵扣券使用规则
     * 
     * @date 2015-10-22 下午1:30:08
     * @author Ruesin
     */
    function debit_rules(){
        $this->display('cart/debit_rules.html');
    }
    

    //=================================
    
    /**
     * 根据地区获取服务点
     *
     * @author Ruesin
     */
    function getServe(){
    
    	$region_id = isset($_POST['region_id']) ? intval($_POST['region_id']) : '';
    	$server_id = isset($_POST['server_id']) ? intval($_POST['server_id']) : 0;
    	if (!$region_id){
    		$this->json_error('error');
    		return;
    	}
    	$msv = m('serve');
    	$servers = $msv->find("region_id = '{$region_id}'");
    	$this->assign('servers',$servers);
    	$this->assign('server_id',$server_id);
    	$content = $this->_view->fetch("cart/figure/server.html");
    	$this->json_result(array(
    			'content'      =>  $content,
    	),'success');
    }
    function getServeRegion(){
    	$region_mod = m('region');
    	$region_list = $region_mod->find("parent_id > 2 AND is_serve = 1"); //这里有问题  直辖市下的服务点定位在区里的 如果下拉只出最后一个，那么会只出个区的
    	$zxs = array(3=>'北京市',22=>'天津市',41=>'上海市',61=>'重庆市');
    	foreach ($region_list as &$row){
    		if(isset($zxs[$row['parent_id']])){
    			$row['region_name'] = $zxs[$row['parent_id']] .' '.$row['region_name'];
    		}
    	}
    
    
    	return $region_list;
    }
    /**
     * 获得有服务点的三级地区
     *
     * @author Ruesin
     */
    function getServeRegionThree()
    {
    	$parent_id = isset($_POST['region_id']) ? $_POST['region_id'] : '';
    	$next_id   = isset($_POST['next_id']) ? $_POST['next_id'] : '';
    	if (!$parent_id)
    	{
    		$this->json_error('error');
    		return;
    	}
    
    	$region_mod = m('region');
    
    	$region_list = $region_mod->find("parent_id=$parent_id AND is_serve = 1");
    
    	$html = "<option value=''>请选择</option>";
    	foreach ($region_list as $key => $value)
    	{
    		if($key == $next_id){
    			$html .= "<option value=".$key." selected >".$value['region_name']."</option>";
    		}else{
    			$html .= "<option value=".$key.">".$value['region_name']."</option>";
    		}
    
    	}
    
    	$this->json_result($html);
    }
    
    
    //=================================
    
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
