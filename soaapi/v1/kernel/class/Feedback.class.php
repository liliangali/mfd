<?php
/**
 * @author zhaoxinran
 * @version v1.1
 * @copyright Copyright 2015 new.api.dev.mfd.cn
 * @package app
 */
class Feedback
{
    var $return;
    var $error = '';
    var $token = '';
    function __construct()
    {
        $this->result = new Result();
        //=====返回结果=====
        $this->return['statusCode'] = 0;
        $this->return['msg']             = '';
    }
    public function test($data){
        global $json;
        $return=123;
        return $json->encode($return);
    }
    public function feedback($data){
        global $json;
      	 $token=isset($data->token)?$data->token:'';
        //$user_id=isset($data->user_id)?$data->user_id:'';
        $content  = isset($data->content) ? $data->content : '';
        if(!$token){
        	$user_id=0;//如果未登陆，就是游客反馈
        }else{
        	$user_info=getUserInfo($token);
        	if(!$user_info){
        		return $this->result->tresult();
        	}
        	$user_id=$user_info['user_id'];
        	if(empty($content)){
        		$this->result->errorCode=102;
        		$this->result->msg='请填写内容';
        		return $this->result->eresult();
        	}
        }
        $data = array();
        $feedback_mod    = m('feedback');
        $data['description']  =	$content;
        $data['add_time'] = time();
        $data['user_id']  = $user_id;
        if ($feedback_mod->add($data)) {
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success'  => '反馈提交成功',
        
                )
            );
            return $json->encode($return);
        } else {
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 103,
                    'msg'       => '反馈提交失败',
                ),
            );
            return $json->encode($return);
        }
    }
}