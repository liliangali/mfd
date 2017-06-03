<?php
/**
 *
 * 订单商品评论（套装 、单品）
 * @author yusw 2015-05-09
 *
 */
class Detail_commentApp extends MallbaseApp
{
    private $_comment_mod;
    private $_answer_mod;


    function __construct(){
        parent::__construct();
        $this->_comment_mod = &m("detail_comment");
        $this->_answer_mod = &m('detail_answer');
    }

    /**
     *
     * 根据商品 获取评论
     * @author yusw (2015-04-16)
     */
    function loadComment(){
        require_once(ROOT_PATH . '/includes/avatar.class.php');
        $face = $this->objAvatar = new Avatar();

        $id = intval($_REQUEST['id']);   //96
        $cate = isset($_REQUEST['cate']) ? $_REQUEST['cate'] : 0; //custom


        if(empty($id) || empty($cate)){
            $this->show_warning("非法操作!");
            die();
        }

        $conditions = "status=1 and comment_id ='$id' and cate='$cate'";
        $page = $this->_get_page(5);
        $comment_list = $this->_comment_mod->find(array(
            'conditions' => $conditions,
            'limit' => $page["limit"],
            'count' => 1,
            'order' => 'addtime DESC'
        ));


        $page['item_count'] = $this->_comment_mod->getCount();
        $list =	 $replyids =  array();
        foreach($comment_list as $key => $val){
//            $comment_list[$key]['ftime'] = TimeFormat($val['addtime']);
            $m_id =$val['hide_name']==1?'':$val['member_id'];
            $comment_list[$key]['face'] = $face->avatar_show_src($m_id,'big');
            $replyids[] = $val["id"];
        }

        $replylist = $this->_answer_mod->find(array(
            "conditions" => "comment_id ".db_create_in($replyids),
        ));

        $comment_list = array_values($comment_list);
        $replylist = array_values($replylist);

        foreach($replylist as $key =>$val){
            $comment_list[$key]['answer'] = $val;
        }


        $list['list'] = $comment_list;
        $list['star_info'] = $this->_get_star_num($id,$cate);

        $this->_format_page($page,7,array(),'loadComment');
        $this->assign("comment_list", $list);
        $this->assign("page_info",$page);
        $content =$this->_view->fetch("comment/detail_comment_list.html");
        $this->json_result($content);
        die();
    }

    /**
     *
     * 获取星评信息
     * @author yusw (2015-04-16)
     */
    function _get_star_num($id,$cate){
        $star=$score=$one=$two=$three=$four=$five=0;
        $res =array();
        $list = $this->_comment_mod->find(array(
                'conditions' => "status=1 and star !=0 and comment_id='{$id}'and cate='{$cate}'",
            )
        );
        foreach($list as $k=>$val){
            switch ($val['star']) {
                case 1:
                    $one++;
                    break;
                case 2:
                    $two++;
                    break;
                case 3:
                    $three++;
                    break;
                case 4:
                    $four++;
                    break;
                case 5:
                    $five++;
                    break;
            }
        }
        $score=$four+$five;
        $res['one_star'] =$one;
        $res['two_star'] =$two;
        $res['three_star'] =$three;
        $res['four_star'] =$four;
        $res['five_star'] =$five;
        $star = $this->_comment_mod->getCount();
        $res['score'] = empty($star) ? 0 : round(($score/$star)*100,1); //百分比 99.8   2-99.89
        return $res;
    }




    /**
     *
     * 会员提交订单商品评论
     * @author yusw (2015-04-16)
     */
    function commit(){
        $content = isset($_REQUEST["content"]) ? trim($_REQUEST['content']) : '';
        $comment_id = intval($_REQUEST["c_id"]);
        $cate = $_REQUEST['cate'];
        $star = intval($_REQUEST['star']);
        $order_id = intval($_REQUEST['o_id']);
        $hide_name = intval($_REQUEST['nm']);
        $rec_id = $_REQUEST['r_id']; //套装存的当前套装最后一条商品id
        $suit_ids = $_REQUEST['suit_ids'];


        if(!$this->visitor->has_login){
            $this->json_result("login");
            return false;
        }


        if(!$comment_id ||!$cate)
        {
            $this->json_error("参数错误");
            return false;
        }

        if(!$star)
        {
            $this->json_error("请进行评分");
            return false;
        }

        if(empty($content)){
            $this->json_error("评论的内容不能为空");
            return false;
        }
        if(empty($order_id)){
            $this->json_error("订单不能为空");
            return false;
        }

        //是否购买过商品  只对订单做判断
        $_mod_order = &m("order");
        $order = $_mod_order->get("order_id = '{$order_id}' AND user_id = ".$this->visitor->get("user_id"));

        if(!$order){
                $this->json_error("你还没有购买过此商品，不能评价商品");
                return false;
        }

        //不能重复评论
        $count =$this->_comment_mod->find(array(
            'conditions' => "rec_id='{$rec_id}' and member_id=".$this->visitor->get("user_id"),
        ));

        if($count){
            $this->json_error("已评论过了！不能重复评论。");
            return false;
        }


        $uid =$this->visitor->get("user_id");
        $nick_name = $this->visitor->get("nickname");
        if($nick_name==''){
            $user_mod =& m('member');
            $info = $user_mod->get_info($uid);
            $nick_name = substr_replace($info['user_name'],'****',2,6);
        }

        $data = array(
            'member_id'		  => $uid,
            'comment_id'	      => $comment_id,
            'content'            => htmlspecialchars($content),
            'addtime'            => gmtime(),
            'status'             => 0,
            'nickname'           => $nick_name,
            'cate'               =>  $cate,
            'star'               =>  $star,
            'come_from'         => 'web',
            'order_id'          =>$order_id,
            'hide_name'         =>$hide_name,
            'rec_id'            =>$rec_id,
        );

        $res = $this->_comment_mod->add($data);
        if($res){
            //标记操作过
            if($cate=='custom'){
                $_order_goods_mod = m('ordergoods');
                $_order_goods_mod->edit($rec_id, array('comment' => 1));
            }else{
                $_order_goods_mod = m('ordergoods');
                $_order_goods_mod->edit(db_create_in(unserialize($suit_ids),'rec_id'), array('comment' => 1));
            }
            $this->json_result($res);
        }else{
            $this->json_error("意外错误~");
            die();
        }
    }

}





?>