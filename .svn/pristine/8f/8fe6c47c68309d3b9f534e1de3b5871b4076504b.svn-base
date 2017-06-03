<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
use Cyteam\Comment\Comment;
/**
 *    我的评论控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class My_commentApp extends MemberbaseApp
{
    var $_ordergoods_mod;
    var $_order_mod;
    var $_detailcomment_mod;
    var $_detailimpress_mod;
    var $commentObj;
    function __construct()
    {	
    		parent::__construct();
    		header("Content-Type:text/html;charset=" . CHARSET);
    		Lang::load(lang_file('common'));
        $this->_ordergoods_mod  = &m('ordergoods');
        $this->_detailimpress_mod =&m('detail_impression');
        $this->_detailcomment_mod = &m('detail_comment');
        $this->_order_mod = &m("order");
       $this->commentObj =new Comment();
    }
	/**
	*-----------------------------------------------------------
	*用户中心-我的评论
	*-----------------------------------------------------------
	*@access public
	*@author cyrus <2621270755@qq.com>
	*@date 2016年5月30日
	*@version 1.0
	*@return 
	*/
    function index()
    {
      	
        $arg = $this->get_params();
        $status = array('uncomment', 'commented','all');
        $user = $this->visitor->get();
        $user_id = $user['user_id'];
        $conditions = " AND order_alias.status = 40 ";
        if(in_array($arg[1], $status)){
            switch ($arg[1]){
                case "uncomment":
                    $conditions .= " AND order_goods.comment=0";
                    break;
                case "commented":
                    $conditions .= " AND order_goods.comment=1";
                    break;
                case "all":
                    $conditions .= " AND (order_goods.comment=1 || order_goods.comment=0)";
                    break;
            }
        }
      $page    =   $this->_get_page(5); 
		
		$comment_list=$this->commentObj->get_order_comment($user_id, $conditions, $page);
		$impress=$this->commentObj->get_impression_arr();

//   var_dump($comment_list);
    $page['item_count'] = $this->_ordergoods_mod->getCount();
    $this->_format_page($page);
  	$this->assign('page_info', $page);
  	$this->assign('impress', $impress);
  	$this->assign("status", $arg[1]);
//     $this->assign("category", $this->_ordersuit_mod->getCategory());
    $this->assign('comment_list', $comment_list);
    $this->_config_seo('title', '我的麦富迪 - 我的评论');
    $this->assign("app", "my_comment");
    $this->display('my_comment.index.html');
    }
    //END function
    //用户中心-评论详情
    function publishInfo(){
        
        return $this->display('my_comment.publish.html');
    }
    //END function
    /**
    *-----------------------------------------------------------
    *用户中心-添加评论
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月30日
    *@version 1.0
    *@return 
    */
    function addComment()
    {
    	$user = $this->visitor->get();
        $id = isset($_POST['id']) ? intval($_POST['id']) : '';
        $type = isset($_POST['type']) ? $_POST['type'] : 'custom';
        $star = isset($_POST['star']) ? $_POST['star'] : '';
        $content = isset($_POST['info']) ? $_POST['info'] : '';
        // 评论的印象内容
        $impress = isset($_POST['impress']) ? $_POST['impress'] :'';
        $is_hide = '0';
        // 印象 做分表 处理
        $impressAttr = explode(";", $impress);

        if(!$id)
        {
            $this->json_error("没有要评论的商品");
            return; 
        }
        $from='pc';
       $is_comment=$this->commentObj->add_comments($id, $user, $type, $from, $star, $content, $impress);
    	if($is_comment){
    		$this->json_result($is_comment);
    		return;
    	}else{
    		$this->json_error("发表评论失败");
    		return ;
    	}    	
    }
    //END function
    
}

?>