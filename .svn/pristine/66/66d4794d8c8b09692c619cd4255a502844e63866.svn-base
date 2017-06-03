<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
use Cyteam\Shop\Type;
use Cyteam\Shop\Type\Types;

/**
 |--------------------------------------------------------------------------
 | 属性diy
 |--------------------------------------------------------------------------
 |
 |
 | @author 小五 <xiao5.china@gmail.com>
 |
 */
    
class FdiyApp extends MallbaseApp
{
    function __construct(){
        parent::__construct();
        header("Content-Type:text/html;charset=" . CHARSET);
        $this->_fdiy_management_mod =& m('fdiy_management');
        $this->_mod_fdict = &m("fdiy_dict");
        $this->_mod_fcrossrule = &m("fdiy_crossrule");
        $this->_mod_fbcategory = &m("fbcategory");
        /* 读取后台树形配置 层级id只能手动定义 */
        $this->_conflictCate = ['0001'=>2,'0003'=>3,'0004'=>4,'0005'=>346,'0006'=>340,'0007'=>347,'0018'=>781];
        $this->_config_seo('title', 	"我要定制");
    }

    /**
     * http://local.mfd.com/fdiy-1-3.html
     * 属性diy入口
     * @param 1            品类
     */
    function index()
    {
        $user = $this->visitor->get();
        $this->assign('mid', empty($user) ? 0 : $user['user_id']);
        $s = isset($_GET['s']) ? $_GET['s'] : "";//搜索
        $pid = isset($_GET['pid']) ? $_GET['pid'] : 0;//搜索
        $this->assign("s",$s);
        $this->assign('pid',$pid);

        $args = $this->get_params();
        $cid = empty($args[0]) ? '1' : $args[0]; //配置一级id
        $args[1] = 3;
        /* 取得diy配置 */
        $acategories = $this->_fdiy_management_mod->get_list($cid);
        if (empty($acategories)){
            $this->show_warning('！该品类下还没有关联定制属性');
            return;
        }

        $sid = empty($args[1]) ? current($acategories)['cate_id'] : $args[1]; //配置二级id
        $dids= array_unique(explode(',', $acategories[$sid]['did']));

        if (empty($dids)){
            $this->show_warning('！该品类下还没有录入定制属性');
            return;
        }
        //=====  犬种分类  =====
        $conditions = "parent_id = 21";

        $list = $this->_mod_fbcategory->find(array(
            'conditions' => $conditions,
            'order' =>"sort_order ASC",
        ));
        $this->assign('plist',$list);
        if($list)
        {
            $quan_ids = i_array_column($list, "cate_id");

            $qconditions = db_create_in($quan_ids,"parent_id");

            if ($pid)
            {
                $qconditions .= " AND parent_id = $pid";
            }
            if ($s)
            {
                $qconditions .= " AND cate_name like '%$s%' ";
            }

            $lists = $this->_mod_fbcategory->find(array(
                'conditions' => $qconditions." AND if_show =1",
                "order"      => "letter_retrieval ASC,sort_order ASC",
                'index_key' => "",
            ));
            if($lists)
            {
                foreach ($lists as $key => $value)
                {
                    if (!$pid && !$s) //=====  点选全部 并且没有搜索 要把 常用的排前面  =====
                    {
                        if($value['if_common'])
                        {
                            $common_lists[]=$value;
                            unset($lists[$key]);
                        }

                    }
//
//                    if (isset($list[$value['parent_id']]))
//                    {
//                        $list[$value['parent_id']]['son_list'][] = $value;
//                    }
                   // $group_lists[$value['letter_retrieval']][$key]=$value;

                }
                // var_dump($group_lists['']);
//                if(isset($group_lists[''])){
//                    array_push($group_lists,$group_lists['']);
//                    unset($group_lists['']);
//                }
//                $letters=array_keys($group_lists);
            }

        }
        $ret_arr = $lists;
        if ($common_lists)
        {
            $ret_arr = array_merge($common_lists,$lists);
        }
        $leg = ceil(count($ret_arr)/12);
        $flist = [];
        for ($i=0;$i<$leg;$i++)
        {
            $fre = [];
            $start = $i*12;
            $larr = array_slice($ret_arr,$start,12);
            $flist[$i]['list'] = $larr;
        }
        $this->assign("flists",$flist);
        $this->display('fdiy/fdiy.dict.html');
    }


    /**
     * http://local.mfd.com/fdiy-1-3.html
     * 属性diy入口
     * @param 1            品类
     */
    function indexs()
    {

        $user = $this->visitor->get();
        $this->assign('mid', empty($user) ? 0 : $user['user_id']);
        $s = isset($_REQUEST['s']) ? $_REQUEST['s'] : "";//搜索
        $pid = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : 0;//搜索
        $this->assign("s",$s);
        $this->assign('pid',$pid);

        $args = $this->get_params();
        $cid = empty($args[0]) ? '1' : $args[0]; //配置一级id
        $args[1] = 3;
        /* 取得diy配置 */
        $acategories = $this->_fdiy_management_mod->get_list($cid);
        if (empty($acategories)){
            $this->show_warning('！该品类下还没有关联定制属性');
            return;
        }

        $sid = empty($args[1]) ? current($acategories)['cate_id'] : $args[1]; //配置二级id
        $dids= array_unique(explode(',', $acategories[$sid]['did']));

        if (empty($dids)){
            $this->show_warning('！该品类下还没有录入定制属性');
            return;
        }
        //=====  犬种分类  =====
        $conditions = "parent_id = 21";

        $list = $this->_mod_fbcategory->find(array(
            'conditions' => $conditions,
        ));
        $this->assign('plist',$list);
        if($list)
        {
            $quan_ids = i_array_column($list, "cate_id");

            $qconditions = db_create_in($quan_ids,"parent_id");

            if ($pid)
            {
                $qconditions .= " AND parent_id = $pid";
            }
            if ($s)
            {
                $qconditions .= " AND (cate_name like '%$s%' OR FIND_IN_SET('$s',sname)) ";
            }

            $lists = $this->_mod_fbcategory->find(array(
                'conditions' => $qconditions." AND if_show =1",
                "order"      => "letter_retrieval ASC,sort_order ASC",
                'index_key' => "",
            ));
            if($lists)
            {
                foreach ($lists as $key => $value)
                {
                    if (!$pid && !$s) //=====  点选全部 并且没有搜索 要把 常用的排前面  =====
                    {
                        if($value['if_common'])
                        {
                            $common_lists[]=$value;
                            unset($lists[$key]);
                        }

                    }
//
//                    if (isset($list[$value['parent_id']]))
//                    {
//                        $list[$value['parent_id']]['son_list'][] = $value;
//                    }
                    // $group_lists[$value['letter_retrieval']][$key]=$value;

                }
                // var_dump($group_lists['']);
//                if(isset($group_lists[''])){
//                    array_push($group_lists,$group_lists['']);
//                    unset($group_lists['']);
//                }
//                $letters=array_keys($group_lists);
            }

        }
        $ret_arr = $lists;
        if ($common_lists)
        {
            $ret_arr = array_merge($common_lists,$lists);
        }
        $leg = ceil(count($ret_arr)/12);
//print_exit($leg);
        $flist = [];
        for ($i=0;$i<$leg;$i++)
        {
            $fre = [];
            $start = $i*12;
            $larr = array_slice($ret_arr,$start,12);
//   print_exit(33);
            $flist[$i]['list'] = $larr;
        }
        $this->assign("flists",$flist);
        $content = $this->_view->fetch('fdiy/dict.html');
// print_exit($content);
        echo $content;exit;
        $this->display('fdiy/fdiy.dict.html');
    }
    
    public function upfile()
    {
        $imgUrl = $_POST['imgUrl'];
// original sizes
        $imgInitW = $_POST['imgInitW'];
        $imgInitH = $_POST['imgInitH'];
// resized sizes
        $imgW = $_POST['imgW'];
        $imgH = $_POST['imgH'];
// offsets
        $imgY1 = $_POST['imgY1'];
        $imgX1 = $_POST['imgX1'];
// crop box
        $cropW = $_POST['cropW'];
        $cropH = $_POST['cropH'];
// rotation angle
        $angle = $_POST['rotation'];

        $jpeg_quality = 100;
        $t = md5(time().rand()).'.jpg';
        $output_filename = "upload/images/diy/croppedImg_".$t;

// uncomment line below to save the cropped image in the same location as the original image.
//$output_filename = dirname($imgUrl). "/croppedImg_".rand()11;

        $what = getimagesize($imgUrl);

        switch(strtolower($what['mime']))
        {
            case 'image/png':
                $img_r = imagecreatefrompng($imgUrl);
                $source_image = imagecreatefrompng($imgUrl);
                $type = '.png';
                break;
            case 'image/jpeg':
                $img_r = imagecreatefromjpeg($imgUrl);
                $source_image = imagecreatefromjpeg($imgUrl);
                error_log("jpg");
                $type = '.jpeg';
                break;
            case 'image/gif':
                $img_r = imagecreatefromgif($imgUrl);
                $source_image = imagecreatefromgif($imgUrl);
                $type = '.gif';
                break;
            default: die('image type not supported');
        }


//Check write Access to Directory

        if(!is_writable(dirname($output_filename))){
            $response = Array(
                "status" => 'error',
                "message" => 'Can`t write cropped File'
            );
        }else{

            // resize the original image to size of editor
            $resizedImage = imagecreatetruecolor($imgW, $imgH);
            imagecopyresampled($resizedImage, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH);
            // rotate the rezized image
            $rotated_image = imagerotate($resizedImage, -$angle, 0);
            // find new width & height of rotated image
            $rotated_width = imagesx($rotated_image);
            $rotated_height = imagesy($rotated_image);
            // diff between rotated & original sizes
            $dx = $rotated_width - $imgW;
            $dy = $rotated_height - $imgH;
            // crop rotated image to fit into original rezized rectangle
            $cropped_rotated_image = imagecreatetruecolor($imgW, $imgH);
            imagecolortransparent($cropped_rotated_image, imagecolorallocate($cropped_rotated_image, 0, 0, 0));
            imagecopyresampled($cropped_rotated_image, $rotated_image, 0, 0, $dx / 2, $dy / 2, $imgW, $imgH, $imgW, $imgH);
            // crop image into selected area
            $final_image = imagecreatetruecolor($cropW, $cropH);
            imagecolortransparent($final_image, imagecolorallocate($final_image, 0, 0, 0));
            imagecopyresampled($final_image, $cropped_rotated_image, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH);
            // finally output png image
            //imagepng($final_image, $output_filename.$type, $png_quality);
            imagejpeg($final_image, $output_filename.$type, $jpeg_quality);
            $response = Array(
                "status" => 'success',
                "url" => $output_filename.$type
            );
        }
        print json_encode($response);
    }
 
    /**
     * http://local.mfd.com/fdiy-1-3.html
     * 属性diy入口
     * @param 1            品类
     */
    function index2()
    {
        $user = $this->visitor->get();
        $this->assign('mid', empty($user) ? 0 : $user['user_id']);

        $args = $this->get_params();
        $cid =  1; //配置一级id

        /* 取得diy配置 */
        $acategories = $this->_fdiy_management_mod->get_list($cid);

        if (empty($acategories)){
            $this->show_warning('！该品类下还没有关联定制属性');
            return;
        }
        $sid = 3; //配置二级id
        $dids= array_unique(explode(',', $acategories[$sid]['did']));

        if (empty($dids)){
            $this->show_warning('！该品类下还没有录入定制属性');
            return;
        }

        $feedmod =& m('feedamount');
        $body_list = $feedmod->getBody();
        $this->assign('body_list',$body_list);


        $plist = $this->_mod_fbcategory->get_descendant_ids($dids);
        unset($plist[0]);
        /* 所有工艺 memcache缓存 */
        $_data = $this->_mod_fbcategory->_get_data(db_create_in($plist,'cate_id')." AND if_show=1",1);
        $_list =  $this->_mod_fbcategory->deep_tree($_data);	//生成嵌套格式的树形数组
        //按照后台关联顺序排序
        $tmp = [];
        foreach ($dids as $key){
            $tmp[] = $_list[$key];
        }

        /* 取得冲突 */
        $_clist = $this->_mod_fcrossrule->find(array(
            'conditions' => db_create_in($dids,'cid'),
            'order' => "id DESC"
        ));

        $quanzhong = 21; //犬种id
        $quanzhong_son = $args[0];
        $quanzhong_info = $this->_mod_fbcategory->get_info($quanzhong_son);
        unset($_list[$quanzhong]);
        $baozhuang_id = 16;
        $guige_id = 11;
        $baozhuang_list = $_list[$baozhuang_id];
        $guige_list = $_list[$guige_id];
        unset($_list[$baozhuang_id]);unset($_list[$guige_id]);
        // 系统后台diy相关配置
        $model_setting = &af('settings');
        $setting = $model_setting->getAll();
        $aprice = isset($setting["diy_aprice"]) ? $setting["diy_aprice"] : 1.5;     //功能料均价
        $ratio = isset($setting["diy_ratio"]) ? $setting["diy_ratio"] : 25;         //单个功能料占比 /100
        $maxnum = isset($setting["diy_maxnum"]) ? $setting["diy_maxnum"] : 3;       //功能料种数上限
        //随机主人寄语
        $word_libraries=isset($setting["word_libraries"])?$setting["word_libraries"]:'';
        $word='';
        if($word_libraries){
            $key=array_rand($word_libraries,1);
            $word=$word_libraries[$key];
        }
        $this->assign('word',$word);//主人寄语
        $this->assign('quanzhong_info',$quanzhong_info);
        $this->assign('clist', $_list);
        $this->assign('baozhuang_list',$baozhuang_list);
        $this->assign('guige_list',$guige_list);
        $this->assign('cid', $cid);
        $this->assign('sid', $sid);
        $this->assign('quanzhong', $quanzhong);
        $this->assign('quanzhong_son', $quanzhong_son);
        $this->assign('aprice', $aprice);
        $this->assign('ratio', $ratio);
        $this->assign('maxnum', $maxnum);
        $this->assign('basedata', json_encode($tmp));
        $this->assign('rule', json_encode(array_values($_clist)));
        $this->assign('ps',2);
        $this->display('fdiy/index2.html');
    }


    function formPrice()
    {
        $cate_id = isset($_REQUEST['cate_id']) ? intval($_REQUEST['cate_id']) : 0;
        $age_id = isset($_REQUEST['age_id']) ? intval($_REQUEST['age_id']) : 0;
        $baozhuang = isset($_REQUEST['baozhuang']) ? intval($_REQUEST['baozhuang']) : 0;
        $guige = isset($_REQUEST['guige']) ? intval($_REQUEST['guige']) : 0;
        $gongxiao = isset($_REQUEST['gongxiao']) ? $_REQUEST['gongxiao'] : '';
        $goods = Types::createObj("fdiy");
        $price_arr = $goods->fmoatPrice($cate_id,$age_id,$baozhuang,$guige,$gongxiao,1);
        if (!$price_arr)
        {
            $this->json_error("此组合无法匹配价格,请联系客服");
            return;
        }
        $this->json_result($price_arr);
    }

    /**
     * 饲喂量建议
     */
    function formFeed()
    {
        $goods = Types::createObj("fdiy");
        $price_arr = $goods->fmoatFeed($_REQUEST);
        if (!$price_arr)
        {
            $this->json_error("饲喂量配置无法获取");
            return;
        }
        $this->json_result($price_arr);

    }


    function __destruct()
	{
		// TODO: Implement __destruct() method.
	}

	/**
     * 获取面料信息        品类｜面料code
     * http://local.mfd.com/fdiy-agf-0003-DSA605A.html
     */
    function agf()
    {
    	$res = ['err'=>1,'msg'=>'请选择品类!'];
    	$args = $this->get_params();
    	$cid = empty($args[0]) ? '0003' : $args[0];
    	$fcode = empty($args[1]) ? 'DSA605A' : $args[1];
    	if (!$cid || !$fcode){
    		echo json_encode($res);
    		exit();
    	}
    	/* 获取面料详细信息 供diy数据展示 */
    	$finfo = $this->_mod_fdict->_get_finfo($fcode,$cid);
    	echo json_encode($finfo);
    }
    
    /**
     * 获取客户指定数据信息        品类｜工艺code
     * http://local.mfd.com/fdiy-agf-0003-0638.html
     *
     */
    function agc(){
    	$res = ['err'=>1,'msg'=>'请选择品类!'];
    	$args = $this->get_params();
    	$cid = empty($args[0]) ? '0003' : $args[0];
    	$code = empty($args[1]) ? '0638' : $args[1];
    	if (!$cid || !$code){
    		echo json_encode($res);
    		exit();
    	}
    	$_fdiy_comm = &m('fdiy_dcomm');
    	$_clist = $_fdiy_comm->find(array(
//    			'conditions' => "cid = '".$cid."' and ecode= '".$code."'",
    			'conditions' => "ecode= '".$code."'",
    			'order' => "id DESC"
    	));
    	echo json_encode(array_values($_clist));
    }
    
    /* 构造并返回树 */
    function &_tree($acategories)
    {
    	import('tree.lib');
    	$tree = new Tree();
    	$tree->setTree($acategories, 'cate_id', 'parent_id', 'cate_name');
    	return $tree;
    }


}

?>