<?php
class FashionApp extends BackendApp{
    var $_fashion_mod;
    var $_mod_custom;
    function __construct(){
        $this->FashionApp();
    }
    function FashionApp(){
        parent::__construct();
        $this->_mod_custom = &m("custom");
        $this->_fashion_mod=&m('fashion');
    }
    function index(){
        $pageper=30;
        $this->import_resource(array('script'=>'inline_edit_admin.js'));//js排序
        $conditions=$this->_get_query_conditions(array(
            array( 
                'field'=>'title',
                'equal'=>'like')           
        ));
        if (isset($_GET['sort']) && isset($_GET['order'])){
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc'))){
                $sort  = 'pubdate';
                $order = 'desc';
            }
        }else{
            $sort  = 'pubdate';
            $order = 'desc';
        }
        $page=$this->_get_page($pageper);
        $fashion_list=$this->_fashion_mod->find(array(
            'condition'=>'1=1'.$conditions,
            'count'=>true,
            'order'=>"$sort $order",
            'limit'=>$page['limit']
        ));
        if(!$fashion_list){
            $pageindex = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);
            $start = ($page -2) * $page_per;
            $pageper=$page['pageper'];
            $page=array();
            $page=array(
                'limit'=>"{$start},{$pageper}",
                'curr_page'=>$pageindex-1,
                'pageper'=>$pageper
            );
            $fashion_list=$this->_fashion_mod->find(array(
                'condition'=>'1=1'.$conditions,
                'count'=>true,
                'order'=>"$sort $order",
                'limit'=>$page['limit']
             ));
        }
        foreach ($fashion_list as $key=>$value){
            $fashion_list[$key]['pubdate']=date('Y-m-d H:i:s',$value['pubdate']);
        }
        $this->assign('fashion_list',$fashion_list);
        $page['item_count'] = $this->_fashion_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info',$page);
        $this->display('fashion/fashion.index.html');
    }
    function add(){
        if(!IS_POST){
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'summary,content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
        
            $this->display('fashion/fashion.add.html');
        
        }else{
            $pubdate=time();
            $data= array(
                'title'=>$_POST['title'],
                'pubdate'=>$pubdate,
                'pphoto'=>$_POST['pphoto'],
                'content'=>$_POST['content'],
                'summary' =>$_POST['summary'],
                'f_sort'=>$_POST['f_sort']           
            );
             
            $id = $this->_fashion_mod->add($data);
            
            if($id){
                $this->addLink($id);
                $this->show_message('添加成功',
                    'back_list', 'index.php?app=fashion'
                );
            }else{
                 
                $this->show_message("添加失败");
            }
        }
    }
    function edit(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        $ret_page=isset($_GET['ret_page'])?intval($_GET['ret_page']):1;
        $data     = $this->_fashion_mod->find($id);
        if (empty($data))
        {        
            $this->show_warning('信息不存在！');
            return;
        }
        if(!$_POST){
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,change_upload.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            $data    =   current($data);
            $link_mod=m('fashionLink');
            $links=$link_mod->find(array(
                'conditions'=>"link.fashion_id='{$id}'",
                'join'=>'belongs_to_custom',
                'fields'=>'c.id as cid,c.name,link.id as linkid'
            ));
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $this->assign('build_editor',$this->_build_editor(array(
                'name'=>'summary,content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));           
            $this->assign("links",$links);
            $this->assign('fashion_list',$data);
            $this->display("fashion/fashion.edit.html");
        }else{

            $data= array(
                'title'=>$_POST['title'],
                'pphoto'=>$_POST['pphoto'],
                'content'=>$_POST['content'],
                'summary' =>$_POST['summary'],
                'f_sort'=>$_POST['f_sort']           
            );
        }
        if (IS_POST){
            $res = $this->_fashion_mod->edit($id, $data);
            if($res){
                $this->addLink($id);
                $this->show_message('潮流信息编辑成功!',
                    'back_list', 'index.php?app=fashion&page='.$ret_page);
            }
        }
    }
    function drop(){
        $id=isset($_GET['id'])?trim($_GET['id']):'';
        $ret_page=isset($_GET['ret_page'])?intval($_GET['ret_page']):1;
        if(!$id){
            $this->show_warning('Hacking Attempt');
            return;
        }
        $ids=explode(',',$id);
        if(!$this->_fashion_mod->drop($ids)){
            $this->show_warning($this->_fashion_mod->get_error());
            return;
        }else{
            $this->show_message('删除成功','back_list', 'index.php?app=fashion&page=' . $ret_page);
        }
    }
    function loadCustom(){
        $ids = trim($_GET['ids']);
        if(!$ids){
            $this->json_error('非法操作');
            return;
        }
    
        $list = $this->_mod_custom->find(array(
            "conditions" => "id IN ($ids)",
            'fields'     => "id,name"
        ));
    
        $retArr = array();
    
        foreach($list as $key => $val){
            $retArr[] = $val;
        }
    
        $this->json_result($retArr);
        die();
    }
    function addLink($fashion_id){
        $recommend = $_POST['newRecommend'];
        $data = array();
        $arr  = array();
         
        $link_mod = &m("fashionLink");
        $links    = $link_mod->find(array(
            "conditions" => "fashion_id='{$fashion_id}'",
        ));
        $deLink = array();
        foreach((array) $links as $key => $val){
            if(isset($recommend[$val['link_id']])){
                 unset($recommend[$val["link_id"]]);
            }else{
                $deLink[] = $val['id'];
            }
         }
        if(!empty($deLink)){
             $link_mod->drop($deLink);
        }
        if(!empty($recommend)){
         foreach((array)$recommend as $key => $val){
            if($fashion_id != $val && !in_array($val, $arr)){
            $data[] = array(
                "fashion_id"   => $fashion_id,
                'link_id'     => $val,
             );
            }
            $arr[] = $val;
         }
         if(!empty($data)){
            $link_mod->add($data);
         }
       }
    }
    function removeLink(){
        $id = intval($_GET["id"]);
        $link_mod = &m("cusLink");
        $link_mod->drop($id);
        die($this->json_result());
    }
}
?>