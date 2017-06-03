<?php
//晒单
class SingleApp extends MallbaseApp
{
    private $_comment_mod;
    function __construct(){
        parent::__construct();
        $this->_comment_mod = &m("singlecomment");
        header("Content-Type:text/html;charset=" . CHARSET);
    }
    /**
    * @deprecated todo
    * @param pType pValueName pInfo
    * @return rType rValueName rInfo
    * @access public
    * @see index
    * @version 1.0.0 (2014-12-20)
    * @author ns
    */
    function index()
    {
        $userphoto_mod =& m('userphoto');
        $sorting = empty($_GET['sorting']) ? 'add_time' : $_GET['sorting'];
        $page   =   $this->_get_page(20);    //获取分页信息
        $single_list = $userphoto_mod->find(array(
            //'fields'=> '*',
            'conditions' => 'uid > 1 AND status=1',
            'count' => true,
            'order' => $sorting.' desc',
            'limit' => $page['limit'],
        ));
        $page['item_count'] = $userphoto_mod->getCount();
        $this->_format_page($page);
        foreach($single_list as $k=>$v){
            $single_list[$k]['img_url'] = getUserPhotoUrl('shaidan',$v['url'],500);
        }

        $this->assign('single_list',$single_list);
        if($sorting != 'add_time'){
            $page['parameter'] = '?sorting='.$sorting;
            if($type_id){
                $page['parameter'] .='&type_id='.$type_id;
            }
        }else{
            if($type_id){
                $page['parameter'] ='?type_id='.$type_id;
            }
        }
        $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条
        $this->display('single.index.html');
    }
    function info(){
        $arg = $this->get_params();
        $id = empty($arg[0]) ? 0 : intval($arg[0]);
        if(!$id){
            $this->show_warning('无此晒单！');
            return;
        }
        $member_single_mod =& m('member_single');
        $member_single = $member_single_mod->get($id);
        $member_single['img_url'] = getUserPhotoUrl('shaidan',$member_single['img_url'],'original');
        $this->assign('member_single', $member_single);
        $this->assign('popularity', $member_single['popularity']+1);

        //获取用户信息
        $member_mod =& m('member');
        $member = $member_mod->get_info($member_single['member_id']);
        $this->assign('member', $member);
        //获取所有图片信息
        $userphoto_mod =& m('userphoto');
        $userphoto = $userphoto_mod->find(array('conditions'=>'status=1 and album_id='.$member_single['id']));
        foreach($userphoto as $k=>$ph){
            $userphoto[$k]['url'] = getUserPhotoUrl('shaidan',$ph['url'],'original');
        }
        $this->assign('userphoto', $userphoto);

        //同个用户作品
        $same_single = $userphoto_mod->find(array(
            'conditions' => 'status=1 and uid ='.$member_single['member_id'].' and  album_id!='.$id,
            'order' => 'like_num desc',
            'limit' => '12'
        ));
        foreach($same_single as $same_k=>$same_v){
            $same_single[$same_k]['url'] = getUserPhotoUrl('shaidan',$same_v['url'],235);
        }
        $this->assign('same_single', $same_single);

        //推荐
        $recommend_single = $userphoto_mod->find(array(
            'conditions' => 'status=1 and uid !='.$member_single['member_id'],
            'order' => 'add_time desc',
            'limit' => '12'
        ));
        foreach($recommend_single as $re_k=>$re_v){
            $recommend_single[$re_k]['url'] = getUserPhotoUrl('shaidan',$re_v['url'],235);
        }
        $this->assign('recommend_single', $recommend_single);
        //添加人气
        $member_single_mod->edit("id='{$id}'",array('popularity'=>$member_single['popularity']+1));
        //给内容添加浏览量
        $userphoto_mod->edit("album_id='{$id}'",array('like_num'=>$member_single['popularity']+1));

        $this->display('single.info.html');
    }
    
    //获取用户评论
    function loadComment(){
        
        require(ROOT_PATH . '/includes/avatar.class.php'); 
        
        $face = $this->objAvatar = new Avatar();
        
        $arg = $this->get_params();

        $max = isset($_GET['max']) ? intval($_GET['max']) : 0;

        $conditions = "(status = 1 OR member_id = '{$this->visitor->get("user_id")}') AND member_single_id = '{$arg[0]}'";
        
        if($max){
            $conditions .= " AND id < '{$max}'";
        }
        
        $comment_list = $this->_comment_mod->find(array(
            'conditions' => $conditions,
            'limit' => 7,
            'count' => 1,
            'order' => 'addtime DESC'
        ));
        
        $count = $this->_comment_mod->getCount();
        
         
        foreach($comment_list as $key => $val){
            $comment_list[$key]['ftime'] = TimeFormat($val['addtime']);
            $str = preg_replace("/\[em_(\d+)\]/", '<img src="../../static/expand/qqface/arclist/$1.gif">' ,$val['content']);
            $comment_list[$key]['content'] = $str;
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
    
    //提交用户评论
    function commit(){
         
        $args = $this->get_params();
        require(ROOT_PATH . '/includes/avatar.class.php');
        $face = $this->objAvatar = new Avatar();
        $id = empty($args[0]) ? 0 : intval($args[0]);

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
            $this->json_result("login");
            return false;
        }
        
        $member_single_mod =& m('member_single');
        $member_single = $member_single_mod->get($id);
        
        if($member_single['member_id'] == $this->visitor->get("user_id")){
            $this->json_error("不能评论自己的晒单");
            return false;
        }
        
        $status = CONF::get('check_comment');
        
    	$data = array(
    		'member_id'			 => $this->visitor->get("user_id"),
    		'member_single_id'	 => $id,
    		'content'            => htmlspecialchars($content),
    	    'addtime'            => gmtime(),
    	    'status'             => $status == 1 ? 0 : 1,
    	    'nickname'           => $this->visitor->get("nickname"),
    	 );
        
    	
    	
        $res = $this->_comment_mod->add($data);
        if($res){
            sendMessage(array(
                'type' => 2,
                'location_url' => "single-info-{$id}.html",
                'to_user_id'    => $member_single['member_id'],
            ));
            $data['ftime'] = TimeFormat($data['addtime']);
            $str = preg_replace("/\[em_(\d+)\]/", '<img src="../../static/expand/qqface/arclist/$1.gif">' ,$data['content']);
            $data['face'] = $face->avatar_show_src($data['member_id'],'big');
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