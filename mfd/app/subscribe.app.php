<?php


class SubscribeApp extends BackendApp
{
	var $_subscribe;
    function __construct()
    {
    	parent::__construct();
        $this->_subscribe=m('subscribe');
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
             $sort  = 'idsubscribe';
             $order = 'desc';
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
                $sort  = 'idsubscribe';
                $order = 'desc';
            }
        }
        
        $page = $this->_get_page(20);
        $subscribes = $this->_subscribe->find(array(
        	'fields'=>'this.*,member.user_name,serve.serve_name',
        	'conditions' => '1=1' . $conditions,   
        	'join' => 'has_member,has_serve', 
        	'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
		
        //var_dump($subscribes);exit;
        
        $this->assign('subscribes', $subscribes);
        $page['item_count'] = $this->_subscribe->getCount();
        $this->_format_page($page);

        $this->assign('page_info', $page);
        
        $this->assign('query_fields', array(
            'subscribe.address'     => LANG::get('address'),
            'subscribe.mobile' => LANG::get('mobile'),
        	'subscribe.username' => LANG::get('username'),
        	'member.user_name' => '注册用户名称',
        ));
        $this->assign('sort_options', array(
            'create_date DESC'   => LANG::get('create_date'),
            'idsubscribe DESC' => LANG::get('idsubscribe'),
        ));
        $this->display('subscribe.index.html');
    }
	function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_user_to_drop');
            return;
        }
        

        $ids = explode(',', $id);

        
        if (!$this->_subscribe->drop($ids))
        {
            $this->show_warning($this->_subscribe->get_error());

            return;
        }

        $this->show_message('drop_ok');
    }
    
	function edit()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $subscribe = $this->_subscribe->get_info($id);
            if (!$subscribe)
            {
                $this->show_warning('user_empty');
                return;
            }

            
            $this->assign('subscribe', $subscribe);

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('state', array(
	            '0'   => LANG::get('state_0'),
            '4'   => LANG::get('state_4'),
            	'3'   => LANG::get('state_3'),
	            '1' => LANG::get('state_1'),
            '5'   => LANG::get('state_5'),
	            '2'     => LANG::get('state_2'),
        	));
        	
        	$this->assign('figure_mode', array(
	            '1'   => LANG::get('figure_mode_1'),
	            '2' => LANG::get('figure_mode_2'),
        	));
        	
        	$this->assign('timeopt', array(
	            '1'   => '上午',
	            '2' => '下午',
        	));
        	
        	$this->assign('acc',LANG::get('edit'));
            $this->display('subscribe.form.html');
        }
        else
        {
            $data = array(
                'state' => $_POST['state'],
            	'employee_name' => $_POST['employee_name'],
            );
            /* 修改本地数据 */
            $this->_subscribe->edit($id, $data);
            $this->show_message('修改成功',
                'back_list',    'index.php?app=subscribe',
                '再次编辑',   'index.php?app=subscribe&amp;act=edit&amp;id=' . $id
            );
        }
    }
	function add()
    {
        if (!IS_POST)
        {
            
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
			$this->assign('state', array(
	            '0'   => LANG::get('state_0'),
	            '1' => LANG::get('state_1'),
	            '2'     => LANG::get('state_2'),
        	));
        	$this->assign('acc',LANG::get('add'));
        	$subscribe['rc_code']=getRandOnlyId();
        	$this->assign('subscribe',$subscribe);
        	
            $this->display('subscribe.form.html');
        }
        else
        {

            $email     = trim($_POST['email']);

            if (!is_email($email))
            {
                $this->show_warning('email_error');

                return;
            }

            $data = array(
                'company_name' => $_POST['company_name'],
                'email'    => $_POST['email'],
                'linkman'     => $_POST['linkman'],
                'enterprise_url'    => $_POST['enterprise_url'],
				'mobile'    => $_POST['mobile'],
            	'post'    => $_POST['post'],
            	'state'    => $_POST['state'],
            	'reason'    => $_POST['reason'],
            	'rc_code'    => $_POST['rc_code'],
            );
            $idsubscribe=$this->_subscribe->add($data);
            if (!$idsubscribe)
            {
                $this->show_warning($this->_subscribe->get_error());
                return;
            }
            
            $this->_service=m('subscribedetail');
	        $this->_service->add(array('idsubscribe'=>$idsubscribe));
            
            $this->show_message('添加成功',
                'back_list',    'index.php?app=subscribe',
                'continue_add', 'index.php?app=subscribe&amp;act=add'
            );
        }
    }
}

?>
