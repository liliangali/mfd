<?php

class MeasureApp extends ShoppingbaseApp
{
    public $types = array("suit",'custom','diy');
    function __construct()
    {
        parent::__construct();
    }
    
	function test(){
	    $this->display('cart/measures/test.html');
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
        
        $this->display('cart/measures/index.html');
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
        
        $condition = " storeid = '{$this->_user_id}' ";
        
        if($val != ''){
            $condition = " (customer_mobile = '{$val}' OR customer_name = '{$val}') "; //(customer_mobile LIKE '%{$phone}%' OR customer_name LIKE '%{$phone}%')
        }
        $condition .= " AND figure_state = 1 ";
        $mCf  = &m('customer_figure');
        $list = $mCf->find($condition);
        
        foreach ($list as $key=>$row){
            $svs[$row['id_serve']] = $row['id_serve'];
        }
        $mServe = &m('serve');
        $serves = $mServe->find(db_create_in($svs,'idserve'));
        foreach ($list as $key=>$row){
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
        $content = $this->_view->fetch('cart/measures/customer.html');
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
         
        $regions = $this->getServeRegion();
        if (!$region_id || !array_key_exists($region_id, $regions)){
            $this->json_error('所选地区有误!');
            return;
        }
        $msv = &m('serve');
        $servers = $msv->find("region_id = '{$region_id}'");
        $this->assign('server_id',$server_id);
        $this->assign('servers',$servers);
        $content = $this->_view->fetch('cart/measures/server.html');
        $this->json_result(array('content'=>$content,'server'=>isset($servers[$server_id]) ? $server_id : ''));
        die();
    }
    
    /**
     * 根据地区获取可独立指定的量体师
     *
     * @date 2015-10-13 下午1:34:28
     * @author Ruesin
     */
    function ajaxFigurer(){
    
        $region_id   = isset($_POST['data']['region']) ? intval($_POST['data']['region']) : 0;
        $ck = isset($_POST['ck']) ? intval($_POST['ck']) : 0;
        
        if(!$region_id){
            $this->json_error('请选择正确的地区!');
            die();
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
    
        foreach ($server as $row){
            $dzIds[$row['userid']] = $row['userid'];
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
        $content = $this->_view->fetch('cart/measures/liangti.html');
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
        $content = $this->_view->fetch('cart/measures/history.html');
        $this->json_result(array('content'=>$content));
        die();
    }

    ####################################################################
    ######################## Diy 量体前置 End ###########################
    ####################################################################
}

