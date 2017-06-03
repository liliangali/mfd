<?php

/* 店铺控制器 */
class StoreApp extends BackendApp
{
    var $_store_mod;
    var $_applylog_mod;

    function __construct()
    {
        $this->StoreApp();
    }

    function StoreApp()
    {
        parent::__construct();
        $this->_store_mod =& m('store');
        $this->_applylog_mod =& m('applylog');
        $this->_brand_mod =& m('brand');
        $this->store_attr_mod =& m('store_attr');
        $this->_userfollow_mod =& m('userfollow');
    }

    function index()
    {
        $conditions = empty($_GET['wait_verify']) ? "state <> '" . STORE_APPLYING . "'" : "state = '" . STORE_APPLYING . "'";
        $filter = $this->_get_query_conditions(array(
            array(
                'field' => 'store_name',
                'equal' => 'like',
            ),
            array(
                'field' => 'sgrade',
            ),
        ));
        $owner_name = trim($_GET['owner_name']);
        if ($owner_name)
        {

            $filter .= " AND (user_name LIKE '%{$owner_name}%' OR owner_name LIKE '%{$owner_name}%') ";
        }
        //更新排序
        if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
                $sort  = 'sort_order';
                $order = 'asc';
            }
        }
        else
        {
            $sort  = 'sort_order';
            $order = 'asc';
        }

        $this->assign('filter', $filter);
        $conditions .= $filter;
        $page = $this->_get_page();
        $stores = $this->_store_mod->find(array(
            'conditions' => $conditions,
            'join'  => 'belongs_to_user',
            'fields'=> 'this.*,member.user_name',
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order"
        ));
        $sgrade_mod =& m('sgrade');
        $grades = $sgrade_mod->get_options();
        $this->assign('sgrades', $grades);

        $states = array(
            STORE_APPLYING  => LANG::get('wait_verify'),
            STORE_OPEN      => Lang::get('open'),
            STORE_CLOSED    => Lang::get('close'),
        );
        foreach ($stores as $key => $store)
        {
            $stores[$key]['sgrade'] = $grades[$store['sgrade']];
            $stores[$key]['state'] = $states[$store['state']];
            $certs = empty($store['certification']) ? array() : explode(',', $store['certification']);
            for ($i = 0; $i < count($certs); $i++)
            {
                $certs[$i] = Lang::get($certs[$i]);
            }
            $stores[$key]['certification'] = join('<br />', $certs);
        }
        $this->assign('stores', $stores);

        $page['item_count'] = $this->_store_mod->getCount();
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->_format_page($page);
        $this->assign('filtered', $filter? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);

        $this->display('store.index.html');
    }
    /**
    * 更改裁缝状态
    * ns add
    */
    function store_state(){
        $store_id = empty($_GET['store_id']) ? 0 : $_GET['store_id'];
        $state = empty($_GET['state']) ? 1 : $_GET['state'];
        if(!$store_id){
            $this->show_warning('此裁缝不存在');
            return;
        }
        $this->_store_mod->edit($store_id,array('state'=>$state));
        //更改用户登录状态
         $_member_mod=m('member');
         $_member_mod->edit($store_id,array('state_info'=>$state));

        $this->show_message('edit_ok','back_list','index.php?app=store');
    }
    /**
     * 申请面料采购
     * ns add
     */
    function store_fabric(){
       $page = $this->_get_page(20);
       $store_fabric_mod =& m('store_fabric');
       $conditions = empty($_GET['wait_verify']) ? "status <> '" . STORE_APPLYING . "'" : "status = '" . STORE_APPLYING . "'";
       //状态
       $status = !empty($_GET['status'])? intval($_GET['status']) : '';

       $filter = $this->_get_query_conditions(array(
            array(
                'field' => 'owner_name',
                'equal' => 'like',
            ),
            array(
                'field' => 'sgrade',
            ),
        ));
        $owner_name = trim($_GET['owner_name']);
        if ($owner_name)
        {

            $filter .= " AND owner_name LIKE '%{$owner_name}%' ";
        }
        if($status){
            $filter .= " AND status={$status}";
        }
       $store_mod =& m('store');
               //更新排序
      if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
                $sort  = 'add_time';
                $order = 'asc';
            }
        }
        else
        {
            $sort  = 'add_time';
            $order = 'asc';
        }
       $this->assign('filter', $filter);
       $conditions .= $filter;
       $store_fabric = $store_fabric_mod->find(array(
                'conditions' => $conditions,
                'limit'      => $page['limit'],
                'order'      => "$sort $order",
                'count'      => true,
        ));
       $status = array(0=>'未审核',1=>'已采购',2=>'采购中',3=>'审核失败',4=>'已受理');
       foreach($store_fabric as $k=>$v){
            $store_fabric[$k]['store'] = $store_mod->get(array('conditions'=>'store_id='.$v['store_id']));
            $store_fabric[$k]['status_name'] = $status[$v['status']];
       }
       $this->assign('store_fabric',$store_fabric);


        $page['item_count'] = $store_fabric_mod->getCount();
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->_format_page($page);
        $this->assign('filtered', $filter? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        $this->display("store_fabric.index.html");
    }

    function edit_store_fabric(){
        $fabric_id = empty($_GET['fabric_id']) ? 0 : $_GET['fabric_id'];
        $status = empty($_GET['status']) ? 1 : $_GET['status'];
        if(!$fabric_id){
            $this->show_warning('此申请不存在');
            return;
        }
        $store_fabric_mod =& m('store_fabric');
        $store_fabric_mod->edit('fabric_id='.$fabric_id,array('status'=>$status));
        $this->show_message('edit_ok',
                'back_list',    'index.php?app=store&act=store_fabric'
            );

    }

    //ns add 裁缝申请量体
    function store_dispatching(){
       $page = $this->_get_page(20);
       $store_dispatching_mod =& m('store_dispatching');
       $conditions = empty($_GET['wait_verify']) ? "status <> '" . STORE_APPLYING . "'" : "status = '" . STORE_APPLYING . "'";
       //状态
       $status = !empty($_GET['status'])? intval($_GET['status']) : '';

       $filter = $this->_get_query_conditions(array(
            array(
                'field' => 'store_name',
                'equal' => 'like',
            ),
            array(
                'field' => 'sgrade',
            ),
        ));
        $store_name = trim($_GET['store_name']);
        if ($store_name)
        {

            $filter .= " AND store_name LIKE '%{$store_name}%' ";
        }
        if($status){
            $filter .= " AND status={$status}";
        }
       $store_mod =& m('store');
               //更新排序
      if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
                $sort  = 'add_time';
                $order = 'asc';
            }
        }
        else
        {
            $sort  = 'add_time';
            $order = 'asc';
        }
       $this->assign('filter', $filter);
       $conditions .= $filter;
       $store_dispatching = $store_dispatching_mod->find(array(
                'conditions' => $conditions,
                'limit'      => $page['limit'],
                'order'      => "$sort $order",
                'count'      => true,
        ));
       $status = array(0=>'未处理',1=>'完成',2=>'处理中',3=>'拒绝');
       $apply_date = array(1=>'下午',0=>'上午');
       foreach($store_dispatching as $k=>$v){
            $store_dispatching[$k]['status_name'] = $status[$v['status']];
            $store_dispatching[$k]['apply_date'] =  $apply_date[$v['apply_date']];
       }
       $this->assign('store_dispatching',$store_dispatching);


        $page['item_count'] = $store_dispatching_mod->getCount();
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->_format_page($page);
        $this->assign('filtered', $filter? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        $this->display("store_dispatching.index.html");

    }

    function edit_store_dispatching(){
        $id = empty($_GET['id']) ? 0 : $_GET['id'];
        $status = empty($_GET['status']) ? 1 : $_GET['status'];
        if(!$id){
            $this->show_warning('此申请不存在');
            return;
        }
        $store_dispatching_mod =& m('store_dispatching');
        $store_dispatching_mod->edit('id='.$id,array('status'=>$status));
        $this->show_message('edit_ok',
                'back_list',    'index.php?app=store&act=store_dispatching'
            );

    }


    function test()
    {
        if (!IS_POST)
        {
            $sgrade_mod =& m('sgrade');
            $grades = $sgrade_mod->find();
            if (!$grades)
            {
                $this->show_warning('set_grade_first');
                return;
            }
            $this->display('store.test.html');
        }
        else
        {
            $user_name = trim($_POST['user_name']);
            $password  = $_POST['password'];

            /* 连接到用户系统 */
            $ms =& ms();
            $user = $ms->user->get($user_name, true);
            if (empty($user))
            {
                $this->show_warning('user_not_exist');
                return;
            }
            if ($_POST['need_password'] && !$ms->user->auth($user_name, $password))
            {
                $this->show_warning('invalid_password');

                return;
            }

            $store = $this->_store_mod->get_info($user['user_id']);
            if ($store)
            {
                if ($store['state'] == STORE_APPLYING)
                {
                    $this->show_warning('user_has_application');
                    return;
                }
                else
                {
                    $this->show_warning('user_has_store');
                    return;
                }
            }
            else
            {
                header("Location:index.php?app=store&act=add&user_id=" . $user['user_id']);
            }
        }
    }

    function add()
    {
    	//ns add 1是默认的default|default，2是钻石店面 diamonds|default，3是皇冠店crown|default
    	$style= array(1=>'default|default',2=>'diamonds|default',3=>'crown|default');
        $user_id = $_GET['user_id'];
        if (!$user_id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        if (!IS_POST)
        {
            /* 取得会员信息 */
            $user_mod =& m('member');
            $user = $user_mod->get_info($user_id);
            $this->assign('user', $user);

            $this->assign('store', array('state' => STORE_OPEN, 'recommended' => 0, 'sort_order' => 65535, 'end_time' => 0));

            $sgrade_mod =& m('sgrade');
            $this->assign('sgrades', $sgrade_mod->get_options());

            $this->assign('states', array(
                STORE_OPEN   => Lang::get('open'),
                STORE_CLOSED => Lang::get('close'),
            ));

            $this->assign('recommended_options', array(
                '1' => Lang::get('yes'),
                '0' => Lang::get('no'),
            ));

            $this->assign('scategories', $this->_get_scategory_options());

            $region_mod =& m('region');
            $this->assign('regions', $region_mod->get_options(0));

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js'
            ));
            $this->assign('enabled_subdomain', ENABLED_SUBDOMAIN);
            $this->display('store.form.html');
        }
        else
        {
            /* 检查名称是否已存在 */
            if (!$this->_store_mod->unique(trim($_POST['store_name'])))
            {
                $this->show_warning('name_exist');
                return;
            }
            $domain = empty($_POST['domain']) ? '' : trim($_POST['domain']);
            if (!$this->_store_mod->check_domain($domain, Conf::get('subdomain_reserved'), Conf::get('subdomain_length')))
            {
                $this->show_warning($this->_store_mod->get_error());

                return;
            }
            $data = array(
                'store_id'     => $user_id,
                'store_name'   => $_POST['store_name'],
                'owner_name'   => $_POST['owner_name'],
                'owner_card'   => $_POST['owner_card'],
                'region_id'    => $_POST['region_id'],
                'region_name'  => $_POST['region_name'],
                'address'      => $_POST['address'],
                'zipcode'      => $_POST['zipcode'],
                'tel'          => $_POST['tel'],
                'sgrade'       => $_POST['sgrade'],
                //ns add 模版控制
            	'theme'       => $style[$_POST['sgrade']],
                'end_time'     => empty($_POST['end_time']) ? 0 : gmstr2time(trim($_POST['end_time'])),
                'state'        => $_POST['state'],
                'recommended'  => $_POST['recommended'],
                'sort_order'   => $_POST['sort_order'],
                'add_time'     => gmtime(),
                'domain'       => $domain,
            );
            $certs = array();
            isset($_POST['autonym']) && $certs[] = 'autonym';
            isset($_POST['material']) && $certs[] = 'material';
            $data['certification'] = join(',', $certs);

            if ($this->_store_mod->add($data) === false)
            {
                $this->show_warning($this->_store_mod->get_error());
                return false;
            }

            $this->_store_mod->unlinkRelation('has_scategory', $user_id);
            $cate_id = intval($_POST['cate_id']);
            if ($cate_id > 0)
            {
                $this->_store_mod->createRelation('has_scategory', $user_id, $cate_id);
            }

            $this->show_message('add_ok',
                'back_list',    'index.php?app=store',
                'continue_add', 'index.php?app=store&amp;act=test'
            );
        }
    }

    function edit()
    {
        $style= array(1=>'default|default',2=>'default|default',3=>'default|default');
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $store = $this->_store_mod->get_info($id);
            if (!$store)
            {
                $this->show_warning('store_empty');
                return;
            }
            if ($store['certification'])
            {
                $certs = explode(',', $store['certification']);
                foreach ($certs as $cert)
                {
                    $store['cert_' . $cert] = 1;
                }
            }

            $this->assign('store', $store);

            $sgrade_mod =& m('sgrade');
            $this->assign('sgrades', $sgrade_mod->get_options());

            $this->assign('states', array(
                STORE_OPEN   => Lang::get('open'),
                STORE_CLOSED => Lang::get('close'),
            ));

            $this->assign('recommended_options', array(
                '1' => Lang::get('yes'),
                '0' => Lang::get('no'),
            ));

            $region_mod =& m('region');
            $this->assign('regions', $region_mod->get_options(0));

            $this->assign('scategories', $this->_get_scategory_options());
            //$this->assign('markets', $this->_get_markets_options());

            $scates = $this->_store_mod->getRelatedData('has_scategory', $id);
            $this->assign('scates', array_values($scates));

             //获取专属品牌信息
             $brand = $this->_brand_mod->get_info($store['brand_id']);
             $this->assign('brand_name', $brand['brand_name']);

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js,mlselection.js'
            ));
            $this->assign('build_editor', $this->_build_editor(array(
            		'name' => 'content',
            		'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            $this->assign('enabled_subdomain', ENABLED_SUBDOMAIN);
            $this->display('store.form.html');
        }
        else
        {
            /* 检查名称是否已存在 */
            if (!$this->_store_mod->unique(trim($_POST['store_name']), $id))
            {
                $this->show_warning('name_exist');
                return;
            }
            $store_info = $this->_store_mod->get_info($id);
            $domain = empty($_POST['domain']) ? '' : trim($_POST['domain']);
            if ($domain && $domain != $store_info['domain'])
            {
                if (!$this->_store_mod->check_domain($domain, Conf::get('subdomain_reserved'), Conf::get('subdomain_length')))
                {
                    $this->show_warning($this->_store_mod->get_error());

                    return;
                }
            }
			//

            $data = array(
                'store_name'   => $_POST['store_name'],
                'owner_name'   => $_POST['owner_name'],
                'owner_card'   => $_POST['owner_card'],
                'region_id'    => $_POST['region_id'],
                'market_id'    => $_POST['market_id'],
                'region_name'  => $_POST['region_name'],
                'address'      => $_POST['address'],
                'zipcode'      => $_POST['zipcode'],
                'tel'          => $_POST['tel'],
                'sgrade'       => $_POST['sgrade'],
            	'store_logo'   => $_POST['store_logo'],
            	'store_logo2'   => $_POST['store_logo2'],
            	'store_banner' => $_POST['store_banner'],
            	'small_banner' => $_POST['small_banner'],
            	'description'  => $_POST['description'],
            	//ns add 模版控制
            	'theme'       => $style[$_POST['sgrade']],
                'end_time'     => empty($_POST['end_time']) ? 0 : gmstr2time(trim($_POST['end_time'])),
                'state'        => $_POST['state'],
                'sort_order'   => $_POST['sort_order'],
                'recommended'  => $_POST['recommended'],
                'domain'       => $domain,
            );
            $data['state'] == STORE_CLOSED && $data['close_reason'] = $_POST['close_reason'];
            $certs = array();
            isset($_POST['autonym']) && $certs[] = 'autonym';
            isset($_POST['material']) && $certs[] = 'material';
            $data['certification'] = join(',', $certs);

            $old_info = $this->_store_mod->get_info($id); // 修改前的店铺信息
            $this->_store_mod->edit($id, $data);

            $this->_store_mod->unlinkRelation('has_scategory', $id);
            $cate_id = intval($_POST['cate_id']);
            if ($cate_id > 0)
            {
                $this->_store_mod->createRelation('has_scategory', $id, $cate_id);
            }

            /* 如果修改了店铺状态，通知店主 */
            if ($old_info['state'] != $data['state'])
            {
                $ms =& ms();
                if ($data['state'] == STORE_CLOSED)
                {
                    // 关闭店铺
                    $subject = Lang::get('close_store_notice');
                    //$content = sprintf(Lang::get(), $data['close_reason']);
                    $content = get_msg('toseller_store_closed_notify',array('reason' => $data['close_reason']));
                }
                else
                {
                    // 开启店铺
                    $subject = Lang::get('open_store_notice');
                    $content = Lang::get('toseller_store_opened_notify');
                }
                $ms->pm->send(MSG_SYSTEM, $old_info['store_id'], '', $content);
                $this->_mailto($old_info['email'], $subject, $content);
            }

            $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
            $this->show_message('edit_ok',
                'back_list',    'index.php?app=store&page=' . $ret_page,
                'edit_again',   'index.php?app=store&amp;act=edit&amp;id=' . $id
            );
        }
    }

    //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();
       if (in_array($column ,array('recommended','sort_order')))
       {
           $data[$column] = $value;
           $this->_store_mod->edit($id, $data);
           if(!$this->_store_mod->has_error())
           {
               echo ecm_json_encode(true);
           }
       }
       else
       {
           return ;
       }
       return ;
   }

    function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_store_to_drop');
            return;
        }

        $ids = explode(',', $id);
        foreach ($ids as $id)
        {
            $this->_drop_store_image($id); // 注意这里要先删除图片，再删除店铺，因为删除图片时要查店铺信息
            $this->_userfollow_mod->drop('follow_uid = '.$id);
        }
        if (!$this->_store_mod->drop($ids))
        {
            $this->show_warning($this->_store_mod->get_error());
            return;
        }

        /* 通知店主 */
        $user_mod =& m('member');
        $users = $user_mod->find(array(
            'conditions' => "user_id" . db_create_in($ids),
            'fields'     => 'user_id, user_name, email',
        ));
        foreach ($users as $user)
        {
            $ms =& ms();
            $subject = Lang::get('drop_store_notice');
            $content = get_msg('toseller_store_droped_notify');
            $ms->pm->send(MSG_SYSTEM, $user['user_id'], $subject, $content);
            $this->_mailto($user['email'], $subject, $content);
            //修改会员信息
            $user_mod->edit($user['user_id'],array('serve_type'=>0));
            $this->_applylog_mod->drop('user_id = '.$user['user_id'].'  AND apply_type = 1');
        }

        $this->show_message('drop_ok');
    }

    /* 更新排序 */
    function update_order()
    {
        if (empty($_GET['id']))
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        $ids = explode(',', $_GET['id']);
        $sort_orders = explode(',', $_GET['sort_order']);
        foreach ($ids as $key => $id)
        {
            $this->_store_mod->edit($id, array('sort_order' => $sort_orders[$key]));
        }

        $this->show_message('update_order_ok');
    }

    /* 查看并处理店铺申请 */
    function view()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $store = $this->_store_mod->get_info($id);
            if (!$store)
            {
                $this->show_warning('Hacking Attempt');
                return;
            }

            $sgrade_mod =& m('sgrade');
            $sgrades = $sgrade_mod->get_options();
            $store['sgrade'] = $sgrades[$store['sgrade']];

            //获取属性
            $store_attr =& m('store_attr');
            $attr_list = $store_attr->find(array('conditions'=>'store_id='.$id));
            //type_id 1、服务2、风格
            foreach($attr_list as $val){
                if($val['type_id'] == 1){
                    $store['attr']['service'] .= $val['attr_name'].'、';
                }elseif($val['type_id'] == 2){
                    $store['attr']['style'] .= $val['attr_name'].'、';
                }
            }

            $this->assign('store', $store);

             //获取专属品牌信息
             $brand = $this->_brand_mod->get_info($store['brand_id']);
             $this->assign('brand_name', $brand['brand_name']);
            //获取店铺所属品牌

            $scates = $this->_store_mod->getRelatedData('has_scategory', $id);
            $this->assign('scates', $scates);
            $this->display('store.view.html');
        }
        else
        {
            $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
            /* 批准 */
            if (isset($_POST['agree']))
            {

                $this->_store_mod->edit($id, array(
                    'state'      => STORE_OPEN,
                    'add_time'   => gmtime(),
                    'sort_order' => 65535,
                ));

                $content = get_msg('toseller_store_passed_notify');
                $ms =& ms();
                $ms->pm->send(MSG_SYSTEM, $id, '', $content);
                $store_info = $this->_store_mod->get_info($id);
                $this->send_feed('store_created', array(
                    'user_id'   =>  $store_info['store_id'],
                    'user_name'   => $store_info['user_name'],
                    'store_url'   => SITE_URL . '/' . url('app=store&id=' . $store_info['store_id']),
                    'seller_name'   => $store_info['store_name'],
                ));
                $this->_hook('after_opening', array('user_id' => $id));
                //修改会员信息--成为创业者后，会有基本的信用积分，
				$model_setting = &af('settings');
			    $setting = $model_setting->getAll(); // 载入系统设置数据
				
                $_member_mod=m('member');
                $_member_mod->edit($store_info['store_id'],array('serve_type'=>1, 'point'=>$setting['initial']));
				
				//成为创业者，执行对应的推荐安装奖励，存入余额
				$member_invite = m("memberinvite");
				$m_invite = $member_invite->get("invitee=".$id);
				//如果奖励开启
				if($m_invite['money']) {
					$member    = $_member_mod->get($m_invite['inviter']);
					//$editMoney = $member['money'] + $m_invite['money'];
				
					//修改邀请者余额
					//$_member_mod->edit($m_invite['inviter'], array('money' => $editMoney));

					//记录到money变化日志表并修改用户余额
					$Mmoney = &m("membermoney");
					$msg    = '用户推荐用户安装获得金额奖励！';
					$Mmoney->change_money($member['user_id'], $m_invite['money'], 3 , $member['user_id'], $msg , $msg );//类型3：推荐奖励
				}

                //添加日志
                $this->_applylog_mod->edit('user_id = '.$id.' AND status = 0 AND apply_type = 1', array(
                    'status'      => '1',
                    'mark' => trim($_POST['reject_reason']),
                ));
                $this->show_message('agree_ok','back_list', 'index.php?app=store');
                // $this->show_message('agree_ok',
                //     'edit_the_store', 'index.php?app=store&amp;act=edit&amp;id=' . $id,
                //     'back_list', 'index.php?app=store&wait_verify=1&page=' . $ret_page
                // );
            }
            /* 拒绝 */
            elseif (isset($_POST['reject']))
            {

                $reject_reason = trim($_POST['reject_reason']);
                if (!$reject_reason)
                {
                    $this->show_warning('input_reason');
                    return;
                }

                $content = get_msg('toseller_store_refused_notify', array('reason' => $reject_reason));
                $ms =& ms();

                $ms->pm->send(MSG_SYSTEM, $id, '', $content);

                $this->_drop_store_image($id); // 注意这里要先删除图片，再删除店铺，因为删除图片时要查店铺信息
                $this->_store_mod->drop($id);

                //添加日志
                $this->_applylog_mod->edit('user_id = '.$id.' AND status = 0 AND apply_type = 1', array(
                    'status'      => '2',
                    'mark' => trim($_POST['reject_reason']),
                ));

                //修改品牌信息
                //$this->_brand_mod->edit('brand_id='.$_POST['brand_id'],'store_id=0');
                $this->store_attr_mod->drop('store_id='.$id);
                $this->show_message('reject_ok','back_list', 'index.php?app=store');
            }
            else
            {
                $this->show_warning('Hacking Attempt');
                return;
            }
        }
    }

    function batch_edit()
    {
        if (!IS_POST)
        {
            $sgrade_mod =& m('sgrade');
            $this->assign('sgrades', $sgrade_mod->get_options());

            $region_mod =& m('region');
            $this->assign('regions', $region_mod->get_options(0));

            $this->headtag('<script type="text/javascript" src="{lib file=mlselection.js}"></script>');
            $this->display('store.batch.html');
        }
        else
        {
            $id = isset($_POST['id']) ? trim($_POST['id']) : '';
            if (!$id)
            {
                $this->show_warning('Hacking Attempt');
                return;
            }

            $ids = explode(',', $id);
            $data = array();
            if ($_POST['region_id'] > 0)
            {
                $data['region_id'] = $_POST['region_id'];
                $data['region_name'] = $_POST['region_name'];
            }
            if ($_POST['sgrade'] > 0)
            {
                $data['sgrade'] = $_POST['sgrade'];
            }
            if ($_POST['certification'])
            {
                $certs = array();
                if ($_POST['autonym'])
                {
                    $certs[] = 'autonym';
                }
                if ($_POST['material'])
                {
                    $certs[] = 'material';
                }
                $data['certification'] = join(',', $certs);
            }
            if ($_POST['recommended'] > -1)
            {
                $data['recommended'] = $_POST['recommended'];
            }
            if (trim($_POST['sort_order']))
            {
                $data['sort_order'] = intval(trim($_POST['sort_order']));
            }

            if (empty($data))
            {
                $this->show_warning('no_change_set');
                return;
            }

            $this->_store_mod->edit($ids, $data);
            $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
            $this->show_message('edit_ok',
                'back_list', 'index.php?app=store&page=' . $ret_page);
        }
    }

    function check_name()
    {
        $id         = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $store_name = empty($_GET['store_name']) ? '' : trim($_GET['store_name']);

        if (!$this->_store_mod->unique($store_name, $id))
        {
            echo ecm_json_encode(false);
            return;
        }
        echo ecm_json_encode(true);
    }

    /* 删除店铺相关图片 */
    function _drop_store_image($store_id)
    {
        $files = array();

        /* 申请店铺时上传的图片 */
        $store = $this->_store_mod->get_info($store_id);
        for ($i = 1; $i <= 3; $i++)
        {
            if ($store['image_' . $i])
            {
                $files[] = $store['image_' . $i];
            }
        }

        /* 店铺设置中的图片 */
        if ($store['store_banner'])
        {
            $files[] = $store['store_banner'];
        }
        if ($store['store_logo'])
        {
            $files[] = $store['store_logo'];
        }

        /* 删除 */
        foreach ($files as $file)
        {
            $filename = ROOT_PATH . '/' . $file;
            if (file_exists($filename))
            {
                @unlink($filename);
            }
        }
    }

    /* 取得店铺分类 */
    function _get_scategory_options()
    {
        $mod =& m('scategory');
        $scategories = $mod->get_list();
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($scategories, 'cate_id', 'parent_id', 'cate_name');

        return $tree->getOptions();
    }

}

?>
