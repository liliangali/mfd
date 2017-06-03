<?php
define('MAX_LAYER', 4);
set_time_limit(0);//设置不超时

/* 地区控制器 */
class DosysmsgApp  extends BackendApp
{
	var   $sys_push;
	var   $member;
	function __construct(){
        $this->DosysmsgApp();
    }
	function DosysmsgApp(){

        parent::__construct();
        $this->member  = & m('member');
		$this->sys_push =  m('sys_push');

        $this->member_lv = array(
            // 			'-1' => '所有会员',
            '100' => '所有用户',
            '2' => "&nbsp;麦富迪达人",
            '3' => '&nbsp;麦富迪精英',
            '4' => '&nbsp;麦富迪领袖',
           // '5' => '&nbsp;麦富迪高级顾问',
           // '8' => '&nbsp;麦富迪顾问',
            //'1' => '消费者',
        );

		$this->send_type = array(
			'all' => '所有设备',
            'ios' => 'IOS设备',
            'android' => "安卓设备",

        );

		$this->assign('obj_type',$this->member_lv);
		$this->assign('send_type',$this->send_type);

    }
	
	//发送记录页面
    function index() {

        $page = $this->_get_page();

        $list = $this->sys_push->findAll(array(
            'limit' => $page['limit'],
            'order' => "id DESC",
            'count' => true,
        ));

        foreach($list as $k=>$v){

            $list[$k]['send_time'] =$v['send_time']?date("Y-m-d H:i:s",($v['send_time']+8*60*60)):'';
        }
		$page['item_count'] = $this->sys_push->getCount();   
        $this->assign('list', $list);
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->display('dosysmsg.index.html');
    }
    
    //发送页面
    function addSend() {

        if(IS_POST){//执行发送操作
			$title      = !empty($_POST['title']) ? $_POST['title'] :  '';
			$content    = !empty($_POST['content']) ? $_POST['content'] :  '';
			$send_type  = !empty($_POST['send_type']) ? $_POST['send_type'] :  'all';
			$obj_type   = !empty($_POST['obj_type']) ? $_POST['obj_type'] :  0;//发送类型
			$obj_val    = !empty($_POST['obj_val']) ? $_POST['obj_val'] :  0;

            

            $send_member_arr =array();
            $conditions = 'serve_type=1  ';
            if ($obj_type  == 0) {//指定会员
                if (!$obj_val ) {
                    $this->show_warning('缺少必填参数');
                    return;
                }
                $zhiding_val = implode(',', $obj_val);

                $conditions .= " AND user_id IN ($zhiding_val)";

            } elseif ($obj_type > 0) {//=====  指定会员类型  =====
                if ($obj_type == 100) {
                    $conditions .= " AND  member_lv_id >= 2 ";
                } elseif (in_array($obj_type, array(1,2,3,4))) {
                    $conditions .= " AND member_lv_id = $obj_type ";
                }

            }

            $memberAll = $this->member->find(array(
                'conditions' => $conditions,
                'fields'     => 'user_id, user_name, user_token',
                'index_key'  => '',
            ));
           $user_message=&m('usermessage');
            if(!$memberAll) {
                $this->show_warning('发送失败，指定会员不存在！');
                return;
            }else{
				
				foreach($memberAll as $key=>$val){
					   
            //信息表
			$mess['from_user_id']       = $this->visitor->info['user_id'];
			$mess['from_nickname']       = $this->visitor->info['user_name'];
			$mess['to_user_id']       = $val['user_id'];
			$mess['title']       = $title;
			$mess['content']       = $content;
			$mess['is_read']       = 0;
			//$mess['type']       = $title;
			$mess['add_time']       = time();
			 $user_message->add($mess);
			
			
				}
			}
            


            $send_member_arr = i_array_column($memberAll,'user_id');

			//记录日志
			$data['title']       = $title;
			$data['content']     = $content;
			$data['add_time']   = time();
			$data['send_type']   = $send_type;//发送类型
			$data['send_obj']    = $obj_type;//发送会员类型
			$data['send_num']    = empty($memberAll)?'':count($memberAll);//发送会员类型
			$data['send_member'] = empty($send_member_arr)?'':json_encode($send_member_arr);
			$data['admin_name']  = $this->visitor->info['user_name'];
			$data['admin_id']    = $this->visitor->info['user_id'];
			
			$id = $this->sys_push->add($data);
			
			if (!$id)  {
				$this->show_warning('添加发送队列失败');
				return;
			}
			
			$this->show_message('发送系统推送',
				'返回列表', 'index.php?app=dosysmsg&amp;act=index',
				'继续发送', 'index.php?app=dosysmsg&amp;act=addSend'
			);
        } else {
			$this->display('dosysmsg.send.html');
		}
    }
	
	//发送详情
    function sendInfo() {
		$id    = !empty($_GET['id']) ? $_GET['id'] :  0;

        $info = $this->sys_push->get(array(
			'conditions' => 'id='.$id,
        ));
		
		if($info['send_member']) {
			$info['send_member'] = json_decode($info['send_member']);
		}

        $this->assign('info', $info);
        $this->display('dosysmsg.info.html');
		
	}

	
}

?>












