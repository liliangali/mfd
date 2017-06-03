<?php
/**
 * 定制需求前台控制类
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: demand.app.php 13292 2016-01-04 05:31:00Z nings $
 * @copyright Copyright 2014 redcollar
 */
class DemandApp extends DemandBase
{
    public $_mod_demanditem;
    public $_mod_demand;
    public $_mod_demandoffer;
    public $item_cate;
    var $_types;
    function __construct(){
        $this->DemandApp();
    }

    function DemandApp(){
        $this->_mod_demanditem = &m('demanditem');
        $this->_mod_demand = &m('demand');
        $this->_mod_demandoffer = &m('demandoffer');
        $this->_types = $this->_mod_demand->_types();
        $this->item_cate = array(
                1 => '主题风格',
                2 => '品类',
                3 => '面料',
                4 => '定制预算',
                5 => '尺寸号码',
        );
        parent::__construct();
    }

    /**
     * 需求详情页
     * @access public
     * @see index
     * @version 1.0.0 (2014-12-17)
     * @author Ruesin
     */
    function index(){

        $args = $this->get_params();
        $id  = isset($args[0]) ? intval($args[0]) : 0;
        if(!$id){
            $this->show_warning('params_error');
            return;
        }

        /* 需求信息 */
        $info = $this->_mod_demand->get($id);
        if(empty($info)){
            $this->show_warning('no_data');
            return ;
        }
        $demand = $this->_get_type($info['md_type']);
        $info   = $demand->_format_info($info);

        $mod_user =& m('member');
        $info['user'] = $mod_user->get(array(
                'conditions'    => "user_id = '{$info['user_id']}'",
                'join'          => 'has_store',                 //关联查找看看是否有店铺
                'fields'        => 'nickname,user_id, user_name, reg_time, last_login, last_ip, store_id, def_addr',
        ));
//         $info['user'] = $this->visitor->get();
        $info['adjunct'] = unserialize($info['adjunct']);

        require(ROOT_PATH . '/includes/avatar.class.php');
        $objAvatar = new Avatar();
        $info['user']['avatar'] = $objAvatar->avatar_show($info['user']['user_id'],'big');

        $offer = $this->_get_offer_list($info);

        //最新需求
        $newList = $this->_mod_demand->find(array(
                'conditions' => "1 = 1 AND status = 2 AND region_code = '{$_COOKIE['cityCode']}' order by views asc ",
                'limit'      => " 0 , 10 ",
        ));
        foreach($newList as &$row){
            $row['params'] = unserialize($row['params']);
        }
        //推荐裁缝
        $store_mod =& m('store');
        $tailor_list = $store_mod->find(array(
                'conditions' => 'state=1',
                'limit' => '10',
        ));
        $tailor_tree = array();
        $group = 1;
        $num = 0;
        $select=0;
        foreach($tailor_list as $key => $val){
            $num++;
            $tailor_tree[$group]['children'][$val["store_id"]] = $val;
            if($val['store_id'] == $article_id){
                $select = $group;
            }

            if($num%5 == 0) $group+=1;
        }
        $this->assign('tailor',$tailor_tree);

        $this->_mod_demand->edit(" md_id = $id",array('views'=>$info['views']+1));

//         echo '<pre>';
//         print_r($info);
        $info['views'] += 1;
        $this->assign('newList',$newList);
        $this->assign('offer',$offer);
        $this->assign('info',$info);
        $this->_config_seo('title', Lang::get('demand_info') . ' - '. Conf::get('site_title'));
        $this->display('demand/info.html');
    }

    /**
     * 需求列表页
     * @access public
     * @see lists
     * @version 1.0.0 (2014-12-17)
     * @author Ruesin
     */
    function lists($pg = 1){
        $args = $this->get_params();
        $pg    = isset($args[0]) ? intval($args[0]) ? intval($args[0]) : 1 : 1;
        $sort  = isset($args[1]) ? intval($args[1]) ? intval($args[1]) : 1 : 1;
        $lm = 10;

        $sc = isset($_GET['sc']) ? trim($_GET['sc']) : '';
        if($sc != ''){
            $ssql = " AND md_title LIKE '%".$sc."%'";
            $sckey = '?sc='.$sc;
        }
        if($sort == 2){
            $order_sort = ' order by add_time desc ';
        }elseif ($sort == 3){
            $order_sort = ' order by take_in desc ';
        }elseif($sort == 4){
        	 $order_sort = ' order by price_rank desc ';
        }
        else {
            $sort = 1;
            //$order_sort = ' order by md_id desc ';
            $order_sort = ' order by views desc ';
        }


        $list = $this->_mod_demand->find(array(
                'conditions' => "1 = 1 AND status = 2 AND region_code = '{$_COOKIE['cityCode']}' ".$ssql.$order_sort,
                'count' => true,
                'limit'      => ' '.($pg-1)*$lm.' , '.$lm,
        ));

        $count = $this->_mod_demand->getCount();


        //会员头像基类
        require(ROOT_PATH . '/includes/avatar.class.php');
        $objAvatar = new Avatar();

        foreach($list as &$row){
            $row['params'] = unserialize($row['params']);
            $row['avatar'] = $avatar = $objAvatar->avatar_show($row['user_id'],'middle');
        }

        $this->assign('sckey',$sckey);
        $this->assign('sort',$sort);
        $this->assign('page',$pg);

        $pageList = $this->_page(array(1=>$sort),array('lm' => $lm,'ct' => $count,'pg'=>$pg),array("app" => "demand","act" => "lists" ));

        $this->assign('pageList',$pageList);

        $this->assign('list',$list);
        $this->_config_seo('title', Lang::get('demand_list') . ' - '. Conf::get('site_title'));
        $this->display('demand/list.html');
    }
    //分页
    function _page($args = array() , $pg = array() , $url = array() , $params = ''){
        if($params == ''){
            if($_SERVER['QUERY_STRING'] != ''){
                $params = $_SERVER['QUERY_STRING'];
            }
        }
        $view = &v();

        $pgCount = ceil($pg['ct']/$pg['lm']);
        if($args){
            foreach ($args as $k => $r){
                $url['arg'.$k] = $r;
            }
        }
        for($i=1;$i<=$pgCount;$i++){
            $url['arg0'] = $i;
            $pgList[$i]['id']  = $i;
            $pgList[$i]['url'] = $view->build_url($url)."?".$params;
        }

        $nextpg = ($pg['pg'] >= $pgCount) ? $pg['pg'] : $pg['pg']+1;
        $url['arg0'] = $nextpg;
        $nextpage = $view->build_url($url)."?".$params;

        $prepg  = ($pg['pg'] <= 1) ? $pg['pg'] : $pg['pg']-1;
        $url['arg0'] = $prepg;
        $prepage = $view->build_url($url)."?".$params;

        $url['arg0'] = 1;
        $fpage = $view->build_url($url)."?".$params;

        $url['arg0'] = $pgCount;
        $epage = $view->build_url($url)."?".$params;


        $return['curpage'] = $pg['pg'];
        $return['nextpage'] = $nextpage;
        $return['prepage']  = $prepage;
        $return['fpage'] = $fpage;
        $return['epage'] = $epage;
        $return['list']  = $pgList;
        $return['count'] = $pgCount;

        return $return;
    }
    /**
     * 发布需求页
     * @access public
     * @see sue
     * @version 1.0.0 (2014-12-17)
     * @author Ruesin
     */
    function sue($type = 'normal'){
        $args = $this->get_params();
        $type  = isset($args[0]) ? trim($args[0]) : 'normal';
//         if(!in_array($type , $this->_types)){
//             $type = 'normal';
//         }
        if(!$this->_types[$type]){
            $type = 'normal';
        }

        if(IS_POST && !empty($_POST)){
            $args['post'] = $_POST;
        }
        $demand = $this->_get_type($type);
        $check  = $demand->_check_param($args);

        if(!$check){
            $this->show_warning('params_error');
            return;
        }

        include_once ROOT_PATH . '/includes/libraries/ipCity.class.php';
        $ipCity = new ipCity();
        $region_id = $ipCity->getRegionsByCode($_COOKIE['cityCode']);

        $data   = $demand->_get_item_data($check);

        $this->assign('regions',$region_id);
        $this->assign('check',$check);
        $this->assign('type',$type);
        $this->assign('cates',$this->item_cate);
        $this->assign('data',$data);
        $this->_config_seo('title', Lang::get('demand_sue') . ' - '. Conf::get('site_title'));
        $this->display('demand/form.html');
    }

    /**
     * 会员发布需求
     * @access public
     * @see subsue
     * @version 1.0.0 (2014-12-17)
     * @author Ruesin
     */
    function subsue(){
        $type   = $_POST['type']    = isset($_POST['type']) ? trim($_POST['type']) : 'normal';
        $status = $_POST['subtype'] = isset($_POST['subtype']) ? intval($_POST['subtype']) : 2;

        if($type !='suit'){ //套装处已做过验证,跳过
            //验证手机验证码
            $res = check::isMobileCode('demand', 'demand', $_POST['contact']['mobile'], $_POST['code']);
            if ($res)
            {
              $this->json_error($res);
              return;
            }
        }

        $region_code = $this->_get_bdCode(trim($_POST['region']['id']));
        if(!$region_code){
            $this->json_error('city_code_error');
            return;
        }

        $demand = $this->_get_type($type);
        $data = array(
                'user_id'  => $this->visitor->get('user_id'),
                'type_id'  => intval($_POST['type_id']),
                'md_title' => trim($_POST['title']),
                'status'   => $status,
                'params'   => $_POST['item'],
                'uname'    => trim($_POST['name']),
                'region'   => $_POST['region'],
                'region_code' => $region_code,
                'contact'  => $_POST['contact'],
                'remark'   => trim($_POST['remark']),
                'fabric'   => trim($_POST['fabric']),
        );
        $data['adjunct'] = !empty($_POST['adjunct']) ? serialize($_POST['adjunct']) : '';
        $res = $demand->add($data);
        if($res)
		{
		    //清除cookies
		    //..
		    //
		    //..
		    $mem = &m('member');
		    $mem->edit($this->visitor->get('user_id'),'demand = demand + 1');
		    //给裁缝发消息
		    sendMessage(array('location_url'=>"demand-{$res}.html",'type'=>4));
			$this->json_result(array('id'=>$res), 'sub_success');
			return ;
		}
   		else
   		{
   			$this->json_error('sub_failure');
   			return;
   		}
    }

    /**
     * 裁缝报价
     * @access public
     * @see offer
     * @version 1.0.0 (2014-12-17)
     * @author Ruesin
     */
    function offer(){
        $offer    = floatval($_POST['bj']);
        $del_time = intval(trim($_POST['jq']));
        $remark   = trim($_POST['bz']);
        $md_id    = intval($_POST['md']);

        if(!$offer || $del_time == ''){
            $this->json_error('sub_failure');
            return ;
        }
        if($del_time<=0){
            $this->json_error('sub_failure');
            return ;
        }
        //验证需求id及状态
        $info = $this->_mod_demand->get("md_id = {$md_id} AND status = 2");
        if(empty($info)){
            $this->json_error('have_not_demand');
            return ;
        }

        $cf_info = $this->visitor->get();

        //是否已经报过价
        $cfs   = $this->_mod_demandoffer->get("cf_id = {$cf_info['user_id']} AND md_id = {$md_id}");
        if(!empty($cfs)){
            $this->json_error('you_had_offer');
            return ;
        }

        $data = array(
                'md_id'    => $md_id,
               // 'cf_name'  => isset($cf_info['real_name']) ? trim($cf_info['real_name']) : trim($cf_info['nickname']),
        		'cf_name'  => $cf_info['real_name']!= '' ? trim($cf_info['real_name']) : trim($cf_info['nickname']),
                'cf_id'    => $cf_info['user_id'],
                'offer'    => $offer,
                'del_time' => $del_time,
                'remark'   => $remark,
                'sub_time' => gmtime(),
        );

        $res = $this->_mod_demandoffer->add($data);

        if($res){
            $this->_mod_demand->edit(" md_id = $md_id",array('take_in'=>$info['take_in']+1));
            //报价后给会员发消息
            sendMessage(array(
                'location_url' => "demand-{$md_id}.html",
                'type' => 6,
                'price' => $offer,
                'to_user_id' => $info['user_id'],
            ));
            //发送短信 add by shao
            smsAuthCode($info['mobile'], 'demand', 'offer', 'get', 'pc','',array('cf_name'=>$cf_info['nickname'],'offer'=>$data['offer']));
            $offer = $this->_get_offer_list($info);
            $this->assign('offer',$offer);

            $content = $this->_view->fetch('demand/offer.html');
            $this->json_result(array(
                    'content' => $content,
                    'takein' => $info['take_in']+1,
            ));
            return ;
        }else{
            $this->json_error('sub_failure');
            return ;
        }
    }
    /**
     * ajax 获取验证码
     * @access public
     * @see ajaxcode
     * @version 1.0.0 (2014-12-17)
     * @author Ruesin
     */
    function ajaxcode ()
    {

        $mobile = $_POST['mobile'];

        if( preg_match("/^1[3,5,8]\d{9}$/",$mobile)===1 )
        {

            $res = smsAuthCode($mobile,'demand','demand','get','pc','');
            if ($res['err'])
            {
                $this->json_error($res['msg']);
                return;
            }

        }else{
            $this->json_error('mobile_num_error');
            return ;
        }

        $this->json_result($rs,'successed');
        return ;

    }
    /**
     * ajax裁缝中标
     * @access public
     * @see ajaxoffer
     * @version 1.0.0 (2014-12-24)
     * @author Ruesin
     */
    function ajaxoffer ()
    {
        $id    = intval($_POST['id']);
        $cfid  = intval($_POST['cfid']);

        if(!$id || !$cfid){
            $this->json_error('params_error');
            return ;
        }

        $userInfo = $this->visitor->get();

        $ofInfo = $this->_mod_demandoffer->get($id);
        if(empty($ofInfo)){
            $this->json_error('have_not_offer');
            return ;
        }

        //验证需求id及状态
        $info = $this->_mod_demand->get("md_id = {$ofInfo['md_id']} AND status = 2 AND user_id = '{$userInfo['user_id']}'");
        if(empty($info)){
            $this->json_error('have_not_demand');
            return ;
        }

        $data = array(
                //'md_id'    => $info['md_id'],
                //'cf_id'    => $ofInfo['cf_id'],
                'status'   => 3,
//                 'offer_id' => $id,
        );
        $res = $this->_mod_demand->edit(" md_id = {$info['md_id']}",$data);
        if($res){
            $oData = array(
                'status'  => 2,
	        );
	        $res1 = $this->_mod_demandoffer->edit("of_id = '{$id}'",$oData);  //需求各种变，删掉需求表的两个字段改成报价表中的这个  --Ruesin
        }
        sendMessage(array(
             'to_user_id'     => $cfid,
             'location_url'   => "demand-{$info['md_id']}.html",
             'type'           => "7",
        ));
       /* //中标后给裁缝发短信/邮件by shao
        $mod_user = &m('member');
        $member=$mod_user->get($cfid);
        if (!check::isMobile($member['user_name']))
        {
        	//$rs = check::isEmail($member['user']['username']);
        	$res = emailcode($member['user_name'],'success','success','pc','get',$_POST['password'],array('username'=>$userInfo['nickname'],'url'=>"demand-{$info['md_id']}.html"));
        }else{
        	smsAuthCode($member['user_name'], 'demand', 'success', 'get', 'pc','',array('username'=>$userInfo['nickname']));
        }
        */
        //中标后给裁缝发短信
        $store =& m('store');
        $store_info = $store->get(array('conditions'=>'store_id='.$cfid));
        smsAuthCode($store_info['tel'], 'demand', 'success', 'get', 'pc','',array('username'=>$userInfo['nickname']));
        if($res1){
            $info = $this->_mod_demand->get($info['md_id']);
            $offer = $this->_get_offer_list($info);
            $this->assign('offer',$offer);

            $content = $this->_view->fetch('demand/offer.html');
            $this->json_result(array(
                    'content' => $content,
            ));
            return ;
        }else{
            $this->json_error('offer_failure');
            return ;
        }
        return ;
    }
    /**
     * 报价翻页
     * @access public
     * @see offerpage
     * @version 1.0.0 (2014-12-24)
     * @author Ruesin
     */
    function offerpage ()
    {
        $page = isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
        $mdId = isset($_GET['md']) ? intval($_GET['md']) : 0;

        if(!$page || !$mdId){
            $this->json_error('params_error');
            return ;
        }

        $info = $this->_mod_demand->get($mdId);
        $offer = $this->_get_offer_list($info , $page);
        $this->assign('offer',$offer);
//         print_r($offer);exit;
        $content = $this->_view->fetch('demand/offer.html');
        $this->json_result(array(
                'content' => $content,
        ));
        return ;

    }
    function upload(){

        if(empty($_FILES) || $_FILES['up_file']['name'] == ''){
            $this->json_error('请选择附件!');
            return ;
        }

        $oName = $_FILES['up_file']['name'];
        $type = $_FILES['up_file']['type'];
        $tp = substr($oName,-4);
        $types = array('image/pjpeg','image/jpeg','image/x-png','image/png','image/gif','image/gif');

        if(!in_array($type, $types)){
            $this->json_error('附件格式!');
            return ;
        }
        $d = '/upload_user_photo/demand/'.date("Ymd").'/';
        $dir = $_SERVER['DOCUMENT_ROOT'].$d;
        mkDirs($dir);

        $name = md5($this->visitor->info['user_id'] . uniqid() . mt_rand(0,255) ) . $tp;

        $res = move_uploaded_file($_FILES['up_file']["tmp_name"],$dir.$name);

        if($res){
//             $src = get_domain() . $d .$name;
            $src = $d .$name;
            $this->json_result(array('name'=>$oName,'src'=>$src),'上传成功!');
            return ;
        }else{
            $this->json_error('上传失败!');
            return ;
        }
    }




}
