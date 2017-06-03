<?php

class Figure_commentsApp extends BackendApp
{
    private  $_comment_mod;
    private $status = array(
        0 => '未操作',
        1 => '通过',
        2 => '拒绝',
    );

    function __construct()
    {
        parent::__construct();
        $this->_comment_mod = &m("figure_comment");

    }

    function index()
    {
        $conditions = '1=1';
        $status = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : 'all';


        //更新排序
        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order, array('asc', 'desc'))) {
                $sort = 'id';
                $order = 'desc';
            }
        } else {
            $sort = 'id';
            $order = 'desc';
        }



        if($status !='all'){
            $conditions .=" AND status = '{$status}'";
        }


        $page = $this->_get_page(30);
        $list = $this->_comment_mod->find(array(
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",
        ));
        $page['item_count'] = $this->_comment_mod->getCount();
        $this->_format_page($page);

        $this->assign('page_info', $page);
        $this->assign('status', $status);
        $this->assign('list', $list);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('comments/figure_comments.index.html');
    }

    function del(){
        $id = $_REQUEST['id'];
        if(!intval($id)){
            show_warning('ID is null');
            return;
        }else{
            $this->_comment_mod->drop($id);
            show_warning('成功删除');
            return;
        }
    }



    /**
     *
     * 操作审核
     * status  :未审核0  通过 1     拒绝2
     */
    function update(){
        $id = intval($_REQUEST['id']);
        $status = intval($_REQUEST['status']);
        if(!$id){
            $this->show_warning('ID is null');
            return;
        }else{
            $this->_comment_mod->edit($id,array('status'=> $status));
            $this->show_message("操作成功",
                '返回评论列表',    'index.php?app=figure_comments'
            );
            return;
        }
    }

}

?>
