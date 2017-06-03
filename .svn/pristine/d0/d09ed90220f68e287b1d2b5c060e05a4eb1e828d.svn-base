<?php
use Cyteam\Goods\Mp;
/* 会员控制器 */
class UserApp extends BackendApp
{
    var $_user_mod;
	var $_account_mod;
	var $_userphoto_mod;
	var $_userphotocomment_mod;
	var $_userphotogallery_mod;
	var $_admin_user_mod;
    var $_admin_userlog_mod;
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
        //ns add 添加用户操作日志
        $this->_admin_userlog_mod =& m('admin_userlog');
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
            ),array(
                'field' => 'reg_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),array(
                'field' => 'reg_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
            ),
            array(
                'field' => 'lv_time',
                'name'  => 'lv_time_to',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),array(
                'field' => 'lv_time',
                'name'  => 'lv_time_from',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
            ),
            array(
                'field' => 'channel_pid',
                'name'  => 'channel_pid',
                'equal' => '=',
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
        $page = $this->_get_page(30);
        $conditions .= " and 1=1";

        $users = $this->_user_mod->find(array(
            'conditions' => '1=1'.$conditions,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));

        //获取会员等级信息-创业者
        $_lv_mod_list = $this->_lv_mod->find(array(
            'conditions' => "lv_type = 'supplier'",
            'fields' => 'name',
        ));

        //$model_order =& m('order');
        foreach ($users as $key => $val)
        {
            if ($val['priv_store_id'] == 0 && $val['privs'] != '')
            {
                $users[$key]['if_admin'] = true;
            }
            //该会员支付过 status = 20代表支付成功
            //$users[$key]['order_num'] = count($model_order->find(array('conditions' => 'status= 20 AND user_id='.$key)));
            $users[$key]['lv_name_info'] = $_lv_mod_list[$val['member_lv_id']]['name'];
            
            if($val['openid'])
            {
                $users[$key]['come'] = "微信";
            }
            elseif ($val['come_from'] == "pc")
            {
                $users[$key]['come'] = "PC";
            }
            else
            {
                $users[$key]['come'] = "APP";
            }

            if($val['type'] > 0)
            {
                $users[$key]['user_name'] = $val['nickname'];
            }
            
            if($val['channel_pid']) //存在上线渠道
            {
                $channel_info = $this->_user_mod->get_info($val['channel_pid']);
                $users[$key]['channel_name'] = $channel_info['user_name'];
            }

        }

        $this->assign('users', $users);
        $this->assign('tname', $this->_lv_mod->_typename);
        
        $member_lv = array();
        $member_lv[0]='全部';
        foreach($_lv_mod_list as $key=>$val){
            $member_lv[$key] = $val['name'];
        }

        //ns add 获取等级
        $this->assign('member_lv', $member_lv);

        $page['item_count'] = $this->_user_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js,jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
            'style'  => 'res:style/jqtreetable.css,jquery.ui/themes/ui-lightness/jquery.ui.css'
        ));

        //获取当前信息
         $this->assign('user_info', $this->visitor->get());

        $this->assign('query_fields', array(
            'user_name' => LANG::get('user_name'),
            'email'     => LANG::get('email'),
            'real_name' => LANG::get('real_name'),
//            'phone_tel' => LANG::get('phone_tel'),
//            'phone_mob' => LANG::get('phone_mob'),
        ));
        $this->assign('sort_options', array(
            'reg_time DESC'   => LANG::get('reg_time'),
            'last_login DESC' => LANG::get('last_login'),
            'logins DESC'     => LANG::get('logins'),
        ));

        if($_GET['serve_type'] == '4')
        {
			$this->display('user.index.html');
            //$this->display('serve_designer.index.html');
		}else{
		    $this->display('user.index.html');
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
    
    public function channel()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $mod = m('member');
        if(!$id)
        {
           $this->show_warning('ERROR');
            return;
        }
        $info = $mod->get_info($id);
        if($info['member_type'] && $info['channel_code'] && $info['channel'])
        {
            $url = $info['channel'];
            header("Location:$url");
            return;
        }
        $ret_url = https_request(DINGO_URL."api/wx/qr",['qid'=>$id,'qtype'=>1]);

        $url = $ret_url['url'];
        if($url)
        {
            $mod->edit($id,['member_type'=>1,'channel_code'=>$id,'channel'=>$url]);
            header("Location:$url");
        }
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
    
    
    
    function change_account(){
    	$user_id = intval($_GET['id']);
    	
    	$type    = $_GET['type'];
    	
    	if(empty($user_id)){
    		$this->show_warning('非法操作！');
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
    		 
    		$this->display('user.change_account.html');
    	}else{
    		
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
			
    		$res = $this->_account_mod->add($log);
    		
    		$_res = false;
    		if($res){
//     			$data = array(
//     					'money'  => "money+{$money}",
//     					'frozen' => "frozen+{$frozen}",
//     			);
    			$field = "money=money+{$money}, frozen=frozen+{$frozen}";
    			$_res = $this->_user_mod->changeAccount($user_id, $field);
    		}
    		
    		if($res && $_res){
    			$this->show_message('edit_ok',
    					'回到用户资金纪录',    'index.php?app=user&act=account&id='.$user_id,
    					'再次调整资金账户',   'index.php?app=user&act=change_account&id=' . $user_id
    			);
    		}else{
    			$this->show_warning('操作失败!');
    			return;
    		}
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
            
            $this->assign('tname',array('supplier'=>'创业者'));
//            $this->assign('tname', $this->_lv_mod->_typename);
            $lvs= $this->_lv_mod->find(array(
//            		'conditions' => 'lv_type="'.current(array_flip($this->_lv_mod->_typename)).'"',
            		'conditions' => 'lv_type="supplier"',
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
            $serve_type  = 1;   //trim($this->_lv_mod->_typeid[$_POST['lv_type']]);
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
               'user_token'=>MD5($user_name.MD5($password)),
               'serve_type'  => $serve_type,
               'member_lv_id'  => $member_lv_id,
                'reg_time'  => time(),
            );

            if($member_lv_id>1){
                $data['cy_time']=time();
            }
            /* 到用户系统中注册 */
            $mark='用户 '.$this->visitor->get('user_name').'添加会员'.$user_name;
            $user_id = $ms->user->register($user_name, $password, $email, $data,$mark);
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

                $portrait && $this->_user_mod->edit($user_id, array('portrait' => $portrait),0);
            }
            //ns add 添加日志
            
            $data_log=array(
                'modified_id' => $this->visitor->get('user_id'),
                'modified_last_ip'=>$this->visitor->get('last_ip'),
                'modified_user_name' => $this->visitor->get('user_name'),
                'user_id'=>$user_id,
                'add_time' => time(),
                'mark' =>$mark,
            );
            $this->_admin_userlog_mod->add($data_log,false,0);
            $this->show_message('add_ok',
                'back_list',    'index.php?app=user&amp;serve_type=0',
                'continue_add', 'index.php?app=user&amp;act=add'
            );
        }
    }
    //ns add 日志查看
    function admin_userlog(){
       $userlog= $this->_admin_userlog_mod->find(array(
        'conditions' => '1=1',
        'order'=>'add_time DESC'
        ));
       //时间戳更改
       foreach($userlog as $k=>$v){
            $userlog[$k]['add_time'] = date("Y-m-d H:i:s", $v['add_time']); 
       }
       $this->assign('userlog', $userlog);
       $this->display('user.log.html');
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
            
            $this->assign('tname',array('supplier'=>'创业者'));
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
            
            //获取绑定信息
            $generalize_member_mod =& m('generalize_member');
            $member_invite_mod =& m('memberinvite');
            
            //member_lv_id 大于1是创业者，反之是消费者
           	$invite = $member_invite_mod->get(array(
            		'conditions'=>'invitee = '. $user['user_id'],
            	));
           if($invite){
           	    //0消费者，1是创业者
	            if( $invite['type'] == 0)
	            	{
	            		//memeber 获取邀请人的登陆账号
	            		$invitation = $this->_user_mod->get(array(
	            				'conditions' => 'user_id='.$invite['inviter'],
	            				'fields'=>'user_name',
	            			));
	            	    $invitation['name'] = '创业者账号为：'.$invitation['user_name'];
	            	}
	            elseif($invite['type'] == 1)
	            	{
	            		//去 generalize_member  获取BD码 BD姓名
	            		$invitation = $generalize_member_mod->get(array(
	            				'conditions' => 'id='.$invite['inviter'],
	            				'fields'=>'name,invite',
	            		));
	            		$invitation['name'] = 'BD姓名：'.$invitation['name'].'<br />BD码：'.$invitation['invite'];
	            	}
	           	//邀请时间
			    $invitation['add_time'] = $invite['add_time'];
           }

           //邀请变量
           $this->assign('invitation', $invitation);
           $this->display('user.form.html');
        }
        else
        {
            $serve_type = trim($this->_lv_mod->_typeid[$_POST['lv_type']]);
            !empty($_POST['email'])    && $email    = trim($_POST['email']);
            $data = array(
                'real_name' => $_POST['real_name'],
                'gender'    => $_POST['gender'],
            	'email'=>$email,
            	'serve_type'  => 1,//$serve_type,
            );

            if (!empty($_POST['password']))
            {
                $password = trim($_POST['password']);
                if (strlen($password) < 6 || strlen($password) > 20)
                {
                    $this->show_warning('password_length_error');

                    return;
                }
                //进行密码的修改
                $data['password'] = md5($_POST['password']);
            }
//            if (!is_email(trim($_POST['email'])))
//            {
//                $this->show_warning('email_error');
//
//                return;
//            }

            if (!empty($_FILES['portrait']))
            {
                $portrait = $this->_upload_portrait($id);
                if ($portrait === false)
                {
                    return;
                }
                $data['portrait'] = $portrait;
            }

            //等级改变
            if($serve_type ==1){
                $member_lv_id = intval($_POST['member_lv_id']);
                $data['member_lv_id']  = $member_lv_id;
                //如果升级创业者
                if($member_lv_id>1){
                    $user = $this->_user_mod->get_info($id);
                    if($user['member_lv_id']==1){
                        $data['cy_time']=time();
                    }
                    if($user['invite'] == ''){
                        //生成码 --创业者
                        $data['invite']=make_order_card();
                    }
                }else{
                    $data['invite'] == '';
				}
                if($user['member_lv_id'] != $member_lv_id){
                    $data['lv_time'] = time();
                }
            }
            $lvs= $this->_lv_mod->find(array(
            			'conditions' => "lv_type='supplier'",
            			'fields'=>'name'
            	));
            //ns add 添加备注。
            $tmp = '用户 '.$this->visitor->get('user_name').' 对 '.$_POST['log_user_name'].' 进行了修改操作:';
            $type_name=$this->_lv_mod->_typename;
            if($_POST['lv_type'] != $_POST['log_lv_type'] && $_POST['lv_type'] && $_POST['log_lv_type']){
     			$log_lv_type=$type_name{$_POST['log_lv_type']};
     			$lv_type=$type_name{$_POST['lv_type']};
                $mark .= ',会员类型从 '.$log_lv_type.' 修改成 '.$lv_type;
            }
            if($_POST['member_lv_id'] != $_POST['log_member_lv_id'] && $_POST['member_lv_id'] && $_POST['log_member_lv_id']){
            	$log_member_lv=$lvs{$_POST['log_member_lv_id']};
            	$member_lv=$lvs{$_POST['member_lv_id']};
                $mark .= ',会员等级从 '.$log_member_lv.' 修改成 '.$member_lv;
            }
            if(!empty($_POST['password'])){
                if($_POST['password'] != $_POST['log_password']){
                    $mark .= ',修改了密码';
                }
            }
            if($_POST['email'] != $_POST['log_email']){
                $_POST['log_email'] = empty($_POST['log_email']) ? 'NULL' : $_POST['log_email'];
                $_POST['email'] = empty($_POST['email']) ? 'NULL' : $_POST['email'];
                $mark .= ',邮箱从 '.$_POST['log_email'].' 修改成 '.$_POST['email'];
            }
            if($_POST['real_name'] != $_POST['log_real_name'] && $_POST['real_name']){
                $_POST['log_real_name'] = empty($_POST['log_real_name']) ? 'NULL' : $_POST['log_real_name'];
                $_POST['real_name'] = empty($_POST['real_name']) ? 'NULL' : $_POST['real_name'];
                $mark .= ',真实姓名从 '.$_POST['log_real_name'].' 修改成 '.$_POST['real_name'];
            }
            $sex = array(0=>'保密',1=>'男',2=>'女');
            if($_POST['gender'] != $_POST['log_gender']){
                $mark .= ',性别从 '.$sex[$_POST['log_gender']].' 修改成 '.$sex[$_POST['gender']];
            }
            // if($_POST['im_qq'] != $_POST['log_im_qq']){
            //     $_POST['log_im_qq'] = empty($_POST['log_im_qq']) ? 'NULL' : $_POST['log_im_qq'];
            //     $_POST['im_qq'] = empty($_POST['im_qq']) ? 'NULL' : $_POST['im_qq'];
            //     $mark .= ',qq从 '.$_POST['log_im_qq'].' 修改成 '.$_POST['im_qq'];
            // }
            // if($_POST['im_msn'] != $_POST['log_im_msn']){
            //     $_POST['log_im_msn'] = empty($_POST['log_im_msn']) ? 'NULL' : $_POST['log_im_msn'];
            //     $_POST['im_msn'] = empty($_POST['im_msn']) ? 'NULL' : $_POST['im_msn'];
            //     $mark .= ',msn从 '.$_POST['log_im_msn'].' 修改成 '.$_POST['im_msn'];
            // }
            $mark=$tmp.ltrim($mark,',');
            /* 修改本地数据 */
            $this->_user_mod->edit($id, $data,1,$mark);
            //ns add 添加日志
            $data_log=array(
                'modified_id' => $this->visitor->get('user_id'),
                'modified_last_ip'=>$this->visitor->get('last_ip'),
                'modified_user_name' => $this->visitor->get('user_name'),
                'user_id'=>$id,
                'add_time' => time(),
                'mark' => $mark,
            );
            $this->_admin_userlog_mod->add($data_log,false,0);

            $this->show_warning('edit_ok',
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
    /**
     * 导出excel
     * @author sauren  <6582701@qq.com>
     * @2015-6-13
     * 本方法用于将会员列表内所有的信息生成excel表
     *
     */
    function export (){
    
    	
    	$users =$this->_user_mod->findAll(array(
    			"conditions" => "",
    			'fields' => 'user_id,user_name,real_name,nickname,gender,phone_mob,reg_time,serve_type',
    	));
    	foreach ($users as &$row){
    		switch ($row['gender']){
    			case 1:
    				$row['gender'] = '男';
    				break;
    			case 2:
    				$row['gender'] = '女';
    				break;
    			default:
    				$row['gender'] = '未知';
    				break;
    		}
    		
    			switch ($row['serve_type']){
    				case 0:
    					$row['serve_type'] = '消费者';
    					break;
    				case 1:
    					$row['serve_type'] = '创业者';
    					break;
    				default:
    					$row['serve_type'] = '未知';
    					break;
    			}
    			$row['reg_time']=date("Y-m-d H:i:s",$row['reg_time']);
    		}
    		
    	$fields_name = array('ID','会员名','真实姓名','昵称','性别','手机号','注册时间','会员角色');
    	array_unshift($users,$fields_name);
    	$this->export_to_csv($users, 'member', 'gbk');
    }

    //ns add 执行用户订单与金额的记录更新
    function member_orderNum(){
       $member_list = $this->_user_mod->find(array(
           'conditions' => "serve_type=1",
           'fields' => 'user_id'
       ));
       $model_order =& m('order');
       foreach($member_list as $me){
            $order_list = $model_order->find(array(
            'conditions'=>'user_id='.$me['user_id'].' and status=40', //40是以完成
            'fields' => 'order.user_id as userid,sum(final_amount) as f_num,count(order_id) as order_num',
            ));
            //统计过的数据添加到member里面
            foreach ($order_list as $key => $value) {
                //判断用户是否有订单
                if($value['userid']){
                  //执行修改操作
                  $this->_user_mod->edit("user_id=".$value['userid'],array('final_amount_num'=>$value['f_num'],'order_num'=>$value['order_num']));  
                }
            }
       }

    }

    //ns add 查看详情-主要是查看消费记录等
    function member_info(){
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $type = isset($_GET['type']) ? $_GET['type']: '';
        $add_time_from = isset($_GET['add_time_from']) ? $_GET['add_time_from']:'';
        $add_time_to   = isset($_GET['add_time_to'])   ? $_GET['add_time_to']:'';
        
        $order_mod =& m('order');
        
        /* 是否存在 */
        $user = $this->_user_mod->get_info($id);
        if (!$user)
        {
            $this->show_warning('user_empty');
            return;
        }
        //等级信息
        $_lv_mod_list = $this->_lv_mod->find(array(
            'conditions' => "lv_type = 'supplier'",
            'fields' => 'name',
        ));
        $user['lv_name_info'] = $_lv_mod_list[$user['member_lv_id']]['name'];

        //查询购买次数与金额
        $order_num_info = $order_mod->get(array(
            'conditions'=>'status=30 and user_id='.$user['user_id'].' or status=40 and user_id='.$user['user_id'].' or status=60 and user_id='.$user['user_id'],
            'fields' => 'count(order_id) as count_num,sum(order_amount) as amount_num,sum(final_amount) as f_amount,sum(money_amount) as m_amount,sum(coin) as coin',
            ));
        //获取购买次数
        $user['count_num'] = empty($order_num_info['count_num']) ? '0' : $order_num_info['count_num'];
        //获取消费金额
        $user['amount_num'] =$order_num_info['f_amount'] + $order_num_info['m_amount']+$order_num_info['coin'];



        //麦富迪币
        $user['coin'] = empty($user['coin']) ? '0' : $user['coin']; 
    
        //酷卡
        $special_code_mod =& m('special_code');
        //会员如果转变创业者、酷卡跟酷卷获取为从新计算。之前的作废。反之也如此
        if ($user['member_lv_id']  > 1)//=====  创业者  ===== 
        {
            $sconditions = "cate>=20 and from_id = {$user['user_id']} ";
        }
        else 
        {
            $sconditions = 'cate>=20  and to_id = '.$user['user_id'];
        }

        $special = $special_code_mod->get(array(
            'conditions' => $sconditions,
            'fields'     => "sum(work_num) as special_sum,count(id) as special_num",
        ));
        //金额统计
        $user['special_sum'] = empty($special['special_sum']) ? '0.00' : $special['special_sum'];
        //数量
        $user['special_num'] = empty($special['special_num']) ? '0' : $special['special_num'];

        //优惠券
        $debit_mod =& m('debit');
        $debit = $debit_mod->get(array(
            'conditions'=>'user_id='.$user['user_id'].' and is_used =0 and expire_time >'.time(),
            'fields'=>'sum(money) as debit_sum,count(id) as debit_num',
            ));
        //金额统计
        $user['debit_sum'] = empty($debit['debit_sum']) ? '0.00' : $debit['debit_sum'];
        //统计多少个
        $user['debit_num'] = empty($debit['debit_num']) ? '0' : $debit['debit_num'];


        //红包
        $bonus_log_mod =& m('bonuslog');
        $bonus_log = $bonus_log_mod->get(array(
            'conditions'=>'user_id='.$user['user_id'],
            'fields'=>'sum(cash_money) as bonus_sum,count(id) as bonus_num',
            ));
        //金额统计
        $user['bonus_sum'] = empty($bonus_log['bonus_sum']) ? '0.00' : $bonus_log['bonus_sum'];
        //统计多少个
        $user['bonus_num'] = empty($bonus_log['bonus_num']) ? '0' : $bonus_log['bonus_num'];

        //余额
        $user['money'] = empty($user['money']) ? '0.00' : $user['money'];

        $this->assign('user', $user);

        //获取绑定信息
        $generalize_member_mod =& m('generalize_member');
        $member_invite_mod =& m('memberinvite');

        //member_lv_id 大于1是创业者，反之是消费者
        $invite = $member_invite_mod->get(array(
        'conditions'=>'invitee = '. $user['user_id'],
        ));
        if($invite){
            //0消费者，1是创业者
            if( $invite['type'] == 0)
            {
            //memeber 获取邀请人的登陆账号
            $invitation = $this->_user_mod->get(array(
            'conditions' => 'user_id='.$invite['inviter'],
            'fields'=>'user_name,member_lv_id',
            ));
                $invitation['name'] = $invitation['user_name'];
                //获取等级
                $invitation['lv_name_info'] = $_lv_mod_list[$invitation['member_lv_id']]['name'];
                $invitation['type'] = 0;
            }
            elseif($invite['type'] == 1)
            {
            //去 generalize_member
            $invitation = $generalize_member_mod->get(array(
            'conditions' => 'id='.$invite['inviter'],
            'fields'=>'name,phone,invite',
            ));
                $invitation['name'] = $invitation['name'];
                $invitation['phone'] = $invitation['phone'];
                $invitation['type'] = 1;

            //获取他的顾客详细信息
            $page = $this->_get_page(5);


            $in_list = $member_invite_mod->find(array(
            'conditions' => 'member_invite.invitee = member.user_id AND member_invite.inviter='.$user['user_id'],
            'join'=>'belongs_to_member',
            'fields'=>'member.user_id,member.last_login,member.user_name,member.order_num,member.final_amount_num,member.reg_time,member_invite.add_time',
            'count' => true,
            'limit' => $page['limit'],
            ));
             foreach($in_list as $key=>$val){
              /*获取用户信息   Lil ADD 
               订单总额 final_amount_num  member
               实付金额   final_amount+money_amount
               收益   实付金额*30%
              */
              $order_l = $order_mod->get(array(
                'conditions' => 'status in (30,40,60) and user_id='.$val['user_id'],
                //'conditions' => 'status=30 and user_id=625',
                'fields' =>'count(order_id) as count ,sum(final_amount) as f_amount,sum(money_amount) as m_amount,sum(coin) as coin,sum(order_amount) as or_amount'
              ));
              if($order_l){
              	$in_list[$key]['final_amount_num'] =$order_l['or_amount'];
              	$in_list[$key]['order_num'] =$order_l['count'];
                $in_list[$key]['s_amount'] = $order_l['f_amount'] + $order_l['m_amount']+$order_l['coin'];
                $in_list[$key]['sy'] = $in_list[$key]['s_amount']*0.3;
              }else{
                $in_list[$key]['s_amount'] = '0.00';
                $in_list[$key]['sy'] = '0.00';
              }

             }
            $page['item_count'] = $member_invite_mod->getCount();
            $this->_format_page($page);
           
            
            
            $this->assign('page_info', $page);
            $this->assign('in_list',$in_list);

            }
        }
        // 最后统计一下  酷卡、麦富迪币、余额、优惠券，已经 红包 的 使用日志记录 汇总 @author tangsj
        $order_cash_log = &m('ordercashlog');
        $_special_code_mod = &m('special_code');
        $_kukaconfig_mod   = &m('kukaconfig');
        $order_kuka = &m('orderkuka');
        $order_debit= &m('orderdebit');
        $member = m('member');
        // $id user_id
        $conditions = " AND 1=1";
        $arr = array('profit'=>0,'integral'=>1,'coin'=>2,'debit'=>3,'money'=>4);
        $typename = array(0=>'收益',1=>'积分',2=>'mfd币',4=>'余额'); // 类型名称
        foreach ($arr as  $key=>$val){
            if($type == $key){
                $conditions .= " AND type =".$val;
            }
        }
        $search_arr= array(
            '1' => "最近三个月的记录",
            '2' => "最近一个月的记录",
            '3' => "最近一周的记录",
            '4' => "最近三天的记录",
        );
        $search = isset($_GET['search']) ? $_GET['search'] : 1;
        if ($search == 1)
        {
            $limit_time = 90*24*60*60;
        }
        elseif ($search == 2)
        {
            $limit_time = 30*24*60*60;
        }
        elseif ($search == 3)
        {
            $limit_time = 7*24*60*60;
        }
        elseif ($search == 4)
        {
            $limit_time = 3*24*60*60;
        }
        else
        {
            $this->show_warning('参数错误');
            return ;
        }
        $l_time = gmtime() - $limit_time;
        
        $conditions .= " AND add_time > $l_time";
        
        if(in_array($type, $arr)){
            $memlogs = $order_cash_log->findAll(array(
                'conditions'	=> "user_id='{$id}' ".$conditions,
                'fields'		=> "*",
                'order'         =>"add_time DESC",
                'count'			=> true,
            ));  
            foreach ($memlogs as $k=>$v){
                $memlogs[$k]['type_name'] = $typename[$v['type']];
                $memlogs[$k]['user_name'] = $user['user_name'];
                $memlogs[$k]['nickname'] = $user['nickname'];
            }
            $this->assign('memlogs', $memlogs);
        }
        if($type == 'kuka'){
            
            if($user['member_lv_id'] >= 2 ) {//创业者
                $conditions .= " AND from_id = '{$id}'";
            }else{
                $conditions .= " AND to_id = '{$id}' ";
            }
            $info = $_special_code_mod->findAll(array(
                'conditions'	=> "1=1 AND cate>19  ".$conditions,
                'fields'		=> "*",
                'order'         =>"add_time DESC",
                'count'			=> true,
            ));
            
            if($info){
                foreach($info as $k=>$v){
                    $info[$k]['user_name'] = $user['user_name'];
                    $info[$k]['nickname'] = $user['nickname'];
                    $info[$k]['donation_mes'] = '';
                    $info[$k]['cate_name']    = '';
                    $info[$k]['expire_time'] = date("Y-m-d",$v['expire_time']);
                    if($v['expire_time'] - time() > 0) {
                        $days = floor(($v['expire_time'] - time())/86400)+1;
                        $info[$k]['day'] = $days;
                    }
                    //=====  获取品类名称  =====
                    if ($v['name'])
                    {
                        $info[$k]['cate_name'] = $v['name'];
                    }
                    else
                    {
                        if($info[$k]['cate'] = '22')
                        {
                            $kuka_online = $_kukaconfig_mod->get("id = '{$v['log_id']}'");
                            if ($kuka_online['kuka_name'])
                            {
                                $info[$k]['cate_name'] = $kuka_online['kuka_name'];
                            }
                            else
                            {
                                $info[$k]['cate_name'] = "";
                            }
                        }
                        else
                        {
                            $info[$k]['cate_name'] = $this->_cate[$v['cate']]['name'];
                        }
                    }
              
            
                    $to_id = trim($v['to_id']);
                    if($v['from_id'] && $to_id ) {//已转赠
                        if($user['user_id'] == $v['from_id'] && !empty($to_id) ) {//转赠人回去抵用券信息
                            $memberinfo = $member->get($to_id);
                            $info[$k]['donation_mes'] = '已转赠给'.$memberinfo['nickname'];
                        }
                        if($user['user_id'] == $v['to_id']  ) {//被转赠人回去抵用券信息
                            $memberinfo = $member->get($v['from_id']);
                            $info[$k]['donation_mes'] = '赠送者：'.$memberinfo['nickname'];
                        }
                    }
            
                }
            
            }
            // 查看是否关联订单信息
            foreach ($info as $k=>$v){
                $ors =  $order_kuka->get("k_sn ='{$v['sn']}'");
                if($ors){
                    $info[$k]['order_id'] = $ors['order_id'];
                    $order_info = $order_mod->get($info[$k]['order_id']);
                    $info[$k]['order_sn'] =  $order_info['order_sn'];
                    $info[$k]['is_active'] = $ors['is_active'];
                }else{
                    $info[$k]['order_sn'] =  '';
                    $info[$k]['is_active'] =  '';
                }
                
            }
            
            $this->assign('memlogs', $info);
        }
        
        if($type =='debit'){
            
            
        }
        
      
        //邀请变量
        $this->assign('invitation', $invitation);
        $this->assign('type', array(
            'money' 	=> '余额',
            'coin'      => '麦富迪币',
            'debit'     => '优惠券',
            'kuka'      => '酷卡',
            'integral'  => '积分',
            'profit'    => '收益',
        ));
        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
            'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->assign('search',$search_arr);
        $this->assign('tp',$_GET['type']);
        $this->display('member_info.index.html');
    }




    /**
     * 用户登陆日志
     *
     * @author yusw
     * @return void
     */
    function user_log()
    {
        $_member_mod =&m('member');
        $source_conf = array(
            1=>'pc',
            2=>'wechat',
//            3=>'m',
        );


        $conditions = 'type=1';

        $user_name = isset($_GET['user_name']) ? trim($_GET['user_name']) : '';

        if($user_name){
            $user_info =$_member_mod->get(array(
                'conditions'=>"user_name='{$user_name}'",
                'fields'=>"user_name"
            ));
            !empty($user_info) &&$conditions .=" and user_id={$user_info['user_id']}";
        }


        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'source',
                'name' =>  'source',
                'equal' => '=',
                'type'  => 'numeric',
            ),
            array(
                'field' => 'add_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),array(
                'field' => 'add_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
            )
        ));


        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order, array('asc', 'desc'))) {
                $sort = 'id';
                $order = 'desc';
            }
        } else {
            $sort = 'id';
            $order = 'desc';
        }


        $page = $this->_get_page(30);
        $memberlogs = &m('memberlogs');
        $list = $memberlogs->find(array(
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",
        ));

        $ids = array();
        foreach($list as $k=>$v){
            $ids[]=$v['user_id'];
        }
        $user_info =$_member_mod->find(array(
              'conditions'=>db_create_in($ids,'user_id'),
              'fields'=>"user_name"
        ));
        $list[$k]['user_name'] = $user_info['user_name'];
        foreach($list as $k=>$v){
            $list[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $list[$k]['user_name'] = $user_info[$v['user_id']]['user_name'];
        }

        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                     'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
//echo '<pre>';print_r($list);exit;





        $page['item_count'] = $memberlogs->getCount();
        $this->_format_page($page);


        $this->assign('source_conf', $source_conf);
        $this->assign('user_info', $user_info);


        $this->assign('page_info', $page);
        $this->assign('list', $list);
//        echo '<pre>';print_r($list);exit;
        
        $this->display('user.logs.html');

    }
}
?>
