<?php
/**
 *
 * 分享详情页
 * @author yusw (2015-05-07)
 */
class ShareApp extends MallbaseApp
{
	private $_comment_mod;
    private $_userwork_mod;
    private $_userworkimg_mod;
    private $_member_mod ;
    private $_dis_mod ;
    private $_shirt_mod ;
    private $_link_mod;
    private $_customs_mod;
    private $_collect_mod;
    private $_invalids = array();//过滤昵称
    private $_inval_contents ="tmd|妈的|TNND|她娘的";//过滤内容





    function __construct(){
        parent::__construct();
		$this->_member_mod = & m('member');
        $this->_comment_mod = &m("com_comment");
        $this->_userwork_mod  =& m('userwork');
        $this->_userworkimg_mod  =& m('userworkimg');
        $this->_dis_mod =& m('jpjz_dissertation');
        $this->_shirt_mod      =& m('shirt');


        $this->_link_mod =& m('links');
        $this->_customs_mod=& m('custom');
        $this->_collect_mod = & m("collect");
    }

    /**
     *
     * 获取用户信息
     */
	function member($id){
		require(ROOT_PATH . '/includes/avatar.class.php');
        $objAvatar = new Avatar();
		$member = $this->_member_mod->get_info($id);
        $member['avatar'] = $objAvatar->avatar_show($id,'big');
		return $member;
	}


    /**
     *
     * 首页
     */
	function index(){
		//work
//        $type = 2;//empty($_GET['type']) ? '' : $_GET['sort'];
//		$id = 96;//empty($_GET['id']) ? '' : $_GET['id'];

        //yh
//        $type = 1;//empty($_GET['type']) ? '' : $_GET['sort'];
//        $id = 344;//empty($_GET['id']) ? '' : $_GET['id'];


        //dis  主题
//        $type = 4;//empty($_GET['type']) ? '' : $_GET['sort'];
//        $id = 213;//empty($_GET['id']) ? '' : $_GET['id'];


        $type = empty($_GET['type']) ? '' : $_GET['type'];
        $id   = empty($_GET['id']) ? '' : $_GET['id'];
        $user_id = empty($_GET['user_id']) ? '' : $_GET['user_id'];

        if(!$type){
            $this->show_warning('非法操作');
            return;
        }

        if(!$id){
            $this->show_warning('非法操作');
            return;
        }

	    //type:印花：1 作品：2  --diy:3  主题:4
		switch ($type) {
		   case 1:
				 $list = $this->get_yh_list($id);
                 $member  =$this->member($user_id);//分享人信息

                 $this->assign('member', $member);
                 $this->assign('list', $list);
                 $this->display('share.yh.html');
				 break;
		   case 2:
				 $list = $this->get_wk_list($id);
				 $member  =$this->member($list['store_id']);

                 $this->assign('member', $member);
                 $this->assign('list', $list);
                 $this->display('share.wk.html');
				 break;
		   case 3:
			   	 $diy_list = $this->get_diy_list($id)	;
//               $this->assign('member', $member);
//               $this->assign('list', $list);
				 break;
		   case 4:
                 $list = $this->get_dis_list($id);
                 $member  =$this->member($user_id);//分享人信息
                 
                 $this->assign('member', $member);
                 $this->assign('list', $list);
                 $this->display('share.dis.html');
		         break;

		}


//echo '<pre>';var_dump($type);echo '</pre>';
//echo '<pre>';var_dump($member);echo '</pre>';
//echo '<pre>';var_dump($list);echo '</pre>';

	}


    /**
     *
     * 衬衣
     */
    function get_yh_list($id){
        //        v_imagevarchar(255) NULL放大图
        //        v_dis_image
        $yh_list     = $this->_shirt_mod->find($id);
        if(empty($yh_list)){
            $this->show_warning('暂无此衬衣');
            return;
        }
        return $yh_list;
    }
    /**
     *
     * 作品
     */
    function get_wk_list($id){
        $member_work =$this->_userwork_mod->get($id);
        if(empty($member_work)){
            $this->show_warning('暂无此作品');
            return;
        }
        if($member_work['id']){
            $imgurl = $this->_userworkimg_mod->find(array(
                'conditions' => 'work_id=' . $member_work['id'],
                'fields'     => 'img_url',
            ));
            $member_work['imgurl']= $imgurl;
            $member_work['imgurl_count']= count($imgurl);
        }

        $member_work['add_time']=date('Y-m-d H:i:s',$member_work['add_time']);
        $member_work['category']=$this->category[$member_work['cloth']];

        return $member_work;
    }

    /**
     *
     * diy
     */
    function get_diy_list($id){
        $diy_list = '';
        return $diy_list;
    }
    /**
     *
     * 主题
     */
    function get_dis_list($id){
        $dis_list     = array_values($this->_dis_mod->find($id));
        if(empty($dis_list)){
            $this->show_warning('暂无此主题');
            return;
        }

        //图片
        $this->_mod_cusGallery = &m("customgallery");
        $gallery_list = $this->_mod_cusGallery->find(array(
            'conditions' => "custom_id = '{$id}'",
            'order' => 'id ASC',
        ));

        foreach($gallery_list as $v){
            $dis_list['img'][] = $v['source_img'];
        }


        $links = $this->_link_mod->find(array(
            "conditions" => "d_id='{$id}'",
            "fields"=>'c_id',
        ));


        $cData = '';
        if(!empty($links)){
            $_ids ='';
            foreach($links as $k=>$v){
                $_ids[]  = $v['c_id'];
            }

            $ids = implode(",", $_ids);

            $cData=$this->_customs_mod->findAll("id IN ({$ids})");//source_img

            $i = $j = 0;
            foreach($cData as $c_k=>$c_v){
                if($i>=2){
                    $i=0;
                    $j++;
                }

                $dis_list['y_img'][$j][$i]['img'] = $c_v['source_img'];
                $dis_list['y_img'][$j][$i]['name'] = $c_v['name'];
                $i++;
            }
        }

        $dis_list['y_img_count'] = count($cData);
        $dis_list['img_count'] = count($dis_list['img']);
        return $dis_list;
    }




    /**
     *
     * 获取用户评论
     */
    function loadComment(){
        $cate = $_REQUEST['cate'];
        require(ROOT_PATH . '/includes/avatar.class.php');
        $face = $this->objAvatar = new Avatar();


        $id = $_REQUEST['id'];
        $max = isset($_GET['max']) ? intval($_GET['max']) : 0;
        $conditions = " comment_id = '{$id}' and cate='{$cate}'";//status=1 and 暂时不用审核
        if($max){
            $conditions .= " AND id < '{$max}'";
        }


        $comment_list = $this->_comment_mod->find(array(
            'conditions' => $conditions,
            'limit' => 7,
            'count' => 1,
            'order' => 'addtime DESC'
        ));

        $count = $this->_comment_mod->getCount();  // work_comment
        foreach($comment_list as $key => $val){
            $comment_list[$key]['ftime'] = TimeFormat($val['addtime']);
//            $str = preg_replace("/\[em_(\d+)\]/", '<img src="../../static/expand/qqface/arclist/$1.gif">' ,$val['content']);
//            $comment_list[$key]['content'] = $str;
            $comment_list[$key]['face'] = $face->avatar_show_src($val['member_id'],'big');
        }
        $end = end($comment_list);

        $this->assign("comment_list", $comment_list);
        $content =$this->_view->fetch("comment_list.html");

        $arr = array(
            'content' => $content,
            'count' => $count < 0 ? 0 : $count,
            'next' => $count > 7 ? 1 : 0,
            'max' => $end['id']
        );

        $this->json_result($arr);
    }



    /**
     *
     * 提交用户评论
     * @author yusw (2015-04-16)
     */
    function commit(){
        $args = $this->get_params();
        require(ROOT_PATH . '/includes/avatar.class.php');
        $face = $this->objAvatar = new Avatar();
        $id = empty($args[0]) ? 0 : intval($args[0]);   //作品id
        $cate = $_POST["cate"];

        if(!$id)
        {
            $this->json_error("参数错误");
            return false;
        }


        $content = isset($_POST["content"]) ? trim($_POST['content']) : '';
        if(empty($content)){
            $this->json_error("评论的内容不能为空");
            return false;
        }

        if(!$this->visitor->has_login){
            $u_id        = 'youke_111' ;
            $u_nick_name = 'yk_'.$this->getCode(5);
//--yusw  添加昵称过滤
//            $u_nick_name = $_REQUEST['nick_name'];
//            if(in_array($u_nick_name,$this->_invalids)){
//                $this->json_error("您的昵称".$u_nick_name."含有非法字符");
//                return false;
//            }


        }else{
            $u_id =$this->visitor->get("user_id");
            $u_nick_name = $this->visitor->get("nickname");
        }

//        $member_work = $this->_userwork_mod->get($id);



        //内容过滤
        $content = preg_replace("/$this->_inval_contents/i",'',$content);

        $status = CONF::get('check_comment');
        $data = array(
            'member_id'		  => $u_id,
            'comment_id'	      => $id,
            'content'            => htmlspecialchars($content),
            'addtime'            => gmtime(),
            'status'             => 0, 
            'nickname'           => $u_nick_name,
            'cate'=>$cate,
            'come_from'         =>'app',
        );

        $res = $this->_comment_mod->add($data);
        if($res){


            if($this->visitor->has_login){
                //作品评选
//                sendMessage(array(
//                    'type' => 11,
//                    'location_url' => "work_score-info-{$id}.html",
//                    'to_user_id'    => $member_work['store_id'],
//                    "c_content"=> htmlspecialchars($content),
//                ));
                $data['face'] = $face->avatar_show_src($data['member_id'],'big');
            }else{
                $data['face'] = $face->avatar_show_src('','big');
            }

            $data['ftime'] = TimeFormat($data['addtime']);
            $str = preg_replace("/\[em_(\d+)\]/", '<img src="../../static/expand/qqface/arclist/$1.gif">' ,$data['content']);

            $data['content'] = $str;
            $list[] = $data;
            $this->assign("comment_list", $list);
            $content =$this->_view->fetch("comment_list.html");
            $this->json_result($content);
            die();
        }else{
            $this->json_error("意外错误~");
            die();
        }
    }




}
?>