<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
use Cyteam\Goods\Goods;
use Cyteam\Goods\Gcategory;
use Cyteam\Goods\Products;
use Cyteam\Goods\Region;
/* 商品 */
class GoodsApp extends MallbaseApp
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**
    *content
    *@author  liang.li
    */
    function checkp() 
    {
       $pid = $_POST['hid'];
       $goodsId = $_POST['goodsId'];
       $retp = explode(",", $pid);
       $productId = [];
       foreach ($retp as $key => $value) 
       {
           if (!$value) 
           {
               continue;
           }
           $val = explode("-", $value);
           $productId[$val[0]] = $val[1];
       }
       
       if (!$productId) 
       {
          $this->json_error("参数为空");
          return;
       }
       ksort($productId);
       $productLib = new Products();
       $productList = $productLib->getProducts($goodsId);
       foreach ((array)$productList as $key => $value) 
       {
          $spec = unserialize($value['spec_desc']);
          $specValueId = $spec['spec_value_id'];
          if (!array_diff($specValueId, $productId)) 
          {
              $defaultProductId = $key;
              break;
          }
       }
       $this->json_result($productList[$defaultProductId]);
       return ;
    }

    function index()
    {


        
    	$args = $this->get_params();
    	$id = empty($args[0]) ? 0 : intval($args[0]);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        $goodsLib =  new Goods();
        $gcategoryLib = new Gcategory();
        $goodsInfo = $goodsLib->getGoodsInfo($id);
        if ($goodsInfo['goods']['marketable'] == 0)
        {
            $this->show_warning('此商品已下架');
            return ;
        }
        $end_time = 0;
        if ($goodsInfo['products']['pd'][$goodsInfo['products']['default_product_id']]['is_pro'])
        {
            $end_time = $goodsInfo['products']['pd'][$goodsInfo['products']['default_product_id']]['pro_info']['endtime'];
            $this->assign('end_time',date("Y/m/d H:i:s",$end_time));
        }
        else
        {
            $this->assign('end_time',0);
        }

        $goodsLink = $goodsLib->getGoodsLink($goodsInfo['goods']);
        $this->assign('goods_link',$goodsLink);
        $this->assign('store',$goodsInfo['products']['pd'][$goodsInfo['products']['default_product_id']]['store']);
        $this->assign('erweima',SITE_URL."/upload/phpqrcode/".$this->getQrcodeImage("goods",$id));

        $attrList = $goodsInfo['goods']['attr_list'];
        $i = 0;
        
        $ret = [];
        foreach ((array)$attrList as $key => $value) 
        {
            $p = [];
            if ($key%2 == 1)  
            {
                $p[] = $value;
                $p[] = $attrList[$key-1];
                $ret[] = $p;
            }
           
        }
        $goodsInfo['goods']['attr_list'] = $ret;
        $this->assign('goodsItem',$goodsInfo);
        
        if ($_COOKIE['cityCode']) 
        {
            $regionLib = new Region();
            $regionInfo = $regionLib->getInfoByCode($_COOKIE['cityCode']);
            if ($regionInfo) 
            {
           
                $p_id = $regionInfo['parent_id'];
                if ($p_id > 2) //===== 三级  =====
                {
                    $sregionList = $regionLib->getInfoByPid($p_id);
                    imports("orders.lib");
                    $orderLib = new Orders();
                    $shipInit = $orderLib->getShipInit(['ship_area_id'=>$regionInfo['region_id'],'weight'=>$goodsInfo['products']['pd'][$goodsInfo['products']['default_product_id']]['weight']]);
                    $price = $orderLib->freightCnt($goodsInfo['products']['pd'][$goodsInfo['products']['default_product_id']]['weight'], $shipInit['defship']);
                    $this->assign('ship_price',$price);
                }
                $this->assign('p_id',$p_id);
            }
        }
        $this->goodsComment($id);
        
        $this->_curlocal(array(
            0 => array("text" => "麦富迪尚品","url" => "gallery.html"),
            1 => array("text" => $goodsInfo['goods']["name"], "url" => "##"),
            ));
        
        $this->assign('sregion_list',$sregionList);
        $this->assign('p_id',$p_id);
        $this->assign('city_code',$_COOKIE['cityCode']);
        $this->_config_seo('title', 	$goodsInfo['goods']["name"]);
        $this->display('goods.index.html');
    }



    /**
     * 获取二维码 图片路径
     * @param  $type	二维码类型
     * @param  $id		标记id
     * @param  $size	尺寸
     * @return string
     */
    function getQrcodeImage($type,$id,$size=4){
        $size = empty($size) ? 4 : $size;
        $fileName = $type.'/'.$type.'_'.md5($id).'_'.$size.'.png';
        if(file_exists(EXAMPLE_TMP_SERVERPATH.$fileName)){ // 已存在删除文件
            return $fileName;
        }
        else
        {
            $this->Qrcode("goods",$id,H5_URL."product-content.html?id=".$id);
        }
        return $fileName;
    }

    /**
     * 生成二维码
     * @param  $type	类型 ：goods商品图|...
     * @param  $id		文件名称加密id
     * @param  $text	生成 文字或是链接
     * @param  $size	1:33*33;2:66*66;3:99*99;4:132*132
     * @return 没有返回值
     */
    function Qrcode($type='goods',$id=1000,$text='http://rctailor.ec51.com.cn/', $avatar=''){

        /* 引用phparcode */
        include (ROOT_PATH.'/phpqrcode/full/qrlib.php');
        /* 定义二维码图片存放路径 */
        $errorCorrectionLevel = 'H';
        foreach (array(1,2,3,4,10) as $size){
            $matrixPointSize = $size;

            $tempDir = EXAMPLE_TMP_SERVERPATH;
            $codeContents = $id;
            /* 扩展 类型 ：goods 商品id */
            $fileName = $type."/".$type.'_'.md5($codeContents).'_'.$size.'.png';
            $pngAbsoluteFilePath = $tempDir.$fileName;
            /**
             * 第一个参数$text，就是上面代码里的URL网址参数，
             * 第二个参数$outfile默认为否，不生成文件，只将二维码图片返回，否则需要给出存放生成二维码图片的路径
             * 第三个参数$level默认为L，这个参数可传递的值分别是L（QR_ECLEVEL_L，7%），M（QR_ECLEVEL_M，15%），Q（QR_ECLEVEL_Q，25%），H（QR_ECLEVEL_H，30%）。
             * 			这个参数控制二维码容错率，不同的参数表示二维码可被覆盖的区域百分比。利用二维维码的容错率，我们可以将头像放置在生成的二维码图片任何区域。
             * 第四个参数$size，控制生成图片的大小，默认为4
             * 第五个参数$margin，控制生成二维码的空白区域大小
             * 第六个参数$saveandprint，保存二维码图片并显示出来，$outfile必须传递图片路径
             */
            @ecm_mkdir(EXAMPLE_TMP_SERVERPATH.'/'.$type);

            @chmod($fileName, 0777);
            $logo = $avatar;
            $QR = $tempDir.$fileName;
            if(file_exists($QR)){ // 已存在删除文件
                unlink($QR);
            }
            QRcode::png($text, $pngAbsoluteFilePath, $errorCorrectionLevel, $matrixPointSize);

            /* 二维码合成 logo */
            $logo = false;
            if($logo !== FALSE){
                $QR = imagecreatefromstring(file_get_contents($QR));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $QR_width = imagesx($QR);
                $QR_height = imagesy($QR);
                $logo_width = imagesx($logo);
                $logo_height = imagesy($logo);
                $logo_qr_width = $QR_width / 5;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            }

            @imagepng($QR,$tempDir.$fileName);
        }
    }



    /**
    *content
    *@author  liang.li
    */
    function goodsComment($goodsId) 
    {
        $comment_mod = &m("detail_comment");
        $comment_list = $comment_mod->find(array(
            'conditions' => "comment_id ='{$goodsId}'",
        ));
        if ($comment_list) 
        {
            $i = 0;
            foreach ($comment_list as $key => $value)
            {
                if ($value['star'] >= 4) 
                {
                    $i ++ ;
                }
            }
        }
        $num = count($comment_list);
        
        $lv = 0;
        if ($num) 
        {
            $lv = sprintf("%.2f", ($i/$num)) * 100;
        }
        $this->assign('feedback',$lv);
        $this->assign('comment_count',$num);
        $this->assign('tags',$comment_list);
    }
    
    
    /**
    *计算运费
    *@author  liang.li
    */
    /* function getShip() 
    {
        $region_id = $_GET['region_id'];
        $p_id = $_GET['p_id'];
        $productLib = new Products();
        $info = $productLib->getInfoById($p_id);
        $weight = $info[''];
    } */
    
    /**
    *获取面料的信息
    *@author liang.li <1184820705@qq.com>
    *@2016年1月15日
    */
    function sfabric() 
    {
        $id = $_POST['sid'];
        $code = $_POST['code'];
        $cate = $_POST['cate'];
        if (!$id || !$code) 
        {
            $this->json_error('参数错误');
            return ;
        }
        $cate_arr = explode(',', $cate);
        $param = array(); [123=>['0006'=>'SDA349A','0003'=>'SDA351A'],124=>['0005'=>'SDA352A']];
        foreach ($cate_arr as $key => $value) 
        {
            $param[$value] = $code;
        }
        $params[$id] = $param;
        imports("diys.lib");
        $diys = new Diys();
        $price = $diys->_getPrice($params);
        if (!$price) 
        {
           $this->json_error('面料读取异常');
           return ;
        }
        $return = $price[$id];
        //=====   面料属性  =====
        //=====  获取面料属性相关  =====
        $fabric_mod = m('fabric');
        $dict_mod   = m('dict1');
        $fabric_list = $fabric_mod->get("CODE = '$code' ");
        $dict_id_arr[] = $fabric_list['COMPOSITIONID'];
        $dict_id_arr[] = $fabric_list['COLORID'];
        $dict_id_arr[] = $fabric_list['FLOWERID'];
        $dict_ids = db_create_in($dict_id_arr,"ID");
        $dict_list = $dict_mod->find(array(
            'conditions' => "$dict_ids",
        ));
        
        
//   print_exit($fabric_list);      
        $return['yanse'] = !empty($fabric_list['color']) ? $fabric_list['color'] : (!empty($dict_list[$fabric_list['COLORID']]['NAME']) ? $dict_list[$fabric_list['COLORID']]['NAME'] : ''); //=====  颜色  =====
        $return['huaxing'] = !empty($dict_list[$fabric_list['FLOWERID']]['NAME']) ? $dict_list[$fabric_list['FLOWERID']]['NAME'] : ''; //=====  花型  =====
        $return['shazhi'] = $fabric_list['SHAZHI']; //=====  砂纸  =====
        $return['chengfen'] = $fabric_list['chengfen']; //=====  砂纸  =====
        $return['fabric'] = $fabric_list['CODE']; //=====  面料编号  =====
        $this->json_result($return);
    }
    
    /**
    *尺码助手
    *@author liang.li <1184820705@qq.com>
    *@2016年1月8日
    */
    function size() 
    {
        $args = $this->get_params();
        $id = empty($args[0]) ? 0 : intval($args[0]);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        $suit_rel_mod = m('suitrelat');
        $custom_mod = m('custom');
        $customs = $suit_rel_mod->get("tz_id = '{$id}'");
        $cusids = $customs['jbk_id'];
        if(empty($cusids)){
            $cusids = 0;
        }
        $cus_list = $custom_mod->find(array(
            'conditions' => "id IN ($cusids)",
        ));
        $size_img = array();
//     print_exit($cus_list);
        foreach((array)$cus_list as $key => $val){
            $cus_list[$key]['dict'] = $custom_fabric_list[$key];
            $cus_list[$key]['cat_name'] = $cData[$val["category"]];
            $cus_list[$key]['customid'] = $val["id"];
            $res = $this->getSizeByCate($val['category'],$val['size_id']);
            $cus_list[$key]['size'] = $res['size'];
            $size_img[] = $res['size_exp'];
        }
//      print_exit($size_img);
        $this->assign('size_img',$size_img);
        $this->display('goods.size.html');
    }
    
    /**
     *获得尺码信息
     *@author liang.li <1184820705@qq.com>
     *@2015年12月25日
     */
    function getSizeByCate($cate,$key=0)
    {
        $return = array();
        $size = array();
        if ($cate != '0021' && $cate != '0012' && $cate != '0011' && $cate != '0032')//=====  女大衣 特殊处理  =====
        {
            
            $filename = ROOT_PATH. '/data/size_json/'.$cate.'_10205.size.json';
            if (file_exists($filename))
            {
                $jsonString = file_get_contents($filename);
                $jsonData = json_decode($jsonString,true);
                $size = $jsonData['sizeAll'];
            }
            $size_exp = SITE_URL."/static/img/".$cate.".png";
        }
        else
        {
            $size_table_mod = m('size_table');
            $size_style_mod = m('size_style');
            $size_style_info = $size_style_mod->get_info($key);
            if (!$size_style_info)
            {
                return $return;
            }
            $size_exp = $size_style_info['img'];
            $size_table_list = $size_table_mod->find(array(
                'conditions' => "name_id = $key",
            ));
            $size_t = array();
            if ($size_table_list)
            {
                foreach ($size_table_list as $key => $value)
                {
                    $size_t[] = $value['standard_code'];
                }
            }
            if ($size_t) 
            {
                foreach ($size_t as $key => $value) 
                {
                    $tmp['Id'] = $value;
                    $tmp['Name'] = $value;
                    $size[] = $tmp;
                }
            }
        }
        $return['size'] = $size;
        $return['size_exp'] = $size_exp;
        return $return;
    }
    
    function ask(){
        $id = intval($_GET['id']);
        import("question.lib");
        $question = new Question();
        $page = $this->_get_page(5);
        $question->set_param(array(
            "id"   => $id,
            "page" => $page,
            'type' => "custom",
        ));

        $result = $question->load();

        $this->_format_page($result['page']);

        //print_R($result["page"]);
        $this->assign("page_info", $result['page']);
        $this->assign("list",      $result["list"]);
        $content = $this->_view->fetch("question.lib.html");
        $this->json_result(array("content" => $content, "recordCount" => $result['page']['item_count'], 'layer' => "questionLayer"));
        die();
    }
    
    function commit(){
        $id = intval($_POST['id']);
        import("question.lib");
        $question = new Question();
        $question->set_param(array(
            "id"   => $id,
            'type' => "custom",
        ));
        $suit_mod = &m("suitlist");
        $info = $suit_mod->get("id='{$id}' AND is_sale=1");
        $question->set_data($info);
        $res = $question->commit();
        if($res){
            $this->json_result("咨询已提交成功，客服会尽快回复!");
            die();
        }else{
            $this->json_error("意外错误，请重试");
            die();
        }
    }
    
    function comment(){
        $id = intval($_GET['id']);
        $comment_mod = &m("detail_comment");
        $answer_mod  = &m('detail_answer');
        require_once(ROOT_PATH . '/includes/avatar.class.php');
        $face = $this->objAvatar = new Avatar();
        $conditions = "status=0 AND comment_id ='{$id}'";
        $page = $this->_get_page(3);
        $comment_list = $comment_mod->find(array(
            'conditions' => $conditions,
            'limit' => $page["limit"],
            'count' => 1,
            'order' => 'addtime DESC'
        ));
        
        $page['item_count'] = $comment_mod->getCount();
        
        $replyids = array();
        
        foreach($comment_list as $key => $val){
            $comment_list[$key]['face'] = $face->avatar_show_src($m_id,'big');
            $replyids[] = $val["id"];
        }
        
        $replylist = $answer_mod->find(array(
            "conditions" => "comment_id ".db_create_in($replyids),
        ));
        
        foreach($replylist as $key =>$val){
            if(isset($comment_list[$val["comment_id"]])){
                $comment_list[$val["comment_id"]]['reply'] = $val;
            }
        }
        $this->_format_page($page);
        $this->assign("page_info", $page);
        $this->assign("comment_list", $comment_list);
        $content = $this->_view->fetch("comment.lib.html");
        $this->json_result(array("content" => $content, "layer" => "commentLayer"));
        die();
    }
    
    /**
    *历史量体数据 
    *@author liang.li <1184820705@qq.com>
    *@2016年1月19日
    */
    function loadFigure(){
        $customer_mod = &m("customer_figure");
        $keyword = isset($_GET['keyword']) ? trim(htmlspecialchars($_GET['keyword'])) : '';
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
        
        
        if(!empty($keyword)){
            $conditions = " figure_state = 1 AND firsttime > 1447171200 AND (customer_name = '{$keyword}' OR customer_mobile = '{$keyword}')";
        }else{
             $conditions = "customer_mobile='{$userinfo['phone_mob']}' AND figure_state = 1 AND firsttime > 1447171200";
        }
        
        $list = $customer_mod->find(array(
            "conditions" => $conditions,
            'order'      => "is_first DESC,lasttime DESC",
            'limit'      => "50",
        ));
        
        $server_mod = &m("serve");

        $aData = array();
        $ids   = array();
        foreach((array)$list as $key => $val){
            $ids[$val["id_serve"]]   = $val["id_serve"]; 
            $aData[] = $val;
        }
        
        $servers = $server_mod->find(array(
            'conditions' => "idserve ".db_create_in($ids),
        ));
        foreach($aData as $key => &$val){
            
            if ($membner_lv_id == VIP_ID || $invi_member_lv_id == VIP_ID)
            {
                if ($val['is_first'] == 0 && $val['service_mode'] == 3)
                {
                    //=====  根据手机号匹配   =====
                    $aData[$key]['is_free'] = 1;
                    if ($servers[$val["id_serve"]]['storetype'] == 2)
                    {
                        $aData[$key]['is_free'] = 0;
                    }
            
                }
                else
                {
                    $aData[$key]['is_free'] = 0;
                }
            }
            else
            {
                $aData[$key]['is_free'] = 0;
            }
            
           /*  $val['is_free'] = 0;
            if (!$val['is_first'] && $val['service_mode'] == 3) 
            {
                $val['is_free'] = 1;
                if ($servers[$val["id_serve"]]['storetype'] == 2)
                {
                    $val['is_free'] = 0;
                }
            } */
            $aData[$key]['address'] = isset($servers[$val["id_serve"]]) ? $servers[$val["id_serve"]]['serve_address'] : ''; 
        }
        
        $this->json_result($aData);
        die();
    }
    
    function loadServers(){
        $region = isset($_GET['region']) ? intval($_GET['region']) : 0;
        $serve_mod = &m("serve");
        $regions = $serve_mod->find(array(
            "conditions" => "region_id = '{$region}'",
        ));
        
        $rData = array();
        foreach($regions as $key => $val){
            $rData[] = $val;
        }
        
        $this->json_result($rData);
        die();
    }
    
    /**
    *content
    *@author liang.li <1184820705@qq.com>
    *@2016年1月19日
    */
    function loadServer() 
    {
        $region_id = isset($_GET['region']) ? intval($_GET['region']) : 0;
        $user_info = $this->visitor->get();
        $user_id = $user_info['user_id'];
        $user_name = $user_info['user_name'];
        $membner_lv_id = $user_info['member_lv_id'];
        if (!$region_id)
        {
            $this->json_error('缺少region_id参数');
            return ;
        }
        
        $serve_mod = &m("serve");
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
        //=====  如果自己或者对应创业者是vip  =====
        $conditions = "region_id = '{$region_id}' AND virtual = '0' AND shop_type IN (1,2)";
        //=====  此时不显示自营门店  =====
        
        $regions = $serve_mod->find(array(
            "conditions" => $conditions,
            //'index_key'  => "",
            'order'      => "idserve DESC,storetype DESC"
        ));
        if ($regions)
        {
            foreach ($regions as $key => $value)
            {
                
                if ($membner_lv_id == VIP_ID || $invi_member_lv_id == VIP_ID)
                {
                    $regions[$key]['is_free'] = 1;
                    if ($value['storetype'] == 2) //=====    自营门店  =====
                    {
                        $regions[$key]['is_free'] = 0;
                        /* if (($value['mobile'] == $user_name) || ($value['mobile'] == $invi_info['user_name']))
                        {
                            $regions[$key]['is_free'] = 0;
                        }
                        else
                        {
                            unset($regions[$key]);
                        } */
                    }
                }
                else
                {
                    $regions[$key]['is_free'] = 0;
                    if ($value['storetype'] == 2) //=====    自营门店  =====
                    {
                       // unset($regions[$key]);
                    }
                }
                
                /* $regions[$key]['is_free'] = 1;
                $regions[$key]['money_name'] = '收费';
                if ($value['storetype'] == 2) //=====    自营门店  =====
                {
                    //=====  根据手机号匹配   =====
                    if (($membner_lv_id == VIP_ID && $value['mobile'] == $user_name) || ($invi_member_lv_id == VIP_ID && $value['mobile'] == $invi_info['user_name']))
                    {
                        $regions[$key]['is_free'] = 0;
                        $regions[$key]['money_name'] = '免费';
                    }
                    else
                    {
                        unset($regions[$key]);
                    }
                } */
        
            }
        }
        $this->json_result(array_values($regions));
        die();
    }
    
    function loadFigurer(){
        $data = $_POST['data'];
        $_SESSION['_assign'] = $data;
        $this->searchFigurer();
    }
    
    function searchFigurer(){
        $data = $_SESSION['_assign'];
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
           "conditions" => "region_id='{$data['region']}'",
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
                  //  unset($servelist[$key]);
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
        
        $title = $rinfo["region_name"];
        
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
        
        $this->json_result(array("content" => $rData, "title" => $title, "assign" => $data));
        die();
    }
    
    
    function history(){
//         $history = array(
//             0 => array(
//                 'figureid'   => 475,
//                 'figuretype' => 5,
//             ),
        
//             1 => array(
//                 "figuretype"  => 2,
//                 "phone" => "13111111111",
//                 "realname" => "real",
//                 "region"	=> "247",
//                 "serveid"	=>"180",
//                 "timepart"	=>"am",
//             ),
        
//             2 => array(
//                 "address"  => "3333333333",
//                 "dateline" => "2015-10-06",
//                 "figurerid" => "2451",
//                 "figuretype" => "6",
//                 "phone"  => "13111111111",
//                 "realname" => "rea",
//                 "region"   => "247"
//             ),
//             3 => array(
//                 "address"  => "3333333333",
//                 "dateline" => "2015-10-06",
//                 "figurerid" => "2451",
//                 "figuretype" => "6",
//                 "phone"  => "13111111111",
//                 "realname" => "rea",
//                 "region"   => "247"
//             )
//         );
        
        $history = json_decode(base64_decode($_COOKIE["suit_history"]),1);
        $figures = array();
        $serves = array();
        $assign = array();
        foreach((array)$history as $key => $val){
            if($val['figuretype'] == 5){
                $figures[] = $val["figureid"];
            }elseif($val["figuretype"] == 2){
                $serves[] = $val["serveid"];
            }elseif($val['figuretype'] == 6){
                $assign[] = $val['figurerid'];
            }
        }
        $server_mod = &m("serve");
        //已有量体
        $aData = array();
        if(!empty($figures)){
            $customer_mod = &m("customer_figure");
            $list = $customer_mod->find(array(
                "conditions" => "figure_sn ".db_create_in($figures),
                'order'      => "lasttime DESC",
                'limit'      => "50",
            ));

            $ids   = array();
            foreach((array)$list as $key => $val){
                $ids[$val["id_serve"]]   = $val["id_serve"];
                $aData[$val["figure_sn"]] = $val;
            }
        
            $servers = $server_mod->find(array(
                'conditions' => "idserve ".db_create_in($ids),
            ));
            foreach($aData as $key => $val){
                $aData[$key]['address'] = isset($servers[$val["id_serve"]]) ? $servers[$val["id_serve"]]['serve_address'] : '';
            }
        }
        
        //服务点
        $regions = $server_mod->find(array(
            "conditions" => "idserve ".db_create_in($serves),
        ));
        
        //指定量体师
        $member_mod = &m("member");
        
        $members = $member_mod->find(array(
            "conditions" =>"figure_liangti.alone=1 AND member.user_id ".db_create_in($assign).$conditions,
            'join'       => "has_lt",
            'fields'     => "member.real_name, member.user_id, member.phone_mob, figure_liangti.manager_id"
        ));
        
        
        $region_mod = &m("region");
        
        $rData = array();
        foreach((array)$members as $key => $val){
            $val['address'] = $address[$val["manager_id"]];
            $rData[] = $val["manager_id"];
        }
        
        $servers = $server_mod->find(array(
           "conditions" => "userid ".db_create_in($rData), 
        ));
        
        $fData = array();
        foreach($servers as $key => $val){
            $fData[$val["userid"]] = $val;
        }
        
        foreach((array)$members as $key => $val){
            if(isset($fData[$val["manager_id"]])){
                $members[$key]['address'] = $fData[$val["manager_id"]]['serve_address'];
            }
        }
        
        foreach((array)$history as $key => $val){
            if($val['figuretype'] == 5){
                if(isset($aData[$val["figureid"]])){
                    $history[$key]['info'] = $aData[$val["figureid"]];
                }
            }elseif($val["figuretype"] == 2){
                if(isset($regions[$val["serveid"]])){
                    $history[$key]['info'] = $regions[$val["serveid"]];
                }
            }elseif($val['figuretype'] == 6){
                $history[$key]['info'] = $members[$val["figurerid"]];
            }
        }
        
        $this->assign("history", $history);
    }
}

?>
