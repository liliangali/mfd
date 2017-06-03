<?php
/**
 *    基本款商品管理控制器
 */
class CustomsApp extends BackendApp{
    var $_customs_mod;
    var $_uploadedfile_mod;
    var $_parts_mod;
    var $_customsparts_mod;
    var $_parttype_mod;
    var $_gcategory;
    var $_member_mod;
    var $cateAll;
    var $cate;
    var $sw = 80;
    var $sh = 80;
    var $mw = 300;
    var $mh = 300;
    function __construct(){
        $this->CustomsApp();
    }
    function CustomsApp(){
        parent::BackendApp();
        $this->_customs_mod      =& m('customs');
        $this->_uploadedfile_mod =& m('uploadedfile');
        $this->_parts_mod        =& m('part');
        $this->_customsparts_mod =& m('customsparts');
        $this->_parttype_mod     =& m('parttype');
        $this->_gcategory        =& m('gcategory');
        $this->_member_mod       =& m('member');
        
        $this->cateAll      = $this->_customs_mod->getCate();
        $this->cate         = $this->_customs_mod->getTopCate();

    }
    function index(){
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'cst_name',
                'equal' => 'like',
            ),
            array(
                'field' => 'cst_cate',
                'equal' => '=',
            ),
        ));
        //update order
        if (isset($_GET['sort']) && isset($_GET['order'])){
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc'))){
                 $sort  = 'cst_id';
                 $order = 'desc';
            }
        }else{
            $sort  = 'cst_id';
            $order = 'desc';
        }
        $page = $this->_get_page(15);
        $goods_list = $this->_customs_mod->find(array(
            'conditions' => "1 = 1 ".$conditions,
            'count' => true,
            'order' => "$sort $order",
            'limit' => $page['limit'],
        ));
        $this->assign('goods_list', $goods_list);
        $this->assign('cates',$this->cate);
        
        $page['item_count'] = $this->_customs_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->display('customs/list.html');
    }
    //add
    function add(){
        $this->assign('action','add');
        $this->_editor();
    }
    //edit
    function edit(){
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $data=$this->_customs_mod->get($id);
        
        if(!empty($data)){

            $user=$this->_member_mod->get($data['cst_author']);
            $this->assign('author_name',$user['user_name']);
            
            $this->assign('data',$data);
            $_gallery_mod = &m("gallery");
            $gallery_list = $_gallery_mod->find(array(
                'conditions' => "cst_id = '{$id}'",
                'order' => 'id ASC',
            ));
            
            $this->assign("gallery_list", $gallery_list);
            
            $this->assign('action','edit');

            $types=$this->cateAll[$data['cst_cate']]['parts_type'];
//             $this->assign('types',$types);

            //摒弃ajax,加载页面是统一加载完
            $typesarr = $this->getParts($types,$id);
            $selects  = $this->getSelects($id);
            $this->assign('types',$typesarr);
            $this->assign('selects',$selects);

            ///---------------------- 编辑前先写入日志文件

//             if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.IMG_PATH."/custom/{$id}/logs/".$id.".txt")){

//                 $ss = $this->_customsparts_mod->find(array(
//                     'conditions' => "cst_id = $id",
//                     'fields'     => "pt_id,pt_cate,is_dft,parent_cate,grand_cate,top_cate,depth",
//                 ));
//                 foreach ($ss as $s){
//                     unset($s['id']);
//                     $write[$s['pt_id']] = $s;
//                 }
//                 array_multisort($write);
//                 $write = var_export($write, true);

//                 $logDir = $_SERVER['DOCUMENT_ROOT'].'/'.IMG_PATH.'/custom/'.$id.'/logs';

//                 mkDirs($logDir);
                
//                 file_put_contents($_SERVER['DOCUMENT_ROOT'].'/'.IMG_PATH."/custom/{$id}/logs/".$id.".txt", $write);
                
//             }
            
        }else{
            $this->assign('action','add');
        }
        $this->_editor($id);
    }
    //editor
    function _editor($id=0,$parts_type=array()){
        $cst_source = array(
        	'0'=>'网站',
            '1'=>'设计',
            '2'=>'街拍',
        );

        $tags=array(
        	'news'  => '新品',
            'rcmnd' => '推荐',
            'sales' => '特惠',
        );
        
        $this->assign('tags',$tags);
        $this->assign('cst_source',$cst_source);
    
        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
        $this->assign('build_editor',$this->_build_editor(array('belogn'=>BELONG_GOODS,'area_id'=>'cst_content')));
        $param = array(
            'dir' => 'gallery',
            'button_text' => "上传相册图片"
        );
        $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
        
        $this->assign('cst_cate',$this->cate);
        
        //===== edit by liang.li =====
        $this->assign('sex_cate',$this->_customs_mod->getSexCate());

        $this->display('customs/info.html');
    }
    //save
    function toAdd(){
//         print_exit($_POST);
        import("image.func");
        $mianliao_zhiding = trim($_POST['mianliao_zhiding']);
        $mianliao_zhiding2 = trim($_POST['mianliao_zhiding2']);

        $cst_name    = trim($_POST['cst_name']);
        $is_active   = trim($_POST['is_active']);
		$is_hot      = trim($_POST['is_hot']); 
		$is_rec      = trim($_POST['is_rec']);
		$description = trim($_POST['cst_description']);
        $content     = trim($_POST['cst_content']);
        $cst_image   = trim($_POST['cst_image']);
        $cst_cate    = trim($_POST['cst_cate']);
        $cst_sex_cate    = trim($_POST['cst_sex_cate']);
        $cst_weight  = trim($_POST['cst_weight']);
        $cst_views   = trim($_POST['cst_views']);
        $cst_rank    = trim($_POST['cst_rank']);
        $cst_sales   = trim($_POST['cst_sales']);
        $cst_store   = trim($_POST['cst_store']);
        $cst_likes   = trim($_POST['cst_likes']);
        $cst_author  = trim($_POST['cst_author']);
        $cst_exp     = trim($_POST['cst_exp']);
        $cst_dis_image = trim($_POST['cst_dis_image']);
        $cst_tag     = trim($_POST['cst_tag']);
        $cst_source  = trim($_POST['cst_source']);
        $cst_source_id  = trim($_POST['cst_source_id']);
        $cst_service_fee  = trim($_POST['service_fee']);
        $cst_sn      = trim($_POST['cst_sn']); 
        $link_cst      = trim($_POST['link_cst']);
        
        if($cst_sn == ''){
            $this->show_warning('编号不能为空!');
            return;
        }
        
        $data = array(
            'cst_name'            => $cst_name,
        	'cst_cate'			  => $cst_cate,
        	'cst_sex_cate'		  => $cst_sex_cate,
        	'cst_image'			  => $cst_image,
        	'cst_weight'          => $cst_weight,
            'cst_description'     => $description,
            'cst_content'         => $content,
        	'is_active'           => $is_active,
			'is_hot'              => $is_hot,
			'is_rec'              => $is_rec,
            'add_time'            => gmtime(),
        	'last_time'           => gmtime(),
        	'cst_views'			  => $cst_views,
            'cst_rank'			  => $cst_rank,
            'cst_sales'			  => $cst_sales,
            'cst_likes'			  => $cst_likes,
            'cst_store'           => $cst_store,
            'cst_author'          => $cst_author,
            'cst_exp'             => $cst_exp,
            'cst_dis_image'       => $cst_dis_image,
            'cst_tag'             => $cst_tag,
            'cst_source'          => $cst_source,
            'cst_source_id'       => $cst_source_id,
			'service_fee'       => $cst_service_fee,
            'cst_sn'            => $cst_sn,
            'link_cst'          => $link_cst,
            'mianliao_zhiding'  => $mianliao_zhiding,
            'mianliao_zhiding2'  => $mianliao_zhiding2,
        );
        
        $Pdata=$this->fmt_Adata($_POST['cates']);//format post data        
        
        if(empty($Pdata)){
            $this->show_warning('parts_empty_error');
            return;
        }
        // 基本款默认价格
        $price=$this->get_dft_price($Pdata,$_POST['cst_cate'],$cst_service_fee);
//         $price['cst_price'] += $cst_service_fee;//加上服务费
        $data=array_merge($data,$price);
        
        if($_POST['cst_id']===''){//add

            $cst_id=$this->_customs_mod->add($data);//return add id
            
            foreach ($Pdata as $key => $row){
                
                $Pdata[$key]['cst_id'] = $cst_id;
                $linkData[] = $Pdata[$key];//要add二维数组  下标必须是从0开始的  所以要格式化一下
                
                //生成目录所需数组
                if($row['top_cate'] == '8001' || $row['top_cate'] == '8030' || $row['top_cate'] == '8050'){
                    $blArr[] =  $row['pt_id'];
                }
                
            }
            
            $cst_pt_id=$this->_customsparts_mod->add($linkData);

            if(!empty($blArr)){
                
                $this->mkCstDir($cst_id,$blArr);
                
            }

            $_POST['cst_id'] = $cst_id;
            
            /* add by v5 生成基本款的 二维码 */
            Qrcode('customs',$cst_id,M_SITE_URL.'/index.php/custom-info-'.$cst_id.'.html');
        }else{ //======Edit
            

            $cst_id=$this->_customs_mod->edit($_POST['cst_id'],$data);//return 1/0
            
            $arrSlc=$this->_customsparts_mod->find(array('conditions'=>'cst_id='.$_POST['cst_id']));
            
            foreach ($arrSlc as $row){
                $arrOld[$row['pt_id']]['pt_id']    = $row['pt_id'];
                $arrOld[$row['pt_id']]['pt_cate']  = $row['pt_cate'];
                $arrOld[$row['pt_id']]['is_dft']   = $row['is_dft'];
                $arrOld[$row['pt_id']]['parent_cate'] = $row['parent_cate'];
                $arrOld[$row['pt_id']]['grand_cate'] = $row['grand_cate'];
                $arrOld[$row['pt_id']]['top_cate'] = $row['top_cate'];
                $arrOld[$row['pt_id']]['depth'] = $row['depth'];
            }
            
            if(!empty($arrOld) && isset($arrOld)){
                $arrDrop=$this->array_diff_assoc2_deep($arrOld, $Pdata);//drop old data
                foreach ($arrDrop as $aDrop){
                    if(!empty($aDrop)){
                        $aPt_id[$aDrop['pt_id']]=$aDrop['pt_id'];
                    }
                }
                if(!empty($aPt_id)){
                    $this->_customsparts_mod->drop(db_create_in($aPt_id,'pt_id')." AND cst_id=".$_POST['cst_id']);
                }
            }
            if(!empty($Pdata) && isset($Pdata)){
                $arrAdd=$this->array_diff_assoc2_deep($Pdata, $arrOld);//add new data
                foreach ($arrAdd as $aAdd){
                    if(!empty($aAdd)){
                        $Adata[] = array(
                            'cst_id'         => $_POST['cst_id'],
                            'pt_id'			 => $aAdd['pt_id'],
                            'pt_cate'        => $aAdd['pt_cate'],
                            'is_dft'		 => $aAdd['is_dft'],
                            'top_cate'       => $aAdd['top_cate'],
                            'parent_cate'    => $aAdd['parent_cate'],
                            'grand_cate'     => $aAdd['grand_cate'],
                            'depth'          => $aAdd['depth'],
                        );
                    }
                }
                $cst_pt_id=$this->_customsparts_mod->add($Adata);

                foreach ($Pdata as $row){

                    //生成目录所需数组
                    if($row['top_cate'] == '8001' || $row['top_cate'] == '8030' || $row['top_cate'] == '8050'){
                        $blArr[] =  $row['pt_id'];
                    }
                
                }
                
                
                if(!empty($blArr)){
                
                    $this->mkCstDir($_POST['cst_id'],$blArr);
                
                }
                
				Qrcode('customs',$_POST['cst_id'],M_SITE_URL.'/index.php/custom-info-'.$_POST['cst_id'].'.html'); 
            }
            
        }
        
        
        //相册图片
        $gallery = array();
        foreach((array)$_POST['gallery'] as $k => $v){
            $f=dirname($v);
            $fname = str_replace($f, '',$v);
            $t = date("YmdHi");
            $sPath = '/upload/thumb/s/'.$t.$fname;
            $mPath = '/upload/thumb/m/'.$t.$fname;
        
            $s_img = make_thumb($v, ROOT_PATH.$sPath,$this->sw, $this->wh);
        
            $m_img = make_thumb($v, ROOT_PATH.$mPath,$this->mw, $this->mh);
        
            if($s_img && $m_img){
                $gallery[] = array(
                    'cst_id' => $_POST['cst_id'],
                    's_img'  => $sPath,
                    'm_img'  => $mPath,
                    'img'    => $v,
                );
            }
        }
        
        if(!empty($gallery)){
            $_gallery_mod = &m("gallery");
        
            $_gallery_mod->add($gallery);
        }
        
        ///---------------------- 记录log
//         array_multisort($Pdata);
//         $write = var_export($Pdata, true);
//         $logs = $_SERVER['DOCUMENT_ROOT']."/custom/{$_POST['cst_id']}/logs/".$_POST['cst_id'].".txt";
//         if(file_exists($logs)){
//             $old   = file_get_contents($logs);
//             if($old != $write){
//                 file_put_contents($_SERVER['DOCUMENT_ROOT']."/custom/{$_POST['cst_id']}/logs/".$_POST['cst_id']."-".date('Ymd-His').".txt", $write);
//                 file_put_contents($_SERVER['DOCUMENT_ROOT']."/custom/{$_POST['cst_id']}/logs/".$_POST['cst_id'].".txt", $write);
//             }
//         }else{
//             file_put_contents($_SERVER['DOCUMENT_ROOT']."/custom/{$_POST['cst_id']}/logs/".$_POST['cst_id'].".txt", $write);
//         }

        $this->show_message('操作成功!',
            'back_list',    'index.php?app=customs'
        );
    }

    function drop_gallery(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        $_gallery_mod = &m("gallery");
        $img = $_gallery_mod->get_info($id);
        
        if($img){
            if(is_file(ROOT_PATH."/".$img['s_img'])){
                unlink(ROOT_PATH."/".$img['s_img']);
            }
            
            if(is_file(ROOT_PATH."/".$img['m_img'])){
                unlink(ROOT_PATH."/".$img['m_img']);
            }
        }
        
        if ($id && $_gallery_mod->drop($id))
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
    
    /**
     * 删除
     * @author Ruesin
     */
    function drop(){
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
        if (!$id){
            $this->show_warning('Hacking Attempt');
            return;
        }
        $ids = explode(',', $id);
        $this->_customs_mod->drop($ids);
        $this->_customs_mod->drop_data($ids);//drop related data,must after drop data
        $this->show_message('drop_ok','back_list', 'index.php?app=customs&page=' . $ret_page);
    }
    /**
     * 检查基本款编号是否唯一
     * @author Ruesin
     */
   function check_cst_sn(){
      	$sn = isset($_GET['cst_sn'])?trim($_GET['cst_sn']):'';
      	$id = isset($_GET['cst_id'])?trim($_GET['cst_id']):'';
      	if($sn == '')return ;
      	if ($sn){
      	    if ($this->_customs_mod->unique_cst_sn($sn,$id)){
      	        echo true;
      	    } else {
      	        echo ecm_json_encode(false);
      	    }
      	}
   }
    /**
     * Ajax 返回默认选择下拉
     * @author Ruesin
     */
    function ajax_parts_select(){
        $ids = isset($_GET['ids']) ? trim($_GET['ids']) : '';
        $dft = isset($_GET['dft']) ? trim($_GET['dft']) : '';
        $arrParts=$this->_parts_mod->findAll(array(
            "conditions"=>db_create_in($ids,'part_id'),
            "fields" => "part_id,part_name",
        ));
        foreach ($arrParts as $v){
            if($v['part_id']==$dft){
                $out.="<option value='".$v['part_id']."' selected =true>".$v['part_name']."</option>";
            }else{
                $out.="<option value='".$v['part_id']."'>".$v['part_name']."</option>";
            }
        }
        echo $out;
    }
    
    /**
     * Ajax 获取组件类型
     * @author Ruesin
     */
    function ajax_parts_type(){
        $cateId = isset($_GET['cateId']) ? intval($_GET['cateId']) : 0;
        $types=$this->cateAll[$cateId]['parts_type'];
        $this->assign('types',$types);
        $this->display('customs/parts_type.html');
    }
    /**
     * Ajax 获取组件分类树
     * @author Ruesin
     */
    function ajax_parts_cate(){ 
        $id    = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $cstId = isset($_GET['cstId']) ? intval($_GET['cstId']) : 0;

        //编辑状态下,剔除没有选择的分类
        $cates = $this->_customs_mod->load_parts_cate($id,$cstId);
        
//         if($cstId == 0){
//             $fabric = $this->_customs_mod->getFabricCate();
//             //面料弹窗条件不一样,在这里判断后加一个条件
//             if($fabric[$id]){
//                 if(!empty($cates)){
//                     foreach ($cates as $key => $row){
//                         $cates[$key]['fabric'] = $fabric[$id][$row['cate_id']];
//                     }
//                 }
//             }
//         }
        
		//下拉
        if($cstId){
        	$links = $this->_customsparts_mod->find(array(
        	    "fields" => "cst_pt.*,p.part_name",
        	    'conditions'=>"cst_pt.cst_id = {$cstId} ",
        	    "join"	=>	"has_parts",
        	));
        	foreach ($links as $row ){
        		$select[$row['pt_cate']][$row['pt_id']] = $row['part_name'];
        	}
        }
        
        $this -> assign('selects',$select);
        $this -> assign('cst_id',$cstId);
        $this -> assign('top_id',$id);
        $this -> assign('cates',$cates);
        $this -> display('customs/parts_cate.html');       
    }
    
    /*
     * get ctype from gcategory 
     */
    function ajax_ctype($cateId=0){
        if($cateId==0){
            $cateId = isset($_GET['cateId']) ? intval($_GET['cateId']) : 0;
        }
        $res=$this->_gcategory->get($cateId);
        // echo $res['ctype'];
        return $res['ctype'];
    }

    
    /**
     * 算出基本款默认价格
     * @param $Pdata  格式化好的中间表数据
     * @param $cateId 分类ID
     * @return array $price 价格数组
     * @author Ruesin
     */
    function get_dft_price($Pdata,$cateId,$cst_service_fee){
    //上衣 : 2.1    西裤 : 1.5    衬衫 : 2.8    马夹 : 0.8    大衣 : 1.68   里料 : 2
//         $this->_customs_mod->setCate();

        $cates = $this->_customs_mod->getCate();
        $craft = $this->_customs_mod->getCraft();
        foreach($Pdata as $row){

            if($row['is_dft']!='' && $row['is_dft']==1){
                if(isset($craft[$row['grand_cate']])){                                              //工艺
                    
//                      $cft = $craft[$row['grand_cate']];
                     
//                      if(isset($cft['son'])){   //工艺有子级
                         
//                          if(isset($cft['son'][$row['pt_cate']])){ //有子级工艺  //应该不用判断这个吧
                             
//                              $arrPrice=$this->_parts_mod->get(array(
//                                  'conditions' => $row['pt_id'],
//                                  'fields'     => 'part_id,cost_price,price,maket_price',
//                              ));
                             
//                          }
    
//                      }else {
                         
                         $arrPrice=$this->_parts_mod->get(array(
                             'conditions' => $row['pt_id'],
                             'fields'     => 'part_id,cost_price,price,maket_price',
                         ));
                         
//                      }
                }elseif($row['top_cate'] == 8001 || $row['top_cate'] == 8050 || $row['top_cate'] == 8030){           //面料
                    $arrPrice=$this->_parts_mod->get(array(
                        'conditions' => $row['pt_id'],
                        'fields'     => 'part_id,cost_price,price,maket_price',
                    ));
                    
                    
//                     $arrPrice['cost_price']   = $cates[$cateId]['fabric_m']*$arrPrice['cost_price'];
                    $arrPrice['price']        = $cates[$cateId]['fabric_m']*$arrPrice['price'];
//                     $arrPrice['market_price'] = $cates[$cateId]['fabric_m']*$arrPrice['maket_price'];
            	    
//             	}elseif ($row['grand_cate'] == 8051 ){    //是里料  //不对吧?
                }elseif ($row['grand_cate'] == 313 || $row['grand_cate'] == 4088 || $row['grand_cate'] == 6291 ){    //里料
            	    
            	    $arrPrice=$this->_parts_mod->get(array(
            	        'conditions' => $row['pt_id'],
            	        'fields'     => 'part_id,cost_price,price,maket_price',
            	    ));
            	    
//             	    $arrPrice['cost_price']   = 3*$arrPrice['cost_price'];
            	    $arrPrice['price']        = $cates[$cateId]['lining_m']*$arrPrice['price'];
//             	    $arrPrice['market_price'] = $cates[$cateId]['lining_m']*$arrPrice['maket_price'];
            	    
            	}
            	
//             	$data['cst_cost']   += $arrPrice['cost_price'];
            	$data['cst_price']  += $arrPrice['price'];
//             	$data['cst_market'] += $arrPrice['maket_price'];
            	unset($arrPrice);//清空数组
            }
        }
        $data['cst_price'] += $cst_service_fee;//加上服务费
        return $data;
    }
    /*
     * compare tow array
    */
    function array_diff_assoc2_deep($array1, $array2) {
        $ret = array();
        foreach ($array1 as $k => $v) {
            if (!isset($array2[$k])){
                $ret[$k] = $v;
                //            }else if (is_array($v) && is_array($array2[$k])){
                //                $ret[$k] = $this->array_diff_assoc2_deep($v, $array2[$k]);
            }else if ($v !=$array2[$k]){
                $ret[$k] = $v;
            }else{
                unset($array1[$k]);
            }
        }
        return $ret;
    }
    /*
     * 获取作者姓名
     */
    function ajax_get_author(){
        $userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
        if($userId==0)exit;
        $out=$this->_member_mod->get($userId);
        echo $out['user_name'];
    }
    /**
     * 格式化POST组件
     * @param $post $_POST['cates']
     * @return array
     * @author Ruesin
     */
    function fmt_Adata($post){
        
        foreach ($post as $row){
            if($row['cate_id']!=''){                                        //选中
                if(isset($row['son_cate']) || !empty($row['son_cate'])){    //有子分类
                    
                    foreach ($row['son_cate'] as $son){
                        
                        if(isset($son['child_cate']) || !empty($son['child_cate'])){//有三级子分类
                            
                        	foreach ($son['child_cate'] as $child){
                        	    
                        	    if($child['cate_items']!='' && $child['cate_id']!=''){         //选中并且有值
                        	        
                        	        $items = array_filter(explode(',', $child['cate_items']));
                        	        
                        	        foreach ($items as $value){
                        	            
                        	            $Adata[$value] = array(
                        	                
                        	                'pt_id'			 => $value,
                        	                'pt_cate'        => $child['cate_id'],
                        	                
                        	            );
                        	            
                        	            if($child['cate_dft']==$value){
                        	                $Adata[$value]['is_dft']=1;
                        	            }else{
                        	                $Adata[$value]['is_dft']=0;
                        	            }
                        	            $Adata[$value]['parent_cate'] = $son['cate_id'];
                        	            $Adata[$value]['grand_cate']  = $row['cate_id'];
                        	            $Adata[$value]['top_cate']    = $row['top_id'];
                        	            $Adata[$value]['depth']       = 3;
                        	        }
                        	    }
                        	    
                        	}
                        }else{  //二级分类
                            if($son['cate_items']!='' && $son['cate_id']!=''){    //选中并且有值
                                $items = array_filter(explode(',', $son['cate_items']));
                                foreach ($items as $value){
                                    $Adata[$value] = array(
                                        'pt_id'			 => $value,
                                        'pt_cate'        => $son['cate_id'],
                                    );
                                    if($son['cate_dft']==$value){
                                        $Adata[$value]['is_dft']=1;
                                    }else{
                                        $Adata[$value]['is_dft']=0;
                                    }
                                    $Adata[$value]['parent_cate'] = $row['cate_id'];
                                    $Adata[$value]['grand_cate']  = $row['cate_id'];
                                    $Adata[$value]['top_cate']    = $row['top_id'];
                                    $Adata[$value]['depth']       = 2;
                                }
                            }
                        }
                    }
                }else{  //只有一级

                    if($row['cate_items']!='' && $row['cate_id']!=''){
                        $items = array_filter(explode(',', $row['cate_items'])) ;
                        foreach ($items as $value){
                            $Adata[$value] = array(
                                'pt_id'			 => $value,
                                'pt_cate'        => $row['cate_id'],
                            );
                            if($row['cate_dft']==$value){
                                $Adata[$value]['is_dft']=1;
                            }else{
                                $Adata[$value]['is_dft']=0;
                            }
                            $Adata[$value]['parent_cate'] = $row['cate_id'];
                            $Adata[$value]['grand_cate']  = $row['cate_id'];
                            $Adata[$value]['top_cate']    = $row['top_id'];
                            $Adata[$value]['depth']       = 1;
                            if($row['cate_id']==8001 || $row['cate_id']==8030 || $row['cate_id']==8050){
                                
                                $Adata[$value] = $this->_customs_mod->cate_stom($Adata[$value]);
                                
                            }
                            
                        }
                    }
                }
            }
        }

        return $Adata;
    }
    
    /**
     * 生成基本款组件目录
     * @param $cst_id 基本款id
     * @param $blArr  面料数组
     * @return 
     * @author Ruesin
     */
    function mkCstDir($cst_id = 0,$blArr = array()){
        
        $rss    = $this->_parts_mod->findAll(array('conditions' => " 1 = 1 AND ".db_create_in($blArr,'part_id')));
        
        $cstDir = $_SERVER['DOCUMENT_ROOT'].'/'.IMG_PATH.'/custom/'.$cst_id.'/';
        
        foreach ($rss as $r){
        
            $fDir['ml']     = $cstDir.$r['code'].'/';
        
            $fDir['front']  = $fDir['ml'].'/10004/';//前
            $fDir['left']   = $fDir['ml'].'/10013/';//侧
            $fDir['back']   = $fDir['ml'].'/10005/';//后
            $fDir['liliao']   = $fDir['ml'].'/material/';//里料
            $fDir['logs']   = $cstDir.'/logs';//日志
            
            foreach ($fDir as $d){
                mkDirs($d);
            }
        
        }
    }
    //因为编辑时不要ajax,故要将所有数据调取出来.
    function getParts($types=array(),$cstId = 0){
    	
        foreach ($types as $key => $type){
            
            $types[$key]['cates'] = $this->_customs_mod->load_parts_cate($type['id'],$cstId);

        }
        return $types;
    }
    function getSelects($cstId = 0){
        
        $links = $this->_customsparts_mod->find(array(
            "fields" => "cst_pt.*,p.part_name",
            'conditions'=>"cst_pt.cst_id = {$cstId} ",
            "join"	=>	"has_parts",
        ));
        
        foreach ($links as $row ){
            $select[$row['pt_cate']][$row['pt_id']] = $row['part_name'];
        }
        return $select;
    }
    
    
    
    
    
    
    
    
    
    ////***************************************////
    function album(){
        $db = &db();
         
        $sql = "select * from rc_album order by add_time desc";
        $album = $db->getAll($sql);
        foreach($album as $k=>$v){
        	if($v['cate'] == 1){
        		$album[$k]['top_url'] = getDesignUrl($v['top_url'],500);
        	}else 
        		$album[$k]['top_url'] = getCameraUrl($v['top_url'],500);
        	
        	$album[$k]['uname'] = getUnameByUid($v['uid']);
        	
        	
        }
        $this->assign('album', $album);
        $this->display('customs/album.html');
    }
    
    function reco_album(){
    	$id = $_REQUEST['id'];
    	$db = &db();
    	
    	$sql = "select * from rc_album  where id = ".$id;
    	$photo = $db->getRow($sql);
    	if(!$photo)
    		echo 0;

    	if($photo['recommend'])
    		$num = 0;
    	else 
    		$num = 1;
    	
    	$sql = "update rc_album set recommend = $num where id = ".$id;
    	$rs = $db->query($sql);
    	
    	echo 1;
    		
    		echo $rs;
    }
    
    function delAlbum(){
    	$id = $_REQUEST['id'];
    	if(!$id)
    		return -1;
    	$m = m('album');
    	$rs = $m->delById($id);
    	echo $rs;
    }
    
    function upload(){
        $db = &db();
        $opt = $_REQUEST['opt'];
        if($opt){
            $uid = $_REQUEST['uid'];
            $title = $_REQUEST['title'];
            $sql ="select * from rc_album where uid = ".$uid;
            $album = $db->getAll($sql);
            //     		if(!$album){//如果用户相册为空，系统自动插入一个默认
            //     			$m = m('album');
            //     			$data = array(
            //     					'add_time'=>time(),
            //     					'title'=>'默认',
            //     					'uid'=>$uid,
            //     					'cate'=>1,
            //     					'description'=>'默认相册描述',
            //     			);
            //     			$album_id = $m->add($data);
            //     		}
    
            $dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/sheji/';
            $fileName = $this->visitor->info['user_id'] ."_de_" .md5( uniqid() . mt_rand(0,255) ) . ".jpg";
    
    
            $fileDirName2 = $dir ."235x315/" . $fileName;
            pro_img('up_file',235,315,$fileDirName2);
            $fileDirName3 = $dir ."520x685/" . $fileName;
            pro_img('up_file',520,685,$fileDirName3);
    
            $fileDirName1 = $dir ."original/" . $fileName;
            $rs = move_uploaded_file($_FILES['up_file']["tmp_name"],$fileDirName1);
            //     		if($album){
            //     			$album_id = $album[0]['id'];
            //     		}else{
            //     			$m->edit($album_id,array('top_url'=>$src,));
            //     		}
            $m = m("userphoto");
            $data = array(
                'add_time'=>time(),
                'url'=>$fileName,
                'cate'=>1,
                //     				'album_id'=>$album_id,
                'uid'=>$uid,
                'title'=>$title,
            );
            $rs  =$m->add($data);
    
            $m = m('member');
            $m->setInc(" user_id = $uid " , 'pic_num');
            //     		$m = m('album');
            //     		$rs = $m->setInc(" id = $album_id " , 'pic_num');
            echo "上传成功";
            exit;
        }
    
         
        $sql = "select user_id,user_name from rc_member ";
        $member = $db->getAll($sql);
         
         
        $this->assign('member', $member);
         
        $this->display('customs/upload.html');
    }
    
    
    
    
    
    
    
    
    
}


