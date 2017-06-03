<?php
class SubscribeApp extends MallbaseApp
{
	
	var $_subscribe_mod,$user_id;
    function __construct()
    {
    	parent::__construct();
        $this->_subscribe_mod = m('subscribe');
        //var_dump($this->visitor->info);
    	$this->_serve=m('serve');
        
        $this->user_id = $this->visitor->get('user_id');
    }
	
    
	
	function index(){

		if (!$this->visitor->has_login)
        {
            $this->show_warning('login_please');
            return;
        }
		
		if(!IS_POST)
		{
		/* 导入jQuery的表单验证插件 */
		$this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
		'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'
            ));
        
		$res=$this->_subscribe_mod->get(array(
    			'conditions' => '1 = 1'));	
		//var_dump($res);
        
		$args = $this->get_params();
		
		$resserve=$this->_serve->get(array(
    			'conditions' => 'serve.idserve = '.$args[0],
				'join' => 'has_serve_detai',));
		
        $this->assign('title',"量体服务-".$resserve['serve_name']."-西服定制中心、男士正装定制、国际高端西服品牌|RCRAILOR");
        
		$this->display('subscribe.index.html');
		}else {
			//var_dump($_POST);
			
			if (!$_POST['username']||!$_POST['mobile']||!isset($_POST['subscribe_type'])||!$_POST['address'])
			{
				$this->show_warning('信息填写不完整！');
				return;
			}
			
			$args = $this->get_params();
		
			if(!$args[0]){
				$this->show_warning('error！');
				return;
			}
			$res=$this->_subscribe_mod->get(array(
    			'conditions' => ' (state=0 or state=3) and subscribe_type = '.$_POST['subscribe_type'].' and userid = '.$this->user_id,));
			
			if(!$res)
			{
				$data=array(
				'username'=>$_POST['username'],
				'mobile'=>$_POST['mobile'],
				'subscribe_type'=>$_POST['subscribe_type'],
				'address'=>$_POST['address'],
				'remarks'=>$_POST['remarks'],
				'figure_mode'=>$_POST['figure_mode'],
				);
				
				
				
				$data['userid']=$this->user_id;
				$data['idserve']=$args[0];
				
				$data['timeserve']=$_POST['timeserve'];
				$data['timeopt']=$_POST['timeopt'];
				$this->_subscribe_mod->add($data);

				$this->assign('redirects', 1);
				$this->show_message('您的量体预约已经提交成功！','我的量体','/index.php/my_body.html','联络服务点','/index.php/service-info-'.$args[0].'.html');

				return;
			}else 
			{
				$this->assign('redirects', 1);
				$this->show_message('您已经提交过量体服务并且还未处理，请返回!','我的量体','/index.php/my_body.html','联络服务点','/index.php/service-info-'.$args[0].'.html');
				return;
			}			
		}
	}
}