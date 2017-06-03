<?php

/**
     *    手机短信
     *
     *    @author    andcpp
     *    @return    void
*/
	 
class MsgApp extends StoreadminbaseApp
{
    
	function __construct()
    {
        $this->MsgApp();
    }

    function MsgApp()
    {
        parent::__construct();
		$this->mod_msg =& m('msg');
		$this->mod_msglog =& m('msglog');
    }
	
    function index()
    {
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                         LANG::get('msg'),         'index.php?app=msg',
                         LANG::get('set')
                         );

        /* 当前所处子菜单 */
        $this->_curmenu('set');
        /* 当前用户中心菜单 */
        $this->_curitem('msg');
        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
		$user_id = $this->visitor->get('user_id');
		$user_name = $this->visitor->get('user_name');
		$row_msg=$this->mod_msg->getrow("select * from ".DB_PREFIX."msg where user_id='$user_id'");
		if (!IS_POST)
        {
			$this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('msg'));
			
			$checked_functions = $functions = array();
            $functions = $this->_get_functions();
            $tmp = explode(',', $row_msg['functions']);
            if ($functions)
            {
                foreach ($functions as $func)
                {
                    $checked_functions[$func] = in_array($func, $tmp);
                }
            }
			$this->assign('mobile',$row_msg[mobile]);
			$this->assign('state',$row_msg[state]);
			$this->assign('num',$row_msg[num]);
			$this->assign('functions', $functions);
			$this->assign('checked_functions', $checked_functions);
			$this->display('msg.index.html');
		}
		else
		{
			$functions = isset($_POST['functions']) ? implode(',', $_POST['functions']) : '';
			$data = array(
				'user_id' => $user_id,
				'user_name' => $user_name,
                'mobile'   => $_POST['mobile'],
                'state' => $_POST['state'],
                'functions'    => $functions,
            );
			if($row_msg)
			{
				$this->mod_msg->edit('user_id='.$user_id,$data);
			}
			else
			{
				$this->mod_msg->add($data);
			}
            $this->show_message('set_ok',
                'back_list',    'index.php?app=msg'
            );
		}
    }
    /**
     *    发送短消息
     *
     *    @author    Hyber
     *    @return    void
     */
    function send()
    {

        if (!IS_POST){
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                             LANG::get('msg'),         'index.php?app=msg',
                             LANG::get('sendmsg')
                             );
            /* 当前所处子菜单 */
            $this->_curmenu('sendmsg');
            /* 当前用户中心菜单 */
            $this->_curitem('msg');

            header('Content-Type:text/html;charset=' . CHARSET);

            //引入jquery表单插件
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js',
            ));
            $this->_config_seo('title', Lang::get('user_center') . ' - ' . Lang::get('sendmsg'));
            $this->display('msg.send.html');
        }
        else
        {			
			$mobile	 = $_POST['to_mobile'];	//号码
			$smsText = trim($_POST['msg_content']);		//内容
			
			$time = time();
			$user_id = $this->visitor->get('user_id');
			$user_name = $this->visitor->get('user_name');
			$row_msg=$this->mod_msg->getrow("select * from ".DB_PREFIX."msg where user_id=".$user_id);
			if($row_msg['state']==0)
			{
				$this->show_message('cuowu_duanxingongnengweikaiqi', 'go_back', 'index.php?app=msg');
				return;
			}
			if($row_msg['num']<=0)
			{
				$this->show_message('cuowu_duanxinshuliangbuzu', 'go_back', 'index.php?app=msg');
				return;
			}
			if($mobile == '')
			{
				$this->show_message('cuowu_shoujihaomabunengweikong', 'go_back', 'index.php?app=msg&act=send');
				return;
			}
			if($smsText == '')
			{
				$this->show_message('cuowu_neirongbunengweikong', 'go_back', 'index.php?app=msg&act=send');
				return;
			}
			//九天接口
			$this->SendSms($mobile,$smsText);
				
			//中国网建接口
			/*$url='http://utf8.sms.webchinese.cn/?Uid='.SMS_UID.'&Key='.SMS_KEY.'&smsMob='.$mobile.'&smsText='.$smsText; 
			$res = $this->Sms_Get($url);
			if($res == '')
			{
				$this->show_message('cuowu_duanxinfasongshibai', 'go_back', 'index.php?app=msg');
				return;
			}
			else if($res>0)
			{
				$num = $row_msg['num']-1;
				$edit_msg = array(
					'num' => $num,
				);
				$add_msglog = array(
					'user_id' => $user_id,
					'user_name' => $user_name,
					'to_mobile' => $mobile,
					'content' => $smsText,
					'state' => $res,
					'time' => $time,
				);
				$this->mod_msglog->add($add_msglog);
				$this->mod_msg->edit('user_id='.$user_id,$edit_msg);
				$this->show_message('send_msg_successed', 'go_back', 'index.php?app=msg');
				return;
			}
			else
			{
				$add_msglog = array(
					'user_id' => $user_id,
					'user_name' => $user_name,
					'to_mobile' => $mobile,
					'content' => $content,
					'state' => $res,
					'time' => $time,
				);
				$this->mod_msglog->add($add_msglog);
				$this->show_message('cuowu_duanxinfasongshibai', 'go_back', 'index.php?app=msg');
				return;
			}*/
			
            //$this->show_message('send_message_successed', 'go_back', 'index.php?app=message&act=privatepm');
        }
    }
	
	/**
     *    购买手机信息
     *
     *    @author    andcpp
     *    @return    void
     */
	 //function buy()
//	 {
//		 /* 当前位置 */
//        $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
//                         LANG::get('msg'),         'index.php?app=msg',
//                         LANG::get('buymsg')
//                         );
//
//        /* 当前所处子菜单 */
//        $this->_curmenu('buymsg');
//        /* 当前用户中心菜单 */
//        $this->_curitem('msg');
//		
//		$user_id = $this->visitor->get('user_id');
//		//$my_money=$this->mod_my_money->getrow("select * from ".DB_PREFIX."my_money where user_id=".$user_id);
//		//$row_msg=$this->mod_msg->getrow("select * from ".DB_PREFIX."msg where user_id=".$user_id);
//        
//		 if(!IS_POST)
//		 {
//			 $this->assign('money', $my_money['money']); 
//			 $this->display('msg.buy.html');
//		 }
//		 else
//		 {
//			 $buy_num = $_POST['num'];
//			 $zf_pass = trim($_POST['zf_pass']);
//			 $edit_money = $buy_num * 0.1;
//			 $new_num = $row_msg['num'] + $buy_num;
//			 $new_money = $my_money['money'] - $edit_money;
//			 $order_sn = date("Y-m-d-His",time());
//			 
//			 $md5zf_pass=md5($zf_pass);
//			 if(empty($zf_pass))
//			{
//				$this->show_warning('cuowu_zhifumimabunengweikong'); 
//				return;
//			}
//			if($my_money['money'] <$edit_money)
//			{
//				$this->show_warning('cuowu_zhanghuyuebuzu');
//				return;
//			}
//			if($my_money['zf_pass'] != $md5zf_pass)
//			{
//				$this->show_warning('cuowu_zhifumimayanzhengshibai'); 
//				return;
//			}
//			$edit_msg = array(
//				'num' => $new_num,
//				);
//			$log_text = $this->visitor->get('user_name')."购买短信".$buy_num."条，支出".$edit_money."元" ;
//			$edit_mymoney = array(
//				'money' => $new_money,
//				);
//			$add_mymoneylog=array(
//				'user_id'=>$user_id,
//				'order_sn' =>$order_sn,
//				'user_name'=>$this->visitor->get('user_name'),
//				'add_time'=>time(),
//				'money_zs'=>'-'.$edit_money,	
//				'log_text'=>$log_text,																			
//			);
//			$this->mod_my_moneylog->add($add_mymoneylog);
//			$this->mod_msg->edit('user_id='.$user_id,$edit_msg);
//			$this->mod_my_money->edit('user_id='.$user_id,$edit_mymoney);
//			$this->show_message('buy_success');
//			return;
//		 }
//	 }

    /**
     *    查看短消息
     *
     *    @author    Hyber
     *    @return    void
     */
    function view()
    {
        $this->_clear_newpm_cache();

        $msg_id = isset($_GET['msg_id']) ? intval($_GET['msg_id']) : 0;
        if (!$msg_id)
        {
            $this->show_warning('no_such_message');
            return;
        }
        $my_id = $this->visitor->get('user_id');
        $ms =& ms();
        if (!IS_POST)
        {
            $message = $ms->pm->get($this->visitor->get('user_id'), $msg_id, true);
            if (empty($message))
            {
                $this->show_warning('no_such_message');
                return;
            };
            $new = $message['topic']['new'];
            !empty($new) && $ms->pm->mark($this->visitor->get('user_id'), array($msg_id), 0); //标示已读
            
            $box = '';
            
            if ($message['topic']['from_id'] == 0 && $message['topic']['to_id'] == 0 )
            {
                $box = 'announcepm';
            }
            elseif ($message['topic']['from_id'] == MSG_SYSTEM)
            {
                $box = 'systempm';
            }
            elseif ($my_id == $message['topic']['from_id'] || $my_id == $message['topic']['to_id'])
            {
                $box = 'privatepm';
            }
            $ms = &ms();
            if ($message['topic']['from_id'] == 0 && $message['topic']['to_id'] == 0)
            {
                $message['topic']['user_name'] = Lang::get('announce_msg');
                $message['topic']['portrait'] = portrait(0, '');
            }
            elseif ($message['topic']['from_id'] == MSG_SYSTEM)
            {
                $message['topic']['user_name'] = Lang::get('system_msg');
                $message['topic']['portrait'] = portrait(0, '');
            }
            else
            {
                $uid = $message['topic']['from_id'];
                $user_info = $ms->user->get($uid);
                $message['topic']['user_name'] = $user_info['user_name'];
                $portrait = portrait($user_info['user_id'], $user_info['portrait']);
                $message['topic']['portrait'] = $portrait;
            }
            
            $uid = 0;
            $user_info = array();
            
            foreach ($message['replies'] as $key => $value)
            {
                $uid = $value['from_id'];
                $user_info = $ms->user->get($uid);
                $message['replies'][$key]['user_name'] = $user_info['user_name'];
                $portrait = portrait($user_info['user_id'], $user_info['portrait']);
                $message['replies'][$key]['portrait'] = $portrait;
            }
            $this->assign('message', $message['topic']);
            $this->assign('replies', $message['replies']);
            $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('view_message'));
            $this->assign('box', $box);
            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),   'index.php?app=member',
                             LANG::get('message'),         'index.php?app=message&amp;act=newpm',
                             LANG::get('view_message')
                             );
            /* 当前所处子菜单（必须放在这里，否则新消息数量不正确） */
            $this->_curmenu('view_message');
            /* 当前用户中心菜单 */
            $this->_curitem('message');
            $this->display('message.view.html');
        }
        else
        {
            $message = $ms->pm->get($this->visitor->get('user_id'), $msg_id);
            $reply_to_id = 0;
            if ($my_id == $message['topic']['to_id'])
            {
                $reply_to_id = $message['topic']['from_id'];
            }
            elseif ($my_id == $message['topic']['from_id'])
            {
                $reply_to_id = $message['topic']['to_id'];
            }

            if (empty($reply_to_id) || $reply_to_id == MSG_SYSTEM)
            {
                $this->show_warning('cannot_replay_system_message');
                return;
            }

            $mod_member = &m('member');
            if (!$mod_member->get_info($reply_to_id))
            {
                $this->show_warning('no_such_user');
                return;
            }
            if (!$msg_id = $ms->pm->send($this->visitor->get('user_id'), $reply_to_id, '', $_POST['msg_content'] , $msg_id))  //获取msg_id
            {
                $this->show_warning($ms->pm->get_error());

                return;
            }
            $this->show_message('send_message_successed');
        }
    }

    /**
     *    删除短消息
     *
     *    @author    Hyber
     *    @return    void
     */
    function drop()
    {
        $msg_ids = isset($_GET['msg_id']) ? trim($_GET['msg_id']) : '';
        if(in_array($_GET['back'],array('newpm','privatepm')))
        {
            $folder = trim($_GET['back']);
        }
        if (!$msg_ids)
        {
            $this->show_warning('no_such_message');
            return;
        }
        $msg_ids = explode(',',$msg_ids);
        if (!$msg_ids)
        {
            $this->show_warning('no_such_message');
            return;
        }
        $ms =& ms();
        if (!$ms->pm->drop($this->visitor->get('user_id'), $msg_ids, $folder))    //删除单条消息
        {
            $this->show_warning('drop_error');

            return;
        }

        /* 删除成功返回 */
        if (in_array($_GET['back'],array('newpm', 'privatepm')))
        {
            $this->show_message('drop_message_successed',
                'back_' . $_GET['back'] ,'index.php?app=message&amp;act=' . $_GET['back']);
        }
        else
        {
            $this->show_message('drop_message_successed');
        }
    }

}

?>
