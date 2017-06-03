<?php
/**
 * 用户中心 我的评论
 */
class My_detailApp extends MemberbaseApp
{
	function __construct()
    {
    	parent::__construct();
    	
    	Lang::load(lang_file('subscribeinfo'));
    	
    	$this->_comment_mod =& m("detail_comment");
        $this->order_mod =& m('order');
        $this->order_goods_mod =& m('ordergoods');
    	$this->user_id = $this->visitor->get('user_id');
    }
    /**
     * 重写模板文件
     * @return void
     * @access public
     */
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
    }

    
    /**
     * 会员中心 我的评论
     * @return void
     * @access public
     */
    function index()
    {
        /* 获取评论信息列表 */
        $this->detail_list();
        
        
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_detail');
    
        /* 显示订单列表 */
        $this->display('my_detail.index.html');
    }
    function detail_list(){
        $page = $this->_get_page(10);
        $comment_list = $this->_comment_mod->find(array(
            'conditions' => 'member_id='.$this->user_id.' and order_id > 0',
            'limit' => $page["limit"],
            'count' => true,
            'order' => 'addtime DESC'
        ));
        //获取订单信息
        foreach($comment_list as $k=>$val){
            $order_add_time = $this->order_mod->get(array(
            'fields'        => "add_time",
            'conditions'    => "order_id=".$val['order_id']." AND user_id=" . $this->user_id,
            ));
            $comment_list[$k]['order_add_time'] = $order_add_time['add_time'];
            //获取订单商品信息
            $comment_list[$k]['goods'] = $this->order_goods_mod->get(array(
                "conditions" => "goods_id =".$val['comment_id']." and order_id=".$val['order_id'],
                'fields' => 'order_id,goods_id,goods_name,goods_image,subtotal,price',
            ));

        }
        $page['item_count'] = $this->_comment_mod->getCount();
        $this->assign("comment_list", $comment_list);
        $this->assign("page_info",$page);

    }
  
}