<?php

class CoinconfigApp extends BackendApp
{
	var $coin_mod;
    function __construct()
    {
        parent::__construct();
        $this->coin_mod =& m('cotcoin');
    }

    function index()
    {
      $page = $this->_get_page(10);
      $list = $this->coin_mod->find(array(
            'conditions' => '1=1',
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));
        $this->assign('list', $list);
        $page['item_count'] = $this->coin_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        
        $this->display('coin/index.html');
    }
    
    function add() 
    {
        if(!IS_POST)
        {
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->display('coin/edit.html');
        }
        else
        {
            $data['facevalue'] = intval($_POST['facevalue']);
            $data['price'] = $_POST['price'];
            $data['integral'] = intval($_POST['integral']);
            $data['is_sale'] = intval($_POST['is_sale']);
            $data['add_time'] = time();
            if(!$this->coin_mod->add($data))
            {
                $this->show_warning('添加失败');
            }
            $this->show_message('添加成功','返回列表',    'index.php?app=coinconfig');
        }
        
    }
    
    function edit()
    {
        $id = intval($_REQUEST['id']);
        if(!IS_POST)
        {
            $info = $this->coin_mod->get_info($id);
            if (!$info) 
            {
                $this->show_warning('无此数据');
                return;
            }
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('info',$info);
            $this->display('coin/edit.html');
        }
        else
        {
            $data['facevalue'] = intval($_POST['facevalue']);
            $data['price'] = $_POST['price'];
            $data['integral'] = intval($_POST['integral']);
            $data['is_sale'] = intval($_POST['is_sale']);
            $this->coin_mod->edit($id,$data);
            $this->show_message('编辑成功','返回列表',    'index.php?app=coinconfig');
        }
    
    }
    
    function drop(){
        $id = intval($_REQUEST['id']);
        $ordergoods_mod = &m("ordergoods");
        $result = $ordergoods_mod->find(array(
            "conditions" => "order_goods.goods_id = '{$id}' AND order_alias.extension = 'coin'",
            "join"       => "belongs_to_order",
            'fields'     => "order_alias.extension",
            'count'      => true,
        ));
        $count = $ordergoods_mod->getCount();
        if($count > 1){
            $this->show_message("该麦富迪币下存在订单无法删除！");
            return;
        }else{
            $this->coin_mod->drop($id);
            $this->show_message("麦富迪币删除成功！");
            return;
        }
    }
    
    function loadrecord(){
        $page = $this->_get_page(10);
        $id = intval($_REQUEST['id']);
        $ordergoods_mod = &m("ordergoods");
        $list = $ordergoods_mod->find(array(
            "conditions" => "order_goods.goods_id = '{$id}' AND order_alias.extension = 'coin'",
            "join"       => "belongs_to_order",
            'limit'      => $page['limit'],
            'order'      => "order_id DESC",
            'fields'     => "order_alias.order_id,order_alias.status, order_alias.order_sn, order_alias.order_amount, order_alias.user_id, order_alias.user_name, add_time",
            'count'      => true,
        ));
        
        $this->assign('list', $list);
        
        $page['item_count'] = $ordergoods_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        
        $this->display('coin/record.html');
    }
}

?>
