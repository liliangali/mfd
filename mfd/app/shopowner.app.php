<?php
//ns add 店长管理
class ShopownerApp extends BackendApp
{
	var $_serve;
	var $_member;
    var $_lv_mod;
    function __construct()
    {       
    	$this->ShopownerApp();
     }
    function ShopownerApp()
    {
        parent::__construct();
       	$this->_serve =& m ('serve');
        $this->_member =& m ('member');
        $this->_lv_mod =& m('memberlv');
    }
    //首页
    function index(){
    	if($_GET['field_name'] == 'serve_name'){  //店铺名称
            $conditions = $this->_get_query_conditions(array(
                    array(
                            'field' => $_GET['field_name'],
                            'name'  => 'field_value',
                            'equal' => 'LIKE',
                    ),
            ));
            $page = $this->_get_page(30);
            //这里先查询店铺名称
            $serve = $this->_serve->find(array(
                 'conditions'=>"1=1".$conditions,
                 'count' => true,
                 'limit' => $page['limit'],
                 'index_key' => 'userid'
                ));
            $cids = array();
            $cls = array();
            foreach($serve as $val){
                $cls[$val["userid"]] =$val;
                $cids[] = $val["userid"];
            }
            //获取名称跟信息
            $managers  = $this->_member->find(array(
                "conditions" => "user_id ".db_create_in($cids),
                'index_key'=>'user_id',
            ));
            //融合在一起
            foreach($cls as $key => $val){
                $cls[$key] = $managers[$key];
                $cls[$key]["ctm"] = $val;
            }
            $page['item_count'] = $this->_serve->getCount();   //获取统计的数据
            $this->_format_page($page);
            $this->assign('liangti',$cls);

            $this->assign('page_info', $page);
        }else{  //普通的
            $conditions = $this->_get_query_conditions(array(
                    array(
                            'field' => $_GET['field_name'],
                            'name'  => 'field_value',
                            'equal' => 'LIKE',
                    ),
            ));
            $page = $this->_get_page(30);
            //获取用户店长信息
            $managers=$this->_member->find(array(
                    'conditions'=>"serve_type=2 AND figure_type=1".$conditions, 
                    'count' => true,
                    'limit' => $page['limit'],
                    'order' => 'reg_time DESC'
            ));
            $cids = array();
            $cls = array();
            foreach($managers as $val){
                $cls[$val["user_id"]] =$val;
                $cids[] = $val["user_id"];
            }
            //获取名称跟信息
            $serve  = $this->_serve->find(array(
                "conditions" => "userid ".db_create_in($cids),
                'index_key'=>'userid',
            ));

            //融合在一起
            foreach($cls as $key => $val){
                $cls[$key]["ctm"] = $serve[$key];
            }
            $page['item_count'] = $this->_member->getCount();   //获取统计的数据
            $this->_format_page($page);
            $this->assign('liangti',$cls);

            $this->assign('page_info', $page);
        }
        $this->assign('query_fields', array(
            'serve_name' =>'门店名称',
            'user_name'     =>'店长手机号',
        ));
        $this->display('shopowner.index.html');
    }
    //编辑店长资料
    function edit(){
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $user = $this->_member->get_info($id);
            if (!$user)
            {
                $this->show_warning('user_empty');
                return;
            }
            
            // 店长证件号码
            $serve_info = $this->_serve->get("userid='{$id}'");
            if($serve_info){
            	
            	$card_number = $serve_info['card_number'];
            	$idserve	 = $serve_info['idserve'];
            	
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
            $this->assign('card_number', $card_number);
            $this->assign('idserve', $idserve);
            $this->assign('phone_tel', explode('-', $user['phone_tel']));
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->display('shopowner.form.html');
        }
        else
        {	
        	$card_number = $_POST['card_number'];
       
            $data = array(
                'real_name' => $_POST['real_name'],
                'email' => $_POST['email'],
                'gender'    => $_POST['gender'],
                'im_qq'     => $_POST['im_qq'],
                'im_msn'    => $_POST['im_msn'],
            		'alone' => $_POST['alone'],
                'phone_tel' => $_POST['phone_tel'], //联系方式-用于门店
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
            $this->_member->edit($id, $data);
            $this->_serve->edit("userid='$id'",array(
            		'card_number'=>$card_number,
            		'linkman' =>$_POST['real_name']
            ));

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
                'back_list',    'index.php?app=shopowner',
                'edit_again',   'index.php?app=shopowner&amp;act=edit&amp;id=' . $id
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
    
    /*检查的唯一性*/
    function  check_card_number()
    {
    	$card_number = empty($_GET['card_number']) ? null : trim($_GET['card_number']);
    	$id = $_GET['id'];
    	
    	if (!$card_number)
    	{
    		echo 0;
    	}
    	
    	$conditions = "card_number ='$card_number'";
    	if($id)
    	{
    		
    		$conditions .= "AND idserve !=$id";
    	}
    	
    	if($this->_serve->get($conditions))
    	{
    		echo 0;	
    	}else
    	{
    		echo 1;
    	}
    	
    	
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




}


?>