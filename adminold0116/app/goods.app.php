<?php
use Cyteam\Goods\Goods;
use Cyteam\Goods\Gcategory;
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
/**---------------------------------------------------------------------
 *    产品管理控制器
 * ---------------------------------------------------------------------   
 * @author shaozz<shaozizhen94@163.com> 
 * 2016-5-18
 */

class GoodsApp extends BackendApp
{
	var $_goods_mod;//商品表
	var $_products;//货品表
	var $_category_goods;//扩展分类关联表
	var $_goodstype_mod;//产品类型
	var $_goodsattribute_mod;//商品属性表
	var $_goodsattr;//商品关联属性表
	var $sw = 120;
	var $sh = 150;
    function __construct()
    {
        $this->GoodsApp();
       
    }
    function GoodsApp()
    {
    	
        parent::BackendApp();

        $this->_goods_mod =& m('goods');
        $this->_products =& m('products');
        $this->_category_goods =& m('categorygoods');
        $this->_goodstype_mod =& m('goodstype');
        $this->_goodsattribute_mod =& m('goodsattribute');
        $this->_goodsattr =& m('goodsattr');
    }

    /* 商品列表 */
    function index()
    {
        $conditions='';
        $query['bn']    =isset($_GET['bn']) ? trim($_GET['bn']) : '';
    	$query['name']     = isset($_GET['name']) ? trim($_GET['name']) : '';
        $p_id = $query['p_id']     = isset($_GET['p_id']) ? trim($_GET['p_id']) : 0;
        $sonId = $query['son_id']     = isset($_GET['son_id']) ? trim($_GET['son_id']) : 0;
        if (!$p_id) 
        {
           $son_id = 0;
        }
        if($query['bn']){
            $conditions .= " AND bn LIKE '%{$query["bn"]}%'";
        }
    	if($query["name"]){
    		$conditions .= " AND name LIKE '%{$query["name"]}%'";
    	}
    	$goods_mod = m('goods');
        $goodsLib = new Goods();
        $gcategoryLib = new Gcategory();
    	$page = $this->_get_page(30);

        if ($sonId) //===== 先判断二级分类  =====
        {
           $catId[] = $sonId;
        }
        elseif ($p_id)
        {
            $sonList = $gcategoryLib->lists($p_id);
            if ($sonList) 
            {
                foreach ($sonList as $key => $value) 
                {
                    $catId[] = $value['cate_id'];
                }
            }
            $catId[] = $p_id;
        }
        else 
        {
            $catId = [];
        }

    	// $goodlists=$this->_goods_mod->find(array(
    	// 		'conditions'=>"1=1".$conditions,
    	// 		'limit' => $page['limit'],
    	// 		'count' => true,
    	// 		'order'=>"p_order DESC,goods_id DESC",
    	// ));
        //===== 排序  =====

        
        $goodlists = $goodsLib->lists($page['limit'],"1=1  ".$conditions,$catId,0,'p_order DESC,goods_id','all',0);
//echo '<pre>';print_r($catId);exit;

//        $goodlists = $goods_mod->find(array(
//            'conditions'    => "1=1  ".$conditions,
//            'limit'         => $page['limit'],
//            'order'         => "goods_id DESC",
//            'count'         => true
//        ));


//        echo '<pre>';print_r($list);exit;
    	$page['item_count'] = $this->_goods_mod->getCount();
    	
    	$this->_format_page($page);
        $gcategoryParentList = $gcategoryLib->lists(0);
        $selctId = $p_id > 0 ? $p_id : 0;
        if ($selctId) 
        {
            $sonList = $gcategoryLib->lists($selctId);
        }
        else 
        {
            $sonList = [];
        }
        $pList=i_array_column($gcategoryParentList,'cate_name','cate_id');
        $sonList=i_array_column($sonList,'cate_name','cate_id');
        $this->assign('pList',$pList);
        $this->assign('sonList',$sonList);
    	$this->assign('page_info', $page);
    	$this->assign('query',$query);
    	$this->assign("options", $this->_get_options());
    	$this->assign('goodslist',$goodlists);
        $this->display('goods/goods.index.html');
    }
    
    public function fgoods()
    {
        $categorygoods_mod=m("categorygoods");
        $list = $this->_goods_mod->find();
        foreach ($list as $index => $item)
        {
            $goods_id = $item['goods_id'];
            $cate_id = $item['cat_id'];
            $cat_info = $categorygoods_mod->get("cate_id = $cate_id AND goods_id = $goods_id");
            if(!$cat_info)
            {
                $cdata['cate_id'] = $cate_id;
                $cdata['goods_id'] = $goods_id;
                $cdata['pry'] = 1;
                $categorygoods_mod->add($cdata);
            }
        }

    }
    
    
    function add(){
    	$categorygoods_mod=m("categorygoods");
    	$goodsgallery_mod=m("goodsgallery");
    	
    	//上架时间
    	if(IS_POST){
    	
    		
    		if($_POST['marketable'] == 1){
    			$uptime=time();
    		}else{
    			$uptime=0;
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
    				'cat_id'=> $_POST['cat_id'],//产品分类
    				'type_id'=> $_POST['type_id'],//产品类型
    				's_img'  =>$simg,
    				'thumbnail_pic'=> $_POST['big_pic'],//产品缩略图
    				'big_pic'=> $_POST['big_pic'],//产品大图
    				'price'=> $_POST['price']?trim($_POST['price']):0,//销售价
    				'weight'=> $_POST['weight']?trim($_POST['weight']):0,//重量
    				'store'=> $_POST['store']?trim($_POST['store']):0,//库存
    				'score'=> $_POST['score']?trim($_POST['score']):0,//积分
    				'marketable'=> $_POST['marketable'],//是否上架
    				'is_debit'=>$_POST['is_debit'],//是否使用优惠券
    				'intro'=> $_POST['editorValue']?trim($_POST['editorValue']):'',//详细介绍
    				'bn'=> $_POST['bn']?trim($_POST['bn']):'',//商品编码
    				'p_order'=> $_POST['p_order']?trim($_POST['p_order']):0,//排序
    				'uptime'=> $uptime,//上架时间
    				'last_modify'=> time(),//更新时间
    				'mktprice'=> $_POST['mktprice']?trim($_POST['mktprice']):0,//市场价
    				'brief'=> $_POST['brief']?trim($_POST['brief']):'',//产品简介
    		        'is_wap'=>$_POST['is_wap'],
    				'is_app'=>$_POST['is_app'],
    				'is_pc'=>$_POST['is_pc'],
    				);
    		$goods_id=$this->_goods_mod->add($goodsData);


    		if (!$goods_id)
    		{
    			$this->show_warning('商品添加不成功');
    			return;
    		}

            //=====  二维码  =====

            $this->Qrcode("goods",$goods_id,H5_URL."product-content.html?id=".$goods_id);


            
    		if($_POST['cat_id']){
    			$data_type1=array(
    					'cate_id'=>$_POST['cat_id'],
    					'goods_id'=>$goods_id,
    					'pry'=>1,
    			);
                
//                echo '<pre>';print_r($data_type1);exit;
                
    			$categorys1=$categorygoods_mod->add($data_type1);
    			if (!$categorys1)
    			{
    				$this->show_warning('扩展分类-主分类添加不成功');
    				return;
    			}
    		}
    		//扩展分类
    		$data_type=array();
    		$data_gallery=array();
    		$type=array_unique($_POST['type']);
    		if($type){
    			foreach($type as $key=>$val){
    			    if(!$val)
    			    {
    			        continue;
    			    }
    				if($val !=$_POST['cat_id']){
    				$data_type[]=array(
    						'cate_id'=>$val,
    						'goods_id'=>$goods_id
    				);
    				}
    			}
    			if($data_type)
    			{
                    $categorys=$categorygoods_mod->add($data_type);
                    if (!$categorys)
                    {
                        $this->show_warning('扩展分类添加不成功');
                        return;
                    }
    			}

    			
    		}
    		if($_POST['gallery']){
    		$this->addGallery($goods_id);
    		
    		}
    		if($_POST['attr_name']){
    			foreach($_POST['attr_name'] as $kk=>$vv)
    			{
    				$datas[]=array(
    						'attr_id'=>$kk,
    						'attr_value'=>$vv,
    		                'goods_id'=>$goods_id,
    				);
    		
    			}
    			$goodattr=$this->_goodsattr->add($datas);
    			if (!$goodattr)
    			{
    				$this->show_warning('产品属性添加不成功');
    				return;
    			}
    		$productData = array(
    				'is_default'=> $_POST['is_default'],//0不是默认规格 1是默认规格
    				/* ''=> $_POST[''],//
    				''=> $_POST[''],//
    				''=> $_POST[''],//
    				''=> $_POST[''],//
    				''=> $_POST[''],//
    				''=> $_POST[''],//
    				''=> $_POST[''],//
    				 */
    				
    				
    				);
    		}
    		
    		
    		//===== 规格 add.liang.li  =====
    		$productsMdl = m("products");
    		$pdata['goods_id'] = $goods_id;
    		$pdata['bn'] = $goodsData['bn']."-product";
    		$pdata['price'] = $goodsData['price'];
    		$pdata['mktprice'] = $goodsData['mktprice'];
    		$pdata['name'] = $goodsData['name'];
    		$pdata['weight'] = $goodsData['weight'];
    		$pdata['store'] = $goodsData['store'];
    		$pdata['is_default'] = 1;
    		$productsMdl->add($pdata);
    		
    		$this->show_message("产品添加成功",
    				'back_list', 'index.php?app=goods');
    		
    	}else{
    		
    		$list = $this->_goodstype_mod->find();
    		if($list){
    			foreach($list as $k=>$v)
    			{
    				$lists[$v['type_id']]=$v['name'];
    			}
    		}
    		$this->assign("list", $lists);
    		$datas=array(
    				'goods_id'=>0,
    		);
    		$this->assign("data", $datas);
    		$this->assign("options", $this->_get_options());
    		$param = array(
    				'dir' => 'gallery',
    				'button_text' => "上传相册图片"
    		);
//    		$this->assign('build_editor', $this->_build_editor(array(
//    				'name' => 'intro',
//    		)));
    		$this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
    		$this->display('goods/goods.info.html');
    	}
    	
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
        //define('EXAMPLE_TMP_SERVERPATH', ROOT_PATH.'/phpqrcode/image/');
        $fileName = $type.'/'.$type.'_'.md5($id).'_'.$size.'.png';
        if(file_exists(EXAMPLE_TMP_SERVERPATH.$fileName)){ // 已存在删除文件
            return $fileName;
        }
        return '';
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
        //define('EXAMPLE_TMP_SERVERPATH', ROOT_PATH.'/phpqrcode/image/');

        $errorCorrectionLevel = 'H';
        // 	$matrixPointSize = 4;

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
//            if(empty($logo)) {
//                $logo = ROOT_PATH.'/phpqrcode/rctailor.png';
//            }
// echo '<pre>';print_r($logo);exit;
            $QR = $tempDir.$fileName;
// echo '<pre>';print_r($pngAbsoluteFilePath);exit;
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
            // 		return SITE_URL.'/phpqrcode/image/'.$fileName;

            // 		return false;
        }
    }




    function edit(){

        
    	$goods_id=$_GET['id'];
    	$categorygoods_mod=m("categorygoods");
    	$goodsgallery_mod=m("goodsgallery");
    	$goods_mod=m("goods");
    	$goods_count=$goods_mod->update_goods_count($goods_id,10);
        $erweime = $this->getQrcodeImage("goods",$goods_id);
        if (!$erweime)
        {
            $this->Qrcode("goods",$goods_id,H5_URL."product-content.html?id=".$goods_id);
        }
    	//上架时间
    	if(IS_POST){
    		 
    
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
    		$goodsData = array(
    				'name'=> $_POST['name']?trim($_POST['name']):'',//产品名
    				'cat_id'=> $_POST['cat_id'],//产品分类
    				'type_id'=> $_POST['type_id'],//产品类型
    				'thumbnail_pic'=> $_POST['big_pic'],//产品缩略图
    				'big_pic'=> $_POST['big_pic'],//产品大图
    				's_img'  =>$simg,
    				'price'=> $_POST['price']?trim($_POST['price']):0,//销售价
    				'weight'=> $_POST['weight']?trim($_POST['weight']):0,//重量
    				'store'=> $_POST['store']?trim($_POST['store']):0,//库存
    				'score'=> $_POST['score']?trim($_POST['score']):0,//积分
    				'marketable'=> $_POST['marketable'],//是否上架
    				'is_debit'=>$_POST['is_debit'],//是否使用优惠券
    				'intro'=> $_POST['editorValue']?trim($_POST['editorValue']):'',//详细介绍
    				'bn'=> $_POST['bn']?trim($_POST['bn']):'',//商品编码
    				'p_order'=> $_POST['p_order']?trim($_POST['p_order']):0,//排序
    				'uptime'=> $uptime,//上架时间
    				'last_modify'=> time(),//更新时间
    				'mktprice'=> $_POST['mktprice']?trim($_POST['mktprice']):0,//市场价
    				'brief'=> $_POST['brief']?trim($_POST['brief']):'',//产品简介
    				'is_wap'=>$_POST['is_wap'],
    				'is_app'=>$_POST['is_app'],
    				'is_pc'=>$_POST['is_pc'],
    		);
    		

    		
    		
    		
    		$goods_edit=$this->_goods_mod->edit($goods_id,$goodsData);

            
    	if($_POST['gallery']){
    		
    		$this->addGallery($goods_id);
    		
    		}
    		
    		if($_POST['cat_id']){
    			$data_type1=array(
    					'cate_id'=>$_POST['cat_id'],
    					'goods_id'=>$goods_id,
    					'pry'=>1,
    			);
    			$categoryy= $this->_category_goods->get(array(
    					'conditions'=>"goods_id = '{$goods_id}' AND pry =1",
    			));
    			if($categoryy){
    				$categoryy1=$this->_category_goods->edit("id='{$categoryy['id']}'",$data_type1);
    			}else{
    				$categoryy1=$this->_category_goods->add($data_type1);
    			}
    		
    			if (!$categoryy1)
    			{
    				$this->show_warning('扩展分类-主分类添加不成功');
    				return;
    			}
    		}
    		
    		//扩展分类编辑
    		$data_type=array();
    		$data_gallery=array();
    		$categorys= $this->_category_goods->find(array(
    				'conditions'=>"goods_id = '{$goods_id}'",
    				'index'=>"cate_id",
    		));
            $_POST['type'][] = $_POST['cat_id'];
    		foreach($categorys as $gg=>$rr)
    		{
    		    if(!$rr)
    		    {
    		        continue;
    		    }
    			if(in_array($rr['cate_id'],$_POST['type'])){
    			}else{
    				$attr_del[]=$rr['id'];
    			}
    			$cates[]=$rr['cate_id'];
    		}
    		foreach((array)$_POST['type'] as $kw=>$vw )
    		{
    		    if(!$vw)
    		    {
    		        continue;
    		    }
    			if(in_array($vw,$cates)){
    				
    			}else{
    				if( $vw!=$_POST['cat_id']){
    					$attr_in[]=array(
    							'cate_id'=>$vw,
    							'goods_id'=>$goods_id,
    					
    					);
    				}
    				
    			}
    		}
    		if($attr_del){
    			foreach($attr_del as $k=>$v){
    				$categorys=$categorygoods_mod->drop("id ='{$v}'");
    			}
    			
    		}
    		if($attr_in){
    			$categorys=$categorygoods_mod->add($attr_in);
    		}
    		if($_POST['attr_name']){
    		
    			//编辑产品属性
    			$goodsattrs=$this->_goodsattr->find(array(
    					'conditions'=>"goods_id='{$goods_id}'",
    					'index_key'=>"attr_id",
    			));
    			$dat_attr=array();
    			if($goodsattrs){
    				foreach($goodsattrs as $d=>$m){
    					 
    					if($m['attr_value'] != $_POST['attr_name'][$d]){
    						$dat_attr=array(
    								'attr_value'=>$_POST['attr_name'][$d],
    						);
    						$this->_goodsattr->edit($m['id'],$dat_attr);
    					}
    				}
    			}else{
    				foreach($_POST['attr_name'] as $kk=>$vv)
    				{
    					$datas[]=array(
    							'attr_id'=>$kk,
    							'attr_value'=>$vv,
    							'goods_id'=>$goods_id,
    					);
    				
    				}
    				$goodattr=$this->_goodsattr->add($datas);
    			}
    		
    			
    		}
    		$this->show_message("产品编辑成功",
    				'back_list', 'index.php?app=goods');
    	}else{
       
    		
    		
         $gallerys=$goodsgallery_mod->find(array(
         		'conditions'=>"goods_id='{$goods_id}'",
         ));
//         var_dump($gallerys);exit;
           $categorys= $this->_category_goods->find(array(
         		'conditions'=>"goods_id = '{$goods_id}' AND pry != 1",
         )); 
           $this->assign('gallery_list',$gallerys);
         $this->assign('categorys',$categorys);
        
         
    		$list = $this->_goodstype_mod->find();
    		if($list){
    			foreach($list as $k=>$v)
    			{
    				$lists[$v['type_id']]=$v['name'];
    			}
    		}
    		$this->assign("list", $lists);
    		
    		$goods=$this->_goods_mod->get(array(
    				'conditions'=>"goods_id='{$goods_id}'",
    		));
    		$type_id=$goods['type_id'];
    		if($type_id){
    			$attrbutes=$this->_goodsattribute_mod->find(array(
    					'conditions'=>"type_id=".$type_id,
    					'index_key'=>"attr_id",
    			));
    			if($attrbutes){
    				foreach($attrbutes as $k=>$v){
    					$attrbutes[$k]['attr_values']=explode("\r\n",$v['attr_values']);
    				}
    			}
    			
    			$goodsattrs=$this->_goodsattr->find(array(
    					'conditions'=>"goods_id='{$goods_id}'",
    					'index_key'=>"",
    			));
    			foreach($goodsattrs as $ks=>$vs){
    				$goodsattrsy[$vs['attr_id']]=$vs['attr_value'];
    			}
    			$this->assign("goodsattrs", $goodsattrsy);
    			$this->assign("attrbutes", $attrbutes);
    		}
    		
    		$this->assign("data", $goods);
    		$this->assign("options", $this->_get_options());
    		$param = array(
    				'dir' => 'gallery',
    				'button_text' => "上传相册图片"
    		);
    		$this->assign('build_editor', $this->_build_editor(array(
    				'name' => 'intro',
    		)));
    		$this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
    		$this->display('goods/goods.info.html');
    	}
    	 
    	 
    	
    	 
    }
    function attrbutes(){
    	
    	$type_id=$_GET['type_id'];
    	$attrbutes=$this->_goodsattribute_mod->find(array(
    			'conditions'=>"type_id=".$type_id,
    			'index_key'=>"",
    	));
    	
    	if($attrbutes){
    		foreach($attrbutes as $k=>$v){
    			$attrbutes[$k]['attr_values']=explode("\r\n",$v['attr_values']);
    		}
    	}
    	
    	die($this->json_result($attrbutes));
    }
    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = NULL)
    {
    	$_cate_mod = & bm('gcategory', array('_store_id' => 0));
    
    	$gcategories = $_cate_mod->get_list();
    	$tree =& $this->_tree($gcategories);
    
    	return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
    
    /* 构造并返回树 */
    function &_tree($gcategories)
    {
    	import('tree.lib');
    	$tree = new Tree();
    	$tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
    	return $tree;
    } 
    function addGallery($id){
    	//相册图片
    	$goodsgallery_mod=m("goodsgallery");
    	import("image.func");
    	$gallery = array();
    	foreach((array)$_POST['gallery'] as $k => $v){
    		$f=dirname($v);
    		$fname = str_replace($f, '',$v);
    		$t = date("YmdHi");
    		$sPath = '/upload/thumb/s/'.$t.$fname;
    		$s_img = make_thumb($v, ROOT_PATH.$sPath,$this->sw, $this->sh);
    		if($s_img){
    			$gallery[] = array(
    					'goods_id'=>$id,
    					'img_url'=>$v,
    					'thumb_url'=>LOCALHOST1.$sPath,
    			);
    		}
    	}
    	 
    	if(!empty($gallery)){
    		$goodsgallery_mod->add($gallery);
    	}
    }  
    function gettype(){
    	$cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    	$gcategory_mod=m("gcategory");
    	$gcategorys=$gcategory_mod->get(array(
    			'conditions'=>"cate_id='{$cat_id}'",
    	));
    	die($this->json_result($gcategorys['type_id']));
    }
    function drop_gallery(){
    	$goodsgallery_mod=m("goodsgallery");
    	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    	$img = $goodsgallery_mod->get_info($id);
    
    	if($img){
    		if(is_file(ROOT_PATH."/".$img['thumb_url'])){
    			unlink(ROOT_PATH."/".$img['thumb_url']);
    		}
    
    		
    	}
    
    	if ($goodsgallery_mod->drop($id))
    	{
    		$this->json_result('drop_ok');
    		return;
    	}
    	else
    	{
    		$this->json_error('drop_error');
    		return;
    	}
    }
    function gallarysort(){
    
    	$gllary = $_POST['photo_id'];
    	$tmpAttr = explode(",", $gllary);
    	//print_r($tmpAttr);die;
    	$tmpAttrArray = array();
    
    	$goodsgallery_mod=m("goodsgallery");
    	foreach ($tmpAttr as $key => $val) {
    		$tmpV = explode(":", $val);
    		$tmpAttrArray[$tmpV[0]] = $tmpV[1];
    
    	}
    
    	$arr = array();
    	foreach ($tmpAttrArray as $k => $v) {
    
    		$arr['sort']= $v;
    
    		$gallery_sort = $goodsgallery_mod->edit("id='$k'",$arr);
    
    
    	}
    
    	if($gallery_sort){
    
    		echo "add success";
    	}
    
    
    }
    function drop(){
    	$goodsgallery_mod=m("goodsgallery");
    	$id = isset($_GET['id']) ? trim($_GET['id']) : '';
    	$ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
    	if (!$id){
    		$this->show_warning('没有商品id');
    		return;
    	}
    	$goods_del=$this->_goods_mod ->drop($id);
    	 $product_del=$this->_products -> drop("goods_id='{$id}'"); 
    	
    	$category_del=$this->_category_goods -> drop("goods_id='{$id}'");
    	$goodsattr_del=$this->_goodsattr -> drop("goods_id='{$id}'");
    	$goodsgallery_del=$goodsgallery_mod-> drop("goods_id='{$id}'");
    	
    	$this->show_message('删除产品成功','back_list', 'index.php?app=goods&page=' . $ret_page);
    }
    
    function check_bn() {
    	$id = isset($_GET['id']) ? trim($_GET['id']) : '';
    	$bn = isset($_GET['bn']) ? trim($_GET['bn']) : '';
    	if (!$bn)
    	{
    		echo 0;
    	}
    	$cons="bn='{$bn}' ";
    	if($id)
    	{
    		$cons.=" AND goods_id != '{$id}'";
    	}
    	if ($this->_goods_mod->get($cons))
    	{
    		echo 0;
    	}else{
    		echo 1;
    	}
    	
    }
    
}
  
?>