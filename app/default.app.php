<?php
/**
 * Mfd licence
 *
 * @copyright  Copyright (c) 2007-2016 mfd.cn Inc. (http://mfd.p.day900.com)
 * @license  http://license.mfd.cn/ mfd License
 */
use Cyteam\Module\Module;
use Cyteam\Goods\Mp;
use Cyteam\Goods\Orders;
/**
 *默认首页控制器
 *@access public
 *@author cyrus <2621270755@qq.com>
 *@date 2016年5月20日
 *@version 1.0
 *@return
 */
class DefaultApp extends MallbaseApp
{
	var $_shuffling_mod;
	var $_shuffling_group_mod;
	var $_motif_mod;
	var $_motif_group_mod;
	var $_motif_rel_content_mod;
	var $_satnav_mod;
	var $_module_obj;
	
    function __construct(){
        parent::__construct();
        $this->_shuffling_mod = & m('shuffling');
        $this->_shuffling_group_mod = & m('shufflinggroup');
        $this->_motif_mod=&m('motif');
        $this->_motif_group_mod=&m('motifgroup');
        $this->_motif_rel_content_mod=&m('motifrelcontent');
        $this->_newpromotion_mod =& m('newpromotion');
        $this->_satnav_mod=&m('satnav');
        $this->_module_obj=new Module();
        header("Content-Type:text/html;charset=" . CHARSET);
    }
   
    function index()
    {

     
        $agent = is_mobile_request();
    	$banner_code=$this->_module_obj->pc_banner_code;
    	$ads_code=$this->_module_obj->pc_ads_code;
    
    	
    	//获取轮播图11
        $indexAds = $this->_module_obj->getBanners($banner_code,1);
        //获取广告位
        $columns=$this->_module_obj->getAds($ads_code);
        $this->assign("indexAds", $indexAds);
        $this->assign("columns", $columns);
        $this->_config_seo('title', 	"首页-".Conf::get('site_title'));
        $this->display("index-new.html");
    }
    function downapp(){
        $this->display('downapp.html');
    }

    public function downaaa()
    {
        $member_mod = m('member');
        $member_mod->setInc("user_id = 2","single",1);
        $member_mod->setInc("user_id = 2","demand",200.45);
        echo '<pre>';print_r(11);exit;
        
        if(isset($_GET['code']))
        {
            echo '<pre>';print_r($_GET['code']);exit;
        }
        else
        {
            $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $baseUrl = urlencode($url);
            $url = "http://h5.mfd.p.day900.com/gxcode.html?appid=wxeb23f7e550e2aa2f&scope=snsapi_base&state=hello-world&redirect_uri=$baseUrl";
            header("Location:$url");
        }
        return;


        $data = array(9200);
        $res = $this->post_to_url($url, array('jsonData'=>json_encode($data)));
        
        echo '<pre>';print_r($res);exit;
        
        $arg = $this->get_params();
        $orderid = intval($arg[0]);//erweima_url
        if(!$orderid)
        {
            return;
        }
        $orderLib = new Orders();
        $res = $orderLib->getOrderInfo($orderid);
        $goods_item = [];
        $fdiy_goods = [];
        foreach ((array)$res['order_goods'] as $index => $item)
        {
            if($item['type'] == 'fdiy') 
            {
                $goods_item[] = $item;
                if($item['style'])
                {
                    $fdiy_goods = $item;
                    break;
                }
            }
        }
        if(!$goods_item)
        {
            return;
        }
        if(!$fdiy_goods)
        {
            $fdiy_goods = $goods_item[0];
        }
        if(!$fdiy_goods['style']) //没有默认图 提供默认图
        {
            $fdiy_goods['style'] = "static/img/mfd_5.png";
        }
        $fdiy_goods['quanqi'] = explode("-",$fdiy_goods['spe_name'])[2];

        $mod_fbcategory = &m("fbcategory");//
        $gongixiao = explode(":",$fdiy_goods['params'][1])[1];
        $fb_list = $mod_fbcategory->find(array(
           'conditions' => 'cate_id IN('.$gongixiao.')',
        ));

        $gongiao_str = "";
        foreach ((array)$fb_list as $index => $item)
        {
            $gongiao_str .= $item['cate_name'].',';
        }
        $gongiao_str = trim($gongiao_str,',');
        $fdiy_goods['gongxiao_str'] = $gongiao_str;

        if(!$res['erweima_url']) //没有宣传二维码 生成一张
        {
            $mpObj = mpObj();
            $mpres = $mpObj->getQRCode($res['user_id'],0);
            if(!$mpres)
            {
                exit($mpObj->errCode.":".$mpObj->errMsg);
                return;
            }
            $url = $mpObj->getQRUrl($mpres['ticket']);
            if(!$url)
            {
                exit('无法获得二维码');
            }
            $order_mod = m('order');
            $order_mod->edit($res['order_id'],['erweima_url'=>$url]);
            $res['erweima_url'] = $url;
        }
        $fdiy_goods['erweima_url'] = $res['erweima_url'];
        $member_mod = m('member');
        $member_info = $member_mod->get_info($res['user_id']);


        //获取微信配置
        require_once "h5/weipay/jssdk.php";
        $jssdk = new JSSDK();

        $signPackage = $jssdk->getSignPackage();
        $configs=array(
            'appId'=> $signPackage["appId"],
            'timestamp'=>$signPackage["timestamp"],
            'nonceStr'=> $signPackage["nonceStr"],
            'signature'=>$signPackage["signature"],
        );

        $this->assign('config',$configs);

        $fdiy_goods['url'] = $signPackage['url'];

        $this->assign('goods_item',$fdiy_goods);
        $this->assign('member_info',$member_info);


        $this->display('downaaa.html');
    }

    function post_to_url($url, $data) {
        $fields = '';
        foreach($data as $key => $value) {
            $fields .= $key . '=' . $value . '&';
        }
        rtrim($fields, '&');


        $post = curl_init();
        $aHeader = array(
            "Password:password",
            "Username:eCommerce"
        );
        curl_setopt($post, CURLOPT_HTTPHEADER, $aHeader);
//        curl_setopt($post,CURLOPT_PROXY,'127.0.0.1:8888');//Charles/
        curl_setopt($post, CURLOPT_URL, $url);
        curl_setopt($post, CURLOPT_POST, count($data));
        curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($post);

//$error_mod = m('error');
//$error_mod->add(['content'=>json_encode($result)]);

        if (curl_error($post)){
            $rs = array('err'=>1,'msg'=>"curl error:".curl_error($post) );//-1:代表CURL请求过程中的错误~
        }else{
            $rs = array('err'=>0,'data'=>json_decode($result,1));
        }
// echo iconv("UTF-8","GBK", $data);
//        echo $result;
//        echo "\r\n";exit;


        curl_close($post);
        return $rs;
    }

    public function fimg()
    {
        $imgstr = str_replace("data:image/png;base64,","",$_POST['imgUrl']);
        $path = ROOT_PATH.'/upload/images/gallery/';
        $img = base64_decode($imgstr);

        /* 品类id＋时间戳 md5 */
        $t = md5(time()).'.png';
        $save = file_put_contents($path.$t,$img);
        if ($save){
            echo '/upload/images/gallery/'.$t;
        }else{
            echo 44;
        }
    }


    function _get_hot_keywords()
    {
        $keywords = explode(',', conf::get('hot_search'));
        return $keywords;
    }
    
    function nindex(){
        $this->_config_seo('title', Conf::get('site_title'));
      
        $this->display("index-new.html");
    }
    
    function miniCart(){
        $cart_mod = &m("cart");

        $result = $cart_mod->_cart_main($this->visitor->get("user_id"),[],$this->_source_from);
        //print_R($result);
        $this->assign("result", $result);
        $content = $this->_view->fetch("miniCart.lib.html");
        die($this->json_result($content));
    }
	//END function
	
    
   
}

?>