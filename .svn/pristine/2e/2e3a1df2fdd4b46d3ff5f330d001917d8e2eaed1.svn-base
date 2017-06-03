<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
use Cyteam\Config\Config;
use Cyteam\Comment\Comment;
/**
 *    我的评论控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class CommentApp extends MallbaseApp
{
    var $_ordergoods_mod;
    var $_order_mod;
    var $_detailcomment_mod;
    var $_detailimpress_mod;
    var $commentObj;
    var $pageSize;
    function __construct()
    {	
	   parent::__construct();
//	   header("Content-Type:text/html;charset=" . CHARSET);
	   Lang::load(lang_file('common'));
       $this->_ordergoods_mod  = &m('ordergoods');
       $this->_detailimpress_mod =&m('detail_impression');
       $this->_detailcomment_mod = &m('detail_comment');
       $this->_order_mod = &m("order");
       $this->_commentimg_mod = &m("commentimg");
       $this->commentObj =new Comment();
       $this->configObj=new Config();
       $this->_fbcategory_mod=&m('fbcategory');
       $this->pageSize=10;
    }
    
    public function test()
    {
        echo 3333;exit;
    }
	/**
	*-----------------------------------------------------------
	*用户中心-我的评论
	*-----------------------------------------------------------
	*@access public
	*@author cyrus <2621270755@qq.com>
	*@date 2016年5月30日
	*@version 1.0
	*@return 
	*/
	  function index(){
		//获取未评价的商品
		$order_goods=m('ordergoods');
		$detail_comment_mob=m('detail_comment');
		$order_mob=m('order');
		$member_mob=m('member');
		$user_id = $this->visitor->get('user_id');
		//获取用户订单id
		$orderids=$order_mob->find(array(
		       'conditions'=>'status=40 AND user_id='.$user_id ,
		 ));
		 $page    =   $this->_get_page(30);
		 if($orderids){
			 $orderidss= array_column($orderids, 'order_id');
			 $orderid=implode(',',$orderidss);
			 //获取评价
		$detail_count=$detail_comment_mob->find(array(
		       'conditions'=>'member_id='.$user_id .' AND order_id in ('.$orderid.')',
			  
		));
		$detail_com=$detail_comment_mob->find(array(
		       'conditions'=>'member_id='.$user_id .' AND order_id in ('.$orderid.')',
			   'limit'     =>$page['limit'],
			   'order'     =>'id DESC',
		));
		
		$page['item_count'] = count($detail_count);
        $this->_format_page($page);
      	$this->assign('page_info', $page);
			  
		 }

		if($detail_com){
			foreach($detail_com as $key=>$val){
			
				$detail_com[$key]['add_time'] = date("Y-m-d H:i:s",$val['addtime']);
				 $comsql="SELECT * FROM cf_comment_img WHERE comment_id='{$val['id']}' order by id asc";
		      		     $commentimgs=$order_goods->getAll($comsql);
		      		     if($commentimgs){
		      		       $detail_com[$key]['commentimgs']=$commentimgs;
		      		     }
					$ordergoods=$order_goods->get(array(
		             'conditions'=>"rec_id='{$val['rec_id']}'",
		             ));
					 if($ordergoods){
						
							
							if($ordergoods['type']=='fdiy'){
								$params=$this->commentObj->getGoodParam($ordergoods['type'],$ordergoods['params']);
								 $paramsArr=json_decode($ordergoods['params'],true);
							   
								$paramsArr=array_values($paramsArr);
								$paramsObj=array();
								foreach ($paramsArr as $k => $v) {
									
									$paramsTemp=explode(':',$v);
									$paramsObj[$paramsTemp[0]]=$paramsTemp[1];
								}
								
								$ordergoods['params']=json_encode($paramsObj,true);
								
								 if(is_array($params)){
									foreach ($params as $k => $v) {
								  
										 $ordergoods['format_params'][$k]=$params;
									}
								 
								}else{
								  
									$ordergoods['format_params']=$params;
								}
			            }
					 }
					 $detail_com[$key]['ordergoods']=$ordergoods;
			}
		}
	

		$this->assign('comment_list',$detail_com);
	
		 $this->display('comment/index.html');
		
	}
    function index_bak()
    {
        // $status = array('uncomment', 'commented','all');
        $user_id = $this->visitor->get('user_id');
        $fbcategorylists = $this->_fbcategory_mod->find(array('conditions'=>'parent_id=34'));
        if($fbcategorylists){
            foreach($fbcategorylists as $key=>$val){
                $fblists[$val['cate_id']]=$val['cate_name'];
            }
        }
        $this->assign('fblists',$fblists);
      
        //$conditions = " AND order_alias.status = 40 ";
        /*if(in_array($arg[1], $status)){
            switch ($arg[1]){
                case "uncomment":
                    $conditions .= " AND order_goods.comment=0";
                    break;
                case "commented":
                    $conditions .= " AND order_goods.comment=1";
                    break;
                case "all":
                    $conditions .= " AND (order_goods.comment=1 || order_goods.comment=0)";
                    break;
            }
        }*/
        if($_GET['type']){
            $dett1=$this->_detailcomment_mod->find(array(
                     'conditions'  =>"is_img ='{$_GET['type']}'",
            ));
            if($dett1){
                 $dett11=i_array_column($dett1,'rec_id');
                $detts1=db_create_in($dett11);
            }else{
				$detts1 ="=0";
			}
           
            $conditions .= " AND order_goods.rec_id ".$detts1;
           
        }
        if($_GET['s_id']){
            $dett2=$this->_detailcomment_mod->find(array(
                'conditions'  =>"jd_id='{$_GET['s_id']}'",
            ));
            if($dett2){
                $dett22=i_array_column($dett2,'rec_id');
                $detts2=db_create_in($dett22);
            }else{
                $detts2=" =0";
            }
             
            $conditions .= " AND order_goods.rec_id ".$detts2;
        }
        $conditions .= " AND order_goods.comment=1";
        $page    =   $this->_get_page($this->pageSize); 
		$comment_list=$this->commentObj->get_order_comment($user_id, $conditions, $page);
        $pc_url=pc_url();
        if($comment_list){
             foreach ($comment_list as $key => $value) {
                 
                if(!strstr($value['goods_image'],'http')){
                    $comment_list[$key]['goods_image']=$pc_url.$value['goods_image'];
                }else if($value['goods_image']=='no pic'){
                    $comment_list[$key]['goods_image']=$pc_url.'/diy/images/cptu.jpg';
                }
                $params=$this->commentObj->getGoodParam($value['type'],$value['params']);
                $paramsArr=json_decode($value['params'],true);
                if($value['type']=='custom'){
                  
                    $comment_list[$key]['product_id']=$paramsArr['oProducts']['product_id'];
                    $comment_list[$key]['goods_id']=$paramsArr['oGoods']['goods_id'];
				    $comment_list[$key]['format_params'][0]=$paramsArr['oGoods']['name'];
                    
                }else{
                    $comment_list[$key]['product_id']=0;
                    $paramsArr=array_values($paramsArr);
                    $paramsObj=array();
                    foreach ($paramsArr as $k => $v) {
                        $paramsTemp=explode(':',$v);
                        $paramsObj[$paramsTemp[0]]=$paramsTemp[1];
                    }
                    $comment_list[$key]['params']=json_encode($paramsObj,true);
					 if(is_array($params)){
                        foreach ($params as $k => $v) {
                         //   $comment_list[$key]['format_params'][$k]=$v['p_name'].':'.$v['s_name'];
                             $comment_list[$key]['format_params'][$k]=$params;
                        }
                        // print_pre($)
                    }else{
                       // $comment_list[$key]['format_params']=explode('-',$params);
                        $comment_list[$key]['format_params']=$params;
                    }
                }
				
                // setcookie('params',$comment_list[$key]['params']);
          
               
                
            }
        }
        $page['item_count'] = count($comment_list);
        $this->_format_page($page);
      	$this->assign('page_info', $page);
        $this->assign('comment_list', $comment_list);
        $this->_config_seo('title', '我的麦富迪 - 我的评论');
        $this->assign("app", "my_comment");

        $this->display('comment/index.html');
    }
	
	  //瀑布流 读取评论信息
    function ajax_order_list(){

	   if($_GET['type']){
			if($_GET['type']==1){
				$is_img='1';
			}elseif($_GET['type']==2){
				$is_img='0';
			}
		
        
            $conditions .= " AND comments.is_img = ".$is_img;
        }
		if($_GET['quan_id']){
			 $conditions .= " AND comments.quan_id = ".$_GET['quan_id'];
		}
        if($_GET['s_id']){
         
             
            $conditions .= " AND comments.jd_id = ".$_GET['s_id'];
        }
		$ps=$_GET['limit'];
		$ps1=$_GET['limit'] + $this->pageSize;
	 $page['limit']="{$ps},{$ps1}";
	$conditions.=" AND comments.status=1 ";
	  $comment_list=$this->commentObj->get_comment(0, $conditions, $page);
	    $pc_url=pc_url();
        if($comment_list){
             foreach ($comment_list as $key => $value) {
                 
                if(!strstr($value['goods_image'],'http')){
                    $comment_list[$key]['goods_image']=$pc_url.$value['goods_image'];
                }else if($value['goods_image']=='no pic'){
                    $comment_list[$key]['goods_image']=$pc_url.'/diy/images/cptu.jpg';
                }
                $params=$this->commentObj->getGoodParam($value['type'],$value['params']);
                $paramsArr=json_decode($value['params'],true);
                if($value['type']=='custom'){
                  
                    $comment_list[$key]['product_id']=$paramsArr['oProducts']['product_id'];
                    $comment_list[$key]['goods_id']=$paramsArr['oGoods']['goods_id'];
				    $comment_list[$key]['format_params'][0]=$paramsArr['oGoods']['name'];
                    
                }else{
                    $comment_list[$key]['product_id']=0;
                    $paramsArr=array_values($paramsArr);
                    $paramsObj=array();
                    foreach ($paramsArr as $k => $v) {
                        $paramsTemp=explode(':',$v);
                        $paramsObj[$paramsTemp[0]]=$paramsTemp[1];
                    }
                    $comment_list[$key]['params']=json_encode($paramsObj,true);
					 if(is_array($params)){
                        foreach ($params as $k => $v) {
                         //   $comment_list[$key]['format_params'][$k]=$v['p_name'].':'.$v['s_name'];
                             $comment_list[$key]['format_params'][$k]=$params;
                        }
                        // print_pre($)
                    }else{
                       // $comment_list[$key]['format_params']=explode('-',$params);
                        $comment_list[$key]['format_params']=$params;
                    }
                }
                // setcookie('params',$comment_list[$key]['params']);
          
               
                
            }
        }
      if ($comment_list)
       {
          $this->json_result($comment_list);
          return;
      }else{
        $this->json_error('无数据了！');
        return;
      }
    }
		/**
	*-----------------------------------------------------------
	*用户中心-所有评论
	*-----------------------------------------------------------
	*@access public
	*@author shaozz
	*@date 2016年5月30日
	*@version 1.0
	*@return 
	*/
    function comments()
    {
       
		$quan_id=$_GET['quan_id'];
		$detail_comment_mob=m('detail_comment');
		$this->assign('quan_id',$quan_id);
        $user_id = $this->visitor->get('user_id');
        $fbcategorylists = $this->_fbcategory_mod->find(array('conditions'=>'parent_id=34'));
       
		$conditions.=" AND comments.status=1 ";
       
			if($quan_id){
				 $conditions .= " AND comments.quan_id = ".$quan_id;
				  $fb_cons .= " AND quan_id = ".$quan_id;
			}
        if($_GET['type']){
			if($_GET['type']==1){
				$is_img='1';
			}elseif($_GET['type']==2){
				$is_img='0';
			}
		
            $conditions .= " AND comments.is_img = ".$is_img;
			 $fb_cons .= " AND is_img = ".$is_img;
			
        }
		
		 if($fbcategorylists){
            foreach($fbcategorylists as $key=>$val){
                $fblists[$val['cate_id']]['name']=$val['cate_name'];
				//获取该阶段数量
				$detail_comments=$detail_comment_mob->find(array(
				       'conditions'=>'status=1 AND jd_id='.$val['cate_id'].$fb_cons,
				 ));
				 $fblists[$val['cate_id']]['num']=count($detail_comments);
            }
        }
		 $this->assign('fblists',$fblists);
        if($_GET['s_id']){
        
            $conditions .= " AND comments.jd_id = ".$_GET['s_id'];
        }
	
       // $conditions .= " AND order_goods.comment=1";
        $page    =   $this->_get_page($this->pageSize); 
		
		$comment_list=$this->commentObj->get_comment(0, $conditions, $page);
        $pc_url=pc_url();
        if($comment_list){
             foreach ($comment_list as $key => $value) {
                 
                if(!strstr($value['goods_image'],'http')){
                    $comment_list[$key]['goods_image']=$pc_url.$value['goods_image'];
                }else if($value['goods_image']=='no pic'){
                    $comment_list[$key]['goods_image']=$pc_url.'/diy/images/cptu.jpg';
                }
                $params=$this->commentObj->getGoodParam($value['type'],$value['params']);
                $paramsArr=json_decode($value['params'],true);
                if($value['type']=='custom'){
                  
                    $comment_list[$key]['product_id']=$paramsArr['oProducts']['product_id'];
                    $comment_list[$key]['goods_id']=$paramsArr['oGoods']['goods_id'];
				    $comment_list[$key]['format_params']=$paramsArr['oGoods']['name'];
                    
                }else{
                    $comment_list[$key]['product_id']=0;
                    $paramsArr=array_values($paramsArr);
                    $paramsObj=array();
                    foreach ($paramsArr as $k => $v) {
                        $paramsTemp=explode(':',$v);
                        $paramsObj[$paramsTemp[0]]=$paramsTemp[1];
                    }
                    $comment_list[$key]['params']=json_encode($paramsObj,true);
					 if(is_array($params)){
                        foreach ($params as $k => $v) {
                         //   $comment_list[$key]['format_params'][$k]=$v['p_name'].':'.$v['s_name'];
                             $comment_list[$key]['format_params'][$k]=$params;
                        }
                        // print_pre($)
                    }else{
                       // $comment_list[$key]['format_params']=explode('-',$params);
                        $comment_list[$key]['format_params']=$params;
                    }
                }
                // setcookie('params',$comment_list[$key]['params']);
          
             
                
            }
        }
        $page['item_count'] = count($comment_list);
        $this->_format_page($page);
      	$this->assign('page_info', $page);
        $this->assign('comment_list', $comment_list);
        $this->_config_seo('title', '我的麦富迪 - 我的评论');
        $this->assign("app", "my_comment");

        $this->display('comment/comment.html');
    }
    //获取点击图片
    function getimage(){
        $commentimg_mob=m('commentimg');
        $comment_id = empty($_POST['cid'])  ? 0 : intval($_POST['cid']);
        
        $detailimgs=$commentimg_mob->find(array(
            'conditions'=>"comment_id = $comment_id",
            'order'     =>'id asc',
        ));
        
        if($detailimgs){
            foreach($detailimgs as $key=>$val){
				if($val['m_image']){
				$detailimg[]=$val['m_image'];	
				}else{
				$detailimg[]=$val['image'];	
				}
                
            }
            $this->json_result($detailimg);
            return;
        }
    }
    //END function
    //用户中心-评论详情
    function publish(){
        $args=$this->get_params();
        $id=!empty($args[0])?intval($args[0]):0;
        $type=!empty($args[1])?trim($args[1]):'';
        if(!$id || !$type){
            return $this->show_warning('参数错误');
        }

        //获取商品评论
        $user_id =$this->visitor->get('user_id');
        $conditions .= "AND order_goods.rec_id={$id} AND order_goods.comment=1";
        $comment=$this->commentObj->get_order_comment($user_id, $conditions, array('limit'=>'0,1'));
		
        $this->_config_seo('title', '订单评论 - 用户中心');
        if($comment){
            $star=$comment[$id]['pl_info'][0]['star'];
            $content=$comment[$id]['pl_info'][0]['content'];
            $impressions=$comment[$id]['pl_info'][0]['impression'];
            $title='查看评论';
        }else{
            $star=0;
            $content='';
            $impressions=array();
            $title='发表评论';
        }
        $impress=$this->commentObj->get_impression_arr();
        $this->assign('star',$star);
        $this->assign('content',$content);
        $this->assign('impressions',$impressions);
        $this->assign('args',$args);
        $this->assign('comment',$comment);
        $this->assign('title',$title);
        foreach($impress as $k=>$v){
            foreach($impressions as $k1=>$v1){
                if($v1==$v['impress_name']){
                    $impress[$k]['on']=1;
                }
            }
        }
        $this->assign('impress', $impress);
        return $this->display('my_comment.publish.html');
    }
    //END function
    /**
    *-----------------------------------------------------------
    *用户中心-添加评论
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月30日
    *@version 1.0
    *@return 
    */
    function addComment()
    {
    	$user = $this->visitor->get();
        $id = isset($_POST['id']) ? intval($_POST['id']) : '';
        $type = isset($_POST['type']) ? $_POST['type'] : 'custom';//普通商品custom 定制商品fdiy
        $star = isset($_POST['star']) ? $_POST['star'] : '';
        $content = isset($_POST['info']) ? $_POST['info'] : '';
        // 评论的印象内容
        $impress = isset($_POST['impress']) ? $_POST['impress'] :'';
        $is_hide = '0';
        // 印象 做分表 处理
        $impressAttr = explode(";", $impress);

        if(!$id)
        {
            $this->json_error("没有要评论的商品");
            return; 
        }
        $from='weixin';
       $is_comment=$this->commentObj->add_comments($id, $user, $type, $from, $star, $content, $impress);
    	if($is_comment){
    		$this->json_result($is_comment);
    		return;
    	}else{
    		$this->json_error("发表评论失败");
    		return ;
    	}    	
    }
	   //异步加载评论列表
    function ajax_comment_list(){
       
        $page    =   $this->_get_page($this->pageSize); 
       //获取未评价的商品
		$order_goods=m('ordergoods');
		$detail_comment_mob=m('detail_comment');
		$order_mob=m('order');
		$member_mob=m('member');
		$user_id = $this->visitor->get('user_id');
		//获取用户订单id
		$orderids=$order_mob->find(array(
		       'conditions'=>'user_id='.$user_id ,
		 ));
		
		 if($orderids){
			 $orderidss= array_column($orderids, 'order_id');
			 $orderid=implode(',',$orderidss);
			 //获取评价
		$detail_count=$detail_comment_mob->find(array(
		       'conditions'=>'member_id='.$user_id .' AND order_id in ('.$orderid.')',
			  
		));
		$detail_com=$detail_comment_mob->find(array(
		       'conditions'=>'member_id='.$user_id .' AND order_id in ('.$orderid.')',
			   'limit'     =>$page['limit'],
			   'order'     =>'id DESC',
		));
		  
		 }

		if($detail_com){
			foreach($detail_com as $key=>$val){
			
				$detail_com[$key]['add_time'] = date("Y-m-d H:i:s",$val['addtime']);
				 $comsql="SELECT * FROM cf_comment_img WHERE comment_id='{$val['id']}' order by id asc";
		      		     $commentimgs=$order_goods->getAll($comsql);
		      		     if($commentimgs){
		      		       $detail_com[$key]['commentimgs']=$commentimgs;
		      		     }
					$ordergoods=$order_goods->get(array(
		             'conditions'=>"rec_id='{$val['rec_id']}'",
		             ));
					 if($ordergoods){
						
							
							if($ordergoods['type']=='fdiy'){
								$params=$this->commentObj->getGoodParam($ordergoods['type'],$ordergoods['params']);
								 $paramsArr=json_decode($ordergoods['params'],true);
							   
								$paramsArr=array_values($paramsArr);
								$paramsObj=array();
								foreach ($paramsArr as $k => $v) {
									
									$paramsTemp=explode(':',$v);
									$paramsObj[$paramsTemp[0]]=$paramsTemp[1];
								}
								
								$ordergoods['params']=json_encode($paramsObj,true);
								
								 if(is_array($params)){
									foreach ($params as $k => $v) {
								  
										 $ordergoods['format_params'][$k]=$params;
									}
								 
								}else{
								  
									$ordergoods['format_params']=$params;
								}
			            }
					 }
					 $detail_com[$key]['ordergoods']=$ordergoods;
			}
		}
	

        if(!$detail_com){   
          return $this->json_error(''); 
        }
        return $this->json_result($detail_com,'');
    }
    //END function
    //异步加载评论列表
    function ajax_comment_list_bak(){
        $user_id = $this->visitor->get('user_id');
        $conditions = " AND order_alias.status = 40 AND order_goods.comment=1";
        $page    =   $this->_get_page($this->pageSize); 
        $comment_list=$this->commentObj->get_order_comment(0, $conditions, $page);
        $pc_url=pc_url();
        if($comment_list){
            foreach ($comment_list as $key => $value) {
                if(!strstr($value['goods_image'],'http')){
                    $comment_list[$key]['goods_image']=$pc_url.$value['goods_image'];
                }else if($value['goods_image']=='no pic'){
                    $comment_list[$key]['goods_image']=$pc_url.'/diy/images/cptu.jpg';
                }

                $params=$this->commentObj->getGoodParam($value['type'],$value['params']);
                $paramsArr=json_decode($value['params'],true);
                if($value['type']=='custom'){
                    
                    $comment_list[$key]['product_id']=$paramsArr['oProducts']['product_id'];
                    
                }else{
                    $comment_list[$key]['product_id']=0;
                    $paramsArr=array_values($paramsArr);
                    $paramsObj=array();
                    foreach ($paramsArr as $k => $v) {
                        $paramsTemp=explode(':',$v);
                        $paramsObj[$paramsTemp[0]]=$paramsTemp[1];
                    }
                    $comment_list[$key]['params']=json_encode($paramsObj,true);
                }

                // setcookie('params',$comment_list[$key]['params']);
                if($params){
                    if(is_array($params)){
                        foreach ($params as $k => $v) {
                            $comment_list[$key]['format_params'][$k]=$v['p_name'].':'.$v['s_name'];
                        }
                        // print_pre($)
                    }else{
                        $comment_list[$key]['format_params']=explode('-',$params);
                    }
                    
                }
                
            }
        }else{
            return $this->json_error('');
        }
        return $this->json_result($comment_list,'');
    }
    //END function
    
}

?>