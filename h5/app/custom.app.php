<?php
/**
 * 定制中心
 * @author yhao.bai
 *
 */
class CustomApp extends MallbaseApp
{
	var $_customs_mod;
	var $_customsparts_mod;
	var $_parttype_mod;
	var $_parts_mod;
	var $_partattr_mod;
	var $_gcategory_mod;
	var $_dis_mod;
	var $_distance = 500;		//移动距离请求
	var $_spage_size = 8;		//每次加载个数
	var $_spage_max =  10;		//加载次数
	var $_comment_size = 3;	    //加载评论数
	var $_comment_limit = '0,3';
	var $_like_mod;
	var $_member_mod;
	var $_link_mod;
    function __construct(){
        $this->CustomApp();
    }
    function CustomApp(){
		 $this->_dis_mod            =& m("dissertation");
    	 $this->_customs_mod      	=& m('customs');
    	 $this->_customsparts_mod 	=& m('customsparts');
    	 $this->_parttype_mod     	=& m('parttype');
    	 $this->_parts_mod        	=& m('part');
    	 $this->_partattr_mod       =& m('partattr');
    	 $this->_gcategory_mod      =& m('gcategory');
    	 $this->_like_mod           =& m('like');
    	 $this->_member_mod         =& m('member');
    	 $this->_link_mod           =& m("links");
        parent::__construct();
    }
    function stable(){
    	$str = $this->_view->fetch("diy/sizetable.html");
    	$this->json_result($str);
    }
    function diy(){
		$args = $this->get_params();
		$uid = 0;
		$v = isset($_GET['v']) ? intval($_GET['v']) : 0;
		//判断app是否完成登录
		if($args['2']) {
			$uid = intval(ApiAuthcode($args['2'], 'DECODE', 'kuteiddiy',0));
		}
		$this->assign('v',$v);
		$this->assign('uid', $uid);
		$this->display('diy/index2.html');
//    	$this->display('diy/index.html');
    }
    function diy2(){
    	$args = $this->get_params();
    	$uid = 0;
    	$v = isset($_GET['v']) ? intval($_GET['v']) : 0;
    	//判断app是否完成登录
    	if($args['2']) {
    		$uid = intval(ApiAuthcode($args['2'], 'DECODE', 'kuteiddiy',0));
    	}
		$user = $this->visitor->get();
		if($user['user_id']){
			do{
				$api_token = ApiAuthcode($user['user_id'], 'ENCODE', 'kuteiddiy', 0);
			} while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
		}else{
			$api_token = 'mfd';
		}

    	$this->assign('v',$v);
    	$this->assign('uid', $uid);
    	$this->assign('tk', $api_token);
    	$this->display('diy/index2.html');
    }
    //get dissertation
    function index(){
		header("Location: /index.php/dissertation.html\n");exit;

        $config = ROOT_PATH . '/data/dissertation/config.php';
    	if(is_file($config)){
    		$cats = include_once($config);
    	}
		if(empty($cats)){
    		$this->show_warning('Hacking Attempt');
            return;
    	}
		$time = gmtime();
		foreach($cats as $key=>$cat){
			$conditions = " AND cat='{$key}' AND start_time <= {$time} AND end_time >= {$time}";
			$items[$key]['cat']=$cat;
			$items[$key]['cat']['key']=$key;
			$items[$key]['items'] = $this->_dis_mod->find(array(
				'conditions' => '1=1 '.$conditions,
				// 'limit' => $page['limit'],
				'limit' => 12,
				'order' => "is_hot DESC, sort_order ASC, add_time DESC",
				'count' => true,
			));
		}
		$this->assign('items', $items);

        $cate=$this->_customs_mod->get_cate_g();
        $this->assign('cates',$cate);
        $this->display('custom/index.html');
    }
    //list customs
    function lists(){
        $args = $this->get_params();
        $id = empty($args[0]) ? 0 : intval($args[0]);
        if (!$id){
            $this->show_warning('Hacking Attempt');
            return;
        }
        //get all cst by cateid
        $data=$this->_customs_mod->findAll(array(
//             'fields'=>'cmt.*,cst.*',
//             'join' =>"has_comments",
//             'conditions' => "cst.cst_cate='{$id}' AND cst.is_active = '1' AND cst_store > 0",
            'conditions' => "cst.cst_cate='{$id}' AND cst.is_active = '1'",
        	'limit' => $this->_spage_size,
            'count' => true,
            'order' => "cst_id desc",
        ));
        $count = $this->_customs_mod->findAll(array(
            'conditions' => "cst.cst_cate='{$id}' AND cst.is_active = '1'",
            'count' => true,
        ));
        $this -> assign('count',count($count));
		//分类名称
		$cat_arr = array(

			'3'=>'西装定制','3000'=>'衬衫定制','4000'=>'马夹定制','2000'=>'西裤定制','6000'=>'大衣定制',

		);
		$cat_name = $cat_arr[$id];

        $this->assign('cat_name',$cat_name);
        $this->assign('title',"定制中心-".$cat_name."、男士正装个性定制|RCTAILOR");
        $data=$this->get_cst_data($data);//get other data
        $cate=$this->_customs_mod->get_cate_g();

        $this->assign('cates',$cate);
        $this->assign('distance',$this->_distance);
        $this->assign('spage_max',$this->_spage_max);

        $this->assign('cateId',$id);

        $this->assign('item_list', $data);

        $this->display('custom/lists.html');
    }
    public function lists_ajax() {
    	$this->wall_ajax($where, $order);
    }
    /**
     * 瀑布加载
     */
    public function wall_ajax($where = array(), $order = 'id DESC', $field = '') {
    	//条件
    	$args = $this->get_params();
    	$id = empty($args[0]) ? 0 : intval($args[0]);
//     	$p =  $_GET['p'];//页码
    	$sp = $_GET['sp'];//子页
    	//计算开始
//     	$start = $this->_spage_size * ($this->_spage_max * ($p - 1) + $sp);
    	$start = $this->_spage_size * $sp;

    	//可以优化下 之前先count下
//     	echo $start.','.$this->_spage_size;
    	$ajaxdata=$this->_customs_mod->findAll(array(
        	'conditions' => "cst.cst_cate='{$id}' AND cst.is_active = '1'",
        	'limit' => $start.','.$this->_spage_size,
        	'count' => true,
        	'order' => "cst_id desc",
    	));
    	$ajaxdata=$this->get_cst_data($ajaxdata);//get other data
    	$this->assign('item_list', $ajaxdata);
    	$resp = $this->_view->fetch("custom/masonry.html");

    	$data = array(
            'isfull' => 1,
            'html' => $resp
    	);
//     	$count <= $start + $spage_size && $data['isfull'] = 0;
    	$this->ajaxReturn(1, '', $data);
    }
    /**
     * AJAX返回数据标准
     *
     * @param int $status
     * @param string $msg
     * @param mixed $data
     * @param string $dialog
     */
     function ajaxReturn($status=1, $msg='', $data='', $dialog='') {
    	header('Content-Type:text/html; charset=utf-8');
    	exit(json_encode(array(
    			'status' => $status,
    			'msg' => $msg,
    			'data' => $data,
    			'dialog' => $dialog,
    	)));
    }
    /*
     * get other data
     */
    function get_cst_data($data=array()){
        if(empty($data))return;
        $ids='';
        $cateIds='';
//         foreach ($data as $row){
//             $cateIds[$row['sec_cate']]=$row['sec_cate'];
//         }
//         $sCate=$this->_gcategory_mod->find(array(
// //             'conditions'=>"cate_id in (".implode(',',$cateIds).")"
//             'conditions'=>db_create_in($cateIds,'cate_id'),
//         ));
        $this->assign("comment",1);

        foreach ($data as $v){

            $data[$v['cst_id']]['cst_cost']   = round($v['cst_cost'],0);;
            $data[$v['cst_id']]['cst_price']  = round($v['cst_price'],0);;
            $data[$v['cst_id']]['cst_market'] = round($v['cst_market'],0);;


//             $data[$v['cst_id']]['sec_cate_name']=$sCate[$v['sec_cate']]['cate_name'];

             $comment_row=$this->_get_goods_comment($v['cst_id'], $this->_comment_size);
            $data[$v['cst_id']]['comment_list']=$comment_row['comments'];
            if($_SESSION['user_info']['user_id']!=''){
                $data[$v['cst_id']]['is_like']= getLikeByUid($_SESSION['user_info']['user_id'], $v['cst_id'], 'dingzhi_like');
            }else{
                $data[$v['cst_id']]['is_like']=0;
            }
        }
        return $data;
    }
    //列表页 Ajax 控制喜欢
    function ajax_like(){
        $id = isset($_GET['itemId']) ? intval($_GET['itemId']) : 0;
        if($id!=0){

            if(!empty($_SESSION['user_info'])){
                $row=$this->_customs_mod->get($id);
                $liked= getLikeByUid($_SESSION['user_info']['user_id'], $id, 'dingzhi_like');
                if($liked==1){//已喜欢
                    return;
                    //$num=-1;
                    //$this->dropLike($_SESSION['user_info']['user_id'],$id,'custom');
                    //$return=array('msg' => '喜欢','cls'=>'xih');
                }else{
                    $num=1;

                    $res = setLike($_SESSION['user_info']['user_id'], $id, "dingzhi_like",'pc');

                    if ($res['err'] == 1){
                        $return=array('num' => 0,'msg' => '操作失败!');
                    }else{

                        /* 喜欢成功加积分 */
                        $p_num = pointTurnNum('dingzhi_like');

                        setPoint($_SESSION['user_info']['user_id'],$p_num,'add','dingzhi_like');

                        $return=array('msg' => '已喜欢','cls'=>'yxih');

                    }

//                     setLike($_SESSION['user_info']['user_id'], $id, 'custom','pc');
//                     $return=array('msg' => '已喜欢','cls'=>'yxih');
                }
                $data=array('cst_likes'=>$row['cst_likes']+$num,);
                $this->_customs_mod->edit($row['cst_id'],$data);

                $return['num'] = $num;

            }else{
                $return=array('num' => 0,'msg' => '请登录');
            }
        }else{
            $return=array('num' => 0,'msg' => '参数错误');
        }
        echo json_encode($return);
    }
    //既然不要取消喜欢,那此功能就没用了.
    //不再喜欢//目前是把已喜欢的记录给删除掉了.感觉不妥
    ////如果已喜欢,现在不喜欢了,也不要删除此记录,加个字段区分,保存着,说明用户曾经喜欢过.
    function dropLike($uid,$id,$type){
        $ids=$this->_like_mod->get(array(
            'conditions'=>"uid={$uid} AND cate='{$type}' AND like_id = {$id}",
        ));
        $this->_like_mod->drop($ids['id']);
        $this->_member_mod->setDec($uid,'like_num');
    }

    /**
     *   产品详情
     * @author yusw
     *  @2015年6月11日
     */
    function goodsInfo()
    {

        $id = intval($_REQUEST['id']);
        $token = '';
        $user_info = array();
        if($this->visitor->has_login){
            $m =&m('member');
            $user_id = $this->visitor->get('user_id');//509
            $user_info = $m->get($user_id);
        }

        //取数据
        $data['id'] = $id;
        $data['token'] = $token;
        $res = $this->goodsInfoj($data,$user_info);
// print_exit($res);
        if (!$res['statusCode'])
        {
            $this->show_warning($res['error']['msg']);
            return;
        }

        $article = m('article');
        $this->assign('article',$article->get_info(140));
        //list_cus  查看更多   suit_info   详细信息
        $this->assign('name',$res['result']['data']['suit_info']['suit_name']);
        $this->assign('info',$res);
        $this->assign('title',$res['result']['data']['suit_info']['suit_name']);
        $this->display('custom/info2.html');
    }

    //收藏套装
    function collect(){
        $id = $_POST['id'];
        $type = $_POST['type'];
        if (!$id)
        {
            $this->json_error('收藏套装不存在！');
            return;
        }
        //获取套装
        $collect_mod =& m("collect");
        $collect  = $collect_mod->find(array(
            "conditions" => "user_id = " . $this->visitor->get('user_id') ." and type='".$type."' and item_id=".$id,
        ));
//     print_exit($collect);
        if($collect){
            $this->json_error('您已经套装过该作品！');
            return;
        }
        $data['user_id'] = $this->visitor->get('user_id');
        $data['type'] = $type;
        $data['item_id'] = $id;
        $data['keyword'] = $keyword;
        $data['add_time'] = time();
        $collect_mod->add($data);
        if($collect_mod->has_error()){
            $this->json_error('收藏套装失败！');
            return;
        }
        $this->json_result('', '收藏套装成功！');
        return;
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
//         echo PROJECT_PATH;exit;
            $filename = ROOT_PATH.'includes/arrayfiles/size_json/'.$cate.'_10205.size.json';
            if (file_exists($filename))
            {
                $jsonString = file_get_contents($filename);
                $jsonData = json_decode($jsonString,true);
                $size_list = $jsonData['sizeAll'] ;
                foreach ($size_list as $key1 => $value1)
                {
                    $size[] = $value1['Id'];
                }
            }
//       print_exit($size);
            $size_exp = SITE_URL."/static/img/".$cate.".png";
        }
        else
        {
            $size_table_mod = m('sizetable');
            $size_style_mod = m('sizestyle');
            $size_style_info = $size_style_mod->get_info($key);
            if (!$size_style_info)
            {
                return $return;
            }
            // 	    return $key;
            $size_exp = $size_style_info['img'];
            $size_table_list = $size_table_mod->find(array(
                'conditions' => "name_id = '$key' ",
            ));
            // 	 return $size_table_list;
            if ($size_table_list)
            {
                foreach ($size_table_list as $key => $value)
                {
                    $size[] = $value['standard_code'];
                }
            }

        }
//    print_exit($return);
        $return['size'] = $size;
        $return['size_exp'] = $size_exp;
//    print_exit($return);
        return $return;
    }

    /**
     *   产品详情
     *@author liang.li <1184820705@qq.com>
     *@2015年6月1日
     *list URL new.api.dev.mfd.cn/soap/gallery.php?act=getList&id=52&token=42ad4378f9b9a3a111b583a89fad58d7
     *info URL  new.api.dev.mfd.cn/soap/gallery.php?act=getList&id=52&token=42ad4378f9b9a3a111b583a89fad58d7
     */
    //http://local.soaapi.mfd.com/soap/profit.php?act=goodsInfo&token=eda6b5ebb68702e0e49841cb9aeb50d3&id=63
    function goodsInfoj($data,$user_info)
    {
        //=====  全球首发  =====
        $f_id = isset($data['f_id']) ? $data['f_id'] : '';
        if ($f_id)
        {
            return $this->goodsWorldInfo($data);
        }

        $id = isset($data['id']) ? $data['id']: 0;
        $token = isset($data['token']) ? $data['token'] : '';
        $m_mod = m('member');
        $time = time();
        $dis_count = 0;
        if ($user_info['dis_count'])
        {
            $dis_count = $user_info['dis_count'];
        }
        $img_url = LOCALHOST1;
        $suit_list_mod      = m('suitlist');
        $suit_rel_mod = m('suitrelat');
        $custom_mod = m('custom');
        $custom_fabirc = m('cusFab');
        $fabric_mod = m('fabric');
        $dict_mod = m('dict1');
        $custom_gallery_mod = m('customgallery');
        $suit_gallery_mod = m('suitgallery');
        $fabricgallery_mod = m('fabricgallery');
        $model_collect =& m('collect');


        $path = dirname(__FILE__);
        if (file_exists(ROOT_PATH.'includes/arrayfiles/pinlei.php'))
        {
            $pinlie = include ROOT_PATH.'includes/arrayfiles/pinlei.php';
        }
        else
        {
            $this->msg = '品类无法获得';
            return $this->eresult();
        }
        $aData = $this->processPrice($user_info['user_id']);
        $suit_list_info = $suit_list_mod->get_info($id);
//    print_exit($suit_list_info);
        if (!$suit_list_info['is_sale'])
        {
            $this->msg = '无此商品或者禁止查看';
            return $this->eresult();
        }

        if($suit_list_info['is_promotion'] == 1 && $time >= $suit_list_info['star_time'] && $time <= $suit_list_info['over_time'])
        {
            $suit_list_info["price"] = $suit_list_info["promotion_price"];
        }

        $suit_list_info["woman_price"] = '';
        if ($suit_list_info['theme'] == 16) //=====  女装要打折  =====
        {
            $suit_list_info["woman_price"] = $suit_list_info["price"] * 0;
        }
        
        $points = $suit_list_info['points'];
        $point_arr = explode("\r\n", $points);
        $suit_list_info['points_arr'] = $point_arr;
        
        $this->fabices= array(
            "1" => "羊毛衣物保养",
            "2" => "棉类衣物保养",
            "3" => "亚麻衣物保养"
        );
        
        $suit_list_info['fabices'] = $this->fabices[$suit_list_info['fabices']];

        $dis_count = 0;
        if ($dis_count)
        {
            $suit_list_info['price'] = _format_price_int($suit_list_info['price'] * $dis_count);
        }
        else
        {
            $suit_list_info['price'] = _format_price_int($suit_list_info['price']);
        }



        if(!in_array($suit_list_info['category'], $aData) && $suit_list_info["is_first"] == 1)
        {
            switch ($suit_list_info['category'])
            {
                case "0003": //西装
                    $suit_list_info["price"] = "999.00";
                    break;
                case "0006": //男衬衣
                    $suit_list_info["price"] = "199.00";
                    break;
                case "0016":  //女衬衣
                    $suit_list_info["price"] = "199.00";
                    break;
            }
        }
        //=====  点击数加1  =====
        $suit_list_mod->setInc($id,'click_num');

        $suit_info = $suit_rel_mod->get("tz_id = $id");
        $cus_list = array();
        $gallery = array();
        $fabricgallery_list = array();
        $size_img = array();
        if ($suit_info)
        {
            $cus_ids = $suit_info['jbk_id'];
            //=====  样衣列表  =====
            $cus_list = $custom_mod->find(array(
                'conditions' => "id IN ($cus_ids)",
                'index_key' => "",
            ));

            //=====  custom_fabric表的面料列表  =====
            $custom_fabric_list = $custom_fabirc->find(array(
                'conditions' => "custom_id IN ($cus_ids) AND is_default = 1",
                'index_key'  => "custom_id",

            ));


            //=====  相册表 关联相册  =====
            $url = SITE_URL;

            //=====  如果套装有相册就去套装的相册  =====
            $gallery = $suit_gallery_mod->find(array(
                'conditions' => "suit_id=".$id,
                'index_key'  => "",
            ));
            if (!$gallery)
            {
                $cus_ids_arr = explode(",", $cus_ids);
                if ($cus_ids_arr)
                {
                    foreach ($cus_ids_arr as $key => $value)
                    {
                        if (!$value)
                        {
                            continue;
                        }
                        $gallery_list = $custom_gallery_mod->find(array(
                            "fields" => "CONCAT(small_img,'$url') as small_img,CONCAT(middle_img,'$url') as middle_img,CONCAT(source_img) as source_img,sort,custom_id,id",
                            'conditions' => "custom_id = $value",
                            'index_key' => "",
                            'order'     => "sort asc",
                        ));
                        $gallery = array_merge($gallery,$gallery_list);
                    }
                }
            }
        }
        $suit_list_info['gallery_list'] = $gallery;
        //=====  取得面料和面料属性  =====
        if ($custom_fabric_list)
        {

            //=====  获得面料相关  =====
            $fabric_id_arr = i_array_column($custom_fabric_list,'item_id');
            $fabric_ids = db_create_in($fabric_id_arr,"ID");
            $fabric_list = $fabric_mod->find(array(
                'conditions' => "$fabric_ids",
            ));

            //=====  获取面料相册相关  =====
            $fabric_ids_g = db_create_in($fabric_id_arr,"fabric_id");
            $fabricgallery_list = $fabricgallery_mod->find(array(
                'conditions' => "$fabric_ids_g",
                'index_key'  => "fabric_id",
            ));


            //=====  获取面料属性相关  =====
            $COMPOSITIONID = i_array_column($fabric_list, 'COMPOSITIONID');
            $COLORID = i_array_column($fabric_list, 'COLORID');
            $FLOWERID = i_array_column($fabric_list, 'FLOWERID');
            $dict_id_arr = array_merge($COMPOSITIONID,$COLORID,$FLOWERID);
            $dict_ids = db_create_in($dict_id_arr,"ID");
            $dict_list = $dict_mod->find(array(
                'conditions' => "$dict_ids",
            ));

            foreach ($custom_fabric_list as $key => $value)
            {
                //$custom_fabric_list[$key]['chengfen'] = $dict_list[$fabric_list[$value['item_id']]['COMPOSITIONID']]['NAME']; //=====  成分  =====
                $custom_fabric_list[$key]['chengfen'] = $fabric_list[$value['item_id']]['chengfen']; //=====  砂纸  =====
                $custom_fabric_list[$key]['yanse'] = $dict_list[$fabric_list[$value['item_id']]['COLORID']]['NAME']; //=====  颜色  =====
                $custom_fabric_list[$key]['huaxing'] = $dict_list[$fabric_list[$value['item_id']]['FLOWERID']]['NAME']; //=====  花型  =====
                $custom_fabric_list[$key]['shazhi'] = $fabric_list[$value['item_id']]['SHAZHI']; //=====  砂纸  =====
                $custom_fabric_list[$key]['fabric'] = $fabric_list[$value['item_id']]['CODE']; //=====  面料编号  =====
                $custom_fabric_list[$key]['fabric_img'] = $fabricgallery_list[$value['item_id']]['source_img']; //=====  面料编号  =====
            }
        }

        $linkAttrs = array();
        if ($cus_list)
        {
            $suit_fabices=SITE_URL."/static/img/fabices/".$suit_list_info['fabices'].".png";
            //=====  取得尺码 和面料相关  =====
            foreach ($cus_list as $key => $value)
            {
                //=====  尺码助手的相册  =====
                $res = $this->getSizeByCate($value['category'],$value['size_id']);
                //        return $res;
                $size_img[] = $res['size_exp'];
                $cus_list[$key]['size'] = $res['size'];

                $cus_list[$key]['price'] = _format_price_int($value['base_price'] * PRODUCT_SHOW_SCALE);//展示价格
                $cus_list[$key]['size_url'] = "http://m.mfd.cn/article/spec.html";
                $cus_list[$key]['cate_name'] = $pinlie[$value['category']]['name'];
                $cus_list[$key]['cate_name_v'] = $pinlie[$value['category']]['name']."尺码";
                //=====  面料属性  =====
                $chengfen = "";
                if ($custom_fabric_list[$value['id']]['chengfen'])
                {
                    $chengfen = $custom_fabric_list[$value['id']]['chengfen'];
                }
                $cus_list[$key]['chengfen'] = $chengfen;
                $cus_list[$key]['yanse']    = $custom_fabric_list[$value['id']]['yanse'];
                $cus_list[$key]['huaxing']  = $custom_fabric_list[$value['id']]['huaxing'];
                $cus_list[$key]['shazhi']   = $custom_fabric_list[$value['id']]['shazhi'];
                $cus_list[$key]['fabric']   = $custom_fabric_list[$value['id']]['fabric'];
                $cus_list[$key]['style'] = $this->style[$value['style']];
                $cus_list[$key]['fabric_img'] = $custom_fabric_list[$value['id']]['fabric_img'];
            }

        }

        //=====  相关搭配  =====
        $suit_link_list = array();
        $suit_link_mod = m('suitlink');
        $suit_link_list = $suit_link_mod->find(array(
            'conditions' => "s_id = $id AND suit_list.is_sale = 1",
            'join'       => "belongs_to_suit",
            'fields'     => "suit_list.*",
            'index_key' => "",
        ));
        //        return $id;
        if ($suit_link_list)
        {
            foreach ($suit_link_list as $key => $value)
            {

                //=====  价格  =====
                $dis_count = 0;
                if ($dis_count)
                {
                    $suit_link_list[$key]['price'] = _format_price_int($value['price'] * $dis_count);
                }
                else
                {
                    $suit_link_list[$key]['price'] = _format_price_int($value['price']);
                }

                if($value['is_promotion'] == 1 && $time >= $value['star_time'] && $time <= $value['over_time'])
                {
                    $suit_link_list[$key]["price"] = $value["promotion_price"];
                }

                $suit_link_list[$key]["woman_price"] = '';
                if ($value['theme'] == 16) //=====  女装要打折  =====
                {
                    $suit_link_list[$key]["woman_price"] = $suit_link_list[$key]["price"] * 0.8;
                }
            }
        }

        //=====  评论数量  =====
        $detai_comment_mod = m('detail_comment');
        $detail = $detai_comment_mod->get(array(
            'conditions' => "comment_id = $id AND cate= 'suit' ",
            'fields' => "count(*) as num",

        ));
        $num = isset($detail['num']) ? $detail['num'] : 0;

        // 添加 此商品是否 背收藏过 标识  条件 是 这个 用户必须登录了之后 token 有值 才可以看到  auth tangsj
        $user_id = $user_info['user_id'];
//       echo $user_id;exit;
        $is_collect = array();
        if ($user_id)
        {
            $is_collect = $model_collect->get("item_id ='{$id}' and user_id='{$user_id}'");
        }
        if($is_collect){
            $this->result['is_collect'] = 1;
        }else{
            $this->result['is_collect'] = 0;
        }



        $this->result['suit_fabices'] = $suit_fabices;
        $this->result['list_cus'] = $cus_list;
        $this->result['list_attr'] = $linkAttrs;
        $this->result['suit_info'] = $suit_list_info;
        $this->result['suit_link'] = $suit_link_list;
        $this->result['size_img'] = $size_img;
        $this->result['comment_num'] = $num;
        return $this->sresult();
    }


    /**
     *   产品详情
     *@author liang.li <1184820705@qq.com>
     *@2015年6月1日
     *list URL new.api.dev.mfd.cn/soap/gallery.php?act=getList&id=52&token=42ad4378f9b9a3a111b583a89fad58d7
     *info URL  new.api.dev.mfd.cn/soap/gallery.php?act=getList&id=52&token=42ad4378f9b9a3a111b583a89fad58d7
     */
    function goodsApiInfo($data)
    {
        $id = isset($data['id']) ? $data['id'] : 0;
        $token = isset($data['token']) ? $data['token'] : '';
        $m_mod = m('member');
        $user_info = array();
        if ($token)
        {
            $user_info = $m_mod->get("user_token = '$token'");
        }
        $dis_count = 0;
        if ($user_info['dis_count'])
        {
            $dis_count = $user_info['dis_count'];
        }
        $img_url = LOCALHOST1;
        $suit_list_mod      = m('suitlist');
        $suit_rel_mod = m('suitrelat');
        $custom_mod = m('custom');
        $custom_fabirc = m('cusFab');
        $fabric_mod = m('fabric');
        $dict_mod = m('dict1');
        $suit_gallery_mod = m('suitgallery');
        $custom_gallery_mod = m('customgallery');

        $path = dirname(__FILE__);
        if (file_exists(ROOT_PATH.'includes/arrayfiles/pinlei.php'))
        {
            $pinlie = include ROOT_PATH.'/includes/arrayfiles/pinlei.php';
        }
        else
        {
            $this->msg = '品类无法获得';
            return $this->eresult();
        }
        $aData = $this->processPrice($user_info['user_id']);
        $suit_list_info = $suit_list_mod->get(array(
            'conditions' => "id = $id",
        ));
        if (!$suit_list_info['is_sale'])
        {
            $this->msg = '无此商品或者禁止查看';
            return $this->eresult();
        }
        $points = $suit_list_info['points'];
        $point_arr = explode("\r\n", $points);
        $suit_list_info['points_arr'] = $point_arr;
// print_exit($point_arr);
        $time = time();
        $dis_count = 0;
        if ($dis_count)
        {
            $suit_list_info['price'] = _format_price_int($suit_list_info['price'] * $dis_count);
        }
        else
        {
            $suit_list_info['price'] = _format_price_int($suit_list_info['price']);
        }

        if($suit_list_info['is_promotion'] && $time >= $suit_list_info['star_time'] && $time <= $suit_list_info['over_time'])
        {
            $suit_list_info["price"] = _format_price_int($suit_list_info["promotion_price"]);
        }

        $suit_list_info["woman_price"] = '';
        if ($suit_list_info['theme'] == 16) //=====  女装要打折  =====
        {
            $suit_list_info["woman_price"] = $suit_list_info["price"] * 0.8;
        }


        if(!in_array($suit_list_info['category'], $aData) && $suit_list_info["is_first"] == 1)
        {
            switch ($suit_list_info['category'])
            {
                case "0003": //西装
                    $suit_list_info["price"] = "999.00";
                    break;
                case "0006": //男衬衣
                    $suit_list_info["price"] = "199.00";
                    break;
                case "0016":  //女衬衣
                    $suit_list_info["price"] = "199.00";
                    break;
            }
        }
        //=====  点击数加1  =====
        $suit_list_mod->setInc($id,'click_num');

        $suit_info = $suit_rel_mod->get("tz_id = $id");
        $cus_list = array();
        if ($suit_info)
        {
            $cus_ids = $suit_info['jbk_id'];
            //=====  样衣列表  =====
            $cus_list = $custom_mod->find(array(
                'conditions' => "id IN ($cus_ids)",
                'index_key' => "",
            ));

            //=====  custom_fabric表的面料列表  =====
            $custom_fabric_list = $custom_fabirc->find(array(
                'conditions' => "custom_id IN ($cus_ids)",
                'index_key'  => "custom_id",
            ));

            //=====  相册表 关联相册  =====
            $url = SITE_URL;

            /* $gallery_list = $custom_gallery_mod->find(array(
             "fields" => "CONCAT(small_img,'$url') as small_img,CONCAT(middle_img,'$url') as middle_img,CONCAT(source_img) as source_img,sort",
             'conditions' => "custom_id IN ($cus_ids)",
             'index_key' => "",
             'order' => "id DESC",
            )); */

            //=====  如果套装有相册就去套装的相册  =====
            $gallery = $suit_gallery_mod->find(array(
                'conditions' => "suit_id=".$id,
                'index_key'  => "",
            ));
            if (!$gallery)
            {
                $cus_ids_arr = explode(",", $cus_ids);
                if ($cus_ids_arr)
                {
                    foreach ($cus_ids_arr as $key => $value)
                    {
                        if (!$value)
                        {
                            continue;
                        }
                        $gallery_list = $custom_gallery_mod->find(array(
                            "fields" => "CONCAT(small_img,'$url') as small_img,CONCAT(middle_img,'$url') as middle_img,CONCAT(source_img) as source_img,sort,custom_id,id",
                            'conditions' => "custom_id = $value",
                            'index_key' => "",
                            'order'     => "sort asc",
                        ));
                        $gallery = array_merge($gallery,$gallery_list);
                    }
                }
            }
        }
        $suit_list_info['gallery_list'] = $gallery;


        //=====  取得面料和面料属性  =====
        if ($custom_fabric_list)
        {

            //=====  获得面料相关  =====
            $fabric_id_arr = i_array_column($custom_fabric_list,'item_id');
            $fabric_ids = db_create_in($fabric_id_arr,"ID");
            $fabric_list = $fabric_mod->find(array(
                'conditions' => "$fabric_ids",
            ));

            //=====  获取面料属性相关  =====
            $COMPOSITIONID = i_array_column($fabric_list, 'COMPOSITIONID');
            $COLORID = i_array_column($fabric_list, 'COLORID');
            $FLOWERID = i_array_column($fabric_list, 'FLOWERID');
            $dict_id_arr = array_merge($COMPOSITIONID,$COLORID,$FLOWERID);
            $dict_ids = db_create_in($dict_id_arr,"ID");
            $dict_list = $dict_mod->find(array(
                'conditions' => "$dict_ids",
            ));

            foreach ($custom_fabric_list as $key => $value)
            {
                //$custom_fabric_list[$key]['chengfen'] = $dict_list[$fabric_list[$value['item_id']]['COMPOSITIONID']]['NAME']; //=====  成分  =====
                $custom_fabric_list[$key]['shazhi'] = $fabric_list[$value['item_id']]['chengfen']; //=====  砂纸  =====
                $custom_fabric_list[$key]['yanse'] = $dict_list[$fabric_list[$value['item_id']]['COLORID']]['NAME']; //=====  颜色  =====
                $custom_fabric_list[$key]['huaxing'] = $dict_list[$fabric_list[$value['item_id']]['FLOWERID']]['NAME']; //=====  花型  =====
                $custom_fabric_list[$key]['shazhi'] = $fabric_list[$value['item_id']]['SHAZHI']; //=====  砂纸  =====
                $custom_fabric_list[$key]['fabric'] = $fabric_list[$value['item_id']]['CODE']; //=====  面料编号  =====
            }
        }

        $linkAttrs = array();
        if ($cus_list)
        {
            //=====  取得属性工艺尺码  暂时屏蔽  =====
            /*  $cus_attr_id = $cus_list[0]['id'];
             $cus_attr_list = $custom_attr->find(array(
             'conditions' => "custom_id = $cus_attr_id",
             'join' => "belongs_to_attribute",
             ));
             if ($cus_attr_list)
             {
             foreach((array) $cus_attr_list as $kw => $val)
             {
             if ($val)
             {
             $tmp['name'] = $val["attr_name"];
             $tmp['value'] = $val['attr_value'];
             $linkAttrs[] = $tmp;
             }
             }
             } */
            //=====  取得尺码 和面料相关  =====
            foreach ($cus_list as $key => $value)
            {
                $cus_list[$key]['price'] = _format_price_int($value['base_price'] * PRODUCT_SHOW_SCALE);//展示价格

                $filename = ROOT_PATH.'includes/arrayfiles/size_json/'.$value['category'].'_10205.size.json';
                if (file_exists($filename))
                {
                    $jsonString = file_get_contents($filename);
                    $jsonData = json_decode($jsonString,true);
                    $size_list = $jsonData['sizeAll'] ;
                    $size = array();
                    foreach ($size_list as $key1 => $value1)
                    {
                        $size[] = $value1['Id'];
                    }
                    $cus_list[$key]['size'] = $size;
                }

                $cus_list[$key]['size_url'] = "http://m.mfd.cn/article/spec.html";
                $cus_list[$key]['cate_name'] = $pinlie[$value['category']]['name'];

                //=====  面料属性  =====
                $cus_list[$key]['chengfen'] = $custom_fabric_list[$value['id']]['chengfen'];
                $cus_list[$key]['yanse']    = $custom_fabric_list[$value['id']]['yanse'];
                $cus_list[$key]['huaxing']  = $custom_fabric_list[$value['id']]['huaxing'];
                $cus_list[$key]['shazhi']   = $custom_fabric_list[$value['id']]['shazhi'];
                $cus_list[$key]['fabric']   = $custom_fabric_list[$value['id']]['fabric'];
                $cus_list[$key]['style'] = $this->style[$value['style']];
            }

        }

        //=====  相关搭配  =====
        $suit_link_list = array();
        $suit_link_mod = m('suitlink');
        $suit_link_list = $suit_link_mod->find(array(
            'conditions' => "s_id = $id",
            'join'       => "belongs_to_suit",
            'fields'     => "suit_list.*",
            'index_key' => "",
        ));
        if ($suit_link_list)
        {
            foreach ($suit_link_list as $key => $value)
            {
                //=====  价格  =====
                $dis_count = 0;
                if ($dis_count)
                {
                    $suit_link_list[$key]['price'] = _format_price_int($value['price'] * $dis_count);
                }
                else
                {
                    $suit_link_list[$key]['price'] = _format_price_int($value['price']);
                }

                if($value['is_promotion'] && $time >= $value['star_time'] && $time <= $value['over_time'])
                {
                    $suit_link_list[$key]["price"] = _format_price_int($value["promotion_price"]);
                }

                $suit_link_list[$key]["woman_price"] = '';
                if ($value['theme'] == 16) //=====  女装要打折  =====
                {
                    $suit_link_list[$key]["woman_price"] = $suit_link_list[$key]["price"] * 0.8;
                }
            }
        }
        //         dump($suit_link_list);

        //=====  评论数量  =====
        $detai_comment_mod = m('detail_comment');
        $detail = $detai_comment_mod->get(array(
            'conditions' => "comment_id = $id AND cate= 'suit' ",
            'fields' => "count(*) as num",

        ));
        $num = isset($detail['num']) ? $detail['num'] : 0;


        $this->result['list_cus'] = $cus_list;
        $this->result['list_attr'] = $linkAttrs;
        $this->result['suit_info'] = $suit_list_info;
        $this->result['suit_link'] = $suit_link_list;
        $this->result['comment_num'] = $num;
        return $this->sresult();
    }

    /**
     *错误的返回结果
     *@author liang.li <1184820705@qq.com>
     *@2015年5月7日
     */
    function eresult()
    {
        $arr['statusCode'] = 0;
        $arr['error']['errorCode'] = $this->errorCode;
        $arr['error']['msg'] = $this->msg;
        return ($arr);
    }

    /**
     *token错误的返回结果
     *@author liang.li <1184820705@qq.com>
     *@2015年5月7日
     */
    function tresult()
    {
        $arr['statusCode'] = 0;
        $arr['error']['errorCode'] = 100;
        $arr['error']['msg'] = '账号不存在';
        return ($arr);
    }

    /**
     *成功的返回结果
     *@author liang.li <1184820705@qq.com>
     *@2015年5月7日
     */
    function sresult()
    {
        $arr['statusCode'] = 1;
        $arr['result']['data'] = $this->result;
        return ($arr);
    }

    function processPrice($userid){
        if(!$userid) return array();
        $mod_first = m("orderfirstlog");
        $list = $mod_first->find(array(
            "conditions" => "user_id='{$userid}' AND is_active=1",
        ));

        $aData = array();

        foreach((array) $list as $key => $val){
            $aData[] = $val["cloth"];
        }

        return $aData;
    }
    
    /**
    *content
    *@author liang.li <1184820705@qq.com>
    *@2016年1月31日
    */
    function test() 
    {
        echo pc_url();exit;
    }


    /**
     *预加载加入购物车
     *@author liang.li <1184820705@qq.com>
     *@2015年11月24日
     */
    function preCart()
    {
        
        $user = $_SESSION['user_info'];
        if(!$user['user_id'])
        {
            $link = array("app" => "member","act" => "login" );
            $_view = &v();
            $url = $_view->build_url($link);
            header('Location: '.$url.'?ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));
            return;
        }
        $args = $this->get_params();
        $id = $args[0];
        if ($id)
        {
            $type = "suit";
        }
        $cid = isset($_GET['cid']) ? $_GET['cid'] : '';
        if ($cid)
        {
            $type = 'diy';
        }
        if (!$id && !$cid)
        {
            $this->show_message('参数错误');
            return ;
        }
        $cstr = "";
        if ($id)
        {
            $data['id'] = $id;
            $user_info = array();
            if($this->visitor->has_login){
                $m =&m('member');
                $user_id = $this->visitor->get('user_id');//509
                $user_info = $m->get($user_id);
            }
            $res = $this->goodsInfoj($data,$user_info);
            if (!$res['statusCode'])
            {
                $this->show_warning($res['error']['msg']);
                return;
            }
        }
        else
        {
            $list['suit_info']['id'] = "0";
            $cid = explode("|", $cid);
            if (!$cid)
            {
                $this->show_message('参数错误');
                return ;
            }
            $path = dirname(__FILE__);
            if (file_exists(ROOT_PATH.'includes/arrayfiles/pinlei.php'))
            {
                $pinlie = include ROOT_PATH.'/includes/arrayfiles/pinlei.php';
            }
            else
            {
                $this->show_message('品类错误');
                return ;
            }
            $cus_list = array();
            foreach ($cid as $key => $value)
            {
                $filename = ROOT_PATH.'includes/arrayfiles/size_json/'.$value.'_10205.size.json';
                if (file_exists($filename))
                {
                    $jsonString = file_get_contents($filename);
                    $jsonData = json_decode($jsonString,true);
                    $size_list = $jsonData['sizeAll'] ;
                    $size = array();
                    foreach ($size_list as $key1 => $value1)
                    {
                        $size[] = $value1['Id'];
                    }
                    $tmp['cate_name'] = $pinlie[$value]['name'];
                    $tmp['cate_name_v'] = $pinlie[$value]['name']."尺码";
                    $tmp['size'] = $size;
                    $tmp['id'] = $value;
                    $cus_list[] = $tmp;
                }
            }
            $list['list_cus'] = $cus_list;
            $this->result = $list;
            $res['statusCode'] = 1;
            $res['result']['data'] = $this->result;
            //       print_exit($list);
            $cstr = $_POST['cstr'];
        }

        $serve_mod = &m("serve");
        $servelist = $serve_mod->find(array(
            "conditions" => " 1 ",
        ));
        $rData = array();
        foreach($servelist as $key => $val){
            $rData[] = $val["region_id"];
        }
        $region_mod = &m("region");
        $citylist = $region_mod->find(array(
            "conditions" => "region_id ".db_create_in($rData),
        ));
        do{
            $api_token = ApiAuthcode($this->visitor->get('user_id'), 'ENCODE', 'kuteiddiy', 0);
        } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));

        $diy_url = diy_url()."cart.html?token=".$api_token;
        $this->history();
        $this->assign("citylist",  $citylist);
        $this->assign('info',$res);
        $this->assign('cstr',$cstr);
        $this->assign('type',$type);
        $this->assign('diy_url',$diy_url);
        $this->display('custom/pre_cart.html');
    }

    /**
     *加载附近门店
     *@author liang.li <1184820705@qq.com>
     *@2015年11月24日
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
                      //  unset($regions[$key]);
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

    /**
     *历史量体数据
     *@author liang.li <1184820705@qq.com>
     *@2015年11月28日
     */
    function loadFigure()
    {
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
            $conditions = " firsttime > 1447171200 AND figure_state = 1 AND (customer_name = '{$keyword}' OR customer_mobile = '{$keyword}')";
        }else{
            $conditions = " firsttime > 1447171200 AND (customer_mobile = '{$userinfo['phone_mob']}') AND figure_state = 1";
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
        foreach($aData as $key => $val)
        {
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
            
           /*  if (!$val['is_first'] && $val['service_mode'] == 3)
            {
                $aData[$key]['is_free'] = 1;
                if ($servers[$val["id_serve"]]['storetype'] == 2)
                {
                    $aData[$key]['is_free'] = 0;
                }
            } */
            $aData[$key]['address'] = isset($servers[$val["id_serve"]]) ? $servers[$val["id_serve"]]['serve_address'] : '';
        }

        $this->json_result($aData);
        die();
    }

    //=====  加载量体师  =====
    function loadFigurer()
    {
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
                    //unset($servelist[$key]);
                }
            }
            
           /*  $servelist[$key]['is_free'] = 1;
            $servelist[$key]['money_name'] = '收费';
            if ($val['storetype'] == 2) //=====    自营门店  =====
            {
                //=====  根据手机号匹配   =====
                if (($membner_lv_id == VIP_ID && $val['mobile'] == $user_name) || ($invi_member_lv_id == VIP_ID && $val['mobile'] == $invi_info['user_name']))
                {
                    $servelist[$key]['is_free'] = 0;
                    $servelist[$key]['money_name'] = '免费';
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
        /*  $history =  array
         (
         [0] => array
         (
         [serveid] => 169
         [type] => suit
         [figuretype] => 2
         [region] => 248
         [realname] => 李亮
         [phone] => 13999999999
         [timepart] => pm
         [dateline] => 2015-12-04
         [region_name] => 青岛
         )

         [1] => array
         (
         [region] => 248
         [address] => 才发士大夫阿萨德
         [realname] => 李亮
         [phone] => 13969666666
         [dateline] => 2015-11-29
         [timepart] => am
         [type] => suit
         [figurerid] => 726
         [figuretype] => 6
         [region_name] => 青岛
         )

         [2] => array
         (
         [figureid] => 1488
         [figuretype] => 5
         [type] => suit
         )

         [3] => array
         (
         [figureid] => 1488
         [figuretype] => 5
         [type] => suit
         )

         ); */
        /*   $history = array(
         0 => array(
         'figureid'   => 1488,//=====  custom_figure表的主键id  =====
         'figuretype' => 5,
         ),

         1 => array(
         "figuretype"  => 2,
         "dateline" => "2015-11-29",
         "phone" => "13111111111",
         "realname" => "real",
         "region"	=> "248",
         "serveid"	=>"170",
         "timepart"	=>"am",
         "address" => "才发士大夫阿萨德",
         ),

         2 => array(
         "address"  => "3333333333",
         "dateline" => "2015-10-06",
         "figurerid" => "1042",
         "figuretype" => "6",
         "phone"  => "13111111111",
         "realname" => "rea",
         "region"   => "248",
         'timepart' => "pm",
         ),
         3 => array(
         "address"  => "3333333333",
         "dateline" => "2015-10-06",
         "figurerid" => "543",
         "figuretype" => "6",
         "phone"  => "13111111111",
         "realname" => "rea",
         "region"   => "248",
         'timepart' => "pm",
         )
        );
        */
        $history = json_decode(base64_decode($_COOKIE["suit_history"]),1);
        //   print_exit($history);
        $figures = array();
        $serves = array();
        $assign = array();
        if (!$history)
        {
            return array();
        }
//      print_exit($history);
        foreach((array)$history as $key => $val){
            if($val['figuretype'] == 5){
                $figures[] = $val["figureid"];
            }elseif($val["figuretype"] == 2){
                $serves[] = $val["serveid"];
            }elseif($val['figuretype'] == 6){
                $assign[] = $val['figurerid'];
            }
        }
//       echo 1;exit;
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
            if ($list)
            {
                foreach((array)$list as $key => $val){
                    $ids[$val["id_serve"]]   = $val["id_serve"];
                    $aData[$val["figure_sn"]] = $val;
                }
            }

            $servers = $server_mod->find(array(
                'conditions' => "idserve ".db_create_in($ids),
            ));
            if ($aData)
            {
                foreach($aData as $key => $val){
                    $aData[$key]['address'] = isset($servers[$val["id_serve"]]) ? $servers[$val["id_serve"]]['serve_address'] : '';
                }
            }

        }

        //服务点
        if ($serves)
        {
            $regions = $server_mod->find(array(
                "conditions" => "idserve ".db_create_in($serves),
            ));
        }


        //指定量体师
        $member_mod = &m("member");
        if ($assign)
        {
            $members = $member_mod->find(array(
                "conditions" =>"figure_liangti.alone=1 AND member.user_id ".db_create_in($assign).$conditions,
                'join'       => "has_lt",
                'fields'     => "member.real_name, member.user_id, member.phone_mob, figure_liangti.manager_id"
            ));
        }

        $region_mod = &m("region");

        $rData = array();
        if ($members)
        {
            foreach((array)$members as $key => $val){
                $val['address'] = $address[$val["manager_id"]];
                $rData[] = $val["manager_id"];
            }
        }

        if ($rData)
        {
            $servers = $server_mod->find(array(
                "conditions" => "userid ".db_create_in($rData),
            ));
        }


        $fData = array();
        if ($servers)
        {
            foreach($servers as $key => $val){
                $fData[$val["userid"]] = $val;
            }
        }


        if ($members)
        {
            foreach((array)$members as $key => $val){
                if(isset($fData[$val["manager_id"]])){
                    $members[$key]['address'] = $fData[$val["manager_id"]]['serve_address'];
                }
            }
        }

        if ($history)
        {
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

        }
        // print_exit($history);
        $this->assign("history", $history);
    }

    /**
     *判断是否登陆
     *@author liang.li <1184820705@qq.com>
     *@2015年11月25日
     */
    function is_login()
    {
        if(!$this->visitor->has_login){
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 200;
            $return['error']['msg'] 	  = '请先登录';
            echo json_encode($return);
            return false;
        }

        $return['statusCode']		  = 1;
        $return['error']['errorCode'] = 200;
        $return['error']['msg'] 	  = '请先登录';
        echo json_encode($return);
    }


    function addDisCart()
    {
        if(!$this->visitor->has_login){
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 200;
            $return['error']['msg'] 	  = '请先登录';
            echo json_encode($return);
            return false;
        }

        $m =&m('member');
        $user_id = $this->visitor->get('user_id');//暂时写死
        $user_info =$m->get($user_id);
        $suit_id = intval($_REQUEST['sid']);
        $dis = stripslashes($_REQUEST['dis']);

        if(empty($user_info['user_token'])){
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 122;
            $return['error']['msg'] 	  = '参数错误';
            echo json_encode($return);
            return false;
        }

        if(!$suit_id){
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 123;
            $return['error']['msg'] 	  = '参数错误';
            echo json_encode($return);
            return false;
        }
        $re = $this->addApiDisCart($user_info['user_token'],$dis,$suit_id);
        if ($re['statusCode'] == 0)
        {
            dump($re['error']['msg']);
        }
        $re = json_encode($re);
        echo $re;

    }

    /**
     * 主题 添加购物车 套装添加购物车
     * @version 1.0.0
     * @author liang.li <1184820705@qq.com>
     * @2015-4-11
     */
    function addApiDisCart($token,$dis,$suit_id)
    {

        $m_mod = m('member');
        $user_info = array();
        if ($token)
        {
            $user_info = $m_mod->get("user_token = '$token'");
        }
        if (!$user_info)
        {
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 100,
                    'msg'       => '账号不存在',
                ),
            );
            return $return;
        }
        if (!$suit_id)
        {
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 1;
            $return['error']['msg'] 	  = 'suit_id参数必传';
            return ($return);
        }
        // dump($user_info);
        $user_id = $user_info['user_id'];
        $dis = stripslashes($dis);
        //$ids = '{"3":"167/18A","4":"198/32A"}';
        $dis_arr = json_decode($dis,true);
        $cart_mod = m('cart');
        $customs_mod = m('custom');
        $suit_list_mod   = m('suitlist');
        $suit_rel_mod = m('suitrelat');


        $suit_info = $suit_rel_mod->get("tz_id = $suit_id");
        if (!$suit_info)
        {
            $this->msg = '无此数据';
            return $this->eresult();
        }
        $cus_ids = $suit_info['jbk_id'];
        $cus_ids_arr = explode(',', $cus_ids);
        if ($cus_ids_arr)
        {
            $arr_cus_ids =array();
            foreach($cus_ids_arr as $i=>$d)
            {
                if($d==0)
                    continue;//if($d!=0){$arr[]=$d}
                $arr_cus_ids[]=$d;
            }
            sort($arr_cus_ids);
        }
        // dump($cus_ids_arr);

        //=====  格式化数据  =====
        $ids = array_keys($dis_arr);
        sort($ids);
        if (!$ids)
        {
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 1;
            $return['error']['msg'] 	  = '无数据';
            return ($return);
        }

        //=====  前段传过来的id和套装对应的id不对应 参数不对  =====
        if ($ids != $arr_cus_ids)
        {
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 1;
            $return['error']['msg'] 	  = 'id无法对应';
            return $return;
        }

        $conditions = db_create_in($ids,'id');
        $custom_list = $customs_mod->find(array(
            'conditions' => $conditions,
        ));
        // dump($custom_list);
        if (!$custom_list)
        {
            $return['statusCode']		  = 0;
            $return['error']['errorCode'] = 1;
            $return['error']['msg'] 	  = '无数据';
            return $return;
        }

        include_once ROOT_PATH.'includes/goods.mbase.php';
        $goods_base_mod = new BasemGoods();
        $data['user_id'] = $user_id;
        $data['type'] = 'suit';
        $data['source_from'] = 'app';
        $data['dis_ident'] = $this->dis_ident();
        $data['suit_id'] = $suit_id;
        foreach ($custom_list as $key => $value)
        {
            if (!$dis_arr[$key])
            {
                $return['statusCode']		  = 0;
                $return['error']['errorCode'] = 1;
                $return['error']['msg'] 	  = '请选择尺码';
                return $return;
            }
            // dump($value);
            // return $value['small_img'];
            $data['image'] = $value['small_img'];
            $data['cloth'] = $value['category'];
            $data['goods_id'] = $value['id'];
            $data['size'] = $dis_arr[$key];

            //=====  取得工艺  =====
            $params = $goods_base_mod->_group_info($key);
            // dump($params);

            $data['params'] = json_encode($params['process']);
            if (!$params['oFabric']['CODE'])
            {
                $return['statusCode']		  = 0;
                $return['error']['errorCode'] = 1;
                $return['error']['msg'] 	  = '找不到面料';
                return $return;
            }

            $data['fabric'] = $params['oFabric']['CODE'];

            //=====    首推  =====
            $first = 0;
            if (count($custom_list) == 1)
            {
                //=====  查询此面料是否是首单面料  =====
                $fab_mod = &m("fabs");
                if ($fab_mod->get("CODE = '{$data['fabric']}' AND is_first = 1 "))
                {
                    $pinlei_frist_arr = array('0006','0016','0003');//=====  男女衬衣和男西服才是首单  =====
                    if(in_array($value['category'], $pinlei_frist_arr))
                    {
                        //=====  购物车是否已经存在此首单面料  =====
                        $has = $cart_mod->get("user_id = '$user_id'  AND first = '{$value['category']}' ");
                        if(!$has)
                        {
                            $aData = $this->processPrice($user_info['user_id']);
                            if(!in_array($value['category'], $aData))
                            {
                                $dis_id = $first = $value['category'];
                                //$dis_id = $first = '0016';
                            }
                        }
                    }
                    // }
                }
            }

            $data['first'] = $first;

            $data['ident'] = $this->gen_ident();
            $cart_mod->add($data);
            //=====  加1   =====
            /*  $cart_info = $cart_mod->get("goods_id={$data['goods_id']} AND type='suit'  AND size='$value' AND user_id=$user_id ");
             if ($cart_info)
             {
             $cart_mod->setInc(array('rec_id'=>$cart_info['rec_id']),'quantity');
             }
             else
             {
            } */
        }

        $return = array(
            'statusCode' => 1,
            'result' => array(
                'success' => '成功',
            )
        );


        return $return;

    }

    function gen_ident($id = 0){
        $cart_mod = m('cart');
        do{
            $str='abcdefghigklmnopqrstuvwxyz0123456789';
            $code ='';
            for($i=0;$i<5; $i++){
                $code .= $str[mt_rand(0, strlen($str)-1)];
            }
            $il = strlen($id);
            for ($i=$il;$i<10;$i++){
                $id = '0'.$id;
            }
            $ident =  $code.$id;
        }
        while($cart_mod->get("ident = '{$ident}'"));
        return $ident;
    }

    /**
     * 购物车套装的dis_ident
     * @param number $id
     * @return string
     */
    function dis_ident($id = 0)
    {
        $cart_mod = m('cart');
        do{
            $str='abcdefghigklmnopqrstuvwxyz0123456789';
            for($i=0;$i<8; $i++){
                $code .= $str[mt_rand(0, strlen($str)-1)];
            }
            $il = strlen($id);
            for ($i=$il;$i<10;$i++){
                $id = '0'.$id;
            }
            $ident =  $code.$id;
        } while($cart_mod->get(" dis_ident = '{$ident}'"));
        return $ident;
    }






























    /**
    *content
    *@author liang.li <1184820705@qq.com>
    *@2016年1月8日
    */
    function getUserInfo($user_id)
    {
        $mod = m('member');
        return $mod->get_info($user_id);
    }


    /**************** 详情页********************/
    function info()
    {

    	$args = $this->get_params();
    	$id = empty($args[0]) ? 0 : intval($args[0]);
    	if (!$id)
    	{
    		$this->show_warning('Hacking Attempt');
    		return;
    	}

    	#排除ipad 访问跳转到wap站
    	if (is_mobile_request()){
    		header("Location:http://m.rctailor.com/index.php/custom-info-$id.html");
    	}

    	if(preg_match('/(ipad|ipod)/i', strtolower($_SERVER['HTTP_USER_AGENT']))){
    		$this->minfo();
    	}else{

			$this->assign("id", $id);

			//-------是否是重新设计的商品，来源于购物车
			$this->assign("rec_id", $args[1]);
			$info = $this->_customs_mod->get($id);
			if($info['cst_store']<=0){
			    $this->assign('cate',$info['cst_cate']);
			    $this->display('custom/nostore.html');
			    return;
			}


			$this->assign("info", $info);
	    	$this->assign('title',"我要定制-".$info['cst_name']);

			/* 记录用户点击 */
			$history = array();
			$history['cid']=$info['cst_id'];
			$history['pid']=$info['cst_cate'];
			$history['child']=$info['sec_cate'];
			$cs =& cs();
			$cs->add_history($history);


			/* 获取定制流程、量体数据 暂时静态id */
			$article_mod =& m('article');
			$this->assign("aflow", $article_mod->get_article_info(48));			//定制流程
			$this->assign("ameasure", $article_mod->get_article_info(64));		//量体指南
			$this->assign("pricedesc", $article_mod->get_article_info(71));		//价格说明

			//$this->assign("recommdata", $cs->get_recomm_costom(12));		//推荐基本款
			//$this->assign("design", getOtherDesign());				    //其他人设计



			$this->assign("recommdata", $this->get_recom($id,12));		//关联基本款


			/* 取得商品评论 */
			$this->_comment_limit='0,10';
	 	    $data = $this->_get_goods_comment($id, 10);
	 	    $this->_assign_goods_comment($data);

	        $this->display('custom/cutom_diy.html');
    	}
    }
    //ipad 页面
    function minfo(){
//     	if(preg_match('/(ipad|ipod)/i', strtolower($_SERVER['HTTP_USER_AGENT']))){
    		$args = $this->get_params();
	    	$id = empty($args[0]) ? 0 : intval($args[0]);
	    	if (!$id)
	    	{
	    		$this->show_warning('Hacking Attempt');
	    		return;
	    	}
	    	$this->assign("id", $id);
	    	$cs =& cs();
	    	$data = $cs->get_basis_info($id,1);
	    	$this->_format_mobile_data($data);

			$info = $this->_customs_mod->get($id);
			$this->assign("info", $info);
	    	$this->assign("current", 'gallery');
			$clist = array();

			if($info["link_cst"]){
			    $links = $this->_customs_mod->find(array(
			        'conditions' => "cst_id IN ({$info["link_cst"]})",
			    ));

			    $num = 0;
			    $group =1;

			    foreach($links as $key => $val){
			        $num ++;
			        $clist[$group][] =$val;
			        if($num % 5 == 0){
			            $group ++;
			        }
			    }
			}

			$this->assign('clist', $clist);
			$this->_config_seo(array(
			    'title' => $info['cst_name'],
			    'description' => Conf::get('site_description'),
			    'keywords' => Conf::get('site_keywords')
			));
			$this->_customs_mod->setInc("cst_id='{$id}'", 'cst_views');

	    	$this->display('custom/mdiy/info.html');
//     	}else{
//     		$this->minfo();
//     	}
    }

    function _format_mobile_data($data){
    	$return = array();
		#面料选择|里料设计|纽扣选择|款式设计|个性签名
    		foreach ($data['data'] as $key=>$v){
    				 		foreach ($data['data'][$key] as $partid => $r){

    				 			if (in_array($partid, $data['fabric'])){
    				 				$temp = array();
    				 				$k = 1;
    				 				$return['data'][$k]['name'] = '面料选择';
    				 				$temp = $r;
    				 			}

    				 			if (in_array($partid, $data['material'])){
    				 				$temp = array();
    				 				$k = 2;
    				 				$return['data'][$k]['name'] = '里料设计';
    				 				$temp = $r;

    				 			}
    				 			if (in_array($partid, $data['buttons'])){
    				 				$temp = array();
    				 				$k = 4;
    				 				$return['data'][$k]['name'] = '纽扣设计';
    				 				$temp = $r;
    				 			}
    				 			if (in_array($key, $data['style'])){
    				 				$temp = array();
    				 				$k = 3;
    				 				$return['data'][$k]['name'] = '款式设计';
    				 				foreach ($v as $p=>$info){
    				 					foreach ($info['part'] as $cid=>$value){
    				 						$v[$p]['name'] = $data['category'][$p]['cate_name'];
    				 						if (!in_array($value['info']['cate_id'], Constants::$graphRuleoutParentCid)){//工艺下不纳入出图规则计算
    				 							$v[$p]['part'][$cid]['info']['alias'] = $data['category'][$value['info']['cate_id']]['alias'];
    				 						}
    				 					}
    				 				}
    				 				$temp['part'] = $v;
    				 			}

    				 			if (in_array($partid, Constants::$signatureParent)){
    				 				$temp = array();
    				 				$k = 5;
    				 				$return['data'][$k]['name'] = '个性签名';
    				 				$temp = $r;
    				 			}

    				 			#别名处理
    				 			if (3!=$k){
    				 				foreach ($temp['part'] as $p=>$info){
    				 					foreach ($info as $cid=>$value){

    				 						if (1 == $k){
    				 							$temp['part'][$p]['info']['alias'] = $data['category'][$value['top_cate']]['alias'];
    				 						}
    				 						if (4 == $k){
    				 							$temp['part'][$p]['info']['alias'] = $data['category'][$value['cate_id']]['alias'];
    				 						}
    				 					}

    				 				}
    				 			}

    				 			$return['data'][$k]['part'] = $temp['part'];
    				 			}

    				 }
//     				 print_r($data['customs']);exit();
    				 $this->assign("data", $return['data']);	//关联基本款
    				 $this->assign("cinfo", $data['customs']);	//关联基本款
    				 $this->assign("size", $data['size']);	//关联基本款
    				 $this->assign("menu", range(1,5));		//左侧大类
    				 $this->assign("sequence", json_encode($data['key_sequence']));		//左侧大类
    				 $this->assign("consumption", json_encode($data['consumption']));	//单耗
    				 $this->assign("processaa", json_encode($data['process']));		//工艺
//     				 $this->assign("process", $data['process']);		//工艺
//     				 var_dump(json_encode($data['process']));exit();
    				 $this->assign("imgurl", SITE_URL.'/'.IMG_PATH.'/custom/'.$data['customs']['cst_id'].'/');		//左侧大类
//     	print_r($return['data']);exit();
//     	print_r(ksort($return['data']));exit();
    }
    /**
     * 关联基本款
     * @param $id 基本款id
     * @return array
     * @author Ruesin
     */
    function get_recom($id,$nums = 12){

        $links = $this->_link_mod->findAll(array(
            "conditions" => "c_id = '{$id}'",
        ));
       if(!empty($links)){

           foreach ($links as $row){
               $dId[] = $row['d_id'];
           }

           $lks = $this->_link_mod->findAll(array(
               "conditions" => db_create_in($dId,'d_id')
           ));
           foreach ($lks as $r){
           	$cId[] = $r['c_id'];
           }
           $cst = $this->_customs_mod->findAll(array(
               "conditions" => db_create_in($cId,'cst_id')
           ));

       }else{
           $cst = $this->_customs_mod->findAll(array(
//                "conditions" => " 1 = 1",
               "order"      => "RAND()",
               "limit"      => $nums,
           ));
       }
       //====== 价格四舍五入 //Ruesin
       foreach ($cst as &$row){
           $row['cst_cost']   = round($row['cst_cost']);
           $row['cst_price']  = round($row['cst_price']);
           $row['cst_market'] = round($row['cst_market']);
       }
       return $cst;
    }























    //---------------------------------------定制 flash 交互数据----------------------------------
    /**
     * 基本款数据
     */
    function get_basis() {

    	$args = $this->get_params();
    	$id = empty($args[0]) ? 0 : intval($args[0]);
    	$rtype = empty($args[1]) ? 'json' : $args[1];

    	/* 实例化基本款的公共接口 */
    	$cs =& cs();
    	return $cs->get_basis_info($id,$rtype);

    }

    /**
     * mobile 基本款数据
     */
    function get_basis_mobile() {

    	$args = $this->get_params();
    	$id = empty($args[0]) ? 0 : intval($args[0]);
    	$rtype = empty($args[1]) ? 'json' : $args[1];

    	/* 实例化基本款的公共接口 */
    	$cs =& cs('Mobile');
    	return $cs->get_basis_info($id,$rtype);

    }

    /**
     * 基本款组件类型数据
     */
    function get_tname(){
    	$cs =& cs();
    	$data = $cs->_get_parttype();
    	ajaxReturn(1,'',$data);
    }


/* 取得商品评论 */
    function _get_goods_comment($goods_id, $num_per_page)
    {
        $data = array();

        $page = $this->_get_page($num_per_page);
        //var_dump($page);
        /*goods_id不能正确读出
        $order_customs_mod =& m('ordergoods');
        $comments = $order_customs_mod->find(array(
            'conditions' => "goods_id = '$goods_id' AND evaluation_status = '1'",
            'join'  => 'belongs_to_order',
            'fields'=> 'buyer_id, buyer_name, anonymous, evaluation_time, comment, evaluation',
            //买家用户ID，买家用户名称,是否匿名,时间,内容,星级
            'count' => true,
            'order' => 'evaluation_time desc',
            'limit' => $page['limit'],
        ));
        */
        /*
        $order_customs_mod =m('comments');
        $comments = $order_customs_mod->find(array(
        	//'conditions' => "1=1",
            'conditions' => "goods_id = '$goods_id' ",
            'fields'=> 'user_id as buyer_id, user_name as buyer_name, anonymous, add_time as evaluation_time,content as comment ,rank as evaluation',
            //买家用户ID，买家用户名称,是否匿名,时间,内容,星级
            'count' => true,
            'order' => 'add_time desc',
            //'limit' => $page['limit'],
            'limit' => '0,'.$num_per_page,
        ));


        //var_dump($page['limit']);
        $data['comments'] = $comments;

        //var_dump($comments);

        $page['item_count'] = $order_customs_mod->getCount();
        */

        $res=getCommentByGid($goods_id,0, 'goods_comment',$this->_comment_limit);
        foreach ($res as $k=>$v){
        	//var_dump($res[$k]);
        	//var_dump($v);exit;
        	$userinfo=getUinfoByUid($v['uid']);
        	//var_dump($userinfo['user_name']);exit;

        	$res[$k]['buyer_name']=$userinfo['user_name'];
        }

        $data['comments'] = $res;



        $this->_format_page($page);
        $data['page_info'] = $page;
        $data['more_comments'] = $page['item_count'] > $num_per_page;

        return $data;
    }

	/* 赋值商品评论 */
    function _assign_goods_comment($data)
    {
        $this->assign('goods_comments', $data['comments']);
        $this->assign('page_info',      $data['page_info']);
        $this->assign('more_comments',  $data['more_comments']);
    }




    /**
    *频道 列表
    *@author liang.li <1184820705@qq.com>
    *@2015年9月2日
    */
    function dindex()
    {
        $dis_mod  = m('jpjz_dissertation');
        $topicArr = $dis_mod->find(array ( 'order' => "sort_order ASC",));
				//ns add 当前app，用于前端样式的判断
				$this->assign('app',APP);
        $this->assign('list',$topicArr);
        $this->display('custom/d_index.html');
    }





    /**
     *
     * 首页 列表（首页）
     *  @author yusw
     * @2015年6月11日
     */
    function d_list()
    {

        $topic = intval($_REQUEST['topic']);
        $lists =$this->d_lists($topic);


        $this->assign('name','麦富迪尚品');
        $this->assign('lists',$lists);
        $this->assign('topic',$topic);
        $this->assign('title','商品列表');
        $this->display('custom/d_lists.html');
    }


    /**
     *  列表页（数据）
     * @author yusw
     * @2015年6月11日
     */
    function d_lists($topic=0,$limit='0,10'){
        $dis_mod  = m('jpjz_dissertation');
        $link_mod = m("links");
        $time = time();
        //主题
        $topicArr = $dis_mod->find(array ( 'order' => "sort_order ASC",));
        $this->assign('top_name',$topicArr[$topic]['title']);
        $current = $topicArr[$topic];
        $topicArr = array_values($topicArr);
        $t_arr = array();
        if($limit == '0,10'){
            $t_arr = i_array_column($topicArr,'title');
        }
        $customArr = $link_mod->find(array (
            "conditions" => "links.d_id = '{$current["id"]}' AND suit_list.is_sale=1 AND suit_list.theme != 0 AND (suit_list.to_site = '0' OR suit_list.to_site = 'APP')",
            "join" => "app_custom",
            'order' => "links.lorder ASC",
            'limit' => $limit, //暂时不要，瀑布流屏蔽
            'count'=>1,
            'fields' => "suit_list.*,suit_list.id as id",
            'index_key' => "",
        ));
        $custom =array();
        $count = 0;
        if(!empty($customArr)){
            foreach((array) $customArr as $key => $val){
                if($this->visitor->has_login){

                    $user_id = $this->visitor->get('user_id');
                    $user_info =$this->getUserInfo($user_id);
                    $aData = $this->processPrice($user_id);

                    if(!empty($user_info)){
                        //  $val['price'] = _format_price_int($val['price'] * $user_info['dis_count']);
                        if(!in_array($val['category'], $aData) && $val["is_first"] == 1){
                            switch ($val['category']){
                                case "0003": //西装
                                    $val["price"] = "999";
                                    break;
                                case "0006": //男衬衣
                                    $val["price"] = "199";
                                    break;
                                case "0016":  //女衬衣
                                    $val["price"] = "199";
                                    break;
                            }
                        }
                    }

                }

                if($val['is_promotion'] == 1 && $time >= $val['star_time'] && $time <= $val['over_time'])
                {
                    $val["price"] = $val["promotion_price"];
                }
                $val['price'] = _format_price_int($val['price']);
//                $val["woman_price"] = '';
//                if ($val['theme'] == 16) //=====  女装要打折  =====
//                {
//                    $val["woman_price"] = _format_price_int($val["price"] * 0.8);
//                }

                $custom[] = $val;
            }

            //            $custom = current($custom);

            $count = $link_mod->getCount();
        }

        $arr = array(
            't_arr'=>$t_arr,
            'content' => $custom,
            'count' => $count < 0 ? 0 : $count,
        );

        return $arr;
    }




}

?>
