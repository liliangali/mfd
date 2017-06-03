<?php

class Detail_commentsApp extends BackendApp
{
    private  $_comment_mod;
    private  $_answer_mod;
    private  $_member_mod;
    private $status = array(
        0 => '未操作',
        1 => '通过',
        2 => '拒绝',
    );

    function __construct()
    {
        parent::__construct();
        $this->_comment_mod = &m("detail_comment");
        $this->_answer_mod = &m('detail_answer');
        $this->_member_mod = &m('member');
    }

    function index()
    {
        $conditions = '1=1';
        $status = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : 'all';

        //按照订单查询
        $order_id = trim($_REQUEST['order_id']);
        //按照商品id查询
        $comment_id = trim($_REQUEST['comment_id']);
        //cate
        $cate = isset($_REQUEST['cate']) ? trim($_REQUEST['cate']) : 'all';

//echo '<pre>';var_dump($order_id);die;


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



        if($cate !='all'){
            $conditions .=" AND cate = '{$cate}'";
        }

        if($order_id){
            $conditions .=" AND order_id = '{$order_id}'";
        }
        if($comment_id){
            $conditions .=" AND comment_id = '{$comment_id}'";
        }
        if($status != 'all'){
            $conditions .= " AND status = '{$status}'";
        }

        $page = $this->_get_page(30);
        $list = $this->_comment_mod->find(array(
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",
        ));
        foreach ($list as $k=>$v){
            $user =  $this->_member_mod->get($v['member_id']);
            $list[$k]['user_name'] = $user['user_name'];
        }
        $page['item_count'] = $this->_comment_mod->getCount();
        $this->_format_page($page);

        $this->assign('page_info', $page);
        $this->assign('status', $status);
        $this->assign('cate', $cate);
        $this->assign('order_id', $order_id);
        $this->assign('comment_id', $comment_id);
        $this->assign('list', $list);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
//echo '<pre>';var_dump($list);die;
        $this->display('comments/detail_comments.index.html');
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
     * 详情页  包含 回复
     * status  :未审核0  通过 1     拒绝2
     */
    function info(){
        $id = intval($_REQUEST['id']);
        if($id <=0){
            show_message("非法操作!");
            return;
        }
        if (!IS_POST) {

        $list = $this->_comment_mod->get("id='{$id}'");
        $reply =$res = $this->_answer_mod->find(array('conditions'=>"comment_id='{$id}'"));

        $this->assign('comment', $list);
        $this->assign('reply', $reply);
        $this->assign('status_options', $this->status);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('comments/detail_comment.info.html');
        }else{
            $member_id = intval($_REQUEST['member_id']);
            $reply = htmlspecialchars($_REQUEST['reply']);
            $status = intval($_REQUEST['status']);

            //detail_comments 表审核状态
            $res = $this->_comment_mod->edit($id, array('status'=>$status));
            if ($this->_comment_mod->has_error()) {
                $this->show_warning($this->_comment_mod->get_error());
                return;
            }

            if($reply !=''){
                $data = array(
                    'gid'			      => 1,//默认管理员id
                    'member_id'          => $member_id,
                    'comment_id'	      => $id,
                    'content'            => htmlspecialchars($reply),
                    'addtime'            => gmtime(),
                    'status'             => 0, //还没有这块审核
                );

                $res = $this->_answer_mod->add($data);
            }

            if($res){
                $this->show_message("操作成功",
                    '返回评论列表',    'index.php?app=detail_comments'
                );
            }

        }
    }


}

?>
