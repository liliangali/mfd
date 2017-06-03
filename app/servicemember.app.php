<?php
class ServicememberApp extends MemberbaseApp
{
	var $_serve,$user_id,$_service;
	function __construct(){
		parent::__construct();
		$this->_serve=m('serve');
		$this->_service=m('servedetail');
		
		$this->user_id = $this->visitor->get('user_id');
		$this->idserve = $this->visitor->get('idserve');
		
		$this->serve_type=$this->visitor->get('serve_type');
		
		$res=$this->_serve->get(array(
	    			'conditions' => 'serve.userid = '.$this->user_id));
		if(!$res)
		{
			$this->show_message('is_not_serve');
			die();
		}elseif($res['state']!=1){
			$this->show_message('state_'.$res['state']);
			die();
		}
		
		
		 /* 当前用户中心菜单 */
        $this->_curitem('service');
		$model_user = m('member');
        $profile    = $model_user->get_info(intval($this->user_id));
        $profile['portrait'] = portrait($profile['user_id'], $profile['portrait'], 'middle');
        
        
        
        $this->assign('SITE_URL',SITE_URL);
        $this->assign('profile',$profile);
        
        $this->assign('title', 'RCTAILOR-酷客中心-服务点信息');
        
	}
	function profile()
	{
		
		if (!IS_POST)
        {
        	/* 当前所处子菜单 */
        	$this->_curmenu('profile');
			$res=$this->_serve->get(array(
	    			'conditions' => 'serve.userid = '.$this->user_id,
					'join'=>'has_serve_detai',
					'order'=>'serve.idserve desc',
			));
				//var_dump($res);exit;
				$this->assign('serve', $res);
				
				 $region_mod =m('region');
            $this->assign('regions', $region_mod->get_options(0));
				
            	 $mlregions=$region_mod->get_parents($res['region_id']);
            //var_dump($mlregions);
            if(count($mlregions)>2)
            {
            	
            
	            $this->assign('r1', $mlregions[0]);
	            $this->assign('r2', $mlregions[1]);
	            $this->assign('value', $res['region_id']);
	            //var_dump($mlregions[2]);
            }
            
			/* 导入jQuery的表单验证插件 */
	            $this->import_resource(array(
	                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js'
	            ));
	            $this->display('servicemember/servicemember.profile.html');
        }else
        {
            $data = array(
                
                'email'    => $_POST['email'],

                'linkman'     => $_POST['linkman'],
                'enterprise_url'    => $_POST['enterprise_url'],
				'mobile'    => $_POST['mobile'],
            	'post'    => $_POST['post'],
            	
            //serve_name
            //serve_address
            //region_name
            //region_id
            //company_name
            
            );
            $_POST["region_name"]=implode(' ',(explode("	",$_POST["region_name"])));
            $data['region_id']=$_POST['region_id'];
            $data['region_name']=$_POST['region_name'];
            $data['company_name']=$_POST['company_name'];
            $data['serve_address']=$_POST['serve_address'];
            $data['serve_name']=$_POST['serve_name'];
            
            if (!is_email(trim($_POST['email'])))
            {
                $this->show_warning('email_error');

                return;
            }


            /* 修改本地数据 */
            $this->_serve->edit('idserve='.$this->idserve, $data);
            
            if(isset($_POST['weixin']))
            {
            	$servedetaildata=array('weixin'=>$_POST['weixin']);
            	if($this->idserve)
            	{
            		$this->_service->edit('idserve='.$this->idserve,$servedetaildata);
            	}
            	
            }
            
            
            
            $this->show_message('修改成功');
        }
		
        
        
        
        
            
        
		
	}
	
	function profilemore()
	{
		
		
		$res=$this->_serve->get(array(
    			'conditions' => 'serve.userid = '.$this->user_id,
				'order'=>'idserve desc',
		));
		
		$memberlv_mod=m('memberlv');
            $memberlv_data=$memberlv_mod->get($res['brokerage_level']);
            //var_dump($memberlv_data['name']);
            //$serve['memberlv_name']=$memberlv_data['name'];
			
			$res['brokerage_level']=$memberlv_data['name']; 
		
			$this->assign('serve', $res);
        
		$this->_curmenu('profilemore');
		if($res['serve_type']==2)
		{
		$view=&v();
		$url=M_SITE_URL.$view->build_url(array('app'=>'service','act'=>'info','arg'=>$res['idserve']));
		
		//var_dump($url);exit;
		QRcode('service',$res['idserve'],$url);
		$mqrcode=getQrcodeImage('service',$res['idserve'],4);
        //var_dump($mqrcode);exit;
		$this->assign('mqrcode',$mqrcode);
		}
		
		if($this->serve_type==3)
		{
			$this->assign('serve_type_name','加盟商');
		}else if($this->serve_type==2)
		{
			$this->assign('serve_type_name','服务点');
		}else 
		{
			$this->assign('serve_type_name','服务点');
		}
		
		$this->display('servicemember/servicemember.profilemore.html');
	}
	
	function servedetail()
	{
		$this->_curitem('servedetail');
		//$this->_curmenu('servedetail');
		$res=$this->_serve->get(array(
    			'conditions' => 'serve.userid = '.$this->user_id,
				'order'=>'idserve desc',
		));
		$id = $res['idserve'];
        if (!IS_POST)
        {
            /* 是否存在 */
            $service = $this->_service->get(array('conditions' => 'serve_detail.idserve='.$id ,
            'join'=>'has_serve', 
            'order'=>'serve_detail.id desc',
            ));
            
            if (!$service)
            {//没有详情表，添加.
            	$this->_service->add(array('idserve'=>$id));
            	$service = $this->_service->get(array('conditions' => 'idserve='.$id ,
            	'join'=>'has_serve',
            	'order'=>'serve_detail.id desc',
            	 ));
            }

            //var_dump($service);exit;
            $this->assign('service', $service);

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'introduce',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            
            
        	$this->display('servicemember/servicemember.servedetail.html');
            
        }
        else
        {
        	
            $data = array(
                //'portrait' => $_POST['portrait'],
                'synopsis'    => $_POST['synopsis'],
                'introduce'     => $_POST['introduce'],
                'longitude'    => $_POST['longitude'],
				'latitude'    => $_POST['latitude'],
            	'qq'    => $_POST['qq'],
            );
			
        	if (!empty($_FILES['portrait']))
            {
            	
                $portrait = $this->_upload_portrait($id);
                //var_dump($portrait);
                if ($portrait === false)
                {
                    return;
                }
                $data['portrait'] = $portrait;
            }
            
            /* 修改本地数据 */
            $this->_service->edit('idserve='.$id, $data);

            

            $this->show_message('修改成功');
        }

		
	}
	
	/**
     *    三级菜单
     *
     *    @author    Hyber
     *    @return    void
     */
    function _get_member_submenu()
    {
        $submenus =  array(
            array(
                'name'  => 'profile',
                'url'   => $this->_view->build_url(array('app'=>'servicemember','act'=>'profile')),
            	
            ),
            array(
                'name'  => 'profilemore',
                'url'   => $this->_view->build_url(array('app'=>'servicemember','act'=>'profilemore')),
            )
        );
        

        return $submenus;
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
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=servicemember&amp;act=profile');
            return false;
        }
        $uploader->root_dir(ROOT_PATH);
        return $uploader->save('data/files/mall/portraitservice/' . ceil($user_id / 500), $user_id);
    }
    
    
	/*检查服务点名称的唯一性*/
	function  check_serve_name()
	{
		$this->_serve_mod = m('serve');

		$serve_name = empty($_GET['serve_name']) ? null : trim($_GET['serve_name']);
		if (!$serve_name)
		{
			echo ecm_json_encode(false);
			return ;
		}
		
		
		
		$conditions = "serve_name = '" . $serve_name . "' and  idserve!= ".$this->idserve;
        $res=count($this->_serve_mod->find(array('conditions' => $conditions))) == 0;
		
		
		echo ecm_json_encode($res);
	}
    
}