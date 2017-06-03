<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
/**
*-----------------------------------------------------------
*我的消息
*-----------------------------------------------------------
*@access public
*@author cyrus <2621270755@qq.com>
*@date 2016年5月24日
*@version 1.0
*@return 
*/
class My_messagesApp extends MemberbaseApp{
	var $_user_message;
	function __construct()
	{
		$this->My_messagesApp();
	}
	function My_messagesApp()
	{
		parent::__construct();
		$this->_user_message=&m('usermessage');	
		
	}
	//END function
	/**
	*-----------------------------------------------------------
	*我的消息默认页 
	*-----------------------------------------------------------
	*@access public
	*@author cyrus <2621270755@qq.com>
	*@date 2016年5月24日
	*@version 1.0
	*@return 
	*/
	function index(){
		$user=$this->visitor->get();
		$page=$this->_get_page(10);
		$user_id=$user['user_id'];
		$conditions="to_user_id=".$user_id;
		$user_messages=$this->_user_message->find(array(
				'conditions'=>$conditions,
				'fields'=>'*',
				'limit'=>$page['limit'],
				'order'      => 'is_read asc,add_time DESC',
				'count'=>true,
		));
		foreach ($user_messages as $k=>$v){
			if ($v['content']!=strip_tags($v['content'])){
				$v['content']=htmlspecialchars($v['content']);
			}
		}
		$page['item_count']=$this->_user_message->getCount();
		$this->_format_page($page);
		$this->assign('user_messages',$user_messages);
		$this->assign('app', APP);
		$this->assign('page_info', $page);
		$this->_config_seo('title', '我的麦富迪 - 我的消息');
		$this->display('my_messages.index.html');
	}
//END function
	/**
	*-----------------------------------------------------------
	* 我的消息删除
	*-----------------------------------------------------------
	*@access public
	*@author cyrus <2621270755@qq.com>
	*@date 2016年5月24日
	*@version 1.0
	*@return 
	*/
	function ajax_drop(){
		$ids=empty($_POST['ids'])?'0':$_POST['ids'];
		if(!$ids){
			$this->json_error('缺失id');
			return ;
		}
		$message_ids=explode(',', $ids);
		foreach ($message_ids as $k=>$message_id){
			$message=$this->_user_message->find(intval($message_id));
			if(!$message){
				$this->json_error('该消息不存在');
				return ;
			}
		}
		if(!$this->_user_message->drop($message_ids)){    //drop
			return $this->json_error($this->_user_message->get_error());
		}
		return $this->json_result(true);
	}
//END function
	/**
	*-----------------------------------------------------------
	*我的消息阅读状态修改
	*-----------------------------------------------------------
	*@access public
	*@author cyrus <2621270755@qq.com>
	*@date 2016年5月24日
	*@version 1.0
	*@return json
	*/
	function ajax_change_read(){
		$id=empty($_GET['id'])?'0':$_GET['id'];
		if(!$id){
			$this->json_error('参数错误');
			return ;
		}
		$messages_info=$this->_user_message->get(array("conditions"=>"id={$id} AND is_read=0"));
		if($messages_info){
			$result=$this->_user_message->edit($id,"is_read=1");
			if(!$result){
				$err=$this->_user_message->get_error();
				$this->json_error($err);
				return ;
			}
			$this->json_result(true);
			return ;
		}else {
			$this->json_error('没有该消息');
			return ;
		}
	}
	//END function
	
	
}