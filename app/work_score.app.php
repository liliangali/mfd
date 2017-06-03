<?php
/**
 *
 * 作品评选
 * @author yusw 2015-04-15
 *
 */
class Work_scoreApp extends MallbaseApp
{
    private $_comment_mod;
    private $_userwork_mod;
    private $_userworkimg_mod;
    private $_collect_mod;
    private $_member_mod;
    private $category =array("3"=>"西服","2000"=>"西裤","3000"=>"衬衫","4000"=>"马甲","6000"=>"套装");
    private $_e_members = array(); //专家列表

    function __construct(){
        parent::__construct();
        $this->_comment_mod = &m("com_comment");
        $this->_userwork_mod  =& m('userwork');
        $this->_userworkimg_mod  =& m('userworkimg');
        $this->_vote = & m("like");
        $this->_collect_mod = & m("collect");
        $this->_member_mod =& m('member');
    }

   /**
    *
    * 首页
    *
    */
    function index()
    {
        $this->display('work/work_score.index.html');
    }



    /**
    *
    * 列表页面
    *
    */
    function loadPage()
    {
        $now_page  = empty($_REQUEST['page_now'])?1:$_REQUEST['page_now'];//$_GET['page_now']
        $page_num  = 6;
        $star_page = ($now_page - 1) * $page_num;
        $limit     = "$star_page,$page_num";
        $sort = $_REQUEST['sort'] == 1 ? 'nscore_num desc':'add_time desc'; //网友

        require_once(ROOT_PATH . '/includes/avatar.class.php');
        $objAvatar = new Avatar();
        $work_list = $this->_userwork_mod->find(array(
            'fields'=> '*',
            'conditions' => 'voting=1 and status=1',//投票审核暂时不加
            'count' => true,
            'order' => $sort,
            'limit' => $limit,
        ));

        empty($work_list)&&$this->json_error('暂无数据');
        $work_list  = array_values($work_list);

        $ids = array();
        foreach ($work_list as $key => $value) {

            $ids[] = $value["id"];
            $work_list[$key]['add_time']=date('Y-m-d H:i:s',$value['add_time']);
            $work_list[$key]['category']=$this->category[$value['cloth']];

            //网友评分相关
            $comment_score = $this->_get_comment_score($work_list[$key]['id'],'0');
            $work_list[$key]['n_score'] = $comment_score['score'];
            $work_list[$key]['n_count'] = $comment_score['count'];

            $work_list[$key]['member']  = $this->_member_mod->get_info($value['store_id']);
            $work_list[$key]['member']['avatar'] = $objAvatar->avatar_show($value['store_id'],'big');
        }


        $imglist = $this->_userworkimg_mod->find(array(
            "conditions" => "work_id ".db_create_in($ids),
            'fields'      => 'img_url',
        ));
        $imglist = array_values($imglist);


        foreach($work_list as $key =>$val){
            $work_list[$key]['imgurl'] = $imglist[$key]['img_url'];
        }

        $this->json_result($work_list);
    }



    /**
     *
     * 作品详情页面
     *
     */
    function info(){
        $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);

        if(!$id){
            $this->show_warning('不合法的参数！');
            return;
        }

        $sort = $_REQUEST['sort'] == 1 ? 'nscore_num desc':'add_time desc';



        $work_list = $this->_userwork_mod->find(array(
            'fields'=> '*',
            'conditions' => 'status=1 and voting=1',// and cloth='.$type,
            'order' => $sort,//暂时add_time
        ));

        $work_keys = array_keys($work_list);
        $key_sign = array_keys($work_keys,$id);
        $n_key = $key_sign[0]+1;
        $nid = $n_key<count($work_list)? $work_keys[$n_key]:'';

        $member_work =$work_list[$id];
        $user_works = $this->_userwork_mod->find(array(
            'conditions' => 'status=1 and store_id='.$member_work['store_id'],
        ));


        if($member_work['id']){
            $imgurl = $this->_userworkimg_mod->find(array(
                'conditions' => 'work_id=' . $member_work['id'],
                'fields'     => 'img_url',
            ));
            $member_work['imgurl']= $imgurl;
        }

        $member_work['add_time']=date('Y-m-d H:i:s',$member_work['add_time']);
        $member_work['category']=$this->category[$member_work['cloth']];

        //获取用户信息
        $member =  $this->_member_mod->get_info($member_work['store_id']);

        require_once(ROOT_PATH . '/includes/avatar.class.php');
        $objAvatar = new Avatar();
        $member['avatar'] = $objAvatar->avatar_show($member_work['store_id'],'big');

        //网友评分相关
        $comment_score = $this->_get_comment_score($id,'0');
        $member_work['n_score'] = $comment_score['score'];
        $member_work['n_count'] = $comment_score['count'];

        //专家评分相关
        $comment_score = $this->_get_comment_score($id,'1');
        $member_work['e_score'] = $comment_score['score'];
        $member_work['e_count'] = $comment_score['count'];

        //添加浏览量
        //$this->_userwork_mod->edit("id='{$id}'",array('views'=>$member_work['views']+1));

        //评论列表
        $comment_list = $this->_get_comment_list($id,'wk',1,1);//zj
        $comment_list2 = $this->_get_comment_list($id,'wk',1,2);//wy

        $member_work['nid'] = $nid;
        $member_work['member'] = $member;

        $this->assign("user_works_num", count($user_works));
        $this->assign("comment_list2", $comment_list2);
        $this->assign("comment_list", $comment_list);
        $this->assign('member_work', $member_work);
        $this->display('work/work_score.info.html');
    }


    /**
     *
     * 获取用户评论
     *
     */
    function loadComment(){
        $id = $_REQUEST['id'];
        $comment_list = $this->_get_comment_list($id,'wk');//($id,$cate,$page);
        $this->assign("comment_list", $comment_list);
        $content =$this->_view->fetch("work/comment_list.html");
        $this->json_result($content);
        die();
    }



    /**
     *
     * 评分 总数
     *
     */
    function _get_comment_score($id,$e_status){

        $arr = array('score'=>'0','count'=>'0');
        $comment_list = $this->_comment_mod->find(array(
            'conditions' => "cate='wk'   and vote_score !='NULL' and e_status='".$e_status."' and comment_id='".$id."'",//and status=1
            'count' => 1,
        ));

        if(!empty($comment_list)){
            $scores = 0;
            foreach($comment_list as $c_v){
                $scores  = $c_v['vote_score']+$scores;
            }
            $count = $this->_comment_mod->getCount();
            $arr['score'] = substr(($scores/$count),0,3);//待定
            $arr['count'] = $count;
        }
        return $arr;
    }

    /**
     *
     * 评论列表数据
     *
     */
    function  _get_comment_list($id,$cate,$page=1,$expert=''){
        $page   =   $this->_get_page(3);
        if($expert==2){

            //wangyou
            $e_status = 'and e_status=0';
        }elseif($expert==1){

            //zhuanjia
            $e_status = ' and e_status=1';
        }else{
            $e_sign = intval($_REQUEST['e_sign']);
            if($e_sign==1){
                //专家
                $e_status = ' and e_status=1';
            }else{
                $e_status = ' and e_status=0';
            }
        }

        $conditions = "comment_id = '{$id}' and cate='{$cate}'".$e_status;//  and status=1

        $comment_list = array();
        $list = $this->_comment_mod->find(array(
            'conditions' => $conditions,
            'limit' =>$page['limit'],
            'count' => 1,
            'order' => 'addtime DESC'
        ));

        $page['item_count'] = $this->_comment_mod->getCount();
        $this->_format_page($page,7,array(),'loadComment');

        require_once(ROOT_PATH . '/includes/avatar.class.php');
        $objAvatar = new Avatar();
        foreach($list as $key => $val){
            $list[$key]['face'] = $objAvatar->avatar_show($val['member_id'],'big');
        }

        $comment_list['page_info'] = $page;
        $comment_list['list'] = $list;
        $comment_list['id'] = $id;

        return $comment_list;
    }



    /**
     *
     * 提交用户评论
     *
     */
    function commit(){
        $args = $this->get_params();
        $id = empty($args[0]) ? 0 : intval($args[0]);   //作品id
        $content = isset($_REQUEST["content"]) ? trim($_REQUEST['content']) : '';
        $vote_socre = $_REQUEST['vote'];

        if(!$id)
        {
            $this->json_error("参数错误");
            return false;
        }

        if(!$vote_socre)
        {
            $this->json_error("请对作品进行打分");
            return false;
        }

        if(empty($content))
        {
            $this->json_error("评论的内容不能为空");
            return false;
        }

        //必须登录用户
        if(!$this->visitor->has_login){
            $this->json_result("login");
            return false;
        }

        //不能重复评论
        $count =$this->_comment_mod->find(array(
            'conditions' => "cate='wk' and vote_score !='NULL' and member_id='".$this->visitor->get("user_id")."' and comment_id='".$id."'",
        ));

//        if($count){
//            $this->json_error("已评论过了！不能重复评论。");
//            return false;
//        }

        require_once(ROOT_PATH . '/includes/avatar.class.php');
        $face = $this->objAvatar = new Avatar();

    	$data = array(
    		'member_id'			 => $this->visitor->get("user_id"),
    		'comment_id'	      => $id,
    		'content'            => htmlspecialchars($content),
    	    'addtime'            => gmtime(),
    	    'nickname'           => $this->visitor->get("nickname"),
            'cate'               =>  'wk',
            'come_from'         =>'web',
            'e_status'          =>in_array($this->visitor->get("user_id"),$this->_e_members)?1:0,   //暂定id
            'vote_score'        =>$vote_socre,
    	 );

        $res = $this->_comment_mod->add($data);
        if($res){
            //评论数量++   记录网友 的评论数
            if(!in_array($this->visitor->get("user_id"),$this->_e_members)){
                $this->_userwork_mod->setInc(array("id"=>$id),"nscore_num");
                $e_sign =2;
            }else{
                $e_sign =1;
            }


//            sendMessage(array(
//                'type' => 11,
//                'location_url' => "work_score-info-{$id}.html",
//                'to_user_id'    => $member_work['store_id'],
//                "c_content"=> htmlspecialchars($content),
//            ));


//            $data['face'] = $face->avatar_show_src($data['member_id'],'big');
//
//            $this->assign("comment_list", $data);
//            $content =$this->_view->fetch("work/one_comment.html");
            $this->json_result(array('e_sign'=>$e_sign));
            die();
        }else{
            $this->json_error("意外错误~");
            die();
        }
    }


}


?>