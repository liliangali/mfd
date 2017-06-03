<?php
class My_order_serveApp extends MemberbaseApp
{
	function __construct()
    {
    	parent::__construct();
    	/* 当前用户中心菜单 */
        $this->_curitem('my_order_serve');
        $this->_curmenu('my_order_serve');
        $this->_subscribe=m('subscribe');
        $this->user_id = $this->visitor->get('user_id');
        $this->idserve = $this->visitor->get('idserve');
        $this->serve_type = $this->visitor->get('serve_type');

        $this->assign('title', 'RCTAILOR-酷客中心-我的消费者');
    }
    function index()
    {
    	$conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'user_name',
                'name'  => 'user_name',
                'equal' => 'like',
            ),
        ));
    	//var_dump($conditions);

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

        $page = $this->_get_page();

		//var_dump($this->serve_type);
        if($this->serve_type=='3')
        {//加盟商
        	$member_mod=m('member');

        	$subscribes = $member_mod->find(array(
	        'fields'=>'member.user_name,member.email,member.phone_mob,member.nickname,member.last_login,member.reg_time',
	        	'conditions' => ' member.parent_id ='.$this->user_id . $conditions,
	        	'limit' => $page['limit'],
	            'order' => "user_id desc",
	            'count' => true,
	        ));


        }else
        {
        	$subscribes = $this->_subscribe->find(array(
	        //'fields'=>'this.*,member.user_name,serve.serve_name',
	        'fields'=>'member.user_name,member.email,member.phone_mob,member.nickname,member.last_login,member.reg_time',
	        	'conditions' => ' subscribe.idserve ='.$this->idserve . $conditions.' group by subscribe.userid ',
	        'join' => 'has_member,has_serve',
	        	'limit' => $page['limit'],
	            'order' => "$sort $order",
	            'count' => true,
	        ));
        }

        $this->assign('subscribes', $subscribes);
        $page['item_count'] = $this->_subscribe->getCount();
        $this->_format_page($page);

        $this->assign('page_info', $page);

        $this->assign('query_fields', array(
            'idsubscribe' => LANG::get('idsubscribe')
        ));
        $this->assign('sort_options', array(
            'create_date DESC'   => LANG::get('create_date'),
            'idsubscribe DESC' => LANG::get('idsubscribe')
        ));

        $this->assign('state', array(
	            '0'   => LANG::get('state_0'),
        		'3' => LANG::get('state_3'),
	            '1' => LANG::get('state_1'),
        		'2' => LANG::get('state_2'),
        '4'   => LANG::get('state_4'),
        '5'   => LANG::get('state_5'),
        	));







        $this->display('my_order_serve.index.html');
    }
	function _get_member_submenu()
    {
        $menus = array(
            array(
                'name'  => 'my_order_serve',
            ),
        );
        return $menus;
    }
}