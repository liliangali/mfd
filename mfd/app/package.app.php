<?php
/**
 * 赠品礼包
 */

class PackageApp extends BackendApp
{
	var $_goods_giveaway_mod; //赠品表
	var $_goods_package_mod;  //礼包表
	var $_goods_mod; //商品表
	var $_fbcategory_mod; //分类表
	function __construct()
    {
        $this->PackageApp();
       
    }
    function PackageApp()
    {
        parent::BackendApp();
        $this->_goods_giveaway_mod =& m('goods_giveaway');
        $this->_goods_package_mod =& m('goods_package');
        $this->_goods_mod =& m('goods');
        $this->_fbcategory_mod =& m('fbcategory');
    }

    function index(){
    	$conditions='';
    	$query['name']     = isset($_GET['name']) ? trim($_GET['name']) : '';
    	if($query["name"]){
    		$conditions .= " AND name LIKE '%{$query["name"]}%'";
    	}
    	$page = $this->_get_page(30);
 
        $packagelist = $this->_goods_package_mod->find(array(
                'conditions'=>'1=1'.$conditions,
                'order'=>"id DESC",
                'limit' => $page['limit'],
                'count' => true,
            ));
        if($packagelist){
            foreach($packagelist as $k=>$val){
                $packagelist[$k]['info'] = $this->_goods_package_mod->getPackageInfo($val['id']);
            }
        }
    	$page['item_count'] = $this->_goods_package_mod->getCount();
    	$this->_format_page($page);
    	$this->assign('page_info', $page);
    	$this->assign('packagelist',$packagelist);
    	$this->display('package/index.html');
    }

    function add(){
    	if(IS_POST){
            //多选犬型、犬期、功效
            if($_POST['quanx_id']){
                foreach($_POST['quanx_id'] as $quanx){
                    $because_x .= $quanx.',';
                }
                $quanx_id =substr($because_x ,0 ,-1);
            }
            if($_POST['quanq_id']){
                foreach($_POST['quanq_id'] as $quanq){
                    $because_q .= $quanq.',';
                }
                $quanq_id =substr($because_q ,0 ,-1);
            }
            if($_POST['gongx_id']){
                foreach($_POST['gongx_id'] as $gongx){
                    $because_g .= $gongx_id.',';
                }
                $gongx_id =substr($because_g ,0 ,-1);
            }
            if($_POST['marketable'] == 1){
                $uptime=time();
            }else{
                $uptime=0;
            }
            //
            if($_POST['cList']){
                foreach($_POST['cList'] as $val){
                $cList .= $val.',';
                }
                $cList =substr($cList ,0 ,-1);
            }
            if($_POST['fList']){
                foreach($_POST['fList'] as $v){
                $fList .= $v.',';
                }
                $fList =substr($fList ,0 ,-1);
            }
            
            if($_POST['big_pic']){
                import("image.func");
                $f=dirname($_POST['big_pic']);
                $fname = str_replace($f, '',$_POST['big_pic']);
                $t = date("YmdHi");
                $sPath = '/upload/thumb/s/'.$t.$fname;
                
                $s_img = make_thumb($_POST['big_pic'],ROOT_PATH.$sPath,60, 60);
                
                if($s_img){
                    $simg =LOCALHOST1.$sPath;
                }
            }
            $goodsData = array(
                    'name'=> $_POST['name']?trim($_POST['name']):'',//产品名
                    's_img'  =>$simg,
                    'c_list' => $cList,
                    'f_list' => $fList,
                    'thumbnail_pic'=> $_POST['big_pic'],//产品缩略图
                    'big_pic'=> $_POST['big_pic'],//产品大图
                    //'store'=> $_POST['store']?trim($_POST['store']):0,//库存
                    'marketable'=> $_POST['marketable'],//是否上架
                    'intro'=> $_POST['editorValue']?trim($_POST['editorValue']):'',//详细介绍
                    'p_order'=> $_POST['p_order']?trim($_POST['p_order']):0,//排序
                    'last_modify'=> time(),//更新时间
                    //'mktprice'=> $_POST['mktprice']?trim($_POST['mktprice']):0,//市场价
                    'quanx_id'=>$quanx_id,
                    'quanq_id'=>$quanq_id,
                    'gongx_id'=>$gongx_id,
                    'h_money'=>$_POST['h_money'],
                    'l_money'=>$_POST['l_money'],
                    'price' =>$_POST['price'] 
                    );
            $goods_id=$this->_goods_package_mod->add($goodsData);
            if (!$goods_id)
            {
                $this->show_warning('礼包添加不成功');
                return;
            }
            
            $this->show_message("礼包添加成功",
                    'back_list', 'index.php?app=package');


    	}else{
    		//查询属性
            $fbcategoryList = $this->_fbcategory_mod->find(array(
                'conditions'=>" parent_id in (1,21,34) AND if_show=1",
                'fields' => 'cate_name,parent_id',
                'order'=>'sort_order ASC',
            ));
            foreach($fbcategoryList as $fbcategory){
                if($fbcategory['parent_id'] == 1){
                    $fbList['gongxList'][] = $fbcategory;
                }
                elseif($fbcategory['parent_id'] == 21){
                    $fbList['quanxList'][] = $fbcategory;
                }
                elseif($fbcategory['parent_id'] == 34){
                    $fbList['quanqList'][] = $fbcategory;
                }
            }
            $this->assign("fbList", $fbList);
            //获取常规商品（赠品）
            $goods['goodsList'] = $this->_goods_mod->find(array(
                'conditions'=>" is_giveaway=2 AND marketable=1",
                'fields' => 'name,bn,mktprice',
                'order'=>'goods_id ASC',
            ));
            //获取非常规商品
            $goods['giveawayList'] = $this->_goods_giveaway_mod->find(array(
                'conditions'=>"marketable=1",
                'fields' => 'name,bn,mktprice',
                'order'=>'goods_id ASC',
            ));
            $this->assign("goods", $goods);
    		$datas=array(
    				'goods_id'=>0,
                    'price' => '0'
    		);
    		$this->assign("data", $datas);
    		$this->display('package/info.html');
    	}
    }


    function edit(){
        $id=$_GET['id'];
        if(IS_POST){
            //多选犬型、犬期、功效
            if($_POST['quanx_id']){
                foreach($_POST['quanx_id'] as $quanx){
                    $because_x .= $quanx.',';
                }
                $quanx_id =substr($because_x ,0 ,-1);
            }
            if($_POST['quanq_id']){
                foreach($_POST['quanq_id'] as $quanq){
                    $because_q .= $quanq.',';
                }
                $quanq_id =substr($because_q ,0 ,-1);
            }
            if($_POST['gongx_id']){
                foreach($_POST['gongx_id'] as $gongx){
                    $because_g .= $gongx_id.',';
                }
                $gongx_id =substr($because_g ,0 ,-1);
            }
            if($_POST['marketable'] == 1){
                $uptime=time();
            }else{
                $uptime='';
            }
            if($_POST['big_pic']){
                import("image.func");
                $f=dirname($_POST['big_pic']);
                $fname = str_replace($f, '',$_POST['big_pic']);
                $t = date("YmdHi");
                $sPath = '/upload/thumb/s/'.$t.$fname;
                
                $s_img = make_thumb($_POST['big_pic'],ROOT_PATH.$sPath,60, 60);
                
                if($s_img){
                    $simg =LOCALHOST1.$sPath;
                }
            }
            if($_POST['cList']){
                foreach($_POST['cList'] as $val){
                $cList .= $val.',';
                }
                $cList =substr($cList ,0 ,-1);
            }
            if($_POST['fList']){
                foreach($_POST['fList'] as $v){
                $fList .= $v.',';
                }
                $fList =substr($fList ,0 ,-1);
            }
            $goodsData = array(
                    'name'=> $_POST['name']?trim($_POST['name']):'',//产品名
                    's_img'  =>$simg,
                    'c_list' => $cList,
                    'f_list' => $fList,
                    'thumbnail_pic'=> $_POST['big_pic'],//产品缩略图
                    'big_pic'=> $_POST['big_pic'],//产品大图
                    //'store'=> $_POST['store']?trim($_POST['store']):0,//库存
                    'marketable'=> $_POST['marketable'],//是否上架
                    'intro'=> $_POST['editorValue']?trim($_POST['editorValue']):'',//详细介绍
                    'p_order'=> $_POST['p_order']?trim($_POST['p_order']):0,//排序
                    'last_modify'=> time(),//更新时间
                    //'mktprice'=> $_POST['mktprice']?trim($_POST['mktprice']):0,//市场价
                    'quanx_id'=>$quanx_id,
                    'quanq_id'=>$quanq_id,
                    'gongx_id'=>$gongx_id,
                    'h_money'=>$_POST['h_money'],
                    'l_money'=>$_POST['l_money'],
                    'price' =>$_POST['price'] 
                    );
            $goods_id=$this->_goods_package_mod->edit($id,$goodsData);
            $this->show_message("编辑成功",
                    'back_list', 'index.php?app=package');
        }else{
            $goodsInfo=$this->_goods_package_mod->get(array(
                    'conditions'=>"id='{$id}'",
            ));
            //犬型
            $g_quanx = explode(",",$goodsInfo['quanx_id']);
            $g_quanq = explode(",",$goodsInfo['quanq_id']);
            $g_gongx = explode(",",$goodsInfo['gongx_id']);
            //查询属性
            $fbcategoryList = $this->_fbcategory_mod->find(array(
                'conditions'=>" parent_id in (1,21,34) AND if_show=1",
                'fields' => 'cate_name,parent_id',
                'order'=>'sort_order ASC',
            ));
            foreach($fbcategoryList as $fbcategory){
                //功效
                if($fbcategory['parent_id'] == 1){
                    if($g_gongx){
                        foreach($g_gongx as $gx_val){
                            if($gx_val == $fbcategory['cate_id']){
                                $fbcategory['is_info'] = 1 ;
                            }
                        }
                    }
                    $fbList['gongxList'][] = $fbcategory;
                }
                //犬型
                elseif($fbcategory['parent_id'] == 21){
                    if($g_quanx){
                        foreach($g_quanx as $qx_val){
                            if($qx_val == $fbcategory['cate_id']){
                                $fbcategory['is_info'] = 1 ;
                            }
                        }
                    }
                    $fbList['quanxList'][] = $fbcategory;
                }
                //犬期
                elseif($fbcategory['parent_id'] == 34){
                    if($g_quanq){
                        foreach($g_quanq as $qq_val){
                            if($qq_val == $fbcategory['cate_id']){
                                $fbcategory['is_info'] = 1 ;
                            }
                        }
                    }
                    $fbList['quanqList'][] = $fbcategory;
                }
            }
            $this->assign("fbList", $fbList);
            $this->assign("data", $goodsInfo);
            $f_list = explode(',',$goodsInfo['f_list']); 
            $c_list = explode(',',$goodsInfo['c_list']); 

            //获取常规商品（赠品）
            $goods['goodsList'] = $this->_goods_mod->find(array(
                'conditions'=>" is_giveaway=2 AND marketable=1",
                'fields' => 'name,bn,mktprice',
                'order'=>'goods_id ASC',
            ));
            foreach($goods['goodsList'] as $k=>$v){
                $goods['goodsList'][$k]['is_info'] = in_array($v['goods_id'],$c_list);
            }
            //获取非常规商品
            $goods['giveawayList'] = $this->_goods_giveaway_mod->find(array(
                'conditions'=>"marketable=1",
                'fields' => 'name,bn,mktprice',
                'order'=>'goods_id ASC',
            ));
            foreach($goods['giveawayList'] as $k=>$v){
                $goods['giveawayList'][$k]['is_info'] = in_array($v['goods_id'],$f_list);
            }
            $this->assign("goods", $goods);
            $this->display('package/info.html');
        }
         
    }

    function drop(){

        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
        if (!$id){
            $this->show_warning('没有礼包id');
            return;
        }
        $goods_del=$this->_goods_package_mod->drop($id);
        $this->show_message('删除产品成功','back_list', 'index.php?app=package&page=' . $ret_page);
    }
    

}



?>