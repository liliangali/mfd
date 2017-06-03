<?php

/* 管理员控制器 */
class AdminApp extends BackendApp
{
    var $_admin_mod;
    var $_grouop_mod;
    var $_user_mod;
    var $group;

    function __construct()
    {
        define('PROJECT_PATH', str_replace('config.php', '', str_replace('\\', '/', __FILE__)));//指到v1
        $this->AdminApp();
        $this->group = [
            '1' => [
                'name' => '领导',
                'son' => [
                    '2' =>'项目经理'
                ],
            ],

            '100' => [
                'name' => '运营',
                'son' => [
                    '101' =>'经理',
                    '102' =>'客服',
                    '103' =>'推广'
                ],
            ],

            '200' => [
                'name' => '技术研发',
                'son' => [
                    '201' =>'经理',
                    '202' =>'专家客服',
                ],
            ],
        ];
    }

    function AdminApp()
    {
        parent::__construct();
        $this->_admin_mod = & m('userpriv');
        $this->_grouop_mod = m('grouppriv');
        $this->_user_mod = & m('member');
    }
    //管理
    function index()
    {
        $pid_arr = [];
        $pid_arr[0] = "请选择全部";
        foreach ($this->group as $index => $item)
        {
            $pid_arr[$index] = $item['name'];
        }

        $this->assign('pid_arr',$pid_arr);
        $this->assign('group_list',$this->group);
        $this->display('group.index.html');
    }
    

    function groupindex()
    {
        $usergroup = m('usergroup');
    	$id = isset($_GET['id']) ? $_GET['id'] : 0;
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
             $sort  = 'user_priv.user_id';
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
                $sort  = 'usergroup.user_id';
                $order = 'DESC';
            }
        }
        
        $conditions .= ' AND group_id = '.$id;
        //更新排序
       // $sort  = 'userpriv.user_id';
        //$order = 'asc';
        $page = $this->_get_page(30);
        $admin_info = $usergroup->find(array(
            'conditions' =>"1=1 ".$conditions,
            'limit' => $page['limit'],
            'join' => 'mall_be_manage',
            'order' => "$sort $order",
            'count' => true,
        ));
//   echo '<pre>';print_r($admin_info);exit;
    
        $page['item_count'] = $usergroup->getCount();
        $this->_format_page($page);
        $this->assign('query_fields', array(
    			'user_name' => '用户名',
    			'real_name'     => '真实姓名',
    	));
    	$this->assign('sort_options', array(
            'reg_time DESC'   => LANG::get('reg_time'),
            'last_login DESC' => LANG::get('last_login'),
            'logins DESC'     => LANG::get('logins'),
        ));
        $this->assign('page_info',$page);
        $this->assign('admins',$admin_info);
        $this->assign('id',$id);
        $this->display('admin.index.html');
    }
    //删除管理员-只进行删除权限。不进行member表操作
    function drop()
    {
        $usergroup = m('usergroup');
        $id = (isset($_GET['id']) && $_GET['id'] !='') ? trim($_GET['id']) : '';
        //判断是否选择管理员
        $ids = explode(',',$id);
        if (!$id||$this->_admin_mod->check_admin($id))
        {
            $this->show_warning('choose_admin');
            return;
        }
        //判断是否是系统初始管理员
        if ($this->_admin_mod->check_system_manager($id))
        {
            $this->show_warning('system_admin_drop');
            return;
         }
         //删除管理员
        $conditions = "store_id = 0 AND user_id " . db_create_in($ids);
        if (!$res = $this->_admin_mod->drop($conditions))
        {
            $this->show_warning('drop_failed');
            return;
        }

        //删除管理员
        $conditions = " user_id " . db_create_in($ids);
        if (!$res = $usergroup->drop($conditions))
        {
            $this->show_warning('drop_failed');
            return;
        }

        $this->show_message('drop_ok', 'admin_list', 'index.php?app=admin');
    }




    function edit()
    {
        $usergroup = m('usergroup');
        $id = (isset($_GET['id']) && $_GET['id'] !='') ? intval($_GET['id']) : '';
        //判断是否选择了管理员
//        if (!$id || $this->_admin_mod->check_admin($id))
//        {
//            $this->show_warning('choose_admin');
//            return;
//        }
        if(!$id)
        {
            $this->show_warning('choose_admin');
            return;
        }
         if ($this->_admin_mod->check_system_manager($id))
        {
            $this->show_warning('system_admin_edit');
            return;
        }
        $admin = [];
        if (!IS_POST)
        {
            
            foreach ($this->group as $index => $item)
            {
                foreach ($item['son'] as $index1 => $item1)
                {
                    if($index1 == $id)
                    {
                        $admin['name'] = $item['name']."|".$item1;
                        $admin['id'] = $index1;
                        break;
                    }
                }
            }

            
            //获取当前管理员权限
            $privs = $this->_grouop_mod->get(array(
                'conditions' => '  group_id = '.$id,
                'fields' => 'privs',
            ));

            $priv=explode(',', $privs['privs']);
           
            include(ROOT_PATH.'/mfd/includes/priv.inc.php');
            $act = 'edit';
            $this->assign('act',$act);
            $this->assign('admin',$admin);
            $this->assign('checked_priv',$priv);
            $this->assign('priv',$menu_data);
            $this->display('admin.form.html');
        }
        else
        {
            //更新管理员权限
            $privs = (isset($_POST['priv']) && $_POST['priv']!='priv') ? $_POST['priv']: '';
            $priv = '';
            if ($privs == '')
            {
                $this->show_warning('add_priv');
                return;
            }
            else
            {
                $priv = implode(',', $privs);
            }
            $data = array(
                    'privs' => $priv,
               );
            if($this->_grouop_mod->get_info($id))
            {
                $this->_grouop_mod->edit($id, $data);
            }
            else
            {
                $data['group_id'] = $id;
                $this->_grouop_mod->add($data);
            }

            //更新会员权限
            $group_list = $usergroup->find(array(
                'conditions' => 'group_id = '.$id,
            ));

           foreach ((array)$group_list as $index => $item)
           {
               $uid = $item['user_id'];
               if($uid)
               {
                   $this->_admin_mod->edit($uid,$data);
               }
           }

            if($this->_admin_mod->has_error())
            {
                 $this->show_warning($this->_admin_mod->get_error());
                 return;
             }
             else
            {
                $this->show_message('edit_admin_ok');
                return true;
             }
        }
    }


    function add()
    {
        $member_mod = m('member');
        $usergroup = m('usergroup');
        $id = (isset($_REQUEST['id']) && $_REQUEST['id'] != '') ? intval($_REQUEST['id']) : '';
        $admin = [];
        foreach ($this->group as $index => $item)
        {
            foreach ($item['son'] as $index1 => $item1)
            {
                if($index1 == $id)
                {
                    $admin['name'] = $item['name']."|".$item1;
                    $admin['id'] = $index1;
                    break;
                }
            }
        }
        $this->assign('admin',$admin);
        if(!IS_POST)
        {
            $this->display('admin.test.html');
        }
        else
        {
            $user_name = (isset($_POST['user_name'])&&$_POST['user_name']!='') ? $_POST['user_name']:'';
            $info = $member_mod->get("user_name = '$user_name' ");
            if (empty($info))
            {
                $this->show_message('add_member', 'go_back', 'index.php?app=admin&amp;act=add', 'to_add_member', 'index.php?app=user&amp;act=add');
                return;
            }
            else
            {
                if($usergroup->get_info("user_id = ".$info['user_id']))
                {
                   $this->show_message("此账号已是管理员");
                   return;
                }

                $gooup_priv = $this->_grouop_mod->get_info($id);
                $groupdata['user_id'] = $info['user_id'];
                $groupdata['group_id'] = $id;
                $usergroup->add($groupdata);//


                $privs = isset($gooup_priv['privs']) ? $gooup_priv['privs'] : '';
                $prisdata['user_id'] = $info['user_id'];
                $prisdata['privs'] = $privs;
                $this->_admin_mod->drop("user_id = ".$info['user_id']);
                $this->_admin_mod->add($prisdata);
                $this->show_message('add_admin_ok', 'admin_list', 'index.php?app=admin', 'user_list', 'index.php?app=user');
                return;
            }
        }


        if (empty($_POST['priv']))
        {

        }
        else
        {
            //获取权限并处理
            $privs = (isset($_POST['priv']) && $_POST['priv'] != 'priv') ? $_POST['priv'] : '';
            $priv = 'default|all,';
            if ($privs == '')
            {
                $this->show_warning('add_priv');
                return;
            }
            else
            {
                $priv .= implode(',', $privs);
            }
             //判断是否已是管理员
             if (!$this->_admin_mod->check_admin($id))
                {
                    $this->show_warning('already_admin');
                    return;
                }
             $data = array(
                    'user_id' => $id,
                    'store_id' => '0',
                    'privs' => $priv,
                );
             if ($this->_admin_mod->add($data) === fasle)
             {
                 $this->show_warning($this->_admin_mod->get_error());
                 return;
             }
             else
            {
                $this->show_message('add_admin_ok', 'admin_list', 'index.php?app=admin', 'user_list', 'index.php?app=user');
             }
        }
    }
    /**
     * 填补管理员信息
     * @author liuchao <280181131@qq.com>
     * @param int $user_id
     */
    function sub()
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
            /* 头像 add by xiao5 START */
            require(ROOT_PATH.'/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();
            //获得用户头像
            $avatar = $objAvatar->avatar_show($user['user_id'], 'big');
            $user['avatar'] = $avatar.'?'.$user['avatar_time'];//加入头像时间，用于app及时更新头像

            $this->assign('user', $user);
            $this->assign('phone_tel', explode('-', $user['phone_tel']));
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->display('admin.sub.html');
        }
        else
        {
            $data = array(
                'is_service' => $_POST['service'],
                'avatar_time'=> time(),
                'nickname'  => $_POST['nickname'],
                'gender'    => $_POST['gender'],
                'im_qq'     => $_POST['im_qq'],
                'im_msn'    => $_POST['im_msn'],
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
                    $this->show_warning('1123');
                    return;
                }
                $data['avatar'] = $portrait;
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
                'back_list',    'index.php?app=admin&amp'

            );
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

        //头像--基础控制器类
        require(ROOT_PATH.'/includes/avatar.class.php');
        $objAvatar = new Avatar();
        $image_name = $objAvatar->uploadAvatar($file, 220, 220, $user_id, 'big');
        return 'upload/avatar/'.$image_name;
/*
        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->addFile($file);
        if ($uploader->file_info() === false)
        {
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=admin&amp;act=sub&amp;id=' . $user_id);
            return false;
        }

        $uploader->root_dir(ROOT_PATH);

        return $uploader->save('upload/avatar/'.$objAvatar->get_avatar($user_id), $user_id);*/
    }

}

?>
