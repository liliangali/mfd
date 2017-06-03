<?php
/* 意见反馈控制器 */
class FeedbackApp  extends BackendApp
{
	function __construct(){
        $this->FeedbackApp();
    }
	function FeedbackApp(){
        parent::__construct();
        $this->_feedback_mod =& m('feedback');
        $this->_member_mod =& m('member');
        $this->_member_lv=&m('memberlv');
    }
//列表
	function index(){
		$conditions = '1=1';
		$page = $this->_get_page(30);
		$feedback = $this->_feedback_mod->find(array(
            'conditions' => $conditions,
            //'join'  => '',
            //'fields'=> '',
            'limit' => $page['limit'],
            'count' => true,
            'order' => "add_time desc"
        ));
        $user_name = '';
		foreach($feedback as $k=>$v){
			if($v['user_id'] == 0){
				$feedback[$k]['user_name'] = '游客';
				$feedback[$k]['nickname'] = '游客';
			}else{
				$user_info = $this->_member_mod->get(array('conditions'=>'user_id='.$v['user_id']));
				if(!empty($user_info['member_lv_id'])){
					$member_lv=$this->_member_lv->get(array('conditions'=>"member_lv_id={$user_info['member_lv_id']}"));
					$user_name=$member_lv['name'];
				}else {
					$user_name='未知';
				}
				$feedback[$k]['user_name'] =$user_name;
				$feedback[$k]['nickname'] = $user_info['nickname'];
			}

			for($i=1;$i<5;$i++){
				$img_name = 'img'.$i.'_url';
				if($v[$img_name]){
					$feedback[$k]['img_list'][$i]['img_url'] = getUser_feedback_imgUrl('',$v[$img_name]);
				}
			}
		}
        $this->assign('feedback', $feedback);
        $page['item_count'] = $this->_feedback_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
		$this->display('feedback.index.html');
	}
	//删除
	function drop(){

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
        $ids = explode(',',$id);
        if ($ids && $this->_feedback_mod->drop(db_create_in($ids, 'id'))) {
            $this->show_message('删除成功!',
                'back_list', 'index.php?app=feedback'
            );
            return;
        } else {
            $this->show_message('删除失败!',
                'back_list', 'index.php?app=feedback'
            );
            return;
        }

	}
	//反馈详情
	function info()
	{

		$id=!empty($_REQUEST['id'])?$_REQUEST['id']:'';
		if(empty($id))
		{
			$this->show_warning('参数错误');
			return ;
		}
		$feedback = $this->_feedback_mod->get(array('conditions'=>"id={$id}"));
		if(empty($feedback))
		{
			$this->show_warning('该反馈不存在');
			return ;
		}
		$user_info = $this->_member_mod->get(array('conditions'=>'user_id='.$feedback['user_id']));
		if(IS_POST)//回复内容
		{
		    $id = $_POST['id'];
			if(!$id)
			{
				$this->show_message('error!',
					'back_list', 'index.php?app=feedback'
				);
				return;
			}
			$replay = $_POST['replay'];
			if(!$replay)
			{
				$this->show_message('参数错误!',
					'back_list', 'index.php?app=feedback'
				);
				return;
			}
			$this->_feedback_mod->edit($id,['replay'=>$replay]);
			$this->sendWx($user_info,$feedback,$replay);
			$res = send_message("feedback",[$user_info['user_id']],['des'=>$feedback['description'],'replay'=>$replay]);
			$this->show_message('回复成功!',
				'back_list', 'index.php?app=feedback'
			);
			return;
		}

		if($feedback['user_id']==0)
		{
			$feedback['user_name'] = '游客';
			$feedback['nickname'] = '游客';
		}
		else
		{
			$user_info = $this->_member_mod->get(array('conditions'=>'user_id='.$feedback['user_id']));
			if (! empty ( $user_info ['member_lv_id'] )) {
				$member_lv = $this->_member_lv->get ( array (
						'conditions' => "member_lv_id={$user_info['member_lv_id']}"
				) );
				$user_name = $member_lv ['name'];
			} else {
				$user_name = '未知';
			}
			$feedback  ['user_name'] = $user_name;
			if(empty($user_info['nickname'])){
				$feedback['nickname'] = $user_info ['phone_mob'];
			}else{
				$feedback['nickname'] = $user_info ['nickname'];
			}
		}
		for($i=1;$i<5;$i++){
			$img_name = 'img'.$i.'_url';
			if($feedback[$img_name]){
				$feedback['img_list'][$i]['img_url'] = getUser_feedback_imgUrl('',$feedback[$img_name]);
			}
		}
		$this->assign('feedback', $feedback);
		$this->display('feedback.info.html');
	}


	function sendWx($member_info,$feed,$replay)
	{
		$mod = m('accesstoken');
		//=====  如果是微信订单要发送消息  =====
		if($member_info['openid'])
		{
			$access_token = $mod->get_token();
			$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
			$data['touser'] = $member_info['openid'];
			$data['template_id'] = 'hpS939HMeCoLEchLhhrmE8TDwX04mzU591tnftIEjXo';
			$data['url'] = "https://www.sobot.com/chat/h5/index.html?sysNum=2b17cdee375a475e963aeed478c37fbf&groupId=84c09075813e45f68eae414cf91e897a";
			$data['data']['first']['value'] = "尊敬的会员,您的反馈已经处理";
			$data['data']['first']['color'] = "#173177";

			$data['data']['keyword1']['value'] = $feed['description'];
			$data['data']['keyword1']['color'] = "#173177";

			$data['data']['keyword2']['value'] = date("Y-m-d H:i:s",$feed['add_time']);
			$data['data']['keyword2']['color'] = "#173177";

			$data['data']['keyword3']['value'] = $replay;
			$data['data']['keyword3']['color'] = "#173177";

			$data['data']['keyword4']['value'] = date("Y-m-d H:i:s",time());
			$data['data']['keyword4']['color'] = "#173177";


			$data['data']['remark']['value'] = "感谢及时反馈,麦富迪祝您生活愉快!";
			$data['data']['remark']['color'] = "#173177";
			$res = https_request($url,json_encode($data));
			if($res['errcode'] == 0)
			{
				//$order_mod->edit($order_info['order_id'],['is_send'=>1]);
			}
			else
			{
				$access_token = $mod->settoken();
				$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
				$res = https_request($url,json_encode($data));
			}

		}
	}


    /**
     * excel
     * @author liuchao  <280181131@qq.com>
     * @2015-5-14
     * 本方法用于将需求列表内所有的信息生成excel表
     *
     */
    function export (){

        $feedback =$this->_feedback_mod->findAll(array(
            "conditions" => "1=1",
            'fields' => '',
        ));
        $feedbacks = array();
        foreach ($feedback as $k=>&$row){
            //id
            $feedbacks[$k]['id'] = $row['id'];
            //反馈角色
            if($row['user_id'] == 0){
                $feedbacks[$k]['user_name'] = '游客';
                $feedbacks[$k]['nickname'] = '游客';
            }else{
                $user_info = $this->_member_mod->get(array('conditions'=>'user_id='.$row['user_id']));
                $feedbacks[$k]['user_name'] = $user_info['serve_type'] > 0 ? '裁缝' : '会员';
                $feedbacks[$k]['nickname'] = $user_info['nickname'];
            }
            //来源地址
            $feedbacks[$k]['from'] = $row['url'];
            //联系方式
            $feedbacks[$k]['mobile'] = $row['mobile'];
            //文字描述
            $feedbacks[$k]['description'] = $row['description'];

            //图片详情
            $feedbacks[$k]['img_list'] = '';
            for($i=1;$i<5;$i++){
                $img_name = 'img'.$i.'_url';
                if($row[$img_name]){
                    $feedbacks[$k]['img_list'] .= getUserPhotoUrl('feedback',$row[$img_name],'original').'<br/>';
                }
            }
            //反馈时间
            $feedbacks[$k]['feed_time'] = date("Y-m-d ", $row['add_time']);

        }
        $fields_name = array('ID','反馈角色身份','反馈角色昵称','来源地址','联系方式','文字描述','图片详情','反馈时间');
        array_unshift($feedbacks,$fields_name);
        $this->export_to_csv($feedbacks, 'feedback', 'gbk');
    }
	
}

?>
