<?php

/* 会员评论控制器 */
class CommentsApp extends BackendApp
{
    var $_mod_comment;
    function __construct()
    {
        parent::__construct();
        $model = $_GET['model'];
        $_mod;
        switch($model){
            case "single":
                $_mod = "singlecomment";
                break;
            case "order":
                $_mod = 'ordercomment';
                break;
            default:
                $_mod = "singlecomment";
        }
        $this->_mod_comment =&m($_mod);
        $this->assign('model', $model);
    }

    function index()
    {
        $status = isset($_GET['status']) ? trim($_GET['status']) : 'all';

        $conditions = ' 1 =  1 ';

        if($status != 'all'){
            $conditions .= " AND status = '{$status}'";
        }

        $page = $this->_get_page(15);
        $list = $this->_mod_comment->find(array(
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => 'addtime DESC',
        ));
        $page['item_count'] = $this->_mod_comment->getCount();
        $this->_format_page($page);

        $this->assign('page_info', $page);

        $this->assign('status', $status);
        foreach ($list as $key => $val){
            $str = preg_replace("/\[em_(\d+)\]/", '<img src="../../static/expand/qqface/arclist/$1.gif">' ,$val['content']);
            $list[$key]['content'] = $str;
        }
        $this->assign('list', $list);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('comments.index.html');
    }
    function del(){
        $id = $_REQUEST['id'];
        if(!intval($id)){
            show_warning('ID is null');
            return;
        }else{
            $this->_mod_comment->drop($id);
            show_warning('成功删除');
            return;
        }
    }

    function deny(){
        $id = $_REQUEST['id'];
        if(!intval($id)){
            show_warning('ID is null');
            return;
        }else{
           
            $this->_mod_comment->edit($id,array('status'=> 2));
            show_warning('操作成功');
            return;
        }
    }

    function pass(){
        $id = $_REQUEST['id'];
        if(!intval($id)){
            show_warning('ID is null');
            return;
        }else{
            $this->_mod_comment->edit($id,array('status'=> 1));
            show_warning('操作成功');
            return;
        }
    }
    function view()
    {
        $id = intval($_GET['id']);
        if($id <=0){
            show_message("非法操作!");
            return;
        }

        $comments = $this->_mod_comments->find(array(
            'conditions' => "id='{$id}'",
        ));



        $row =  $comments ? current($comments) : array();

        $this->assign('comment', $row);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('comment.view.html');
    }

    function verify(){
        $id = intval($_GET["id"]);

        $row = $this->_mod_comments->get_info($id);


        $data = array();
        if(isset($_POST['finished'])){
            $data['status'] = COM_FINISHED;
        }
        $data['remark'] = $_POST['admin_remark'];

        if(isset($_POST["invalid"])){
            $data['status'] = COM_INVALID;
        }

        $this->_mod_comments->edit($id, $data);


        $this->show_message("操作成功",
            '返回评论列表',    'index.php?app=comments'
        );
    }
    /**
     * 之前删除评论后数目出错，程序改正常了，把数据梳理一遍
     * @author Ruesin
     */
    function upData(){
        $all = $this->_mod_comments->findAll();
        foreach ($all as $row){
            if($row['cate'] == 'personaldesign' || $row['cate'] == 'streetinfo' || $row['cate'] == 'jiepai_comment' || $row['cate'] == 'sheji_comment'){
                $userphoto[$row['comment_id']] +=1;
            }
            $user[$row['uid']] +=1;
        }
        $mUp =& m('userphoto');
        $mUs =& m('member');
        
        $db = &db();
        $csh = "update cf_userphoto set comment_num = '0'";
        if($db->query($csh)){
            echo "userphoto has clean! <br>";
        }
        
                
        foreach ($userphoto as $key => $val){
            if($key == 0)continue;
            if(!$mUp->edit($key,array('comment_num'=>$val))){
                echo 'up fl '.$key.'<br>';
            }else {
                echo 'up sc '.$key.'-'.$val.'<br>';
            }
        }
        echo '<hr>';
        $csh = "update cf_member set comment_num = '0'";
        if($db->query($csh)){
            echo "member has clean! <br>";
        }
        foreach ($user as $key => $val){
            if($key == 0)continue;
            if(!$mUs->edit($key,array('comment_num'=>$val))){
                echo 'us fl '.$key.'<br>';
            }else {
                echo 'us sc '.$key.'-'.$val.'<br>';
            }
        }
        
        print_exit($all);
    }
    

}

?>
