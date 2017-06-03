<?php
/**
 *
 * 量体师评论
 * @author yusw 2015-05-22
 *
 */
class Figure_commentApp extends MallbaseApp
{
    private $_comment_mod;
    private $_figure_mod;
    function __construct(){
        parent::__construct();
        $this->_comment_mod = &m("figure_comment");
        $this->_figure_mod = &m("member_figure");
    }

    /**
     *
     * 会员提交评论
     * @author yusw (2015-05-22)
     */
    function commit(){
        $content = isset($_REQUEST["content"]) ? trim($_REQUEST['content']) : '';
        $star = intval($_REQUEST['star']);
        $hide_name = intval($_REQUEST['nm']);
        $measure_id = intval($_REQUEST['m_id']);
        $figure_sn = intval($_REQUEST['f_sn']);


        if(!$this->visitor->has_login){
            $this->json_result("login");
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


        if(!$measure_id){
            $this->json_error("量体师id不能为空");
            return false;
        }



        $figure_sn_sign = $this->_figure_mod->get("figure_sn = '{$figure_sn}' AND is_new=0");
        if($figure_sn_sign){
            $this->json_error("没有新的量体，不能评价");
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
            'content'            => htmlspecialchars($content),
            'addtime'            => gmtime(),
            'nickname'           => $nick_name,
            'star'               =>  $star,
            'come_from'         => 'web',
            'hide_name'         =>$hide_name,
            'measure_id'         =>$measure_id,
        );

        $res = $this->_comment_mod->add($data);
        if($res){
            //标记操作过
            $res = $this->_figure_mod->edit($figure_sn,array('is_new'=>0));
            $this->json_result($res);
        }else{
            $this->json_error("意外错误~");
            die();
        }

    }




}





?>