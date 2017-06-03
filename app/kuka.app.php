<?php

/**
 * 酷卡控制器
 *
 * @author yushw
 *
 */
class KukaApp extends MemberbaseApp
{
    var $_fabricbook_mod;
    var $_mod;
    var $_member_mod;
 

    function __construct()
    {
		parent::__construct();
		header("Content-Type:text/html;charset=" . CHARSET);
		Lang::load(lang_file('common'));

        $path = ROOT_PATH."/data/config/special_code.php";
        file_exists($path) && $this->_cate = include $path;
        
        $this->_mod =& m('kukaconfig');
        $this->_member_mod = & m('member');
     
        $this->type = array(
            '0003' => '西服',
            '0004' => '西裤',
            '0005' => '马甲',
            '0006' => '衬衣',
            '0007' => '大衣',
        
        );
        $this->assign('type',$this->type);
        

    }

	/**
	 * 酷卡页面
	 * yusw
	 * @return void
	 */
    function index()
    {
        $arg = $this->get_params();
        $status = array('wzs', 'yzs','ygq');
        $user=$this->visitor->get();
        
       
        if($user['member_lv_id'] > 1 ) {
            $now_time = time();
            if(in_array($arg[1], $status)){
                switch ($arg[1]){
                    case "wzs":
                        $con = " AND to_id ='' AND expire_time>'{$now_time}' ";
                        break;
                    case "yzs":
                        $con = " AND to_id !='' AND expire_time>'{$now_time}' ";
                        break;
                    case "ygq":
                        $con = " AND expire_time<'{$now_time}' ";
                        break;
                }
            }
             
        }
        
        
        $this->kukainfo($con);
        $this->_config_seo('title','我的麦富迪 - 我的酷卡');
        $this->assign("status", $arg[1]);
        $this->assign('app', APP);
        $this->display('kuka.index.html');
    }


    /**
     *  麦富迪e卡
     *
     * @param  array $arr 消息数组信息
     *
     * @return  string
     */
    public function kuka()
    {
        //没有登陆
        if (!$this->visitor->has_login)
        {
            $this->json_error("请先登录再进行操作");
            return;
        }


        $return = array();
        $member_mod  = & m('member');
        $_special_code_mod = & m('special_code');
        $sn =isset($_REQUEST['sn'])?trim($_REQUEST['sn']):'';
        $user_info=$this->visitor->get();
        $user_id = $user_info['user_id'];

        if(!$sn){
            $this->json_error("麦富迪E卡不能为空");
            return;
        }
        $url = mfdURL."soap/user.php?act=kuka&token=".$user_info['user_token']."&sn=".$sn;
        $res = file_get_contents($url);//=====  远程请求api接口  =====
        $res = json_decode($res,true);
        if (!$res['statusCode']) 
        {
            $this->json_error($res['error']['msg']);
            return ;
        }
        $sn_info = $_special_code_mod->get("sn = '$sn' ");
        $result = array(
            'html'=>'<tr><td>'.$sn_info['name'].'</td><td>￥'.$sn_info['work_num'].'</td><td>'.$sn.'</td><td>'.local_date('Y-m-d H:i:s',$time).'</td></tr>',
            'msg'=>'恭喜您，已经成功激活麦富迪E卡',
        );

        $this->json_result($result);

    }

    /**
     *  麦富迪e卡 详细信息
     *
     * @param  array $arr 消息数组信息
     *
     * @return  string
     */
/*     function kuka_info(){
        //没有登陆
        if (!$this->visitor->has_login)
        {
            $this->assign('kuka_info','暂无数据');
            return false;
        }

        $return = array();
        $member_mod  = & m('member');
        $_special_code_mod = & m('special_code');
        $user_info=$this->visitor->get();

        $user_id = $user_info['user_id'];

        $info = $_special_code_mod->find(array(
            'index_key' =>'',
            'conditions'=>'cate>19 and is_used=1 and to_id="'.$user_info['user_id'].'"',
        ));

        if(empty($info)){
            $this->assign('kuka_info','');
            return false;

        }
        foreach($info as $k=>$v){
            $info[$k]['cate_name'] = $v['name'];
            $info[$k]['description'] = $v['content'];
        }
        $this->assign('kuka_info',$info);

    } */
    
    /**
     *  麦富迪e卡 详细信息
     *
     * @param  array $arr 消息数组信息
     *
     * @return  string
     */
    function kukainfo($con){
        //没有登陆
      
        if (!$this->visitor->has_login)
        {
            $this->assign('kuka_info','暂无数据');
            return false;
        }
        $member = m('member');
        $_special_code_mod = & m('special_code');
        $_kukaconfig_mod   = & m('kukaconfig');
        $user_info=$this->visitor->get();
        $user_id = $user_info['user_id'];
    
        $path = ROOT_PATH."/data/config/special_code.php";
        file_exists($path) && $this->_cate = include $path;
    
        //$con = " and to_id = '{$user_id}' ";//默认消费者
        if($user_info['member_lv_id'] >= 2 ) {//创业者
            $con .= " and from_id = '{$user_id}'";
        }
        //var_dump($con);exit();
        //cate >=20  暂时
        $page    =   $this->_get_page(6);
        $info = $_special_code_mod->findAll(array(
            'index_key' =>'',
            'conditions'=>"cate>19 ".$con,
            'limit' => $page['limit'],
            'count'       => true,
            'order' => "add_time DESC",
        ));
        
        if($info){
    
            foreach($info as $k=>$v){
                $info[$k]['donation_mes'] = '';
                $info[$k]['cate_name']    = '';
                $info[$k]['expire_time'] = date("Y-m-d",$v['expire_time']);
                $info[$k]['work_num'] = _format_price_int($v['work_num']);
                $info[$k]['price']    = _format_price_int($v['price']);
                if($v['expire_time'] - time() > 0) {
                    $days = floor(($v['expire_time'] - time())/86400)+1;
                    $info[$k]['day'] = $days;
                }
                //=====  获取品类名称  =====
                if ($v['name'])
                {
                    $info[$k]['cate_name'] = $v['name'];
                }
                else
                {
                    if($info[$k]['cate'] = '22')
                    {
                        $kuka_online = $_kukaconfig_mod->get("id = '{$v['log_id']}'");
                        if ($kuka_online['kuka_name'])
                        {
                            $info[$k]['cate_name'] = $kuka_online['kuka_name'];
                        }
                        else
                        {
                            $info[$k]['cate_name'] = "";
                        }
                    }
                    else
                    {
                        $info[$k]['cate_name'] = $this->_cate[$v['cate']]['name'];
                    }
                }
                if ($v['content'])
                {
                    $info[$k]['description'] = $v['content'];
                }
                else
                {
                    $info[$k]['description'] = $this->_cate[$v['cate']]['description'];
                }
    
                $to_id = trim($v['to_id']);
                if($v['from_id'] && $to_id ) {//已转赠
                    if($user_info['user_id'] == $v['from_id'] && !empty($to_id) ) {//转赠人回去抵用券信息
                        $memberinfo = $member->get($to_id);
                        $info[$k]['donation_mes'] = '已转赠给'.$memberinfo['nickname'];
                    }
                    if($user_info['user_id'] == $v['to_id']  ) {//被转赠人回去抵用券信息
                        $memberinfo = $member->get($v['from_id']);
                        $info[$k]['donation_mes'] = '赠送者：'.$memberinfo['nickname'];
                    }
                }
    
            }
    
        }
         
    
        unset($info[0]['cate']);
        $page['item_count'] = $_special_code_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $now_time = time();
        // 未使用
        $unused = $_special_code_mod->get(array(
            'conditions'=>"cate>19  AND to_id ='' AND from_id = '{$user_id}' AND expire_time>'{$now_time}' ",
            'fields'       => 'count(*) as unused_number',
        ));
        // 已使用
        $hasused = $_special_code_mod->get(array(
            'conditions'=>"cate>19  AND to_id !='' AND from_id = '{$user_id}'  AND expire_time>'{$now_time}' ",
            'fields'       => 'count(*) as hasused_number',
        ));
        // 已过期
      
        $hasovered = $_special_code_mod->get(array(
            'conditions'=>"cate>19  AND expire_time< '{$now_time}' AND from_id = '{$user_id}'",
            'fields'       => 'count(*) as has_over',
        ));
        
        $this->assign('unused', $unused['unused_number']);
        $this->assign('hasused', $hasused['hasused_number']);
        $this->assign('hasover', $hasovered['has_over']);
        $this->assign('info', $info);
         
    }
    

    
    /**
     *  麦富迪e卡 购买列表
     *
     * @param  array $arr 消息数组信息
     *
     * @return  string
     */
    
    function buy_kuka()
    {
        $user_id = $this->visitor->get('user_id');
    
        $_speciallist = $this->_mod->findAll(array(
            'index_key'  => '',
            'conditions' => "is_show = 1 and expire_time > ".time()." ",
            'fields'     => 'id, kuka_name, kuka_num, sale_price, expire_time, add_time ,is_show,content',
            'order'      => "id desc",
        ));
        foreach ($_speciallist as $k=>$v){
            $_speciallist[$k]['kuka_num'] = _format_price_int($v['kuka_num']);
            $_speciallist[$k]['sale_price'] = _format_price_int($v['sale_price']);
        }
    
        $this->assign('list', $_speciallist);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        $this->_config_seo('title', Lang::get('member_center') . ' - 酷卡购买');
        $this->display('buy_kuka.index.html');
    }
    
    /**
     *  麦富迪e卡 消费者列表 因为 创业者 和消费者 酷卡列表 不同 在这里做了区分
     *
     * @param  array $arr 消息数组信息
     *
     * @return  string
     */
    
    function customer_kuka()
    {
        if (!$this->visitor->has_login)
        {
            $this->assign('kuka_info','暂无数据');
            return false;
        }
        $member = m('member');
        $_special_code_mod = & m('special_code');
        $_kukaconfig_mod   = & m('kukaconfig');
        $user_info=$this->visitor->get();
        $user_id = $user_info['user_id'];
        
        $path = ROOT_PATH."/data/config/special_code.php";
        file_exists($path) && $this->_cate = include $path;
        
        $arg = $this->get_params();
        $status = array('wjh', 'yjh','ygq');
        $con .= " AND to_id = '{$user_id}' ";//默认消费者
        if($user_info['member_lv_id'] < 2 ) {//消费者 
            $now_time = time();
            if(in_array($arg[1], $status)){
                switch ($arg[1]){
                    case "wjh":
                        $con .= " AND is_used= 0 AND expire_time>'{$now_time}'";
                        break;
                    case "yjh":
                        $con .= " AND is_used=1 AND expire_time>'{$now_time}'";
                        break;
                    case "ygq":
                        $con .= " AND expire_time<'{$now_time}' ";
                        break;
                }
            }
           
        }
        //cate >=20  暂时
        $page    =   $this->_get_page(3);
        $info = $_special_code_mod->findAll(array(
            'index_key' =>'',
            'conditions'=>"cate>19 ".$con,
            'limit' => $page['limit'],
            'count'       => true,
            'order' => "add_time DESC",
        ));
        
        if($info){
        
            foreach($info as $k=>$v){
                $info[$k]['donation_mes'] = '';
                $info[$k]['cate_name']    = '';
                $info[$k]['expire_time'] = date("Y-m-d",$v['expire_time']);
                if($v['expire_time'] - time() > 0) {
                    $days = floor(($v['expire_time'] - time())/86400)+1;
                    $info[$k]['day'] = $days;
                }
                //=====  获取品类名称  =====
                if ($v['name'])
                {
                    $info[$k]['cate_name'] = $v['name'];
                }
                else
                {
                    if($info[$k]['cate'] = '22')
                    {
                        $kuka_online = $_kukaconfig_mod->get("id = '{$v['log_id']}'");
                        if ($kuka_online['kuka_name'])
                        {
                            $info[$k]['cate_name'] = $kuka_online['kuka_name'];
                        }
                        else
                        {
                            $info[$k]['cate_name'] = "";
                        }
                    }
                    else
                    {
                        $info[$k]['cate_name'] = $this->_cate[$v['cate']]['name'];
                    }
                }
                if ($v['content'])
                {
                    $info[$k]['description'] = $v['content'];
                }
                else
                {
                    $info[$k]['description'] = $this->_cate[$v['cate']]['description'];
                }
        
                $to_id = trim($v['to_id']);
                if($v['from_id'] && $to_id ) {//已转赠
                    if($user_info['user_id'] == $v['from_id'] && !empty($to_id) ) {//转赠人回去抵用券信息
                        $memberinfo = $member->get($to_id);
                        $info[$k]['donation_mes'] = '已转赠给'.$memberinfo['nickname'];
                    }
                    if($user_info['user_id'] == $v['to_id']  ) {//被转赠人回去抵用券信息
                        $memberinfo = $member->get($v['from_id']);
                        $info[$k]['donation_mes'] = '赠送者：'.$memberinfo['nickname'];
                    }
                }
        
            }
        
        }
        unset($info[0]['cate']);
        $page['item_count'] = $_special_code_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('info', $info);
        $this->_config_seo('title','我的麦富迪 - 我的酷卡');
        $this->assign('app', APP);
        $this->assign("status", $arg[1]);
        $this->display('kuka.customer.html');
        
        
    }
    
    /**
     *  酷卡推荐用户列表
     *
     * @param  array $arr 消息数组信息
     *
     * @return  string
     */
    
    
    function getInviteUser()
    {   
        if(!$_POST){
            $sn = $_GET['sn'];
            if(!$sn){
                return false;
            }
            $meminvite_mod = &m('memberinvite');
            $this->_debit_mod = &m("debit");
            $this->_gift_mod = &m("gift");
            $img_url = LOCALHOST1."/";
            $user_info=$this->visitor->get();
            $user_id = $user_info['user_id'];
       
            $page    =   $this->_get_page(3);
            //模糊查询
            $conditions = '';
            if($fliter) {
                $conditions = " and (member.nickname LIKE '%$fliter%' || member.user_name LIKE '%$fliter%' )";
            }
        
            $list = $this->_member_mod->find(array(
                'conditions'=> 'member_invite.inviter =' . $user_id . $conditions,
                'join'      => 'has_member',
                'fields'    => 'member_invite.inviter, member_invite.invitee, member_invite.add_time, member_invite.come_from, member.user_id, member.nickname, member.user_name, member.avatar, member.phone_mob, member.real_name',
                'order'     => 'member_invite.add_time DESC',
                'limit' => $page['limit'],
                'count'       => true,
            ));
       
     
            /* 头像 add by xiao5 START */
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();
            if($list)
            {
                foreach ($list as $key=> $val){
                  
                    $list[$key]['add_time'] =  date('y-m-d',$val['add_time']);
                    if($val['avatar'] == ''){
                         
                        $list[$key]['avatar'] = $objAvatar->avatar_show($val['invitee'], 'big');//头像
                    }else{
                        $list[$key]['avatar'] = SITE_URL.'upload/avatar/'.$val['avatar'];//头像
                    }
                
                
                    $gift_info = $this->_gift_mod->find(array(
                        'conditions'=>"user_id =".$val['inviter'],
                        'fields'    =>'money',
                        'index_key' => '',
                
                    ));
                
                
                    $result = array();
                    foreach ($gift_info as $keys=> $value) {
                        $result['money'] = $value['money'];
                    }
                    $list[$key]['money'] = isset($result['money'])? $result['money'] : 0;
                     
                    //获得当前顾客已获得购物券信息
                    $count_debit = $this->_debit_mod->get(array(
                        'conditions'=>"from_uid = {$user_id} AND user_id ={$val['invitee']}",
                        'fields'    =>'sum(money) as count_debit, count(id) as count_id',
                    ));
                    //未使用赠送券金额
                    $ucount_debit = $this->_debit_mod->get(array(
                        'conditions' => "from_uid = {$user_id} AND user_id ={$val['invitee']} and is_used = 0 ",
                        'fields'     => 'sum(money) as ucount_debit',
                    ));
                
                    $list[$key]['count_id']     = !empty($count_debit['count_id']) ? $count_debit['count_id'] : 0;//统计已转增的券数
                    $list[$key]['count_debit']  = _format_price_int($count_debit['count_debit']);//统计已转增的券总额
                    $list[$key]['ucount_debit'] = _format_price_int($ucount_debit['ucount_debit']);//统计已转增的未使用的券总额
                }
            }

            
            $page['item_count'] =   $this->_member_mod->getCount();
            $this->_format_page($page);
            $this->assign('page_info', $page);
        }else{
            //var_dump($_POST);die;
            $sn = $_POST['sncode'];
            $fliter = ($_POST['keyword'] == '用户名或手机号') ? '' : $_POST['keyword'];
            $meminvite_mod = &m('memberinvite');
            $this->_debit_mod = &m("debit");
            $this->_gift_mod = &m("gift");
            $img_url = LOCALHOST1."/";
            $user_info=$this->visitor->get();
            $user_id = $user_info['user_id'];
       
            $page    =   $this->_get_page(3);
            //模糊查询
            $conditions = '';
            if($fliter) {
                $conditions = " and (member.nickname LIKE '%$fliter%' || member.user_name LIKE '%$fliter%' )";
            }
        
            $list = $this->_member_mod->find(array(
                'conditions'=> 'member_invite.inviter =' . $user_id . $conditions,
                'join'      => 'has_member',
                'fields'    => 'member_invite.inviter, member_invite.invitee, member_invite.add_time, member_invite.come_from, member.user_id, member.nickname, member.user_name, member.avatar, member.phone_mob, member.real_name',
                'order'     => 'member_invite.add_time DESC',
                'limit' => $page['limit'],
                'count'       => true,
            ));
          
           
     
            /* 头像 add by xiao5 START */
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();
            if($list)
            {
                foreach ($list as $key=> $val){
                  
                    $list[$key]['add_time'] =  date('y-m-d',$val['add_time']);
                    if($val['avatar'] == ''){
                         
                        $list[$key]['avatar'] = $objAvatar->avatar_show($val['invitee'], 'big');//头像
                    }else{
                        $list[$key]['avatar'] = SITE_URL.'upload/avatar/'.$val['avatar'];//头像
                    }
                
                
                    $gift_info = $this->_gift_mod->find(array(
                        'conditions'=>"user_id =".$val['inviter'],
                        'fields'    =>'money',
                        'index_key' => '',
                
                    ));
                
                
                    $result = array();
                    foreach ($gift_info as $keys=> $value) {
                        $result['money'] = $value['money'];
                    }
                    $list[$key]['money'] = isset($result['money'])? $result['money'] : 0;
                     
                    //获得当前顾客已获得购物券信息
                    $count_debit = $this->_debit_mod->get(array(
                        'conditions'=>"from_uid = {$user_id} AND user_id ={$val['invitee']}",
                        'fields'    =>'sum(money) as count_debit, count(id) as count_id',
                    ));
                    //未使用赠送券金额
                    $ucount_debit = $this->_debit_mod->get(array(
                        'conditions' => "from_uid = {$user_id} AND user_id ={$val['invitee']} and is_used = 0 ",
                        'fields'     => 'sum(money) as ucount_debit',
                    ));
                
                    $list[$key]['count_id']     = !empty($count_debit['count_id']) ? $count_debit['count_id'] : 0;//统计已转增的券数
                    $list[$key]['count_debit']  = _format_price_int($count_debit['count_debit']);//统计已转增的券总额
                    $list[$key]['ucount_debit'] = _format_price_int($ucount_debit['ucount_debit']);//统计已转增的未使用的券总额
                }
            }

            
            $page['item_count'] =   $this->_member_mod->getCount();
            $this->_format_page($page);
            $this->assign('page_info', $page);

        }
       
        $this->assign('list', $list);
        $this->_config_seo('title','我的麦富迪 - 我的酷卡');
        $this->assign('kw',$_POST['keyword'] );
        $this->assign('app', APP);
        $this->assign('sn',$sn);
        $this->display('kuka.addinvite.html');
    
       
    }
    
    /**
     * 酷卡转赠
     *
     * @param string $token 用户token; int $spcode 要转赠的酷卡的唯一sn; int $to_userid 要转赠给对应用户的id;
     *
     * @access protected
     * @author tangshoujian <963830611@qq.com>
     * @return void
     */
    function kukaDonation()
    {
       
     
        $uid = isset($_POST['invitee']) ? intval($_POST['invitee']) : '';
        $spcode = isset($_POST['sn']) ? $_POST['sn'] : '';
     
        if(!$uid && !$sn){
            $this->json_error("参数错误");
            return ;
        }
      
        $_special_code_mod = & m('special_code');
        $now_time = time();
    
        $user_info=$this->visitor->get();
        $touser_info = $this->_member_mod->get($uid);
        
        if (!$user_info) {
         
            $this->json_error("帐号不存在");
            return ;
             
        }
        $user_id = $user_info['user_id'];
    
        if (!$touser_info) {
          
             $this->json_error("没有要转赠的顾客");
             return ;
             
        }
    
        //获得当前抵用券信息
        $special_code_info = $_special_code_mod->get(array(
            'conditions' => "cate > 19 and from_id = '{$user_id}' and is_used = 0 and sn = '{$spcode}' and to_id = '' and expire_time>{$now_time}",
        ));
    
        if (!$special_code_info) {
            $this->json_error("未发现此酷卡");
            return ;
        }
    
        $update_arr = array();
        $update_arr['to_id']     = $uid;//使用人id
        $update_arr['user_name'] = $touser_info['user_name'];//使用码的用户
    
        //执行转赠操作
        $_special_code_edit = $_special_code_mod->edit($special_code_info['id'], $update_arr);
        if($_special_code_edit){

            $this->json_result($_special_code_edit);
            return;
        }else{
            $this->json_error("转赠失败");
            return ;
        }
    
        //组装返回数据
     
      
    }
    
     
}

?>