<?php

class MeasureApp extends FrontendApp
{
    public $types = array("suit",'custom','diy');
    public $_measure_way = array(/*'1'=>'预约上门量体',*/'2'=>'去附近门店量体','5'=>'现有量体数据','6'=>'指定量体师',/* '3'=>'现有量体数据' *//*,'4'=>'标准尺码'*/);//标准尺码没算法  先屏蔽
    function __construct()
    {
        $this->_mod_cart = &m('cart');
        parent::__construct();
        if($this->visitor->has_login){
            $this->_user_id = $this->visitor->get('user_id');
        }
    }
    
    function _run_action()
    {
        $postActs = array();
        if(!IS_POST && in_array(ACT, $postActs)){
            return ;
        }
        $guestActs = array();
        if (!$this->visitor->has_login && !in_array(ACT, $guestActs)){
            $this->json_error('login_please','login');
            return;
        }
        parent::_run_action();
    }
    
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
        $this->_view->template_dir = ROOT_PATH . "/view/".LANG."/mall/{$template_name}";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }
	function test(){
	    $this->display('cart/measure/test.html');
	}
    /**
     * 量体方式列表
     * 
     * @date 2015-10-13 上午9:50:46
     * @author Ruesin
     */
    function index(){
        
        if(!$_GET['fn'] || !$_GET['cid'])
            die("<script>alert('params error!');</script>");
        
        $cid = explode('|', $_GET['cid']);
        
        ## 品类及尺码
        imports("diys.lib");
        $oLogs = new Diys();
        $cloths = $oLogs->_customs();
        foreach((array)$cid as $val){
            if(!isset($cloths[$val]))
                die("<script>alert('params_error!');</script>");
            $cloth[$val]['name'] = $cloths[$val]['cate_name'];
            
            $filename = ROOT_PATH. '/data/size_json/'.$val.'_10205.size.json';
            if (file_exists($filename)){
                $jsonData = json_decode(file_get_contents($filename),1);
                $cloth[$val]['size'] = $jsonData['sizeAll'];
            }
        }
        $this->assign('cloth',$cloth);
        ## 量体方式列表
        $measure_way = $this->_measure_way;
        $measure_way['-1'] = '操作记录';
        $this->assign('measurelist',$measure_way);
        
        ##服务地区
        $regions = $this->getServeRegion();
        $this->assign('region',$regions);
        foreach ($regions as $row){
            $rIds[$row['region_id']] = $row['region_id'];
        }
        $mServe = &m('serve');
        $server = $mServe->find(array(
                'conditions' => db_create_in($rIds,'region_id') . "  AND shop_type IN (1,2)",
                'index_key'  => 'region_id'
        ));
        foreach ($regions as $key=>$row){
            if(!$server[$key]){
                unset($regions[$key]);
            }
        }
        
        $this->assign('ltregion',$regions);
        
        $this->assign('fn',$_GET['fn']);
        
        $this->display('cart/measure/index.html');
    }
    
    /**
     * 获取当前用户的顾客量体信息
     * 
     * @date 2015-10-14 上午8:59:57
     * @author Ruesin
     */
    function ajaxCustomer(){
        
        $val   = isset($_POST['val']) ? trim($_POST['val']) : '';
        $ckid  = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $member_mod = &m("member");
        $userinfo = $member_mod->get($this->visitor->get("user_id"));
        $user_id = $userinfo['user_id'];
        $user_name = $userinfo['user_name'];
        $membner_lv_id = $userinfo['member_lv_id'];
        if ($membner_lv_id == 1) //=====  消费者  =====
        {
            $member_invite_mod = m('memberinvite');
            $invite_info = $member_invite_mod->get("invitee = $user_id AND type=0 ");
            if ($invite_info)
            {
                $inviter_id = $invite_info['inviter'];
                $invi_info = $member_mod->get_info($inviter_id);
                $invi_member_lv_id = $invi_info['member_lv_id'];
            }
        }
        
        
        $condition = " storeid = '{$this->_user_id}' ";
        
        if($val != ''){
            $condition = "  (customer_mobile = '{$val}' OR customer_name = '{$val}') "; //(customer_mobile LIKE '%{$phone}%' OR customer_name LIKE '%{$phone}%')
        }
        $condition .= " AND figure_state = 1 AND  firsttime > 1447171200";
        $mCf  = &m('customer_figure');
        $list = $mCf->find($condition);
        
        foreach ($list as $key=>$row){
            $svs[$row['id_serve']] = $row['id_serve'];
        }
        $mServe = &m('serve');
        $serves = $mServe->find(db_create_in($svs,'idserve'));
        foreach ($list as $key=>$row){
            if ($membner_lv_id == VIP_ID || $invi_member_lv_id == VIP_ID)
            {
                if ($row['is_first'] == 0 && $row['service_mode'] == 3)
                {
                    //=====  根据手机号匹配   =====
                    $result['list'][$key]['is_free'] = 1;
                    if ($servers[$row["id_serve"]]['storetype'] == 2)
                    {
                        $result['list'][$key]['is_free'] = 0;
                    }
            
                }
                else
                {
                    $result['list'][$key]['is_free'] = 0;
                }
            }
            else
            {
                $result['list'][$key]['is_free'] = 0;
            }
            
           /*  $result['list'][$key]['is_free'] = 0;
            if (!$row['is_first'] && $row['service_mode'] == 3)
            {
                $result['list'][$key]['is_free'] = 1;
            } */
            $result['list'][$key]['id'] = $key;
            $result['list'][$key]['name'] = $row['customer_name'];
            $result['list'][$key]['phone'] = $row['customer_mobile'];
            $result['list'][$key]['liangti_id'] = $row['liangti_id'];
            $result['list'][$key]['liangti_name'] = $row['liangti_name'];
            $result['list'][$key]['serve_name'] = $serves[$row['id_serve']]['serve_name'];
            $result['list'][$key]['serve_id'] = $row['id_serve'];
        }
        $this->assign('check',$ckid);
        $this->assign('data',$result);
        $content = $this->_view->fetch('cart/measure/customer.html');
        $this->json_result(array('content'=>$content,'check'=>isset($result['list'][$ckid])?$ckid:''));
        die();
    }
    
    /**
     * 门店
     *
     * @date 2015-10-13 上午10:49:55
     * @author Ruesin
     */
    function ajaxServer(){
    
        $region_id = isset($_POST['id']) ? intval($_POST['id']) : '';
        $server_id = isset($_POST['sv']) ? intval($_POST['sv']) : 0;
        $user_info = $this->visitor->get();
        $user_id = $user_info['user_id'];
        $user_name = $user_info['user_name'];
        $membner_lv_id = $user_info['member_lv_id'];
        $member_mod = m('member');
        if ($membner_lv_id == 1) //=====  消费者  =====
        {
            $member_invite_mod = m('memberinvite');
            $invite_info = $member_invite_mod->get("invitee = $user_id AND type=0 ");
            if ($invite_info)
            {
                $inviter_id = $invite_info['inviter'];
                $invi_info = $member_mod->get_info($inviter_id);
                $invi_member_lv_id = $invi_info['member_lv_id'];
            }
        }
        
        $regions = $this->getServeRegion();
        if (!$region_id || !array_key_exists($region_id, $regions)){
            $this->json_error('所选地区有误!');
            return;
        }
        $msv = &m('serve');
        $servers = $msv->find(array(
            'conditions' => "region_id = '{$region_id}'",
             'order'      => "idserve DESC,storetype DESC",    
        ));
        
        if ($servers)
        {
            foreach ($servers as $key => $value)
            {
                
                if ($membner_lv_id == VIP_ID || $invi_member_lv_id == VIP_ID)
                {
                    $servers[$key]['is_free'] = 1;
                    if ($value['storetype'] == 2) //=====    自营门店  =====
                    {
                        $servers[$key]['is_free'] = 0;
                       /*  if (($value['mobile'] == $user_name) || ($value['mobile'] == $invi_info['user_name']))
                        {
                            $servers[$key]['is_free'] = 0;
                        }
                        else
                        {
                            unset($servers[$key]);
                        } */
                    }
                }
                else
                {
                    $servers[$key]['is_free'] = 0;
                    if ($value['storetype'] == 2) //=====    自营门店  =====
                    {
                      //  unset($servers[$key]);
                    }
                }
                /* $servers[$key]['is_free'] = 1;
                if ($value['storetype'] == 2) //=====    自营门店  =====
                {
                    //=====  根据手机号匹配   =====
                    if (($membner_lv_id == VIP_ID && $value['mobile'] == $user_name) || ($invi_member_lv_id == VIP_ID && $value['mobile'] == $invi_info['user_name']))
                    {
                        $servers[$key]['is_free'] = 0;
                    }
                    else
                    {
                        unset($servers[$key]);
                    }
                } */
        
            }
        }
        $this->assign('server_id',$server_id);
        $this->assign('servers',$servers);
        $content = $this->_view->fetch('cart/measure/server.html');
        $this->json_result(array('content'=>$content,'server'=>isset($servers[$server_id]) ? $server_id : ''));
        die();
    }
    /**
    *量体师
    *@author liang.li <1184820705@qq.com>
    *@2016年1月26日
    */
    function ajaxFigurer(){
        $region_id   = isset($_POST['data']['region']) ? intval($_POST['data']['region']) : 0;
        $ck = isset($_POST['ck']) ? intval($_POST['ck']) : 0;
        if(!$region_id){
            $this->json_error('请选择正确的地区!');
            die();
        }
        
        $user_info = $this->visitor->get();
        $user_id = $user_info['user_id'];
        $user_name = $user_info['user_name'];
        $membner_lv_id = $user_info['member_lv_id'];
        if ($membner_lv_id == 1) //=====  消费者  =====
        {
            $member_invite_mod = m('memberinvite');
            $member_mod = m('member');
            $invite_info = $member_invite_mod->get("invitee = $user_id AND type=0 ");
            if ($invite_info)
            {
                $inviter_id = $invite_info['inviter'];
                $invi_info = $member_mod->get_info($inviter_id);
                $invi_member_lv_id = $invi_info['member_lv_id'];
            }
        }
        $serve_mod = &m("serve");
        $keyword = isset($_GET['keyword']) ? trim(htmlspecialchars($_GET['keyword'])) : '';
        $conditions='';
        if(!empty($keyword)){
            $conditions = " AND (member.real_name = '{$keyword}' OR member.phone_mob = '{$keyword}')";
        }
    
        $servelist = $serve_mod->find(array(
            "conditions" => "region_id='$region_id'",
            'index_key' => 'userid',
            'order' => "storetype DESC,idserve DESC",
        ));
        $title;
        $userids = array();
        $address = array();
        foreach($servelist as $key => $val){
            if ($membner_lv_id == VIP_ID || $invi_member_lv_id == VIP_ID)
            {
                $servelist[$key]['is_free'] = 1;
                if ($val['storetype'] == 2) //=====    自营门店  =====
                {
                    $servelist[$key]['is_free'] = 0;
                   /*  if (($val['mobile'] == $user_name) || ($val['mobile'] == $invi_info['user_name']))
                    {
                        $servelist[$key]['is_free'] = 0;
                    }
                    else
                    {
                        unset($servelist[$key]);
                    } */
                }
            }
            else
            {
                $servelist[$key]['is_free'] = 0;
                if ($val['storetype'] == 2) //=====    自营门店  =====
                {
                   // unset($servelist[$key]);
                }
            }
           /*  $servelist[$key]['is_free'] = 1;
            if ($val['storetype'] == 2) //=====    自营门店  =====
            {
                //=====  根据手机号匹配   =====
                if (($membner_lv_id == VIP_ID && $val['mobile'] == $user_name) || ($invi_member_lv_id == VIP_ID && $val['mobile'] == $invi_info['user_name']))
                {
                    $servelist[$key]['is_free'] = 0;
                }
                else
                {
                    unset($servelist[$key]);
                }
            } */
    
            if(!empty($servelist[$key]["region_id"])){
                $address[$val['userid']] = $val["serve_address"]; //店长+地址
                $userids[] = $val["userid"];
            }
        }
    
        $region_mod = &m("region");
    
        $rinfo = $region_mod->get("region_id='{$data['region']}'");
    
        //$title = $rinfo["region_name"];
        $mRegion = &m('region');
        $title = $mRegion->get($region_id);
        
    
        $member_mod = &m("member");
        //量体师
        $members = $member_mod->find(array(
            "conditions" =>"figure_liangti.alone=1 AND figure_liangti.manager_id ".db_create_in($userids).$conditions,
            'join'       => "has_lt",
            'fields'     => "member.real_name, member.user_id, member.phone_mob, figure_liangti.manager_id"
        ));
        //加个店长，需求变更，只能这么解决了
        $managers = $member_mod->find(array(
            "conditions" => "alone=1 AND user_id ".db_create_in($userids).$conditions,
        ));
        $rData = array();
        foreach((array)$members as $key => $val){
            $val['address'] = $address[$val["manager_id"]];
            $val['is_free'] = $servelist[$val['manager_id']]['is_free'];
            $val['money_name'] = $servelist[$val['manager_id']]['money_name'];
            $rData[] = $val;
        }
        foreach((array) $managers as $key => $val){
            $val['address'] = $address[$val["user_id"]];
            $val['is_free'] = $servelist[$key]['is_free'];
            $val['money_name'] = $servelist[$key]['money_name'];
            $rData[] = $val;
        }
        $sort = array(
            'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'is_free',       //排序字段
        );
        $arrSort = array();
        foreach($rData AS $uniqid => $row)
        {
            foreach($row AS $key=>$value)
            {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction'])
        {
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $rData);
        }
        $this->assign('check',$ck);
        $this->assign('liangtis',$rData);
        $content = $this->_view->fetch('cart/measure/liangti.html');
        $this->json_result(array('region'=>$title,'content'=>$content,'check'=>isset($rData['liangtis'][$ck]) ? $ck : '' ));
        die();
    
        $this->json_result(array("content" => $rData, "title" => $title, "assign" => $data));
        die();
    }
    
    /**
     * 根据地区获取可独立指定的量体师
     *
     * @date 2015-10-13 下午1:34:28
     * @author Ruesin
     */
    function ajaxFigurers(){
    
        $region_id   = isset($_POST['data']['region']) ? intval($_POST['data']['region']) : 0;
        $ck = isset($_POST['ck']) ? intval($_POST['ck']) : 0;
        
        if(!$region_id){
            $this->json_error('请选择正确的地区!');
            die();
        }
        $user_info = $this->visitor->get();
        $user_id = $user_info['user_id'];
        $user_name = $user_info['user_name'];
        $membner_lv_id = $user_info['member_lv_id'];
        if ($membner_lv_id == 1) //=====  消费者  =====
        {
            $member_invite_mod = m('memberinvite');
            $member_mod = m('member');
            $invite_info = $member_invite_mod->get("invitee = $user_id AND type=0 ");
            if ($invite_info)
            {
                $inviter_id = $invite_info['inviter'];
                $invi_info = $member_mod->get_info($inviter_id);
                $invi_member_lv_id = $invi_info['member_lv_id'];
            }
        }
    
        $mRegion = &m('region');
        $result['region'] = $mRegion->get($region_id);
    
        $mServe = &m('serve');
        $server = $mServe->find(array(
                'conditions' => "region_id = '{$region_id}' AND shop_type IN (1,2)", //AND virtual = '0'
                'index_key'  => 'userid',
        ));
        if(!$server){
            $this->json_error('该地区下无门店!');
            die();
        }
    
        foreach ($server as &$val){
            $val['is_free'] = 1;
            if ($val['storetype'] == 2) //=====    自营门店  =====
            {
                //=====  根据手机号匹配   =====
                if (($membner_lv_id == VIP_ID && $val['mobile'] == $user_name) || ($invi_member_lv_id == VIP_ID && $val['mobile'] == $invi_info['user_name']))
                {
                    $val['is_free'] = 0;
                }
                else
                {
                    unset($val);
                }
            }
            if ($val) 
            {
                $dzIds[$val['userid']] = $val['userid'];
            }
        }
    
        $lts = $dzs = array();
        
        $mLiangti =& mr('figure_liangti');
        $lts = $mLiangti->find(array(
                'conditions' => db_create_in($dzIds,'manager_id') . " AND alone = '1' ",
                'index_key'  => 'liangti_id',
        ));
        
        $mMember = &m('member');
        $dzs = $mMember->find(array(
                'conditions' => db_create_in($dzIds,'user_id') . " AND alone = '1'  AND figure_type = '1' AND serve_type = '2' ",
                'index_key'  => 'user_id',
        ));
        
        //$liangtis = array_merge($liangti,$dzs);
        $liangti = $lts;
        if($dzs){
            foreach ($dzs as $key=>$row){
                $row['manager_id'] = $row['liangti_id'] = $key;
                $liangti[$key] = $row;
            }
        }
        
        if(!$liangti){
            $this->json_error('该地区下无可指定量体师!');
            die();
        }
    
        foreach ($liangti as $row){
            $uIds[$row['liangti_id']] = $row['liangti_id'];
            $svIds[$row['manager_id']] = $row['manager_id'];
        }
    
        $mServe = &m('serve');
        $serve = $mServe->find(array(
                'conditions' => db_create_in($svIds,'userid') . "   AND shop_type IN (1,2)", //AND virtual = '0'
                'index_key'  => 'userid',
        ));
    
        $users = $mMember->find(array(
                'conditions' => db_create_in($uIds,'user_id'),
        ));
    
        foreach ($liangti as $rows){
            //$row['info'] = $users[$row['liangti_id']];
            //$row['seve'] = $serve[$row['manager_id']];
            if(isset($users[$rows['liangti_id']]) && isset($serve[$rows['manager_id']])){
                $liangtis[$rows['liangti_id']] = $rows;
                $liangtis[$rows['liangti_id']]['info'] = $users[$rows['liangti_id']];
                if ($users[$rows['liangti_id']]['avatar']){
                    $liangtis[$rows['liangti_id']]['info']['avatar'] = SITE_URL.$users[$rows['liangti_id']]['avatar'];
                }
                $liangtis[$rows['liangti_id']]['seve'] = $serve[$rows['manager_id']];
            }
        }
        foreach($liangtis as $key=>$row){
            $result['liangtis'][$key]['realname'] = $row['info']['real_name'];
            $result['liangtis'][$key]['nickname'] = $row['info']['nickname'];
            //$result['liangtis'][$key]['user_name'] = $row['info']['user_name'];
            $result['liangtis'][$key]['phone_mob'] = $row['info']['phone_mob'];
            //$result['liangtis'][$key]['phone_tel'] = $row['info']['phone_tel'];
            $result['liangtis'][$key]['avatar'] = $row['info']['avatar'];
            $result['liangtis'][$key]['serve_name'] = $row['seve']['serve_name'];
            $result['liangtis'][$key]['address'] = $row['seve']['serve_address'];
            //$result['liangtis'][$key]['store_mobile'] = $row['serve']['store_mobile'];
            //$result['liangtis'][$key]['linkman'] = $row['serve']['linkman'];
            $result['liangtis'][$key]['serve_id'] = $row['seve']['idserve'];
        }
        $this->assign('check',$ck);
        $this->assign('liangtis',$result['liangtis']);
        $content = $this->_view->fetch('cart/measure/liangti.html');
        $this->json_result(array('region'=>$result['region'],'content'=>$content,'check'=>isset($result['liangtis'][$ck]) ? $ck : '' ));
        die();
    }
    
    function saveData(){
        $data = $_POST['data'];
        $data['figuretype'] = $_POST['figuretype'];
        
        if($data['figuretype'] == '-1'){
            if($this->getHistory($data) !== true){
                $this->json_error('操作记录有误!');
                die();
            }
        }
        
        $this->json_result($data);
        die();
    }
    
    function getHistory(&$data){
        if ($data['history'] != '1' || !isset($data['history_id'])){
            return '量体方式错误';
        }
        $history = json_decode(base64_decode($_COOKIE["suit_history"]),1);
        if (!isset($history[$data['history_id']])){
            return '操作记录不存在';
        }
        $check = $history[$data['history_id']];
        
        $data = array_merge($data,$check);  //暂时不做严格验证了 这里请求太多 太耗资源 放后面再验证
        
        /* if($check['figuretype'] == 5){
            if (!isset($check["figureid"]) || !$check["figureid"]){
                return '操作记录有误';
            }
            //$mCf = &m("customer_figure");
            //$customer = $mCf->get("figure_sn = '{$check["figureid"]}'");
            $data['figuretype'] = 5;
            $data['figureid']   = $check["figureid"];
        }elseif ($check['figuretype'] == 2){
            $data = array_merge($data,$check);
        }elseif ($check['figuretype'] == 6){
            $data = array_merge($data,$check);
        } */
        return true;
    }
    
    protected function getServeRegion(){
        $region_mod = m('region');
        $region_list = $region_mod->find("parent_id > 2 AND is_serve = 1"); //这里有问题  直辖市下的服务点定位在区里的 如果下拉只出最后一个，那么会只出个区的
        $zxs = array(3=>'北京市',22=>'天津市',41=>'上海市',61=>'重庆市');
        foreach ($region_list as &$row){
            if(isset($zxs[$row['parent_id']])){
                $row['region_name'] = $zxs[$row['parent_id']] .' '.$row['region_name'];
            }
        }
        return $region_list;
    }
    
    /**
     * ajax选择预约记录
     * 
     * @date 2015-10-13 上午9:57:51
     * @author Ruesin
     */
    function ajaxHistory(){
        //$str = 'W3sidHlwZSI6InN1aXQiLCJmaWd1cmV0eXBlIjo1LCJmaWd1cmVpZCI6MTAwOX0seyJ0eXBlIjoic3VpdCIsImZpZ3VyZXR5cGUiOjIsInJlZ2lvbiI6MjQ4LCJyZWFsbmFtZSI6Iua1i+ivleWImOW5v+S/oSIsInBob25lIjoxMzExMTExMTExMSwiZGF0ZWxpbmUiOiIyMDE1LTEwLTA2IiwidGltZXBhcnQiOiJhbSIsInNlcnZlaWQiOjE2MiwicmVnaW9uX25hbWUiOiLpnZLlspsifSx7InR5cGUiOiJzdWl0IiwiZmlndXJldHlwZSI6NiwicmVnaW9uIjoyNDgsImZpZ3VyZXJpZCI6NTQ1LCJyZWFsbmFtZSI6Iua1i+ivleWImOW5v+S/oSIsInBob25lIjoiMTMxMTExMTExMTEiLCJkYXRlbGluZSI6IjIwMTUtMTAtMDYiLCJ0aW1lcGFydCI6ImFtIiwiYWRkcmVzcyI6IjMzMzMzMzMzMzMiLCJyZWdpb25fbmFtZSI6IumdkuWymyJ9XQ==';
        //$history = json_decode(base64_decode($str),1);
        $history = json_decode(base64_decode($_COOKIE["suit_history"]),1);
        
        
        if(empty($history)){
            $this->json_error('暂无操作记录');
            die();
        }
        $his = $svs = $lts = array();
        foreach((array)$history as $key => $val){
            if($val['figuretype'] == 5){
                $his[] = $val["figureid"];  //已有id
            }elseif($val["figuretype"] == 2){
                $svs[] = $val["server_id"];    //门店id
            }elseif($val['figuretype'] == 6){
                $lts[] = $val['figurerid'];  //量体师
            }
        }
        
        //已有量体
        $aData = array();
        
        if (!empty($his)){
            $mCf = &m("customer_figure");
            $cstFigure = $mCf->find(db_create_in($his,'figure_sn'));
            foreach((array)$cstFigure as $key => $val){
                $svs[$val["id_serve"]]   = $val["id_serve"];
            }
        }
        
        $mServe = &m("serve");
        $servers = $mServe->find(db_create_in($svs,'idserve'));
        
        foreach((array)$cstFigure as $key=>$row){
            $cstFigure[$key]['serve_name'] = isset($servers[$row["id_serve"]]) ? $servers[$row["id_serve"]]['serve_name'] : '';
        }
        
        $mLiangti =& mr('figure_liangti');
        $liangti = $mLiangti->find(db_create_in($lts,'liangti_id') ." AND alone = '1' ");
        
        foreach ($liangti as $key=>$row){
            $mngs[$row['manager_id']] = $row['manager_id'];
            $liangtishi[$row['liangti_id']] = $row;
        }
        
        $mServe = &m('serve');
        $server = $mServe->find(db_create_in($mngs,'userid') . " AND shop_type IN (1,2)"); // AND virtual = '0' // AND region_id = '{$data['region_id']}'
        foreach ($server as $row){
            $serveByManage[$row['userid']] = $row;
        }
        foreach ($history as $key=>$row){
            if($row['figuretype'] == 5){
                $result['history'][$key]['name']   = $cstFigure[$row['figureid']]['customer_name'];
                $result['history'][$key]['phone']  = $cstFigure[$row['figureid']]['customer_mobile'];
                $result['history'][$key]['region'] = $cstFigure[$row['figureid']]['serve_name'];
            }elseif($row["figuretype"] == 2){
                $result['history'][$key]['name']   = $row['realname'];
                $result['history'][$key]['phone']  = $row['phone'];
                $result['history'][$key]['region'] =  $servers[$row['server_id']]['serve_name'];
            }elseif($row['figuretype'] == 6){
                $result['history'][$key]['name']   = $row['realname'];
                $result['history'][$key]['phone']  = $row['phone'];
                $result['history'][$key]['region'] =  $serveByManage[$liangtishi[$row['figurerid']]['manager_id']]['serve_name'];
            }
        }
        $this->assign('data',$result['history']);
        $content = $this->_view->fetch('cart/measure/history.html');
        $this->json_result(array('content'=>$content));
        die();
    }

    ####################################################################
    ######################## Diy 量体前置 End ###########################
    ####################################################################
}

