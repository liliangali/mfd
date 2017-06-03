<?php
use Cyteam\Member\Member;
class Wxeb23f7e550e2aa2fapp extends MallbaseApp
{
    public $mpObj;
    function __construct()
    {
        parent::__construct();
        $this->mpObj = mpObj();
    }


    function index()
    {
$error = m('error');
        $res = $this->mpObj->valid(1);
        $this->mpObj->getRev();
        $event = $this->mpObj->getRevEvent();
//$error->add(['content'=>33]);
//$error->add(['content'=>$this->mpObj->getRevType()]);
//$error->add(['content'=>json_encode($event)]);
        if(($this->mpObj->getRevType() == 'event') && ($event['event'] == 'subscribe'))
        {
//$error->add(['content'=>1]);
            $sceneId = $this->mpObj->getRevSceneId();

            if($sceneId)//渠道二维码
            {
                $member_mod = m('member');
                $openid = $this->mpObj->getRevFrom();
                $m_info = $member_mod->get("openid = '$openid'");
                if(!$m_info && !$m_info['user_id']) //只认初次关注的会员
                {
                    $channel_info = $member_mod->get(array(
                        'conditions' => "channel_code = '$sceneId' ",
                    ));
                    if($channel_info && $channel_info['user_id'])//二级渠道人员
                    {
                        $mdata['channel_pid'] = $channel_info['user_id'];
                    }
                    else//临时渠道人员
                    {
                        $mdata['channel_pid'] = $sceneId;
                    }

                    $user_info = $this->mpObj->getUserInfo($openid);
                    $mdata['openid'] = $openid;
                    $mdata['reg_time'] = time();
                    if($user_info)
                    {
                        $nicname = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $user_info['nickname']);
                        if (!get_magic_quotes_gpc())
                        {
                            $nicname = addslashes($nicname);
                        }
                        if(!$nicname)
                        {
                            $nicname = "**";
                        }
                        $mdata['nickname'] = $nicname;
                        $mdata['user_name'] = $nicname;
                        $mdata['avatar'] = $user_info['headimgurl'];
                    }
                    $user_id = $member_mod->add($mdata);//注册
                    $member = new Member();
                    $member->debit($user_id,$user_info['nickname']);//送券
                }
            }
            $this->mpObj->text('aaa');
        }
        $this->mpObj->reply();
        return ;
    }

}

?>