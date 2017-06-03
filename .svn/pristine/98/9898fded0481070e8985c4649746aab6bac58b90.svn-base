<?php
/**
 * 用户维权投诉管理控制器
 *
 * @author    yusw
 * @usage    none
 */
class ComplaintApp extends BackendApp
{
    var $_complaint_mod;
    var $_user_mod;
    var $status = array(
        0 => '未操作',
        1 => '通过',
        2 => '拒绝',
    );

    function __construct()
    {
        define("TYPE", "complaint/");
        $this->ComplaintApp();
    }

    function ComplaintApp()
    {
        parent::BackendApp();
        $this->_complaint_mod =& m('complaint');
        $this->_user_mod = & m('member');
    }

    /**
     * 投诉列表
     * @return void
     * @access public
     * @version 1.0.0 (2015-1-20)
     * @author yusw
     */
    function index()
    {
        $conditions = '';
        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'status',
                'equal' => '=',
                'assoc' => 'AND',
                'name' => 'status_id',
                'type' => 'string',
            ),
        ));

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

        $page = $this->_get_page(30);
        $complaints = $this->_complaint_mod->find(array(
            'conditions' => "1=1 " . $conditions,
            'limit' => $page['limit'],
            'order' => "$sort $order",

            'count' => true,
        ));

        //获取统计数据
        $page['item_count'] = $this->_complaint_mod->getCount();
        $this->_format_page($page);

        foreach ($complaints as $k => $v) {
            $complaints[$k]['from_nickname'] = $this->get_nick_name($v['from_id']);
            $complaints[$k]['to_nickname'] = $this->get_nick_name($v['to_id']);
        }

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
        ));

        isset($_GET['status_id']) && $this->assign('status_id', $_GET['status_id']);
        $this->assign('status_options', $this->status);
        $this->assign("page_info", $page);
        $this->assign('complaints', $complaints);
        $this->display(TYPE . 'complaint.index.html');
    }


    /**
     * 投诉处理
     * @return void
     * @access public
     * @version 1.0.0 (2015-1-20)
     * @author yusw
     */
    function reply()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $this->show_warning('异常id');
            return;
        }
        if (!IS_POST) {
            $complaint = $this->_complaint_mod->get($id);

            $complaint['from_nickname'] = $this->get_nick_name($complaint['from_id']);
            $complaint['to_nickname'] = $this->get_nick_name($complaint['to_id']);


            for ($i = 1; $i <= 4; $i++) {
                if ($complaint['img' . $i . '_url'] != '') {
                    $complaint['img_url_' . $i] = getUserPhotoUrl('complaints', $complaint['img' . $i . '_url'], 200);
                    $complaint['img_url_big_' . $i] = getUserPhotoUrl('complaints', $complaint['img' . $i . '_url'], '');
                }
            }


            $this->assign('status_options', $this->status);
            $this->assign('complaint', $complaint);
            $this->display(TYPE . 'complaint.reply.html');
        } else {

            $data = array();
            $data['reply'] = trim($_POST['reply']);
            $data['status'] = $_POST['status'];
            $data['id'] = $_POST['c_id'];
            $to_id = $_POST['to_id'];

            //拒绝原因
            if ($data['status'] == 2 && !$data['reply']) {
                $this->show_warning('input_reason');
                return;
            }

            $rows = $this->_complaint_mod->edit($id, $data);
            if ($this->_complaint_mod->has_error()) {
                $this->show_warning($this->_complaint_mod->get_error());
                return;
            }

            //edit member complaint_num  此处暂时不要了
//            if ($data['status'] == '1') {
//                if(isset($to_id)){
//                    $this->_member_mod =  & m('member');
//                    $rows = $this->_member_mod->changeAccount($to_id, "complaint_num = complaint_num+1");
//                    if ($this->_member_mod->has_error()) {
//                        $this->show_warning($this->_member_mod->get_error());
//                        return;
//                    }
//                } else {
//                    $this->show_message('member_complaint_error',
//                        'back_list', 'index.php?app=complaint',
//                        'edit_again', 'index.php?app=complaint&amp;act=reply&amp;id=' . $id);
//                    return;
//                }
//            }


            //审核拒绝 站内信
            if ($data['status'] == 2) {
                $from_id = $_POST['from_id'];
                $from_nickname = $_POST['from_nickname'];
                $to_nickname = $_POST['to_nickname'];

                $img = $sign = '';
                for ($i = 1; $i <= 4; $i++) {
                    $_img = "img$i";
                    $_img_big = "img$i$i";
                    $$_img = $_POST['img_url_' . $i];
                    $$_img_big = $_POST['img_url_big_' . $i];
                    if (!isset($$_img)) continue;
                    if ($sign == '') {
                        $img = '<br/>';
                        $sign = 1;
                    }
                    $img .= '<a target="_blank" href="' . $$_img_big . '"><img src="' . $$_img . '"></a>';
                }

                if (isset($to_id) && isset($from_id)) {
                    //to tailor
                    sendMessage(array('type' => 9, 'to_user_id' => $to_id, "reason" => $data['reply'], "c_content" => $_POST['content'], "location_url" => '', "name" => $from_nickname, "from_user_id" => $from_id, "img" => $img));
                    //to user
                    sendMessage(array('type' => 10, 'to_user_id' => $from_id, "reason" => $data['reply'], "c_content" => $_POST['content'], "location_url" => '', "name" => $to_nickname, "from_user_id" => $from_id, "img" => $img));
                }
            }

            $this->show_message('edit_successed',
                'back_list', 'index.php?app=complaint',
                'edit_again', 'index.php?app=complaint&amp;act=reply&amp;id=' . $id);
        }
    }

    /**
     * id获取nickname
     * @return void
     * @access public
     * @version 1.0.0 (2015-1-20)
     * @author yusw
     */
    function get_nick_name($id)
    {
        $ret = $this->_user_mod->getById($id);
        return ($ret['nickname'] != '') ? $ret['nickname'] : $ret['user_name'];
    }

    /**
     * 删除投诉
     * @return void
     * @access public
     * @version 1.0.0 (2015-1-20)
     * @author yusw
     */
    function drop()
    {

            $this->show_message('删除功能暂时不开放',
                'back_list', 'index.php?app=complaint');
return;
        $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;

        if ($ids && $this->_complaint_mod->drop(db_create_in($ids, 'id'))) {
            $this->show_message('drop_true',
                'back_list', 'index.php?app=complaint'
            );
            return;
        } else {
            $this->show_message('drop_error',
                'back_list', 'index.php?app=complaint'
            );
            return;
        }
    }


}

?>
