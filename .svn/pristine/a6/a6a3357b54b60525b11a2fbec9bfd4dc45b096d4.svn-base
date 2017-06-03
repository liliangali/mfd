<?php
/**
 * 用户中心 app- 我的投诉
 * @author xiao5 <xiao5.china@gmail.com>
 * @version $Id: my_complaint.app.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 www.alicaifeng.com
 * @package app
 */
class My_complaintApp extends MemberbaseApp
{
    function __construct()
    {
        $this->My_complaintApp();
    }

    /**
     * 重写模板文件
     * @return void
     * @access public
     * @see _config_view
     * @version 1.0.0 (2014-12-17)
     * @author Xiao5
     */
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name = $this->_get_style_name();
        $this->_view->template_dir = ROOT_PATH . "/view/" . LANG . "/mall/{$template_name}/user_center";
        $this->_view->compile_dir = ROOT_PATH . "/temp/compiled/mall/{$template_name}/user_center";
        $this->_view->res_base = SITE_URL . "/view/" . LANG . "/mall/{$template_name}/styles/{$style_name}";
    }

    function My_complaintApp()
    {
        parent::__construct();
    }



	/**
	 * ajax 上传图片
	 * @return void
	 * @access public
	 * @version 1.0.0 (2015-2-13)
	 * @author yusw
	 */
    function upload(){
        $num = $_REQUEST['num'];

        $dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/complaints/';
        $fileName = $this->visitor->info['user_id'] ."_" . $num . "_" .md5( uniqid() . mt_rand(0,255) ) . ".jpg";

        $fileDirName2 = $dir ."235x315/" . $fileName;
        pro_img('up_file',235,315,$fileDirName2);
        $fileDirName3 = $dir ."520x685/" . $fileName;
        pro_img('up_file',520,685,$fileDirName3);

        $fileDirName1 = $dir ."original/" . $fileName;
        $rs = move_uploaded_file($_FILES['up_file']["tmp_name"],$fileDirName1);


        $src= get_domain() . "/upload_user_photo/complaints/235x315/".$fileName;
        $arr = array('src'=>$src,'file'=>$fileName);
        echo json_encode($arr);
        exit;
    }


	/**
	 * ajax 投诉
	 * @return void
	 * @access public
	 * @version 1.0.0 (2015-2-13)
	 * @author yusw
	 */

    public function add(){

            $_store_mod =& m('store');
            $_complaint_mod =& m('complaint');

            //店铺id
            $data = array();
            $store_name = trim($_REQUEST['shopName']);
            $data['content'] = htmlspecialchars($_REQUEST['description']);
            $data['c_time'] = gmtime();

            $store_id = $_store_mod->get(array(
                'conditions' => "store_name = '$store_name'",
                'fields' => 'store_id',
            ));

            if($store_id == ''){
                echo json_encode(array('res'=>0,'message'=>'不存在的店铺'));
                return;
            }


            $user = $this->visitor->get();
            $data['from_id'] = $user['user_id'];
            $data['to_id'] = $store_id['store_id'];

            if($data['from_id'] == $data['to_id']){
                echo json_encode(array('res'=>0,'message'=>'不能举报自己'));
                return;
            }

            $img_total = 10;
            $num= 1;
            for($i=1;$i<=$img_total;$i++){
                if($_REQUEST['input_'.$i]){
                    $data['img'.$num.'_url'] = $_REQUEST['img'.$i.'_url'];
                    $num++;
                }
            }

//        print_exit($data);
            //入库
            $_complaint_mod->add($data);

            //发信息给品牌商
            if($_complaint_mod->has_error()){
                $this->show_warning($_complaint_mod->get_error());
                return;
            }
            sendMessage(array('type'=>8,'to_user_id' =>$store_id['store_id'],'location_url' => "","c_content"=>$data['content']));

            echo json_encode(array('res'=>1,'message'=>'提交成功，感谢您的反馈！'));
            return;

        }
    /**
     * 会员中心 我发起的投诉
     * @return void
     * @access public
     * @version 1.0.0 (2015-1-18)
     * @author yusw
     */
    public function index()
    {

        // 当前用户中心菜单
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_complaint'));
        $this->assign('ac', ACT);
        $this->assign('type', 'user');

        $user = $this->visitor->get();
        $this->assign('user', $user);

        //获取头像
        require(ROOT_PATH . '/includes/avatar.class.php'); //基础控制器类
        $objAvatar = new Avatar();
        $avatar = $objAvatar->avatar_show($this->visitor->get('user_id'), 'big');
        $this->assign('avatar', $avatar);

        //获取数据
        $page = $this->_get_page(8);
        $_complaint_mod =& m('complaint');
        $complaints = $_complaint_mod->find(array(
            'conditions' => 'from_id = '.$user['user_id'],
            'limit' => $page['limit'],
            'order' => "complaint.id DESC",
            'count' => true,
        ));
        foreach($complaints as $k=>$v){
            $complaints[$k]['from_nickname'] = $this->get_nick_name($v['from_id']);
            $complaints[$k]['to_nickname'] = $this->get_nick_name($v['to_id']);
            $complaints[$k]['manage'] = 1;

            if($v['status'] == 2){
             $complaints[$k]['result'] = "尊敬的 ". $complaints[$k]['from_nickname']."：<br>您对".$complaints[$k]['to_nickname']."的举报内容经我们调查不属实。理由：".$complaints[$k]['reply']."<br/>感谢您的参与，一起维护阿里裁缝网公平、和谐的商业氛围";
            }elseif($v['status'] == 1){
                $complaints[$k]['result'] = "尊敬的 ". $complaints[$k]['from_nickname']."：<br>您的举报内容经我们调查属实，现给予被举报人".$complaints[$k]['to_nickname']."扣除诚信度10分，永久查封账户。感谢您的参与，一起维护阿里裁缝网公平、和谐的商业氛围";
            }else{
                $now_time = gmtime();
                $now_time - $v['c_time'] >= 7200 && $complaints[$k]['manage'] = 0;
            }
        }
        //获取统计数据
        $page['item_count'] = $_complaint_mod->getCount();
        $this->_format_page($page);

        // 当前用户中心菜单
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_complaint');
    
        $this->assign('complaints', $complaints);
        $this->assign("page_info", $page);
        $this->display('my_complaint.index.html');
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
        $member_mod = & m('member');
        $ret = $member_mod->getById($id);
        return ($ret['nickname'] != '') ? $ret['nickname'] : $ret['user_name'];
    }


    /**
     * 店铺昵称验证
     * @return void
     * @access public
     * @version 1.0.0 (2015-1-20)
     * @author yusw
     */
    public function check_store()
    {
        $store_name = $_REQUEST['store_name'];
        !isset($store_name) && $this->json_error('请输入店铺名称!');;

        //查询店铺是否存在
        $_store_mod =& m('store');
        $unique = $_store_mod->unique($store_name);
        if ($unique) {
            $this->json_error('店铺名称不存在!');
            return;
        }
        $this->json_result('ok');
    }



}

?>
