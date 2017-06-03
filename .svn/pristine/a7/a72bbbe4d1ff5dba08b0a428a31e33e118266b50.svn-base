<?php

/* 会员控制器 */
class ProfitApp extends BackendApp
{
    var $_user_mod;
	var $_account_mod;
	var $_userphoto_mod;
	var $_userphotocomment_mod;
	var $_userphotogallery_mod;
	var $_admin_user_mod;
	var $_jipaijizuo;
    var $sw = 80;
    var $sh = 80;
    var $mw = 300;
    var $mh = 300;
    var $coefficient = 5.2;
    function __construct()
    {
        $this->UserApp();
        $this->style = array(
            "1" => "正装",
            "2" => "休闲",
            "3" => "礼服"
        );
        $this->init = array(
            /*
            "0001" => array(
                   'name'  => "套装(2pcs)",
                   'items' => array(
                       "0003"     => array("fabric" =>'8001'),
                       '0004'     => array("fabric" =>'8001'),
                    ),
             ),
            "0002" => array(
                'name'  => "套装(3pcs)",
                'items' => array(
                    "0003"     => array("fabric" =>'8001'),
                    '0004'     => array("fabric" =>'8001'),
                    '0005'     => array("fabric" =>'8001'),
                ),
            ),
            */
            "0003" => array(
                "name"  => "西服",
                'mldh' => "2.2" ,
                'lldh' => "2",
                "ll" => "313",
                'dict' => array(
                    array(
                        "name" => "缝制工艺 ",
                        "list" => array(
                            array(
                                "name"  => "机器缝制",
                                'price' => "360",
                            ),array(
                                "name"  => "半手工缝制",
                                'price' => "1260",
                            ),array(
                                "name"  => "全手工缝制",
                                "price" => "1700"
                            ),
                        )
                    ),
                    array(
                        "name" => "衬工艺&nbsp; ",
                        "list"  => array(
                            array(
                                "name" => "粘合衬工艺",
                                'price' => "160",
                            ),
                            array(
                                "name" => "半麻衬工艺",
                                "price" => "500"
                            ),
                            array(
                                "name"  => "全麻衬工艺",
                                "price" => "700",
                            ),
                        )
                    ),
                ),
                "items" => array(
                    "0003"     => array("design" =>'24', "deep" => "298"),
                )
            ),

            "0004" => array(
                "name"  => "西裤",
                'mldh' => "1.6" ,
                'lldh' => "0",
                "dict" => array(
                    array(
                        "name" => "缝制工艺",
                        "list"  => array(
                            array(
                                "name" => "机器缝制",
                                'price' => "260",
                            ),
                            array(
                                "name" => "手工工艺",
                                "price" => "860"
                            ),
                        )
                    ),
                ),
                "items" => array(
                    "0004"     => array("design" =>'2021', "deep" => "2157"),
                )
            ),

            "0005" => array(
                "name"  => "马甲",
                'mldh' => "1" ,
                'lldh' => "0",
                "dict" => array(
                    array(
                        "name" => "缝制工艺",
                        "list"  => array(
                            array(
                                "name" => "机器缝制",
                                'price' => "260",
                            ),
                            array(
                                "name" => "手工工艺",
                                "price" => "600"
                            ),
                        )
                    ),
                ),
                "items" => array(
                    "0005"     => array("design" =>'4016', "deep" => "4075"),
                )
            ),

            "0006" => array(
                "name"  => "衬衣",
                'mldh' => "1.8" ,
                'lldh' => "0",
                "dict" => array(
                    array(
                        "name" => "缝制工艺",
                        "list"  => array(
                            array(
                                "name" => "机器缝制",
                                'price' => "180",
                            ),
                            array(
                                "name" => "手工工艺",
                                "price" => "480"
                            ),
                        )
                    ),
                ),
                "items" => array(
                    "0006"     => array("design" =>'3016', "deep" => "3184"),
                )
            ),

            "0007" => array(
                "name"  => "大衣",
                'mldh' => "2.8" ,
                'lldh' => "0",
                'll'   => "6291",
                "dict" => array(
                    array(
                        "name" => "缝制工艺",
                        "list"  => array(
                            array(
                                "name" => "机器缝制",
                                'price' => "1260",
                            ),
                            array(
                                "name" => "手工工艺",
                                "price" => "1760"
                            ),
                        )
                    ),
                ),
                "items" => array(
                    "0007"     => array("design" =>'6007', 'deep' => "298"),
                )
            ),
        );
    }

    function UserApp()
    {
        parent::__construct();
        $this->_user_mod =& m('member');
        $this->_lv_mod =& m('memberlv');
        $this->_account_mod =& m('account');
        $this->_userphoto_mod =& m('userphoto');
        $this->_userwork_mod  =& m('userwork');
        $this->_work_mod  =& m('works');
        $this->_mod_custom = &m("workCustom");
        $this->_mod_cusItem = &m("workItem");
        $this->_mod_cusFab = &m("workFab");
        $this->_mod_designer = &m("designer");
        $this->_mod_type     = &m("customtype");
        $this->_mod_attribute     = &m("customattribute");
        $this->_mod_cusAttr  = &m("workAttr");
        $this->_mod_suit = &m("workSuit");
        $this->_mod_suitre = &m("workSuitRelat");
        $this->_mod_suitattr = &m("workSuitAttr");
        $this->_mod_cusGallery = &m("customgallery");
        $this->_userworkimg_mod  =& m('userworkimg');
        $this->_userphotocomment_mod =& m('userphotocomment');
        $this->_userphotogallery_mod =& m('userphotogallery');
        $this->_admin_user_mod=$this->_user_mod->get('');
        //var_dump($this->_admin_user_mod);
        $this->_jipaijizuo='/upload/jipaijizuo/';

        $this->assign('jipaijizuo',$this->_jipaijizuo);
    }

    function index()
    {
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'like',
            ),
        ));
        //更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'user_id';
             $order = 'DESC';
            }
        }
        else
        {
            if (isset($_GET['sort']) && empty($_GET['order']))
            {
                $sort  = strtolower(trim($_GET['sort']));
                $order = "";
            }
            else
            {
                $sort  = 'user_id';
                $order = 'DESC';
            }
        }
//  echo $sort,$order;exit;
//         echo  '1=1' . ' and serve_type='.$this->_lv_mod->_typeid[$_GET['field_serve_type']].$conditions;
        $page = $this->_get_page(30);
        $_GET['serve_type'] = empty($this->_lv_mod->_typeid[$_GET['field_serve_type']]) ? 1 : $this->_lv_mod->_typeid[$_GET['field_serve_type']];
        $users = $this->_user_mod->find(array(
            //'join' => 'has_store,manage_mall',
            //'fields' => 'this.*,store.store_id,userpriv.store_id as priv_store_id,userpriv.privs',
            'conditions' => '1=1' . ' and serve_type='.$_GET['serve_type'].$conditions,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));

        $model_order =& m('order');
        foreach ($users as $key => $val)
        {
            if ($val['priv_store_id'] == 0 && $val['privs'] != '')
            {
                $users[$key]['if_admin'] = true;
            }
            //该会员支付过 status = 20代表支付成功
            $users[$key]['order_num'] = count($model_order->find(array('conditions' => 'status= 20 AND user_id='.$key)));

        }

        $this->assign('users', $users);
       
        $this->assign('tname', $this->_lv_mod->_typename);
        $page['item_count'] = $this->_user_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        $this->assign('query_fields', array(
            'user_name' => LANG::get('user_name'),
            'email'     => LANG::get('email'),
            'real_name' => LANG::get('real_name'),
//            'phone_tel' => LANG::get('phone_tel'),
//            'phone_mob' => LANG::get('phone_mob'),
        ));
        $this->assign('sort_options', array(
            'user_id DESC'   => LANG::get('reg_time'),
            'last_login DESC' => LANG::get('last_login'),
            'logins DESC'     => LANG::get('logins'),
        ));

        if($_GET['serve_type'] == '4')
        {
			$this->display('user.index.html');
            //$this->display('serve_designer.index.html');
		}else{
		 $this->display('profit/user.index.html');
		}

        //$this->display('user.index.html');
    }
//	审批图片
    function check_pic(){
    	$db = &db();
    	
    	$m = m('userphoto');
    	
    	$id = $_REQUEST['id'];
    	$status = $_REQUEST['status'];
    	if(!$id)
    		exit(' id is null ');

    	$photo = $m->getById($id);
    	if(!$photo)
    		exit(' id not in db');
    	
    	if(!$status)
    		exit(' status is null ');
    	
    	if(intval($status) <=0 || intval($status) >2)
    		exit (" status <=0 and > 2");
    	
    	//状态如果相同就不用更新状态了
    	if($status == $photo['status'])
    		exit(" 状态相同，无须修改");
    	
    	$sql = "update cf_userphoto set status = $status where id = $id limit 1 ";
    	$rs = $db->query($sql);
    	
    	if($photo['cate'] == 1)
    		$cate = 'sheji_reward';
    	else
    		$cate = "sheji_reward";
    	
    	
    	if ($status == 1 ){
    		$log = getPointLog($photo['uid'], $cate , '' ,$id);
    		if(!$log){
		    	$num = pointTurnNum($cate);
		    	setPoint($photo['uid'], $num, 'add', $cate, 'system' , '上传审核通过','',$id);
    		}
    	}
    	
    	echo $rs;
    		
    } 
	
    function upload_recommend()
    {
    	if(!IS_POST)
    	{
	    	$this->assign('status',array("0"=>"未审核","1"=>"已通过","2"=>"已拒绝"));
		    			
		    			
		    $this->assign('clothingtypes',array("0"=>"未选择","1"=>"西服","2"=>"西裤","3"=>"马甲","4"=>"衬衣","5"=>"大衣"));
		    			
		    $this->assign('clothingstyles',array("0"=>"未选择","1"=>"男装","2"=>"女装"));
		    
		    
	    	$this->display('user.upload_recommend.html');
	    }else 
	    {

	    	include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
			$imageTool = new ImageTool(ROOT_PATH.'/upload/jipaijizuo/');
	    	$imageTool->_upload_dir=ROOT_PATH.'/upload/jipaijizuo/';
	    	
	    	$data=array('clothingtypes'=>$_POST['clothingtypes'],'clothingstyles'=>$_POST['clothingstyles'],'status'=>1,'add_time'=>time(),'cate'=>3,'uid'=>0);
    				
    		$id=$this->_userphoto_mod->add($data);
			
    		
    		
    		if($id){
    			
    			$upfile=array();
		    	foreach ($_FILES as $k=>$v)
		    	{
		    		
		    		if($k=='url'||$k=='url2'||$k=='url3')
		    		{
		    			//$upfile[$k]=$this->_upload_photo($v, $id, $k);
		    			//var_dump($imageTool->_upload_dir);
		    			//var_dump($v);
		    			$upfile[$k]=$imageTool->uploadImage($v);
		    			//var_dump($rfile);
        				//var_dump($imageTool->_error_info);
        				//exit;
        				if($upfile[$k])
        				{
	        				$goods_thumb=$imageTool->makeThumb($imageTool->_upload_dir.$upfile[$k], 100, 100);
	        				
			    			if($k=='url')
			    			{
			    				//$upfile['url_thumb1']='thumb_100_100_'.$upfile[$k];
			    				$upfile['url_thumb1']=$goods_thumb;
			    			}elseif($k=='url2')
			    			{
			    				//$upfile['url2_thumb1']='thumb_100_100_'.$upfile[$k];
			    				$upfile['url3_thumb1']=$goods_thumb;
			    			}elseif($k=='url3')
			    			{
							    //$upfile['url3_thumb1']='thumb_100_100_'.$upfile[$k];
							    $upfile['url3_thumb1']=$goods_thumb;
			    			}
        				}
		    			
		    		}
		    		else
		    		{
		    			//$upfilegallery[$k]=$this->_upload_photo($v, $id, $k);
		    			$upfilegallery[$k]=$imageTool->uploadImage($v);
		    			if($upfilegallery[$k])
		    			{
		    				$this->_userphotogallery_mod->add(array('project_id'=>$id,'img_url'=>$upfilegallery[$k]));
		    			}
		    		}
		    		
		    		
		    	}
		    	$id=$this->_userphoto_mod->edit($id,$upfile);
    		}
		    
		    
		    $this->show_message('添加成功',
                '返回列表',    'index.php?app=user&amp;act=upload',
                '再次添加', 'index.php?app=user&amp;act=upload_recommend'
            );
		    	
	    }
	    
	    
	    
    }
    
    function upload_edit()
    {
    		$id=intval($_GET['id']);
    		
    		
    		if($id)
    		{	
    			
    			$this->_userphoto_mod->edit($id,array('statuscode'=>0));
    			
    			
    			$this->_userphotocomment_mod->edit('  comment_id='.$id,array('statuscode'=>0));
    			
    			if(!IS_POST)
    			{
	    			$this->assign('status',array("0"=>"未审核","1"=>"可满足需求","2"=>"暂不满足需求"));
	    			
	    			
	    			$this->assign('clothingtypes',array("0"=>"未选择","1"=>"西服","2"=>"西裤","3"=>"马甲","4"=>"衬衣","5"=>"大衣"));
	    			
	    			$this->assign('clothingstyles',array("0"=>"未选择","1"=>"男装","2"=>"女装"));
	    			
	    			
	    			
	    			$this->assign('paystate',array("0"=>"未选择","1"=>"不可支付","2"=>"可以支付","3"=>"支付完成"));
	    			
	    			
	    			
	    			$data = $this->_userphoto_mod->get($id);
	    			
	    			$userphotogallerydata=$this->_userphotogallery_mod->find(array(
	    				'conditions' => 'cate= \'jipai\' and project_id='.$id,
			    		'limit' => 10,
			    		'order' => "img_id asc",
			    		'count' => true,
	    			));
	    			/*
	    			foreach ($userphotogallerydata as $k=>$v)
	    			{
	    				if($v['img_url'])
	    				{
	    					$userphotogallerydata[$k]['img_url']=getSingleUrl($v['img_url']);
	    				}
	    			}
	    			*/
	    			//var_dump($userphotogallerydata);
	    			$this->assign('userphotogallerydata', $userphotogallerydata);
	    			
    				$cate=empty($_GET['cate'])?3:$_GET['cate'];
	    			
	    			/*
		    			if($cate == 1){
		    				
			    			$data['url'] =!empty($data['url'])?getDesignUrl($data['url']):'';
			    			
			    			$data['url2'] =!empty($data['url2'])?getDesignUrl($data['url2']):'';
			    			
			    			$data['url3'] =!empty($data['url3'])?getDesignUrl($data['url3']):'';
			    			
			    			$data['url4'] =!empty($data['url4'])?getDesignUrl($data['url4']):'';
			    			
			    			$data['url5'] =!empty($data['url5'])?getDesignUrl($data['url5']):'';
			    			
			    			$data['url6'] =!empty($data['url6'])?getDesignUrl($data['url6']):'';
			    			
			            }elseif($cate == 2){ 
			    			$data['url'] = !empty($data['url'])?getCameraUrl($data['url']):'';
			    			
			    			$data['url2'] =!empty($data['url2'])?getCameraUrl($data['url2']):'';
			    			
			    			$data['url3'] =!empty($data['url3'])?getCameraUrl($data['url3']):'';
			    			
			    			$data['url4'] =!empty($data['url4'])?getCameraUrl($data['url4']):'';
			    			
			    			$data['url5'] =!empty($data['url5'])?getCameraUrl($data['url5']):'';
			    			
			    			$data['url6'] =!empty($data['url6'])?getCameraUrl($data['url6']):'';
			    			
			    		}elseif($cate == 3){
			                $data['url'] = !empty($data['url'])?getSingleUrl($data['url']):'';
			                
			                
			                $data['url2'] =!empty($data['url2'])?getSingleUrl($data['url2']):'';
			    			
			                $data['url3'] =!empty($data['url3'])?getSingleUrl($data['url3']):'';
			    			
			                $data['url4'] =!empty($data['url4'])?getSingleUrl($data['url4']):'';
			    			
			                $data['url5'] =!empty($data['url5'])?getSingleUrl($data['url5']):'';
			    			
			                $data['url6'] =!empty($data['url6'])?getSingleUrl($data['url6']):'';
			                
			    			
			            }
			            */
	    			$this->assign('data', $data);
	    			
	    			$comment_data=$this->_userphotocomment_mod->findAll(array(
			    			'conditions' => 'comment_id='.$id,
			    			'limit' => 10,
			    			'order' => "add_time desc,id desc",
			    			'count' => true,
			    			'include'=>array(
	    						'has_photo_gallery',
	    					),
			    	));
			    	
			    	
	    			//print_r($comment_data);exit;
			    				    	
	    			$this->assign('comment_data', $comment_data);
	    			
	    			$this->display('user.upload_edit.html');
    			}
    			else 
    			{
    				//var_dump($_POST);exit;
    				if($_POST['replytype']=='1')
    				{
	    				$data=array('clothingtypes'=>$_POST['clothingtypes'],'clothingstyles'=>$_POST['clothingstyles'],'status'=>$_POST['status']);
	    		
	    				$data['paystate']=$_POST['paystate'];
	    				$this->_userphoto_mod->edit($id,$data);
	    				
	    				$this->show_message('edit_ok');
	    				//exit;
    				}
    				else
    				{
    					include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
						$imageTool = new ImageTool(ROOT_PATH.'/upload/jipaijizuo/');
	    				$imageTool->_upload_dir=ROOT_PATH.'/upload/jipaijizuo/';
    					
    					$arr=array('uid'=>$this->_admin_user_mod['user_id'],'user_name'=>$this->_admin_user_mod['user_name'],'content'=>trim($_POST['reply']),'add_time'=>time(),'comment_id'=>$id,'cate'=>1);
    					
	    				$comment_id=$this->_userphotocomment_mod->add($arr);		
    					//exit;
		    			if($_FILES)
		    			{
		    				foreach ($_FILES as $k=>$v)
					    	{
					    			if($v['name'])
					    			{
						    			$upfilegallery[$k]=$imageTool->uploadImage($v);
	
						    			//$upfilegallery[$k]=$this->_upload_photo($v, $id, $k);
						    			if($upfilegallery[$k])
						    			{
						    				$this->_userphotogallery_mod->add(array('project_id'=>$comment_id,'img_url'=>$upfilegallery[$k],'cate'=>'msg'));
						    				
						    			}
					    			}
					    	}
					    	
					    	
					    	
					    	
		    			}
	    				
	    				
	    				//var_dump(1);
    					$this->show_message('回复成功');
    					//exit;
    				}
    				
    				
    			}
    		}
    }
    
    function work_edit(){
        
        $id=intval($_GET['id']);
        if($id){
            if(!IS_POST){
				$this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
                $this->assign('category',array("0003"=>"西服","0004"=>"西裤","0006"=>"衬衫","0005"=>"马甲","0007"=>"大衣","pcs"=>"套装"));
                
                $this->assign('type',array("1"=>"男装","2"=>"女装"));

                $data = $this->_userwork_mod->get($id);

                $this->assign('data',$data);
                $this->display('user.work_edit.html');
               
            }else{
                $stime = isset($_POST['start_time']) ? strtotime($_POST['start_time']) : 0;
                $etime = isset($_POST['end_time']) ? strtotime($_POST['end_time']) : 0;
                if($stime && $etime){
                    if($stime >= $etime){
                        $this->show_message("主题结束时间必须大于开始时间！");
                        return false;
                    }
                }
                $data=array('cloth'=>$_POST['category'],'type'=>$_POST['type'],'start_time'=>$stime,'end_time'=>$etime,);

             
                $this->_userwork_mod->edit($id,$data);
                

                $this->show_message('edit_ok',
                    '返回列表',    'index.php?app=user&amp;act=work'
                    );
                
                
                
            }
            
            
            
        }
     
        
        
        
    }
    
	function upload_comment()
    {
    	
    		$id=$_GET['id'];
    		$content=$_GET['content'];
    		
    		if($content)
    		{
	    		$arr=array('user_name'=>'admin','content'=>trim($content),'add_time'=>time(),'comment_id'=>$id,'cate'=>1);
	    		$this->_userphotocomment_mod->add($arr);
    		}
    		
    		echo ecm_json_encode(true);
    }
    
    function upload_ajax(){
    	$cate = $_REQUEST['cate'];
    	if(!$cate)
    		$cate = 3;  //默认晒图
    	
		$sort = ($_GET['sort'] == 'add_time') ? 'add_time' : 'id';
    	$order = empty($_GET['order']) ? 'desc' : $_GET['order'];
    	
    	$conditions = "cate = ".$cate;
    	
    	$date = empty($_GET['date']) ? '' : $_GET['date'];
    	
    	if($date)
    	{
    		if(strlen($date)==13)
    		{
				//var_dump($date);
    			$date=substr($date,0,10);
				//var_dump($date);
    		}
    		
//    		$date=$date-120;
    		
    		$conditions.=' and add_time >= '.$date;
    		//var_dump($conditions);exit;
    	}
    	
    	//查询条件
    	$conditions .= $this->_get_query_conditions(array(
    			array(
    					'field' => $_GET['field_name'],
    					'name'  => 'field_value',
    					'equal' => 'like',
    			),
    	));
    	
    	$data = $this->_userphoto_mod->findAll(array(
    			'fields' => 'this.id,this.add_time,this.uid',
    			'conditions' => 'statuscode=1 and ' . $conditions,
    			'order' => "add_time desc,id desc",
    			'count' => true,
    			'index_key'=>false,
    			//'include'=>array(
    			//	'has_photo_comment',
    			//),
    	));
    	
    	
    	
    	foreach($data as $k=>$v){
    		/*
    		if($v['url'])
    		{
    		if($cate == 1){
    			$data[$k]['url'] =getDesignUrl($v['url']);
            }elseif($cate == 2){ 
    			$data[$k]['url'] = getCameraUrl($v['url']);
    		}elseif($cate == 3){
    			
                $data[$k]['url'] = getSingleUrl($v['url']);
            }
    		}
    		*/
    		if($v['album_id']){
    			$m = m('album');
    			$album = $m->getById($v['album_id']);
    			$data[$k]['album'] = $album['title'];
    		}
    		$user = array();
    		$user = getUinfoByUid($v['uid']);
			//var_dump($v);exit;
            
    		
    		$data[$k]['title'] = '即拍即做';
    		$data[$k]['content'] = '【'.$user['user_name'].'】发起了一个新的即拍，即拍ID:'.$v['id'].'';
    		unset($data[$k]['id']);
    		unset($data[$k]['uid']);
    		
    	}
    	
    	if($date)
    	{
    		$conditionscomment.=' and add_time >= '.$date;
    	}
    	
    	$datacomment= $this->_userphotocomment_mod->find(array(
    		'fields' => 'this.add_time,this.user_name,this.comment_id',
    		'conditions' => ' user_name<>\'admin\' and statuscode=1 '.$conditionscomment,
    		'order' => "add_time desc,id desc",
    		'index_key'=>false,
    	));
    	
    	
    	foreach($datacomment as $k=>$v){
    		$datacomment[$k]['title'] = '即拍即做';
    		$datacomment[$k]['content'] = '【'.$v['user_name'].'】发起了一个新的评论，即拍ID:'.$v['comment_id'].'';
    		
    		unset($datacomment[$k]['user_name']);
    		unset($datacomment[$k]['comment_id']);
    		
    		
    	}
    	
    	$data=array_merge($data,$datacomment);
    	
    	//var_dump($datacomment);exit;
    	//var_dump($this->_userphoto_mod->_relation['has_photo_comment']);exit;
    	
    	//print_r($data);exit;
    	
    	echo json_encode($data);exit;
    	
    	
    }
    
    function upload(){
    	$db = &db();    	
    	$cate = $_REQUEST['cate'];
    	if(!$cate)
    		$cate = 3;  //默认晒图
    	
    	
    	if ($_GET['field_name'] == 'uid'){
    		$temp = $_GET['field_value'];
    		$sql = "select user_id from cf_member where user_name = '".$_GET['field_value']."'";
    		$uinfo = $this->_user_mod->getRow($sql);
    		$_GET['field_value'] = $uinfo['user_id'];
    	}
    	
    	$conditions = "cate = ".$cate;
    	//查询条件
    	$conditions .= $this->_get_query_conditions(array(
    			array(
    					'field' => $_GET['field_name'],
    					'name'  => 'field_value',
    					'equal' => 'like',
    			),
    	));
    	
    	$sort = ($_GET['sort'] == 'add_time') ? 'add_time' : 'id';
    	$order = empty($_GET['order']) ? 'desc' : $_GET['order'];
    	
    	
    	$page = $this->_get_page(25);
    	$data = $this->_userphoto_mod->find(array(
    			'fields' => 'this.*',
    			'conditions' => '1=1 and ' . $conditions,
    			'limit' => $page['limit'],
    			'order' => "$sort $order",
    			'count' => true,
    	));
    	 
    	
    	$page['item_count'] = $this->_userphoto_mod->getCount();
    	$this->_format_page($page);
    	$this->assign('filtered', $_GET['field_value']? 1 : 0); //是否有查询条件
    	$this->assign('page_info', $page);

    	$this->assign('query_fields', array(
    			'title' => '标题',
    			'uid'     => '用户',
    	));

    	foreach($data as $k=>$v){
    		/*
    		if($v['url'])
    		{
    		if($cate == 1){
    			$data[$k]['url'] =getDesignUrl($v['url']);
            }elseif($cate == 2){ 
    			$data[$k]['url'] = getCameraUrl($v['url']);
    		}elseif($cate == 3){
    			
                $data[$k]['url'] = getSingleUrl($v['url']);
            }
    		}
    		*/
    		if($v['album_id']){
    			$m = m('album');
    			$album = $m->getById($v['album_id']);
    			$data[$k]['album'] = $album['title'];
    		}
    		$user = array();
    		$user = getUinfoByUid($v['uid']);

            $data[$k]['date'] = date('Y-m-d H:i:s',$v['add_time']);
    		$data[$k]['uname'] = $user['user_name'];
    		
    	}
    	
    	$this->assign('field_value',$_GET['field_value']);
    	
    	if ($_GET['field_name'] == 'uid')	$this->assign('field_value',$temp);
    	
    	$this->assign('cate',$cate);
    	$this->assign('data',$data);
    	
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    	
    	$this->display('user.upload.html');
    }
    function work(){
        $category = array("0003"=>"西服","0004"=>"西裤","0006"=>"衬衫","0005"=>"马甲","0007"=>"大衣","pcs"=>"套装","0001"=>"套装","0002"=>"套装");
         
        $db = &db();
        $cate = $_REQUEST['cate'];
        if(!$cate)
            $cate = 3;  //默认晒图
         
         
        if ($_GET['field_name'] == 'uid'){
            $temp = $_GET['work_value'];
            $sql = "select user_id from cf_member where user_name = '".$_GET['field_value']."'";
            $uinfo = $this->_user_mod->getRow($sql);
            $_GET['field_value'] = $uinfo['user_id'];
        }
         
    
        //查询条件
        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'work_name',
                'name'  => 'work_name',
                'equal' => '=',
            ),
        ));


        $sort = ($_GET['sort'] == 'work.add_time') ? 'wrok.add_time' : 'work.id';
        $order = empty($_GET['order']) ? 'desc' : $_GET['order'];
         
         
        $page = $this->_get_page(30);
        $data = $this->_userwork_mod->find(array(
            'fields' => 'this.*',
            'conditions' => '1=1' . $conditions,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        )); 

       foreach ($data as $key => $value) {
           if($data[$key]['id']){
            $imgurl = $this->_userworkimg_mod->find(array(
                        'conditions' => 'work_id=' . $data[$key]['id'],
                        'fields'     => 'img_url',
                    ));
            $cus_arr[] = $this->_mod_custom->find(array(
                   'conditions' => 'work_id=' . $data[$key]['id'],
               ));
                      
            $data[$key]['imgurl']= $imgurl;

           }

            $data[$key]['add_time']=date('Y-m-d H:i:s',$value['add_time']);
            $data[$key]['start_time']=date('Y-m-d H:i:s',$value['start_time']);
            $data[$key]['end_time']=date('Y-m-d H:i:s',$value['end_time']);
            $data[$key]['category']=$category[$value['cloth']];
            
            
       }
         
        $page['item_count'] = $this->_userwork_mod->getCount();
        $this->_format_page($page);
        $this->assign('cus_arr',$cus_arr);
        $this->assign('filtered', $_GET['field_value']? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
 
        
     
        
    //  foreach($data as $k=>$v){
         /*    if($v['url'])
            {
                if($cate == 1){
                    $data[$k]['url'] =getDesignUrl($v['url']);
                }elseif($cate == 2){
                    $data[$k]['url'] = getCameraUrl($v['url']);
                }elseif($cate == 3){
                     
                    $data[$k]['url'] = getSingleUrl($v['url']);
                }
            }
            if($v['album_id']){
                $m = m('album');
                $album = $m->getById($v['album_id']);
                $data[$k]['album'] = $album['title'];
            }
            $user = array();
            $user = getUinfoByUid($v['uid']);
         */
           // $data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
          
        
      //  }
         
     
         
        $this->assign('cate',$cate); 
        $this->assign('data',$data);
         
        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
         
        
        $this->display('user.work.html');
        
    }

    function delWork(){
        $id = $_REQUEST['id'];

        $work = $this->_work_mod->get("id=$id");
        if($work['cloth']=='pcs'){//删除套装
            $suit = $this->_mod_suit-> get("work_id=".$work['id']);
            $this->_mod_suit->drop("suit_id=".$suit['id']);
            $this->_mod_suitre->drop("suit_id=".$suit['id']);
            $this->_mod_suitattr->drop("suit_id=".$suit['id']);
            $isdel=$this->_userswork_mod->drop("id=$id");

        }else{//删除基本款
            $cus  = $this->_mod_custom->get("work_id=".$work['id']);
            $this->_mod_custom->drop("id=".$cus['id']);
            $this->_mod_cusItem->drop("custom_id=".$cus['id']);
            $this->_mod_cusFab->drop("custom_id=".$cus['id']);
            $isdel=$this->_userwork_mod->drop("id=$id");
        }

        if($isdel){
            echo '删除成功 ';
        }else{
            
            echo "删除失败";
        }
      
    }
    
    function check_pic_work(){
        $db = &db();
         
       $this->userwork=&m('userwork');
         
        $id = $_REQUEST['id'];
        $status = $_REQUEST['status'];
    
        if(!$id)
            exit(' id is null ');
    
        $work = $this->userwork->get($id);
   
        if(!$work)
            exit(' id not in db');
         
        if(!$status)
            exit(' status is null ');
         
        if(intval($status) <=0 || intval($status) >2)
            exit (" status <=0 and > 2");
         
        //状态如果相同就不用更新状态了
    
        if($status == $work['status'])
            exit(" 状态相同，无须修改");
         
        $sql = "update cf_works set status = $status where id = $id limit 1 ";
        $rs = $db->query($sql);
         
       /*  if($photo['cate'] == 1)
            $cate = 'sheji_reward';
        else
            $cate = "sheji_reward";
         
         
        if ($status == 1 ){
            $log = getPointLog($photo['uid'], $cate , '' ,$id);
            if(!$log){
                $num = pointTurnNum($cate);
                setPoint($photo['uid'], $num, 'add', $cate, 'system' , '上传审核通过','',$id);
            }
        } */
         
        echo $rs;
    
    }

    function delPhoto(){
    	$id = $_REQUEST['id'];
    	
    	delUserPhoto($id);
    }

    function delBatPhoto(){
        $ids = $_REQUEST['ids'];
        if(!$ids)
            exit('ids is null');

        $ids = substr($ids,0,strlen($ids)-1);
        $ids = explode(',',$ids);
        foreach($ids as $k=>$v){
            delUserPhoto($v);
        }
    }
    //推荐到首页将图片
    function pic_recommend(){
    	$id = $_REQUEST['id'];
    	$db = &db();
    	
    	$sql = "select * from cf_userphoto  where id = ".$id;
    	$photo = $db->getRow($sql);
    	if(!$photo)
    		echo 0;

    	if($photo['recommend'])
    		$num = 0;
    	else 
    		$num = 1;
    	
    	$sql = "update cf_userphoto set recommend = $num where id = ".$id;
    	$rs = $db->query($sql);
    	
    	echo 1;
    	
    }
    
/**
    *后台资金调整
    *@author liang.li <1184820705@qq.com>
    *@2015年7月15日
    */
    function type1($user_id) 
    {
        $condition = "";
        $page = $this->_get_page(2);
        $account = $this->_account_mod->find(array(
            'fields' => '*',
            'conditions' => "user_id = '{$user_id}'" . $condition,
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
        
        $page['item_count'] = $this->_account_mod->getCount();
        
        $this->_format_page($page);
        
        $this->assign('page_info', $page);
        $this->assign('account_list', $account);
    }
    
    /**
    *订单返现记录
    *@author liang.li <1184820705@qq.com>
    *@2015年7月15日
    */
    function type2($user_id) 
    {
        
//     echo $user_id;
        $mod = m('ordercashlog');
        $condition = " is_point = 0";
        $page = $this->_get_page(20);
        $account = $mod->find(array(
            'fields' => '*',
            'conditions' => "user_id = '{$user_id}' AND " . $condition,
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
//   dump($account);      
        $page['item_count'] = $mod->getCount();
        
        $this->_format_page($page);
        
        $this->assign('page_info', $page);
       
        $this->assign('account_list', $account);
    }
    
    
    /**
     *注册送红包资金
     *@author liang.li <1184820705@qq.com>
     *@2015年7月15日
     */
    function type3($user_id)
    {
    
        //     echo $user_id;
        $mod = m('ordercashlog');
        $condition = " is_point = 1";
        $page = $this->_get_page(20);
        $account = $mod->find(array(
            'fields' => '*',
            'conditions' => "user_id = '{$user_id}' AND " . $condition,
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
//   dump($account);      
        $page['item_count'] = $mod->getCount();
        
        $this->_format_page($page);
        
        $this->assign('page_info', $page);
       
        $this->assign('account_list', $account);
    }
    
    /**
     *订单送积分
     *@author liang.li <1184820705@qq.com>
     *@2015年7月15日
     */
    function type4($user_id)
    {
    
        //     echo $user_id;
        $mod = m('gift');
        $condition = " ";
        $page = $this->_get_page(20);
        $account = $mod->find(array(
            'fields' => '*',
            'conditions' => "user_id = '{$user_id}' " . $condition,
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
        //   dump($account);
        $page['item_count'] = $mod->getCount();
    
        $this->_format_page($page);
    
        $this->assign('page_info', $page);
         
        $this->assign('account_list', $account);
    }
    
    /**
     *订单送积分
     *@author liang.li <1184820705@qq.com>
     *@2015年7月15日
     */
    function type5($user_id)
    {
    
        //     echo $user_id;
        $mod = m('debit');
        $condition = " ";
        $page = $this->_get_page(20);
        $account = $mod->find(array(
            'fields' => '*',
            'conditions' => "user_id = '{$user_id}' " . $condition,
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
        //   dump($account);
        $page['item_count'] = $mod->getCount();
    
        $this->_format_page($page);
    
        $this->assign('page_info', $page);
         
        $this->assign('account_list', $account);
    }
    
    function account(){
    	$user_id = intval($_GET['id']);
    	$user = $this->_user_mod->get_info($user_id);
		$type    = isset($_GET['type']) ? $_GET['type'] : 1;
    	if(empty($user_id))
    	{
    		$this->show_warning('非法操作！');
    		return;
    	}
    	$this->assign('user', $user);
    	$this->assign('user_id', $user_id);
    	$this->assign('type', $type);
    	
    	if ($type == 1) 
    	{
    	    $this->type1($user_id);
    	    $this->display('profit/user.account.html');
    	}
    	elseif ($type == 2)
    	{
    	    $this->type2($user_id);
    	    $this->display('profit/user.account2.html');
    	}
    	elseif ($type == 3)
    	{
    	    $this->type3($user_id);
    	    $this->display('profit/user.account3.html');
    	}
    	
    	elseif ($type == 4)
    	{
    	    $this->type3($user_id);
    	    $this->display('profit/user.account4.html');
    	}
    	
    	elseif ($type == 5)
    	{
    	    $this->type5($user_id);
    	    $this->display('profit/user.account5.html');
    	}
    	
    	
    }
    
    function change_account(){
    	$user_id = intval($_GET['id']);
    	
    	$type    = $_GET['type'];
    	
    	if(empty($user_id)){
    		$this->show_warning('非法操作！');
    	
    		return;
    	}
        //判断是否是管理员
        if($_SESSION['admin_info']['user_name'] != 'admin'){
            $this->show_warning('你不是管理员，不能进行操作！');
            return;
        }
    	
    	$user = $this->_user_mod->get_info($user_id);
    	if (!$user)
    	{
    		$this->show_warning('user_empty');
    		return;
    	}
    
    	if (!IS_POST)
    	{
    		$this->assign('user_id', $user_id);
    		 
    		$this->assign('user', $user);
    		 
    		$this->display('profit/user.change_account.html');
    	}
    	else
    	{
    		
    		$remark    = sub_str($_POST['remark'], 255, false);
    		$money     = floatval($_POST['change_money']) * abs(floatval($_POST['money']));
    		$frozen   = floatval($_POST['change_frozen']) * abs(floatval($_POST['frozen']));
// 			if($user['money']+$money < 0){
// 				$this->show_warning('可用资金不能小于0!');
// 				return;
// 			}
			
// 			if($user['money']-($frozen+$user['frozen']) < 0){
// 				$this->show_warning('冻结资金不能大于可用资金!');
// 				return;
// 			}
            if (!is_numeric($money) || !is_numeric($frozen)) 
            {
                $this->show_warning('请填写数字');
                return;
            }
// dump($_POST);			
			if(empty($remark)){
				$this->show_warning('请填写备注!');
				return;
			}
			
			$log = array(
				'dateline' => time(),
				'remark'   => $remark,
				'money'	   => $money,
				'frozen'   => $frozen,
				'user_id'  => $user_id,
				'type'     => CHANGE_TYPE1,
			);
			
			
			$transaction = $this->_account_mod->beginTransaction();
			$res = $this->_account_mod->submit($log);
			if (!$res)
			{
			    $this->_account_mod->rollback();
			    $this->show_warning('失败');
			    return;
			}
			$this->_account_mod->commit($transaction);
			$this->show_message('成功');
			
    	}
    }
    
    function add()
    {
        if (!IS_POST)
        {
            $this->assign('user', array(
                'gender' => 0,
            ));
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $ms =& ms();
            $this->assign('set_avatar', $ms->user->set_avatar());
            $this->assign('tname', $this->_lv_mod->_typename);
            $lvs= $this->_lv_mod->find(array(
            		'conditions' => 'lv_type="'.current(array_flip($this->_lv_mod->_typename)).'"',
            		'fields'=>'name'
            ));
            $this->assign('lvs', $lvs);
            
            $this->display('user.form.html');
        }
        else
        {
        	
            $user_name = trim($_POST['user_name']);
            $password  = trim($_POST['password']);
            $email     = trim($_POST['email']);
            $real_name = trim($_POST['real_name']);
            $gender    = trim($_POST['gender']);
            $im_qq     = trim($_POST['im_qq']);
            $serve_type    = trim($this->_lv_mod->_typeid[$_POST['lv_type']]);
            $member_lv_id    = trim($_POST['member_lv_id']);
            $im_msn    = trim($_POST['im_msn']);

            if (strlen($user_name) < 3 || strlen($user_name) > 25)
            {
                $this->show_warning('user_name_length_error');

                return;
            }

            if (strlen($password) < 6 || strlen($password) > 20)
            {
                $this->show_warning('password_length_error');

                return;
            }

            if (!is_email($email))
            {
                $this->show_warning('email_error');

                return;
            }

            /* 连接用户系统 */
            $ms =& ms();

            /* 检查名称是否已存在 */
            if (!$ms->user->check_username($user_name))
            {
                $this->show_warning($ms->user->get_error());

                return;
            }

            /* 保存本地资料 */
            $data = array(
                'real_name' => $_POST['real_name'],
                'gender'    => $_POST['gender'],
//                'phone_tel' => join('-', $_POST['phone_tel']),
//                'phone_mob' => $_POST['phone_mob'],
                'im_qq'     => $_POST['im_qq'],
                'im_msn'    => $_POST['im_msn'],
//                'im_skype'  => $_POST['im_skype'],
//                'im_yahoo'  => $_POST['im_yahoo'],
//                'im_aliww'  => $_POST['im_aliww'],
               'serve_type'  => $serve_type,
               'member_lv_id'  => $member_lv_id,
                'reg_time'  => gmtime(),
            );

            /* 到用户系统中注册 */
            $user_id = $ms->user->register($user_name, $password, $email, $data);
            if (!$user_id)
            {
                $this->show_warning($ms->user->get_error());

                return;
            }

            if (!empty($_FILES['portrait']))
            {
                $portrait = $this->_upload_portrait($user_id);
                if ($portrait === false)
                {
                    return;
                }

                $portrait && $this->_user_mod->edit($user_id, array('portrait' => $portrait));
            }


            $this->show_message('add_ok',
                'back_list',    'index.php?app=user&amp;serve_type=0',
                'continue_add', 'index.php?app=user&amp;act=add'
            );
        }
    }

    /*检查会员名称的唯一性*/
    function  check_user()
    {
          $user_name = empty($_GET['user_name']) ? null : trim($_GET['user_name']);
          if (!$user_name)
          {
              echo ecm_json_encode(false);
              return ;
          }

          /* 连接到用户系统 */
          $ms =& ms();
          echo ecm_json_encode($ms->user->check_username($user_name));
    }

    function edit()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $user = $this->_user_mod->get_info($id);
            if (!$user)
            {
                $this->show_warning('user_empty');
                return;
            }

            $ms =& ms();
            $this->assign('set_avatar', $ms->user->set_avatar($id));
            $this->assign('user', $user);
            
            $this->assign('tname', $this->_lv_mod->_typename);
            $mtyid = array_flip($this->_lv_mod->_typeid);
            $this->assign('tid', $mtyid[$user['serve_type']]);
            $lvs= $this->_lv_mod->find(array(
            		'conditions' => 'lv_type="'.$mtyid[$user['serve_type']].'"',
            		'fields'=>'name'
            ));
            $this->assign('lvs', $lvs);
            
            $this->assign('phone_tel', explode('-', $user['phone_tel']));
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->display('user.form.html');
        }
        else
        {
            $data = array(
                'real_name' => $_POST['real_name'],
                'gender'    => $_POST['gender'],
//                'phone_tel' => join('-', $_POST['phone_tel']),
//                'phone_mob' => $_POST['phone_mob'],
                'im_qq'     => $_POST['im_qq'],
                'im_msn'    => $_POST['im_msn'],
//                'im_skype'  => $_POST['im_skype'],
//                'im_yahoo'  => $_POST['im_yahoo'],
//                'im_aliww'  => $_POST['im_aliww'],
            		'serve_type'  => trim($this->_lv_mod->_typeid[$_POST['lv_type']]),
            		'member_lv_id'  => trim($_POST['member_lv_id']),
            );
            if (!empty($_POST['password']))
            {
                $password = trim($_POST['password']);
                if (strlen($password) < 6 || strlen($password) > 20)
                {
                    $this->show_warning('password_length_error');

                    return;
                }
            }
            if (!is_email(trim($_POST['email'])))
            {
                $this->show_warning('email_error');

                return;
            }

            if (!empty($_FILES['portrait']))
            {
                $portrait = $this->_upload_portrait($id);
                if ($portrait === false)
                {
                    return;
                }
                $data['portrait'] = $portrait;
            }

            /* 修改本地数据 */
            $this->_user_mod->edit($id, $data);

            /* 修改用户系统数据 */
            $user_data = array();
            !empty($_POST['password']) && $user_data['password'] = trim($_POST['password']);
            !empty($_POST['email'])    && $user_data['email']    = trim($_POST['email']);
            if (!empty($user_data))
            {
                $ms =& ms();
                $ms->user->edit($id, '', $user_data, true);
            }

            $this->show_message('edit_ok',
                'back_list',    'index.php?app=user&amp;serve_type=0',
                'edit_again',   'index.php?app=user&amp;act=edit&amp;id=' . $id
            );
        }
    }

    function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_user_to_drop');
            return;
        }
        $admin_mod =& m('userpriv');
        if(!$admin_mod->check_admin($id))
        {
            $this->show_message('cannot_drop_admin',
                'drop_admin', 'index.php?app=admin');
            return;
        }

        $ids = explode(',', $id);

        /* 连接用户系统，从用户系统中删除会员 */
        $ms =& ms();
        if (!$ms->user->drop($ids))
        {
            $this->show_warning($ms->user->get_error());

            return;
        }

        $this->show_message('drop_ok');
    }

    /**
     * 上传头像
     *
     * @param int $user_id
     * @return mix false表示上传失败,空串表示没有上传,string表示上传文件地址
     */
    function _upload_portrait($user_id)
    {
        $file = $_FILES['portrait'];
        if ($file['error'] != UPLOAD_ERR_OK)
        {
            return '';
        }

        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->addFile($file);
        if ($uploader->file_info() === false)
        {
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=user&amp;act=edit&amp;id=' . $user_id);
            return false;
        }

        $uploader->root_dir(ROOT_PATH);
        
        return $uploader->save('data/files/mall/portrait/' . ceil($user_id / 500), $user_id);
    }
    
	
    
	function _upload_photo($file,$user_id,$type)
    {
        
        if ($file['error'] != UPLOAD_ERR_OK)
        {
            return '';
        }

        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->addFile($file);
        if ($uploader->file_info() === false)
        {
            return false;
        }

        $uploader->root_dir(ROOT_PATH);
        //var_dump( '/upload_user_photo/shaidan/520x685/' . $user_id.'_'.$type);exit;
        //return $uploader->save('/upload_user_photo/shaidan/520x685', $type.'_'.$user_id.'_'.$uploader->random_filename());
        $rndname=$uploader->random_filename();
        $uploader->save('/upload_user_photo/shaidan/520x685', $type.'_'.$user_id.'_'.$rndname);
        
        
        
        
        $file_arr=$uploader->file_info();
         
         
		
        return $type.'_'.$user_id.'_'.$rndname.'.'.$file_arr['extension'];
    }
    
    /**
     * 梳理街拍/设计数据
     * @author Ruesin
     */
    function upPhotoData(){
        $mLike =&m ('like');
        $mUp   =&m('userphoto');
        $arr = $mLike->findAll(array(
            'conditions' => ' cate in ("sheji_like","jiepai_like")',
        ));
    
        foreach ($arr as $row){
            $del[$row['id']] = $row['like_id'];
            $up[$row['like_id']] +=1;
        }
        foreach ($del as $key => $id){
            $row = $mUp->get($id);
            if(empty($row)){
                //为了记录删除的日志,就没有挪出去,虽然效率慢了点,但可以接受
                if($mLike->drop($key)){
                    unset($arr[$key]);
                    unset($up[$id]);
                    echo 'clean data success '.$key.'<br>';
                }
            }
        }
        //////=============== 清理完成 等待更新
        echo '<hr>';
        foreach($up as $key=>$val){
            if($mUp->edit($key,array('like_num'=>$val))){
                echo 'update data success '.$key.'<br>';
            }
        }
    }
    /**
     * 梳理会员喜欢数
     * 由于之前只梳理了对街拍和设计的喜欢,所以梳理完这个之后数字有可能还是错误的!!!!因为主题和基本款都没有改呢.
     * @author Ruesin
     */
    function upUserLikes(){
        $mLike =&m ('like');
        $mMem  =&m('member');
        $all = $mLike->findAll();
        foreach ($all as $row){
            $user[$row['uid']] +=1;
        }
        $db = &db();
        $csh = "update cf_member set like_num = '0'";
        if($db->query($csh)){
            echo "member like has clean! <br>";
        }
        foreach ($user as $key=>$val){
            if($mMem->edit($key,array('like_num'=>$val))){
                echo "member like has update! ".$key."-".$val." <br>";
            }
        }
        print_exit($user);
    }
   /*  ajax 获取角色下的等级 */
    function getLvs(){
    	$mty = isset($_POST['mty']) ? trim($_POST['mty']) : 'member';
    	
    	$lvs= $this->_lv_mod->find(array(
    			'conditions' => 'lv_type="'.$mty.'"',
    			'fields'=>'name'
    	));
    	$this->assign('lvs', $lvs);
    	
    	
    	$content = $this->_view->fetch("lvs.fetch.html");
    	$this->json_result(array(
    			'content'      =>  $content,
    	),'success');
    }
    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = NULL)
    {
        $_cate_mod = & bm('gcategory', array('_store_id' => 0));

        $gcategories = $_cate_mod->get_list();
        $tree =& $this->_tree($gcategories);

        return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
    /* 构造并返回树 */
    function &_tree($gcategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree;
    }
    function processPrice($data = array()){
        $mod_fabric = &m("fabs");
        $mod_dict   = &m("dict");
        $category = $this->init[$data["category"]];

        //面料价格---------------------------------------
        $fabricePrice = 0;
        if($data["fabric"]){
            $info= $mod_fabric->find(array(
                "conditions" => "fp.AREAID = '20151' AND f.ID='{$data["fabric"]}'",
                'join'       => "belongs_to_price",
                'fields'     => "fp.RMBPRICE"
            ));

            $info = current($info);

            if(!empty($info)){
                $fabricePrice = $info["RMBPRICE"];
            }
        }

        $fabricePrice *= $category["mldh"];

        //里料价格---------------------------------------
        $llPrice = 0;
        $llId = 0;


        foreach((array) $data["items"] as $key => $val){
            if($val == $category["ll"] && isset($data['default'][$val])){
                $llId = $val["item_id"];
            }
        }
        if($llId){
            $ll = $mod_dict->get("id='{$llId}'");
            if($ll){
                $llPrice = $ll["price"];
            }
        }

        $llPrice *= $category["lldh"];

        //指定工艺-----------------------------------------
        $assignPrice =0;
        foreach((array)$data["assignPrice"] as $key => $val){
            $assignPrice += $val;
        }

        $base_price = $fabricePrice + $llPrice + $assignPrice + $data["service_price"];

        return array("base_price" => $base_price, 'price' => round($base_price * $this->coefficient,0));

    }
    function formatData($sData, $adData){
        $upData = array(); //设置默认
        $dnData = array(); //取消默认
        $deData = array();

        foreach((array)$sData as $key => $val){
            if(isset($adData[$val["item_id"]])){
                $is_defalut = $adData[$val['item_id']]['is_default'];
                if($val['is_default'] < $is_defalut){
                    $upData[] = $val["id"];
                }

                if($val["is_default"] > $is_defalut){
                    $dnData[] = $val["id"];
                }
                unset($adData[$val["item_id"]]);
            }else{
                $deData[] = $val["id"];
            }
        }

        $newArr = array();
        foreach($adData as $key => $val){
            $newArr[] = $val;
        }
        return array(
            'adData' => $newArr,
            'upData' => $upData,
            'dnData' => $dnData,
            'deData' => $deData
        );
    }
    // 拍图作品补充详情
    function jbk_sub(){
        $id=intval($_GET['id']);

        if(IS_POST){
            $name          = trim($_POST['name']);
            $style         = trim($_POST['style']);
            $category      = trim($_POST['category']);
            $item          = $_POST['item'];
            $assign        = $_POST['assign'];
            $assignPrice   = $_POST["assignPrice"];
            $service_price = $_POST['service_price'];
            $is_default    = $_POST["is_default"];
            $content       = trim($_POST["content"]);
            $fab           = $_POST['fab'];
            $fabs          = $_POST['fabs'];
            $is_sale       = intval($_POST['is_sale']);
            $cat_id        = intval($_POST['cat_id']);
            $attr          = $_POST['attr'];
            $work_info = $this->_work_mod->find("id=".$id);


            $priceArr = $this->processPrice(array(
                'category'      => $category,
                'service_price' => $service_price,
                'fabric'        => $fab,
                'items'         => $item,
                'assignPrice'   => $assignPrice,
                'default'       => $is_default,
            ));

            $cusData = array(
                'id'       => $work_info['id'],
                'name'     => $name,
                'style'    => $style,
                'category' => $category,
                'content'  => $content,
                'is_sale'  => $is_sale,
                'price'    => $priceArr["price"],
                'base_price'    => $priceArr["base_price"],
                'assignprice'   => serialize($assignPrice),
                'service_price' => $service_price,
                'cat_id'   => $cat_id,
                'add_time'  => time(),
                'last_time' => gmtime(),
                'work_id'   => $id,
            );

            $custom_id = $this->_mod_custom->add($cusData);
            $w_arr = array(
                'work_name' => $name,
            );
            $this->_work_mod->edit($id,$w_arr);
            $itemData = array();
            foreach((array)$item as $key => $val){
                $itemData[] = array(
                    'custom_id' => $custom_id,
                    'item_id'   => $key,
                    'menu_id'   => $val,
                    'is_default'=> isset($is_default[$key]) && $is_default[$key] == $key ? 1 : 0,
                    'assign'    => $assign[$key],
                );
            }
            if(!empty($itemData)){
                $this->_mod_cusItem->add($itemData);
            }

            $fabData = array();
            foreach((array)$fabs as $key => $val){
                $fabData[] = array(
                    'custom_id'  => $custom_id,
                    'item_id'  => $val,
                    'is_default' => $fab == $val ? 1 : 0,
                );
            }

            if(!empty($fabData)){
                $this->_mod_cusFab->add($fabData);
            }

            $attrData = array();

            foreach((array)$attr as $key => $val){
                if($val){
                    $attrData[] = array(
                        "attr_id"     => $key,
                        'attr_value'  => $val,
                        "custom_id"   => $custom_id,
                    );
                }
            }

            if(!empty($attrData)){
                $this->_mod_cusAttr->add($attrData);
            }

            $this->_work_mod->edit($id,array('is_sub'=>1));

            $this->show_message("作品信息补录成功",'back_list', 'index.php?app=user&amp;act=work');
        }else{
            import("dict.lib");

            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $id = intval($_GET["id"]);
            $data = $this->_mod_custom->get($id);

            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
            )));

            $fabrics = $this->_mod_cusFab->find(array(
                "conditions" => "custom_id = '{$id}'",
                'join'       => "belongs_to_fab",
                'fields'     => "fab.*,f.CODE,f.ID"
            ));

            $items = $this->_mod_cusItem->find(array(
                'conditions' => "custom_id = '{$id}'",
                'join'       => "belongs_to_dict",
                'fields'     => "im.*,c.name,c.ecode"
            ));

            $ids = array();
            foreach((array) $items as $key => $val){
                $ids[] = $val['menu_id'];
            }

            $mod_dict = &m("dict");
            $list = $mod_dict->find(array(
                "conditions" => "id ".db_create_in($ids),
                'fields'     => "name",
            ));

            foreach($items as $key => $val){
                if(isset($list[$val["menu_id"]])){
                    $items[$key]['name'] = $list[$val["menu_id"]]["name"].":".$val["name"];
                }
            }

            $this->assign("options", $this->_get_options());

            $design_list = $this->_mod_designer->find();

            $dsn = array();
            foreach($design_list as $key => $val){
                $dsn[$val["id"]] = $val["username"];
            }

            $this->assign("design_list", $dsn);

            $typelist = $this->_mod_type->find();
            $types = array();
            foreach($typelist as $key => $val){
                $types[$val["type_id"]] = $val["type_name"];
            }

            $this->assign("types", $types);

            $link_mod = &m("cusLink");

            $links = $link_mod->find(array(
                "conditions" => "link.custom_id='{$id}'",
                'join'       => "belongs_to_custom",
                'fields'     => "c.id as cid,c.name,link.id as linkid",
            ));

            $_cate_mod = & bm('gcategory', array('_store_id' => 0));
            $info = $_cate_mod->get_info($data["cat_id"]);
            $attrlist = array();
            if($info['attrid']){
                $attrlist = $this->_mod_attribute->find(array(
                    "conditions" => "attr_id IN ({$info["attrid"]})",
                    'order'      => "sort_order ASC",
                ));
            }

            foreach($attrlist as $key => $val){
                $attrlist[$key]['values'] = explode(",", $val['attr_values']);
            }

            $linkAttr = $this->_mod_cusAttr->find(array(
                'conditions' => "custom_id = '{$id}'",
            ));

            $linkAttrs = array();

            $gallery_list = $this->_mod_cusGallery->find(array(
                'conditions' => "custom_id = '{$id}'",
                'order' => 'sort ASC',
            ));

            $this->assign("gallery_list", $gallery_list);

            foreach((array) $linkAttr as $key => $val){
                $linkAttrs[$val["attr_id"]] = $val["attr_value"];
            }
            $param = array(
                'dir' => 'gallery',
                'button_text' => "上传相册图片"
            );
            $this->assign("priceList", $this->init[$data["category"]]["dict"]);

            $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
            $this->assign("attrlist", $attrlist);
            $this->assign("linkattrs", $linkAttrs);
            $this->assign("assignPrice", unserialize($data["assignprice"]));
            $this->assign("items", $items);
            $this->assign("fabrics", $fabrics);
            $this->assign("style_list", $this->style);
            $this->assign("cat_list",   $this->init);
            $this->assign("links",      $links);
            $this->assign("data", $data);
            $this->display("work_sub.html");
        }
    }
    //作品基本款修改
    function jbk_edit(){
        $id = intval($_GET['id']);

        $ids = $this->_mod_custom->find(array(
            'conditions' => "work_id=".$id,
            'fields'     => 'id',
        ));
        foreach($ids as $val){
            $cid = $val['id'];
        }
        if(IS_POST){
            $name      = trim($_POST['name']);
            $style     = trim($_POST['style']);
            $category  = trim($_POST['category']);
            $item          = $_POST['item'];
            $assign          = $_POST['assign'];
            $assignPrice  = $_POST["assignPrice"];
            $service_price         = $_POST['service_price'];
            $is_default      = $_POST["is_default"];
            $content       = trim($_POST["content"]);
            $fab           = $_POST['fab'];
            $fabs          = $_POST['fabs'];
            $is_sale       = intval($_POST['is_sale']);
            $cat_id       = intval($_POST['cat_id']);
            $attr         = $_POST["attr"];

            $priceArr = $this->processPrice(array(
                'category'      => $category,
                'service_price' => $service_price,
                'fabric'        => $fab,
                'items'         => $item,
                'assignPrice'   => $assignPrice,
                'default'       => $is_default,
            ));

            $cusData = array(
                'name'     => $name,
                'style'    => $style,
                'category' => $category,
                'content'  => $content,
                'is_sale'      => $is_sale,
                'price'    => $priceArr["price"],
                'base_price'    => $priceArr["base_price"],
                'assignprice'   => serialize($assignPrice),
                'service_price' => $service_price,
                'cat_id'   => $cat_id,
                'last_time' => gmtime(),
            );


            $itemData = array();
            foreach((array)$item as $key => $val){
                $itemData[$key] = array(
                    'custom_id' => $cid,
                    'item_id'   => $key,
                    'menu_id'   => $val,
                    'is_default'=> isset($is_default[$key]) && $is_default[$key] == $key ? 1 : 0,
                    'assign'    => $assign[$key],
                );
            }

            $fabData = array();
            foreach((array)$fabs as $key => $val){
                $fabData[$val] = array(
                    'custom_id'  => $cid,
                    'item_id'  => $val,
                    'is_default' => $fab == $val ? 1 : 0,
                );
            }
            $this->_mod_custom->edit("id=$cid", $cusData);
            $w_arr = array(
                'work_name' => $name,
            );
            $this->_work_mod->edit($id,$w_arr);


            /********************************************************************************************/
            $items = $this->_mod_cusItem->find(array(
                'conditions' => "custom_id = '{$cid}'",
            ));
            $fData = $this->formatData($items, $itemData);


            //新增
            if(!empty($fData["adData"])){
                $this->_mod_cusItem->add($fData["adData"]);
            }

            //移除
            if(!empty($fData["deData"])){
                $this->_mod_cusItem->drop($fData["deData"]);
            }

            //取消
            if(!empty($fData["dnData"])){

                $this->_mod_cusItem->edit($fData["dnData"], array("is_default" => 0));
            }

            //设置
            if(!empty($fData["upData"])){
                $this->_mod_cusItem->edit($fData["upData"], array("is_default" => 1));
            }


            /********************************************************************************************/

            /********************************************************************************************/
            $items = $this->_mod_cusFab->find(array(
                'conditions' => "custom_id = '{$cid}'",
            ));

            $fData = $this->formatData($items, $fabData);
            //新增
            if(!empty($fData["adData"])){
                $this->_mod_cusFab->add($fData["adData"]);
            }

            //移除
            if(!empty($fData["deData"])){
                $this->_mod_cusFab->drop($fData["deData"]);
            }

            //取消
            if(!empty($fData["dnData"])){
                $this->_mod_cusFab->edit($fData["dnData"], array("is_default" => 0));
            }

            //设置
            if(!empty($fData["upData"])){
                $this->_mod_cusFab->edit($fData["upData"], array("is_default" => 1));
            }
            /********************************************************************************************/

            $attrlist = $this->_mod_cusAttr->find(array(
                "conditions" => "custom_id='{$cid}'",
            ));

            $upAttr = array();
            $deAttr = array();
            foreach((array) $attrlist as $key => $val){
                if(isset($attr[$val['attr_id']]) && !empty($attr[$val['attr_id']])){
                    if($attr[$val['attr_id']] != $val['attr_value']){
                        $upAttr[$val["attr_id"]] = $attr[$val['attr_id']];
                    }
                    unset($attr[$val["attr_id"]]);
                }else{
                    $deAttr[] = $val['id'];
                }
            }

            if(!empty($deAttr)){
                $this->_mod_cusAttr->drop($deAttr);
            }

            if(!empty($upAttr)){
                foreach($upAttr as $key => $val){
                    $this->_mod_cusAttr->edit("custom_id='{$cid}' AND attr_id = '{$key}'", array("attr_value" => $val));
                }
            }

            if(!empty($attr)){
                $newAttr = array();
                foreach($attr as $key => $val){
                    if(!empty($val)){
                        $newAttr[] = array(
                            'custom_id' => $cid,
                            'attr_id'   => $key,
                            'attr_value' => $val,
                        );
                    }
                }
                if(!empty($newAttr)){
                    $this->_mod_cusAttr->add($newAttr);
                }
            }

            $this->show_message("作品样衣编辑成功",'back_list', 'index.php?app=user&amp;act=work');
        }else{
            import("dict.lib");

            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $data = $this->_mod_custom->get($cid);

            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
            )));

            $fabrics = $this->_mod_cusFab->find(array(
                "conditions" => "custom_id = '{$data["id"]}'",
                'join'       => "belongs_to_fab",
                'fields'     => "fab.*,f.CODE,f.ID"
            ));

            $items = $this->_mod_cusItem->find(array(
                'conditions' => "custom_id = '{$data["id"]}'",
                'join'       => "belongs_to_dict",
                'fields'     => "im.*,c.name,c.ecode"
            ));

            $ids = array();
            foreach((array) $items as $key => $val){
                $ids[] = $val['menu_id'];
            }

            $mod_dict = &m("dict");
            $list = $mod_dict->find(array(
                "conditions" => "id ".db_create_in($ids),
                'fields'     => "name",
            ));

            foreach($items as $key => $val){
                if(isset($list[$val["menu_id"]])){
                    $items[$key]['name'] = $list[$val["menu_id"]]["name"].":".$val["name"];
                }
            }

            $this->assign("options", $this->_get_options());


            $typelist = $this->_mod_type->find();
            $types = array();
            foreach($typelist as $key => $val){
                $types[$val["type_id"]] = $val["type_name"];
            }

            $this->assign("types", $types);

            $link_mod = &m("cusLink");

            $links = $link_mod->find(array(
                "conditions" => "link.custom_id='{$cid}'",
                'join'       => "belongs_to_custom",
                'fields'     => "c.id as cid,c.name,link.id as linkid",
            ));

            $_cate_mod = & bm('gcategory', array('_store_id' => 0));
            $info = $_cate_mod->get_info($data["cat_id"]);
            $attrlist = array();
            if($info['attrid']){
                $attrlist = $this->_mod_attribute->find(array(
                    "conditions" => "attr_id IN ({$info["attrid"]})",
                    'order'      => "sort_order ASC",
                ));
            }

            foreach($attrlist as $key => $val){
                $attrlist[$key]['values'] = explode(",", $val['attr_values']);
            }

            $linkAttr = $this->_mod_cusAttr->find(array(
                'conditions' => "custom_id = '{$cid}'",
            ));

            $linkAttrs = array();

            $gallery_list = $this->_mod_cusGallery->find(array(
                'conditions' => "custom_id = '{$cid}'",
                'order' => 'sort ASC',
            ));

            $this->assign("gallery_list", $gallery_list);

            foreach((array) $linkAttr as $key => $val){
                $linkAttrs[$val["attr_id"]] = $val["attr_value"];
            }
            $param = array(
                'dir' => 'gallery',
                'button_text' => "上传相册图片"
            );
            $this->assign("priceList", $this->init[$data["category"]]["dict"]);

            $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
            $this->assign("attrlist", $attrlist);
            $this->assign("linkattrs", $linkAttrs);
            $this->assign("assignPrice", unserialize($data["assignprice"]));
            $this->assign("items", $items);
            $this->assign("fabrics", $fabrics);
            $this->assign("style_list", $this->style);
            $this->assign("cat_list",   $this->init);
            $this->assign("links",      $links);
            $this->assign("data", $data);
            $this->display("work_sub.html");
        }
    }
    //作品套装补录信息
    function tz_sub(){
        $id=intval($_GET['id']);
        $_cate_mod = & bm('gcategory', array('_store_id' => 0));
        if(IS_POST){
            // $tzids     = trim($_POST['tzids'],",");//基本款id
            $name      = trim($_POST['name']);//套装名
            $brief     = trim($_POST['brief']);//简介
            $style     = trim($_POST['style']);//风格
            $is_sale       = intval($_POST['is_sale']);//是否上架 1.是 2.否
            $content       = trim($_POST["content"]);//详细内容
            $service_price = $_POST['service_price'];//服务费
            $num3 = $_POST['0003']?$_POST['0003'] :0;
            $num4 = $_POST['0004']?$_POST['0004'] :0;
            $num5 = $_POST['0005']?$_POST['0005'] :0;
            $num6 = $_POST['0006']?$_POST['0006'] :0;
            $num7 = $_POST['0007']?$_POST['0007'] :0;
            $tzids = $num3.','.$num4.','.$num5.','.$num6.','.$num7;//基本款id
            $attr         = $_POST['attr'];
            $cat_id       = intval($_POST['cat_id']);
            $cusData = array(
                'suit_name' => $name,
                'jianjie'   => $brief,
                'content'   => $content,
                'price'     => $service_price,
                'styles'    => $style,
                'is_sale'   => $is_sale,
                'cat_id'    =>$cat_id,
                'add_time'  => gmtime(),
                'last_time' =>gmtime(),
                'work_id'   => $id,
            );
            $suit_id = $this->_mod_suit->add($cusData);
            $tzData = array(
                'tz_id'    => $suit_id,
                'jbk_id'   => $tzids,
            );

            $tz_id = $this->_mod_suitre->add($tzData);
            $w_arr = array(
                'work_name' => $name,
            );
            $this->_work_mod->edit($id,$w_arr);
            $desData = array();
            $_dictids = array();
            foreach((array)$design_ids as $key => $val){
                foreach((array)$val as $_k => $v){
                    $desData[] = array(
                        "suit_id"   => $suit_id,
                        'type'        => "design",
                        'is_default'  => $design[$key] == $v ? 1 : 0,
                        'menu_id'     => $key,
                        'item_id'     => $v,
                    );

                    if($design[$key] == $v && in_array($key, $this->init[$category]["dict"])){
                        $_dictids[] = $v;
                    }
                }
            }

            foreach((array)$deep_ids as $key => $val){
                foreach((array)$val as $_k => $v){
                    $desData[] = array(
                        "suit_id"   => $suit_id,
                        'type'        => "deep",
                        'is_default'  => $deep[$key] == $v ? 1 : 0,
                        'menu_id'     => $key,
                        'item_id'     => $v,
                    );

                    if($deep[$key] == $v && in_array($key, $this->init[$category]["dict"])){
                        $_dictids[] = $v;
                    }
                }
            }

            $price = $service_price;

            if(!empty($_dictids)){
                $_mod_dict = &m("dict");
                $list = $_mod_dict->find(array(
                    "conditions" => "id ".db_create_in($_dictids),
                    'fields'     => "id, price, parentid",
                ));

                foreach((array)$list as $key => $val){
                    if($val["parent_id"] == $this->init[$category]["ll"]){
                        $price += $this->init[$category]["lldh"]*$val["price"];
                    }else{
                        $price += $val["price"];
                    }
                }
            }



            if(!empty($desData)){
                // $this->_mod_customs->add($desData);
            }
            $attrData = array();

            foreach((array)$attr as $key => $val){
                if($val){
                    $attrData[] = array(
                        "attr_id"     => $key,
                        "attr_value"  => $val,
                        "suit_id"   => $suit_id,
                    );
                }
            }
            if(!empty($attrData)){
                $this->_mod_suitattr->add($attrData);
            }
            $this->_work_mod->edit($id,array('is_sub'=>1));

            $this->show_message("套装补充成功成功",
                'back_list', 'index.php?app=user&amp;act=work');
        }else{
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));

            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
            )));
            // 添加套装关联设计师 tangshoujian start
            $design_list = $this->_mod_designer->find();
            $dsn = array();
            $options= $_cate_mod->find(array(
                'conditions' =>"parent_id=0",
                'fields'  =>"cate_name",

            ));

            foreach($options as $key=> $val){
                $options[$key]=$val['cate_name'];
            }
            foreach($design_list as $key => $val){
                $dsn[$val["id"]] = $val["username"];
            }


            $this->assign("design_list", $dsn);
            // end
            $this->assign("options",   $options);
            $this->assign("style_list", $this->style);
            $this->assign("cat_list",   $this->init);
            $this->display("user.suit_edit.html");
        }
    }
    //套装补录信息编辑
    function tz_edit(){
        $id = intval($_GET['id']);
        $ids = $this->_mod_suit->find(array(
            'conditions' => "work_id=".$id,
            'fields'     => 'id',
        ));
        foreach($ids as $val){
            $sid = $val['id'];
        }
        if(IS_POST){
            // $tzids     = trim($_POST['tzids'],",");//基本款id
            $name      = trim($_POST['name']);//套装名
            $brief     = trim($_POST['brief']);//简介
            $style     = trim($_POST['style']);//风格
            $is_sale       = intval($_POST['is_sale']);//是否上架 1.是 2.否
            $content       = trim($_POST["content"]);//详细内容
            $service_price = $_POST['service_price'];//服务费
            $num3 = $_POST['0003']?$_POST['0003'] :0;
            $num4 = $_POST['0004']?$_POST['0004'] :0;
            $num5 = $_POST['0005']?$_POST['0005'] :0;
            $num6 = $_POST['0006']?$_POST['0006'] :0;
            $num7 = $_POST['0007']?$_POST['0007'] :0;
            $cat_id       = intval($_POST['cat_id']);
            $tzids = $num3.','.$num4.','.$num5.','.$num6.','.$num7;//基本款id
            $attr         = $_POST["attr"];
            $cusData = array(
                'suit_name' => $name,
                'jianjie'   => $brief,
                'content'   => $content,
                'price'     => $service_price,
                'styles'    => $style,
                'is_sale'   => $is_sale,
                'cat_id'    => $cat_id,
                'add_time'  => gmtime(),
                'last_time' => gmtime(),
            );
            $tzData = array(
                'tz_id'    => $sid,
                'jbk_id'   => $tzids,
            );
            /********************************************************************************************/

            $items = $this->_mod_suit->find(array(
                'conditions' => "id = '{$sid}'",
            ));


            /*    $fData = $this->formatData($items, $desData);
                //新增
                if(!empty($fData["adData"])){
                    $this->_mod_customs->add($fData["adData"]);
                }

                //移除
                if(!empty($fData["deData"])){
                    $this->_mod_customs->drop($fData["deData"]);
                }

                //取消
                if(!empty($fData["dnData"])){

                    $this->_mod_customs->edit($fData["dnData"], array("is_default" => 0));
                }

                //设置
                if(!empty($fData["upData"])){
                    $this->_mod_customs->edit($fData["upData"], array("is_default" => 1));
                }*/
            $jiben=$this->_mod_suitre->get(array(
                'conditions' => "tz_id = '{$sid}'",
            ));
            $t_id=$jiben["id"];
            $this->_mod_suit->edit($sid, $cusData);
            $w_arr = array(
                'work_name' => $name,
            );
            $this->_work_mod->edit($id,$w_arr);

            $tz_id = $this->_mod_suitre->edit($t_id,array('jbk_id'=>$tzData['jbk_id']));



            /********************************************************************************************/

            /********************************************************************************************/
            /*     $items = $this->_mod_cusFab->find(array(
                     'conditions' => "id = '{$id}'",
                 ));



                 $fData = $this->formatData($items, $fabData);

                 //新增
                 if(!empty($fData["adData"])){
                     $this->_mod_cusFab->add($fData["adData"]);
                 }

                 //移除
                 if(!empty($fData["deData"])){
                     $this->_mod_cusFab->drop($fData["deData"]);
                 }

                 //取消
                 if(!empty($fData["dnData"])){
                     $this->_mod_cusFab->edit($fData["dnData"], array("is_default" => 0));
                 }

                 //设置
                 if(!empty($fData["upData"])){
                     $this->_mod_cusFab->edit($fData["upData"], array("is_default" => 1));
                 }*/
            /********************************************************************************************/
            $attrlist = $this->_mod_suitattr->find(array(
                "conditions" => "suit_id='{$sid}'",
            ));

            $upAttr = array();
            $deAttr = array();
            foreach((array) $attrlist as $key => $val){
                if(isset($attr[$val['attr_id']]) && !empty($attr[$val['attr_id']])){
                    if($attr[$val['attr_id']] != $val['attr_value']){
                        $upAttr[$val["attr_id"]] = $attr[$val['attr_id']];
                    }
                    unset($attr[$val["attr_id"]]);
                }else{
                    $deAttr[] = $val['id'];
                }
            }

            if(!empty($deAttr)){
                $this->_mod_suitattr->drop($deAttr);
            }

            if(!empty($upAttr)){
                foreach($upAttr as $key => $val){
                    $this->_mod_suitattr->edit("suit_id='{$sid}' AND attr_id = '{$key}'", array("attr_value" => $val));
                }
            }

            if(!empty($attr)){
                $newAttr = array();
                foreach($attr as $key => $val){
                    if(!empty($val)){
                        $newAttr[] = array(
                            'suit_id' => $sid,
                            'attr_id'   => $key,
                            'attr_value' => $val,
                        );
                    }
                }
                if(!empty($newAttr)){
                    $this->_mod_suitattr->add($newAttr);
                }
            }


            $this->show_message("套装编辑成功",
                'back_list', 'index.php?app=user&amp;act=work');
        }else{
            import("dict.lib");
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $design = array();
            $deep   = array();
            // $datac = $this->_mod_customs->get($id);
            $data = $this->_mod_suit->get($sid);
            $jiben=$this->_mod_suitre->get(array(
                'conditions' => "tz_id = '{$sid}'",
            ));

            $str=$jiben["jbk_id"];
            $jbk_id = explode(",",$str);

            $jbk=array();

            foreach($jbk_id as $key=>$val){
                if(empty($val)){
                    $val='0';
                }
                $custom = $this->_mod_custom->find(array(
                    "conditions" => "id = {$val}"

                ));
                $jbk[]=$custom[$val]["name"];
                $jbk_idarr[]=$val;
            }
            $dict = new Dict($this->init[$data["category"]]['items']);

            // 添加套装关联设计师 tangshoujian start
            $design_list = $this->_mod_designer->find();
            $_cate_mod = & bm('gcategory', array('_store_id' => 0));
            $info = $_cate_mod->get_info($data["cat_id"]);
            $attrlist = array();
            if($info['attrid']){
                $attrlist = $this->_mod_attribute->find(array(
                    "conditions" => "attr_id IN ({$info["attrid"]})",
                    'order'      => "sort_order ASC",
                ));
            }
            foreach($attrlist as $key => $val){
                $attrlist[$key]['values'] = explode(",", $val['attr_values']);
            }

            $options= $_cate_mod->find(array(
                'conditions' =>"parent_id=0",
                'fields'  =>"cate_name",

            ));

            foreach($options as $key=> $val){
                $options[$key]=$val['cate_name'];
            }
            $linkAttr = $this->_mod_suitattr->find(array(
                'conditions' => "suit_id = '{$sid}'",
            ));

            $dsn = array();
            foreach($design_list as $key => $val){
                $dsn[$val["id"]] = $val["username"];
            }
            foreach((array) $linkAttr as $key => $val){
                $linkAttrs[$val["attr_id"]] = $val["attr_value"];
            }
            $this->assign("design_list", $dsn);
            // end
            $this->assign("options", $options);
            $this->assign("linkattrs", $linkAttrs);
            $this->assign("attrlist", $attrlist);
            $this->assign("jbk", $jbk);
            $this->assign("jbk_idarr", $jbk_idarr);
            $this->assign("data", $data);
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
            )));

            //  $fabrics = $this->_mod_cusFab->find(array(
            //    "conditions" => "id = '{$data["id"]}'",
            //  'join'       => "belongs_to_fab",
            // 'fields'     => "fab.*,f.CODE,f.ID"
            // ));

            $menus = $dict->getMenu();
            // $items = $this->_mod_customs->find(array(
            //    'conditions' => "suit_id = '{$data["id"]}'",
            //    'join'       => "belongs_to_dict",
            //    'fields'     => "im.*,c.name"
            // ));

            foreach((array)$items as $key => $val){
                ${$val["type"]}[$val["menu_id"]][$val["item_id"]] = $val;
            }

            foreach((array)$menus["design"] as $_k => $_v){
                $menus["design"][$_k]["children"] = $design[$_k];
            }

            foreach((array)$menus["deep"] as $_k => $_v){
                $menus["deep"][$_k]["children"]   = $deep[$_k];
            }

            $this->assign("menus", $menus);
            $this->assign("fabrics", $fabrics);
            $this->assign("style_list", $this->style);
            $this->assign("cat_list",   $this->init);
            $this->display("user.suit_edit.html");
        }
    }
}

?>
