<?php

/**
 * 系统消息通信类
 */
class messageCenter{
    
    /**
     * 同步会员
     * 
     * @author 高飞
     */
    function syncMember($uid,$type,$nickname,$loginMoblie,$loginMail,$loginPasswd)
    {
        $url = DOMAIN_MESSAGE_CENTER.'/api/sync/userAdd';
        $post = array(
                'mid'=>$uid,
                'type'=>$type,
                'name'=>$loginMoblie,
                'passwd'=>$loginPasswd,
                'mail'=>$loginMail,
                'nickname'=>$nickname,
            );
        return curlPost($url.'?key='.md5('gaofei'.DOMAIN_MESSAGE_CENTER),$post);
    }
    
    /**
     * 发送系统消息
     * 
     * int $messageType 消息类型 1交易提醒 2评论提醒 3互动提醒
     * int $sendmid 发消息身份ID
     * int $acceptMid 接收消息人ID
     * string $content 发送内容
     * 
     * @author 高飞
     */
    function addSystemMessage($messageType=1,$sendmid,$acceptMid,$content)
    {
        $url = DOMAIN_MESSAGE_CENTER.'/api/system/add';
        $post = array(
                'messageType'=>$messageType,
                'sendmid'=>$sendmid,
                'acceptMid'=>$acceptMid,
                'content'=>$content,
            );
        return curlPost($url.'?key='.md5('gaofei'.DOMAIN_MESSAGE_CENTER),$post);
    }
    /**
     * 查询系统消息
     * 
     * int $messageType 消息类型 1交易提醒 2评论提醒 3互动提醒
     * int $listNumber 条数
     * int $totalNumber 总条数
     * int $pageNumber当前页数
     * 
     * @author 高飞
     */
    function getSystemMessage($messageType=1,$listNumber,$totalNumber,$pageNumber=1)
    {
        $url = DOMAIN_MESSAGE_CENTER.'/api/system/select';
        $post = array(
                'messageType'=>$messageType,
                'listNumber'=>$listNumber,
                'totalNumber'=>$totalNumber,
                'pageNumber'=>$pageNumber,
            );
        return curlPost($url.'?key='.md5('gaofei'.DOMAIN_MESSAGE_CENTER),$post);
    }
    /**
     * 删除系统消息
     * 
     * array $systemId 要删除的系统消息ID
     * 
     * @author 高飞
     */
    function delSystemMessage($systemId=array())
    {
        $url = DOMAIN_MESSAGE_CENTER.'/api/system/del';
        $post = array('id'=>  json_encode($systemId));
        return curlPost($url.'?key='.md5('gaofei'.DOMAIN_MESSAGE_CENTER),$post);
    }
    
}
