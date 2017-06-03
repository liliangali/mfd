<?php

/* 店铺控制器 */
class DesignerApp extends BackendApp
{
    var $_designer_mod;
    var $_member_mod;
    var $sw = 80;
    var $sh = 80;
    var $mw = 300;
    var $mh = 300;
    var $_mod_cyzGallery;

    function __construct()
    {
        $this->DesignerApp();
    }

    function DesignerApp()
    {
        parent::__construct();
        $this->_designer_mod =& m('designer');
        $this->_member_mod =& m('member');
        $this->_mod_cyzGallery =& m('cystory_gallery');
     
    }

    function index()
    {
      
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'username',
                'equal' => 'like',
            ),
            array(
                'field' => 'englishname',
                'equal' => 'like',
            ),
        ));
        
        
        $sort = ($_GET['sort'] == 'l_order') ? 'l_order' : 'id';
        $order = empty($_GET['l_order']) ? 'desc' : $_GET['l_order'];
         
         
        $page = $this->_get_page(30);
        $data = $this->_designer_mod->find(array(
            'fields' => 'this.*',
            'conditions' => '1=1' . $conditions,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        )); 
    
        $page['item_count'] = $this->_designer_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $_GET['field_value']? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        $this->assign('data',$data);
         
        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
    
      
        $this->display('designer.index.html');
    }
    
    
    function add(){
        if(!IS_POST){
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            
            $param = array(
            		'dir' => 'gallery',
            		'button_text' => "上传相册图片"
            );
            $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
            
            $this->display('designer.add.html');
            
        }else{
            
             $data= array(
                'username'=>$_POST['chinaname'],
                'signaturl' =>$_POST['signaturl'],
                'content'=>$_POST['content'],
                 'link'  =>$_POST['link'],
                 'linktype'=>$_POST['linktype'],
                 'l_order' =>$_POST['l_order'],
                'photo_url'=>$_POST['small_img'],
                 'userid' =>$_POST['parents'],
                'is_show'   => $_POST['if_show'],
               
            );
             $id = $this->_designer_mod->add($data);
             $this->addGallery($id);
             if($id){
             
                $this->show_message('添加成功',
    				'back_list', 'index.php?app=designer&amp;act=index&amp;cate_id='.$data['cate_id']
    		);
             }else{
             
                 $this->show_message("添加失败");
             }
            
            
        }
        
    }
    function edit(){
      
        if(!IS_POST){
            $id = empty($_GET['id']) ? 0 :intval($_GET['id']);
            $designer_info = $this->_designer_mod->get_info($id);
            $user_info = $this->_member_mod->get("user_id = '$designer_info[userid]'");

            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            
            $this->_mod_cyzGallery = &m("cystory_gallery");
            $gallery_list = $this->_mod_cyzGallery->find(array(
                'conditions' => "custom_id = '{$id}'",
                'order' => 'sort ASC',
               
            ));

			$param = array(
            		'dir' => 'gallery',
            		'button_text' => "上传相册图片"
            );
            $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
            
            $this->assign("gallery_list", $gallery_list);
            $this->assign("designer",$designer_info);
            $this->assign("userinfo",$user_info);
            $this->display('designer.edit.html');
            
        }else{
            $id = empty($_GET['id']) ? 0 :intval($_GET['id']);
            $data['username']	     = trim($_POST['chinaname']);
            $data['signaturl']     = trim($_POST['signaturl']);
            $data['content']        = trim($_POST['content']);
            $data['link']        = trim($_POST['link']);
            $data['linktype']        = trim($_POST['linktype']);
            $data['l_order']        = trim($_POST['l_order']);
            $data['photo_url']        = trim($_POST['small_img']);
            $data['is_show']          = trim($_POST['if_show']);
            if($_POST['parents'])
            {
                  $data['userid']        = trim($_POST['parents']);
            }
          
            
            if($id){  //add
             
               $is_edit = $this->_designer_mod->edit("id=".$id,$data);
               $a = $this->addGallery($id);
               if($is_edit){
                   
                   $this->show_message('修改成功',
                       'back_list', 'index.php?app=designer&act=index&amp;cate_id='.$cate_id
                   );
                
                   
               }
            }
            
           
            
            
        }
        
    }
    
    function del(){
        
        $id = $_REQUEST['id'];
        $isdel=$this->_designer_mod->drop("id=$id");
        if($isdel){
            $this->json_result('drop_ok');
            return;
        }else{
        
            $this->json_error('drop_error');
            return;
        }
    }

    function loadCyz(){

        $ids = trim($_GET['ids']);
        $name = trim($_GET['name']);


        if(!$ids){
            $this->json_error('非法操作');
            return;
        }

        $list = $this->_member_mod->find(array(

            "conditions"=>"user_id IN ($ids)  AND serve_type=1",
            'fields'    =>"user_id,nickname,user_name"
            ));

        $retArr = array();
        
        foreach((array)$list as $key => $val){
            $retArr['parents']['children'][] = $val;
        }
        //print_r($retArr);die;
        $this->assign("list", $retArr);
        $this->assign("name",  $name);
        $content = $this->_view->fetch("cyzinfo.html");
        $this->json_result($content);
        die();
    }

    function removeLink(){
        $id = intval($_GET["id"]);
        $link_mod = &m("cusLink");
        $link_mod->drop($id);
        die($this->json_result());
    }
    
    function addGallery($id)
    {
        $this->_mod_cyzGallery = &m("cystory_gallery");
        //相册图片
        import("image.func");
        $gallery = array();

        foreach((array)$_POST['gallery'] as $k => $v){

            $f=dirname($v);
            $fname = str_replace($f, '',$v);
            $t = date("YmdHi");
            $sPath = '/upload/cystory/s/'.$t.$fname;
            $mPath = '/upload/cystory/m/'.$t.$fname;

            $s_img = make_thumb($v, ROOT_PATH.$sPath,$this->sw, $this->wh);

            $m_img = make_thumb($v, ROOT_PATH.$mPath,$this->mw, $this->mh);

            if($s_img && $m_img){
                $gallery[] = array(
                    'custom_id' => $id,
                    'small_img'  => $sPath,
                    'middle_img'  => $mPath,
                    'source_img'    => $v,
                );
            }
        }
      
        if(!empty($gallery)){
            $this->_mod_cyzGallery->add($gallery);
        }

        

    }

        function drop_gallery(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
        $img = $this->_mod_cyzGallery->get_info($id);
    
        if($img){
            if(is_file(ROOT_PATH."/".$img['small_img'])){
                unlink(ROOT_PATH."/".$img['small_img']);
            }
    
            if(is_file(ROOT_PATH."/".$img['middle_img'])){
                unlink(ROOT_PATH."/".$img['middle_img']);
            }
        }
    
        if ($this->_mod_cyzGallery->drop($id))
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
    
    
}

?>
