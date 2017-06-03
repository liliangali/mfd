<?php
/**
 * 诚信度管理
 * @author tangshoujian <tangshoujian@126.com>
 * @version $Id: sincerity.app.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2015 www.alicaifeng.com
 * @package app
 */

class SincerityAPP extends MemberbaseApp
{
    
    var $sin_mod;
    function __construct()
    {
        $this->SincerityApp();
    }
    function SincerityAPP(){
        parent::__construct();
        $ms =& ms();   
    }
    
    //默认
    function index()
    { 
        $user = $this->visitor->get();
        $user_id = $this->visitor->get('user_id');
        if (!$user['has_store'])
        {
            /*  show_message('您还不是裁缝，请返回！');  */
            header("Location:apply.html");
        }
        $complaint_mod = &m('complaint');
        $comment_mod = &m('ordercomment');
        // 好评数量
        $good_num = $comment_mod->get(array(
            'conditions' =>"tailor_id ='$user_id' AND status=1 AND approve=1",
            'fields'     =>"count(*) as c"
        ));
        
        //差评数量
        $bad_num = $comment_mod->get(array(
            'conditions' =>"tailor_id ='$user_id' AND status=1 AND approve=3",
            'fields'     =>"count(*) as c"
        ));
        // 投诉数量
        $complaint_num = $complaint_mod ->get(array(
            'conditions'=>"to_id ='$user_id' AND status =1 OR status =0 ",
            'fields'    =>"count(*) as c"
        ));
        
        $current_score =  100-$bad_num[c]*5+$good_num[c]*5-$complaint_num[c]*10;
        if($current_score >100){
            $current_score =100;
        }elseif($current_score<0){
            $current_score =0;
        }
        $this->assign('good_num', $good_num[c]*5);
        $this->assign('bad_num', $bad_num[c]*5+$complaint_num[c]*10);
        $this->assign('current_score', $current_score); 
        $this->assign('title','诚信度是阿里裁缝为了维护良好的平台氛围，保障顾客权益而建立的一套信用体系');
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->display('my_sincerity.index.html'); 
    }
    
    function ajax_sin(){

        $user_id = $this->visitor->get('user_id');
        $score = isset($_POST['score']) ? intval($_POST['score']) : '';
      
        if($score==1){
            $conditions = " AND status=1 AND approve=1 ";
            $con  = " AND status =3 ";
        }elseif($score==3){
            $conditions = " AND status=1 AND approve=3 ";
            $con  = " AND status =1 ";
        }elseif($score==4){
            $conditions = " AND status=1 AND (approve=3 OR approve =1) ";
            $con  = " AND status =1 OR status =0";
        }

        //举报
        $complaint_mod = &m('complaint');
        $complaint = $complaint_mod->find(array(
            'join'   =>'belongs_to_user',
            'conditions' =>"to_id =" . $user_id  . "{$con}",
            'fields'     =>'this.*,member.nickname',
            'count'      =>true,
            'order'      =>'c_time DESC'
            ));

  
        // 好评差评
        $comment_mod = &m('ordercomment');

        $good_comment = $comment_mod->find(array(
            'join'      =>'belongs_to_user',
            'conditions' => "tailor_id =" .$user_id. "{$conditions}" ,
            'fields'     =>'this.*,member.nickname',
            'count'     =>true,
            'order'      =>'addtime DESC'
        
                ));
        
        $this->assign('good_comment', $good_comment);
        $this->assign('complaint', $complaint);
        $content = $this->_view->fetch('../user/my_sin.html');
        $this->json_result(array(
            'content' => $content,
        ));
        return ;
        
        
        
    }
    function rule(){
        $article_mod = &m('article');
        $rule = $article_mod ->get_article_info(90,1);
        $this->assign('rule', $rule['content']);
        $this->assign('rule_title', $rule['title']);
        $this->display('my_sincerity_rule.html');
       
        
        
        
    }
    function problem(){
        $article_mod = &m('article');
        $problem = $article_mod ->get_article_info(91,1);
        $this->assign('problem', $problem['content']);
        $this->assign('problem_title', $problem['title']);
        $this->display('my_sincerity_problem.html');
        
        
    }
    
    
    
    
    
    
    
    
}
