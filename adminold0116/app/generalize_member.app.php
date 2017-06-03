<?php
/**
 * 推广后台-用户管理
 *
 * @author    yusw  2015-07-31
 */
class Generalize_memberApp extends BackendApp
{
    private $_generalize_mod;
    private $_g_mod;
    private $_g_invite_mod;
    private $status = array(
        10=>'全部',
        0 => '离职',
        1 => '在职',
    );
    private $gender = array(
        0 => '男',
        1 => '女',
    );
    //角色 ->jiaose
    private $type = array(
        10=>'全部',
        0 => '实习',
        1 => '专员',
        2 => '主管',
        3 => '经理',
        4 => '高级经理',
    );

    function __construct()
    {
        parent::__construct();
        define("TYPE", "generalize/");
        $this->_g_mod = & m('generalize');
        $this->_generalize_mod =& m('generalize_member');
        $this->_g_invite_mod = & m('memberinvite');

    }

    /**
     * 首页
     *
     */
    function index()
    {
        $conditions = '1=1 ';
        $status = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : '10';
        $name = trim($_REQUEST['name']);
        $phone = trim($_REQUEST['phone']);
        $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '10';
        $ge = isset($_REQUEST['ge']) ? trim($_REQUEST['ge']) : '0';


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


        if($phone !=''){
            $conditions .=" AND phone like '%{$phone}%'";
        }
        if($type !='10'){
            $conditions .=" AND type = '{$type}'";
        }
        if($name !=''){
            $conditions .=" AND name like '%{$name}%'";
        }
        if($status != '10'){
            $conditions .= " AND status = '{$status}'";
        }
        if($ge != '0'){
            $conditions .= " AND g_id = '{$ge}'";
        }


        $page = $this->_get_page(30);
        $list = $this->_generalize_mod->find(array(
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",
        ));

        //注册的创业者数
        $inviters = $this->_g_invite_mod->find(db_create_in(i_array_column($list,'id'), 'inviter').' and type=1');
        $inviter_ids = array_count_values(i_array_column($inviters,'inviter'));//'用户id'=>个数

        //组织
        $generalize = $this->_g_mod->get_g();
        $generalize[0] = '全部';;

        $page['item_count'] = $this->_generalize_mod->getCount();
        $this->_format_page($page);

        $this->assign('page_info', $page);
        $this->assign('status', $status);
        $this->assign('_status', $this->status);
        $this->assign('type', $type);
        $this->assign('_type', $this->type);
        $this->assign('name', $name);
        $this->assign('phone', $phone);
        $this->assign('generalize', $generalize);
        $this->assign('ge', $ge);
        $this->assign('inviter_ids', $inviter_ids);
        $this->assign('list', $list);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display(TYPE.'index.html');
    }


    /**
     *
     * 删除
     *
     */
    function del(){
        return;
        $id = $_REQUEST['id'];
        if(!intval($id)){
            show_warning('ID is null');
            return;
        }

        $this->_generalize_mod->drop($id);
        show_warning('成功删除');
        return;

    }

    /*
     * 重置绑定
     * */
    function reset_bd(){
        $id = $_REQUEST['id'];
        if(!intval($id)){
            show_warning('ID不能为空');
            return;
        }

        $invite_lists = $this->_g_invite_mod->get('type=1 and inviter='.$id);
        $gw_info = $this->_generalize_mod->get("name='麦富迪顾问'");

        if($gw_info['id'] == $id){
            show_warning('您已经是麦富迪顾问，不能重置绑定');
            return;
        }

        if(empty($gw_info)){
            show_warning('没有公共账号麦富迪顾问，请及时添加');
            return;
        }
        $res = $this->_g_invite_mod->edit('type=1 and inviter='.$id,array('inviter'=>$gw_info['id']));

        if(!$res){
            show_warning('系统异常');
            return;
        }

        //添加日志记录
        $jb_log = & m('cy_reload_log');
        $data_log['user_id']  = $id;
        !empty($invite_lists) && $data_log['invitee'] = json_encode($invite_lists);//inviter是 清理 创业者和用户的关系  后台暂时不做处理
        $data_log['add_time'] = time();
        $data_log['type']      =3;                    //新增类型  代表重置绑定关系
        $jb_log->add($data_log);

        show_message('重置绑定成功');
    }


    /**
     * 详情页
     *
     */
    function info(){
       $id = intval($_REQUEST['id']);
        if($id <=0){
            show_message("id不能为空");
            return;
        }

        $status1 =$_REQUEST['status1'];
        $name1 = $_REQUEST['name1'];
        $jiaose1 =$_REQUEST['jiaose1'];
        $ge1 = $_REQUEST['ge1'];

        if (!IS_POST) {
            //组织
            $generalize = $this->_g_mod->get_g();
            $list = $this->_generalize_mod->get("id='{$id}'");
            array_shift($this->type);
            array_shift($this->status);

            $this->assign('list', $list);
            $this->assign('type', $this->type);
            $this->assign('status', $this->status);
            $this->assign('gender', $this->gender);
            $this->assign('generalize', $generalize);

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));

            $this->display(TYPE.'info.html');
        }else{
            $data = array(
                'g_id'=>$_POST['generalize'],
                'phone' => $_POST['phone'],
                'name' => $_POST['name'],
                'invite' => $_POST['invite'],
                'type' => $_POST['type'],
                'gender' => $_POST['gender'],
                'status' => $_POST['status'],
                'birthday' => $_POST['birthday'],
                'address' => $_POST['address'],
                'im_qq' => $_POST['im_qq'],
                'email' => $_POST['email'],
                'wechat' => $_POST['wechat'],
            );

            $res = $this->_generalize_mod->edit($id,$data);
            if($res){
                //&status=$status1&name=$name1&ge=$ge1&type=$jiaose1
                $this->show_message("编辑成功",
                    '返回列表',    "index.php?app=generalize_member"
                );
            }

        }
    }


    /**
     * 新增页面
     */
    function add()
    {
        $id = empty($_REQUEST['id']) ? 1 : intval($_REQUEST['id']);
        if (!IS_POST)
        {

            //组织
            $generalize = $this->_g_mod->get_g();
            array_shift($this->type);
            array_shift($this->status);

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));

            $this->assign('id', $id);
            $this->assign('generalize', $generalize);
            $this->assign('status', $this->status);
            $this->assign('type', $this->type);
            $this->assign('gender', $this->gender);
            $this->display(TYPE.'add.html');
        }
        else
        {
            $data = array(
                'name'  => $_POST['name'],
                'phone'  => $_POST['phone'],
                'g_id' => $_POST['generalize'], //传id
                'type' => $_POST['type'],
                'gender' => $_POST['gender'],
                'status' => $_POST['status'],
                'birthday' => $_POST['birthday'],
                'address' => $_POST['address'],
                'im_qq' => $_POST['im_qq'],
                'email' => $_POST['email'],
                'wechat' => $_POST['wechat'],
                'invite'=>$_POST['invite'], //12位db码
                'add_time'=>gmtime(),
            );


            /* 检查名称等等是否已存在 */
            $gm_p = $this->_generalize_mod->get(array(
                'conditions' => "phone='".$data['phone']."'",
            ));
            if (!empty($gm_p))
            {
                $this->show_warning('用户手机已经存在');
                return;
            }

            $gm_n = $this->_generalize_mod->get(array(
                'conditions' => "name='".$data['name']."'",
            ));

            if (!empty($gm_n))
            {
                $this->show_warning('用户用户名已经存在');
                return;
            }


            $gm_id = $this->_generalize_mod->add($data);
            if (!$gm_id)
            {
                $this->show_warning($this->_generalize_mod->get_error());
                return;
            }

            $this->show_message('添加成功',
                '返回列表页',    'index.php?app=generalize_member',
                '继续添加', 'index.php?app=generalize_member&amp;act=add'
            );
        }
    }



    /**
     * 检查推广员唯一性
     *
     */
    function  check_user()
    {
        $phone = empty($_GET['phone']) ? null : trim($_GET['phone']);
        $invite= empty($_GET['invite'])?null:trim($_GET['invite']);
        $name= empty($_GET['name'])?null:trim($_GET['name']);
        $id = $_REQUEST['id'];


        if (!$phone && !$invite && !$name)
        {
            echo ecm_json_encode(false);
            return ;
        }


        if($phone){
            $param = "phone = '{$phone}'";
        }

        if($invite){
            $param = "invite = '{$invite}'";
        }


        if($name){
            $param = "name = '{$name}'";
        }
        //编辑页面过来的
        if($id !=''){
            $param .=" and id !=".$id;
        }


        if ($this->_generalize_mod->get($param)) {
            echo ecm_json_encode(false);
            return;
        }

        echo ecm_json_encode(true);

    }



}

?>
