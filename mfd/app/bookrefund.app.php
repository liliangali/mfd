<?php
class BookrefundApp extends BackendApp
{
	var $_bookrefund_mod;
    function __construct()
    {
        parent::__construct();
        $this->_bookrefund_mod =& m('bookrefund');
    }

    function index()
    {
        $page = $this->_get_page();
        $list = $this->_bookrefund_mod->find(array(
            'join' => 'belongs_to_order',
            'fields' => 'this.*,order_alias.order_sn',
            'limit' => $page['limit'],
            'order' => "id DESC, status ASC",
            'count' => true,
        ));
		
        $this->assign('list', $list);
        $page['item_count'] = $this->_bookrefund_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('bookrefund.index.html');
    }
    
    function view(){
        $id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
        $info = $this->_bookrefund_mod->find(array(
            'join' => 'belongs_to_order',
            'fields' => 'this.*,order_alias.order_sn',
            'conditions' => "id='{$id}'",
        ));
        $info = current($info);
        if(empty($info)){
            $this->show_warning("非法操作~");
            return;
        }
        $this->assign("info", $info);
        $this->display('bookrefund.info.html');
    }
    
    function process(){
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        if(isset($_POST["finished"])){
            $this->_bookrefund_mod->edit($id, array("status" => 1));
        }
        
        $this->show_message("操作成功！");
    }
}

?>
