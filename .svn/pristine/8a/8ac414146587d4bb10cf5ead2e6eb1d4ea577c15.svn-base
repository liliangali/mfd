<?php
/*
 * 我的消息
 * @author daniel
 *   */
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
	/* 我的消息默认页 */
	function index(){
		$user=$this->visitor->get();
		$page=$this->_get_page(10);
		$conditions="to_user_id={$user['user_id']}";
		$user_messages=$this->_user_message->find(array(
				'conditions'=>$conditions,
				'fields'=>'*',
				'limit'=>"{$page['limit']}",
				'order'      => 'is_read ASC,add_time DESC',
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
		//var_dump($user_messages);
		//ns add 前端判断样式
		$this->assign('app', APP);
		$this->assign('page_info', $page);
		//var_dump($page);
		$this->display('my_messages.list.html');
	}
	/* ajax加载消息列表 */
	function ajax_messages_lists(){
		$user=$this->visitor->get();
		$page=$this->_get_page(10);
		$conditions="to_user_id={$user['user_id']}";
		$user_messages_list=array();
		$user_messages_list=$this->_user_message->find(array(
				'conditions'=>$conditions,
				'fields'=>'*',
				'limit'=>"{$page['limit']}",
				'order'      => 'add_time DESC',
				'count'=>true,
		));
		foreach ($user_messages_list as $k=>$v){
			if ($v['content']!=strip_tags($v['content'])){
				$user_messages_list[$k]['content']=htmlspecialchars($v['content']);
			}
			$user_messages_list[$k]['add_time']=date('Y/m/d',$v['add_time']);
		}
		$page['item_count']=$this->_user_message->getCount();
		$this->_format_page($page);
		$user_messages_list=array_values($user_messages_list);
		$arr = array(
            'content' => $user_messages_list,
            'count' => $page['page_count'],
        );
		$this->json_result($arr);
	}
	function info(){
		$args = $this->get_params();
		if(!$args[0]){
		   $this->show_warning('参数错误');
		   return ;
		}
		$messages_info=$this->_user_message->get($args[0]);
		if($messages_info){
			$result=$this->_user_message->edit($args[0],"is_read=1");
/* 			if(!$result){
				$err=$this->_user_message->get_error();
				$this->show_warning($err);
				return ;
			} */
			$this->assign('message',$messages_info);
			$this->display('my_messages.info.html');
		}else {
			$this->show_warning('没有该消息');
			return ;
		}

		//var_dump($messages_info);

	}
	/* 我的消息删除  */
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
	/* 删除全部消息 */
	function  ajax_drop_all(){
		$user=$this->visitor->get();
		if(!$this->_user_message->drop("to_user_id={$user['user_id']}")){
			return $this->json_error($this->_user_message->get_error());
		}
		return $this->json_result(true,"删除成功");
	}
	
	/* 删除单个消息  */
	function ajax_drop_message(){
		$id=empty($_POST['id'])?'0':intval(trim($_POST['id']));
		if(!$id){
			$this->json_error('参数错误，请联系客服');
			return ;
		}
		$message=$this->_user_message->get($id);
		if(!$message){
			$this->json_error('该消息不存在');
			return ;
		}
		if(!$this->_user_message->drop($id)){    //drop
			return $this->json_error($this->_user_message->get_error());
		}
		return $this->json_result(true);
	}
























}
