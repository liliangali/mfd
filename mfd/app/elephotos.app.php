<?php

/**
 *    电子相册控制器
 *
 *    @author    liu.chao
 *    @usage    none
 */
class ElephotosApp extends BackendApp
{
	var $_series_mod;
    var $_good_mod;
    var $_goodimg_mod;
    var $_uploadedfile_mod;
    var $_cover_mod;

    function __construct()
    {
        $this->ElephotosApp();

    }

    function ElephotosApp()
    {
        parent::BackendApp();

        $this->_series_mod = &m('series');
        $this->_good_mod   = &m('newgoods');
        $this->_goodimg_mod= &m('newgoodimg');
        $this->_uploadedfile_mod = &m('uploadedfile');
        $this->_cover_mod = &m('cover');
    }

    /**
     *    系列列表
     *
     *    @author    Hyber
     *    @return    void
     */
    function index()
    {
        $series_list = $this->_series_mod->find(array(
            'conditions'=> '',
            'order'=> 'sort_order ASC',
            'count'=>true,
        ));
        foreach($series_list as $key=>$val){
            $series_list[$key]['image'] = LOCALHOST1.'/'.$val['image'];
        }
//        print_exit($series_list);
        $page   =   $this->_get_page(20);   //获取分页信息
        $page['item_count']=$this->_series_mod->getCount();

//        print_exit($series_list);
        $this->_format_page($page);
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->assign('page_info', $page);   //将分页信息传递给视图，用于形成分页条
        $this->assign('series', $series_list);
        $this->display('series.index.html');
    }
     /**
     *    新增系列
     *
     *    @author    Hyber
     *    @return    void
     */
    function add_series()
    {
        if (!IS_POST)
        {
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $this->assign('build_upload', $this->_build_upload(array('belong' => BELONG_ARTICLE, 'item_id' => 0))); // 构建swfupload上传组件
            $this->display('series.form.html');
        }
        else
        {
            $data = array();
            $data['series_name']   =   $_POST['title'];
            $data['is_show']    =   $_POST['if_show'];
            $data['sort_order'] =   $_POST['sort_order'];
            $path="../upload/series/";
            if(!file_exists($path)){
                mkdir("$path", 0777);
            }
            $tp = array("image/gif","image/png","image/jpeg",'image/jpg');
            if(!in_array($_FILES["img"]["type"],$tp)){
                $this->show_warning('格式不对','<<返回');
                exit;
            }
            if($_FILES["img"]["name"]){
                $file1=$_FILES["img"]["name"];
                $file2 = $path.time().$file1;
                $flag=1;
            }
            if($flag){
                $result=@move_uploaded_file($_FILES["img"]["tmp_name"],$file2);
                $data['image'] = $file2;
            }else{
                $this->show_warning('文件传输失败','back');
                return ;
            }
            if($result){
                $this->_series_mod->add($data);
            }else{
                $this->show_warning('文件传输失败','back');
                return ;
            }

            $this->show_message('add_article_successed',
                'back_list', 'index.php?app=elephotos&amp;act=index',
                'continue_add', 'index.php?app=elephotos&amp;act=add_series'
            );
        }
    }
     /**
     *    编辑系列
     *
     *    @author    Hyber
     *    @return    void
     */
    function edit_series()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id)
        {
            $this->show_warning('no_such_article');
            return;
        }
         if (!IS_POST)
        {
            $find_data     = $this->_series_mod->get($id);
            $find_data['image'] = LOCALHOST1.'/'.$find_data['image'];
            if (empty($find_data))
            {
                $this->show_warning('no_such_article');

                return;
            }
            $this->assign('data',$find_data);
            $this->assign('build_upload', $this->_build_upload(array('belong' => BELONG_ARTICLE, 'item_id' => $id))); // 构建swfupload上传组件
            $this->display('series.form.html');
        }
        else
        {
            $data = array();
            $data['series_name']    =   $_POST['title'];
            $data['is_show']        =   $_POST['is_show'];
            $data['sort_order']     =   $_POST['sort_order'];
            if($_FILES['img']['name']){
                $path="../upload/series/";
                if(!file_exists($path)){
                    mkdir("$path", 0777);
                }
                $tp = array("image/gif","image/png","image/jpeg",'image/jpg');
                if(!in_array($_FILES["img"]["type"],$tp)){
                    $this->show_warning('格式不对','<<返回');
                    exit;
                }
                if($_FILES["img"]["name"]){
                    $file1=$_FILES["img"]["name"];
                    $file2 = $path.time().$file1;
                    $flag=1;
                }
                if($flag){
                    $result=@move_uploaded_file($_FILES["img"]["tmp_name"],$file2);
                    $data['image'] = $file2;
                }else{
                    $this->show_warning('文件传输失败','back');
                    return ;
                }
            }
            $rows=$this->_series_mod->edit($id, $data);
            if ($this->_series_mod->has_error())
            {
                $this->show_warning($this->_article_mod->get_error());
                return;
            }
            $this->show_message('edit_article_successed',
                'back_list',    'index.php?app=elephotos&amp;act=index',
                'edit_again',    'index.php?app=elephotos&amp;act=edit_series&amp;id=' . $id);
        }
    }

    /**
     *    商品列表
     *
     *    @author    Hyber
     *    @return    void
     */
    function good_list()
    {
        //获取商品列表
        $conditon = '1=1';
        $con = isset($_GET['con']) ? trim($_GET['con']) : '';
        if($con){
            $conditon .= ' AND belong_series='.$con;
        }

        $good_list = $this->_good_mod->find(array(
            'conditions'=> $conditon,
            'order'=> 'sort_order ASC,add_time DESC',
            'count'=>true,
        ));

//        print_exit($good_list);
        $series = $this->get_series();

        $page   =   $this->_get_page(20);   //获取分页信息
        $page['item_count']=$this->_series_mod->getCount();

        $this->assign('series',$series);
        $this->_format_page($page);
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->assign('page_info', $page);   //将分页信息传递给视图，用于形成分页条
        $this->assign('goods', $good_list);
        $this->display('newgoods.index.html');
    }
    /**
     *    获取系列
     *
     *    @author    Hyber
     *    @return    void
     */
    function get_series(){
        //获取显示的系列
        $series_list = $this->_series_mod->find(array(
            'conditions'=> 'is_show=0',
            'order'     => 'sort_order ASC',
            'fields'    => 'series_name',
        ));
        $series = array();
        foreach($series_list as $key=>$val){
            $series[$key] = $val['series_name'];
        }
        return $series;
    }
    /**
     *    添加商品
     *
     *    @author    Hyber
     *    @return    void
     */
    function add_good(){

        if(IS_POST){
            $data = array(
                'good_name'  => $_POST['name'],
                'good_price' => $_POST['price'],
                'good_process' => $_POST['content'],
                'fabric'       => $_POST['fabric'],
                'belong_series'=> $_POST['series'],
                'sort_order'   => $_POST['sort_order'],
                'add_time'     => time(),
                'is_show '     => $_POST['is_show'],
            );

            $good_id = $this->_good_mod->add($data);

            foreach((array)$_POST['gallery'] as $k => $v){
                $gallery = array(
                    'good_id'    => $good_id,
                    'img_url'    => $v,
                    'add_time'   => time(),
                );
                if(!empty($gallery)){
                    $this->_goodimg_mod->add($gallery);
                }

            }
//            $this->addGallery($good_id);
            $this->show_message("商品添加成功",'back_list', 'index.php?app=elephotos&act=good_list');
        }else{
            $cdata = $this->_goodimg_mod->find();

            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));

            $this->assign('build_editor1', $this->_build_editor(array(
                'name' => 'content',
            )));
            $this->assign('build_editor2', $this->_build_editor(array(
                'name' => 'fabric',
            )));
            $series = $this->get_series();

            $this->assign("options", $this->_get_options());

            $param = array(
                'dir' => 'gallery',
                'button_text' => "上传相册图片"
            );

            $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
            $this->assign('series',$series);
            $this->assign("to_site", $this->to_site);
            $this->assign("style_list", $this->style);
            $this->assign("cat_list",   $this->init);
            $this->display("newgoods.info.html");
        }
    }
    /**
     *    删除商品
     *
     *    @author    Hyber
     *    @return    void
     */
    function drop_good(){
        $ids = isset($_GET['id']) ? trim($_GET['id']) : '';

        if (!$ids)
        {
            $this->show_warning('未找到相应商品');

            return;
        }
        $ids=explode(',', $ids);
        $message = 'drop_ok';
        foreach ($ids as $key=>$val){
            //删除商品
            if(!$this->_good_mod->drop($val)){
                $this->show_warning($this->_good_mod->get_error());
                return;
            }else{
                //删除商品图片
                $this->_goodimg_mod->drop('good_id='.$val);
            }
        }

        $this->show_message($message,'back_list');
    }
    /**
     *    编辑商品
     *
     *    @author    Hyber
     *    @return    void
     */
    function edit_good(){
        $id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if(IS_POST){
            $data = array(
                'good_name'  => $_POST['name'],
                'good_price' => $_POST['price'],
                'good_process' => $_POST['content'],
                'fabric'       => $_POST['fabric'],
                'belong_series'=> $_POST['series'],
                'sort_order'   => $_POST['sort_order'],
                'add_time'     => time(),
                'is_show '     => $_POST['is_show'],
            );
//            print_exit($data);

             $this->_good_mod->edit($id,$data);

            foreach((array)$_POST['gallery'] as $k => $v){
                $gallery = array(
                    'good_id'    => $id,
                    'img_url'    => $v,
                    'add_time'   => time(),
                );
                if(!empty($gallery)){
                    $this->_goodimg_mod->add($gallery);
                }

            }
//            $this->addGallery($good_id);
            $this->show_message("商品编辑成功",'back_list', 'index.php?app=elephotos&act=good_list');
        }else{

            $data = $this->_good_mod->get($id);
            $idata = $this->_goodimg_mod->find('good_id='.$id);
//            print_exit($data);
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));

            $this->assign('build_editor1', $this->_build_editor(array(
                'name' => 'content',
            )));
            $this->assign('build_editor2', $this->_build_editor(array(
                'name' => 'fabric',
            )));
            $series = $this->get_series();

            $this->assign("options", $this->_get_options());

            $param = array(
                'dir' => 'gallery',
                'button_text' => "上传相册图片"
            );

            $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
            $this->assign('data',$data);
            $this->assign('idata',$idata);
            $this->assign('series',$series);
            $this->assign("to_site", $this->to_site);
            $this->assign("style_list", $this->style);
            $this->assign("cat_list",   $this->init);
            $this->display("newgoods.info.html");
        }
    }
    function drop_gallery(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($this->_goodimg_mod->drop($id))
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
     *    封面列表
     *
     *    @author    Hyber
     *    @return    void
     */
    function cover(){
        $cover_list = $this->_cover_mod->find(array(
            'conditions'=> '',
            'order'=> 'location ASC',
            'count'=>true,
        ));

        foreach($cover_list as $key=>$val){
            $cover_list[$key]['img_url'] = LOCALHOST1.'/'.$val['img_url'];
        }
        $page   =   $this->_get_page(20);   //获取分页信息
        $page['item_count']=$this->_series_mod->getCount();

//        print_exit($series_list);
        $this->_format_page($page);
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->assign('page_info', $page);   //将分页信息传递给视图，用于形成分页条
        $this->assign('cover', $cover_list);
        $this->display('cover.list.html');
    }
    /**
     *    添加封面
     *
     *    @author    Hyber
     *    @return    void
     */
    function add_cover(){
        if (!IS_POST)
        {

            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $this->assign('build_upload', $this->_build_upload(array('belong' => BELONG_ARTICLE, 'item_id' => 0))); // 构建swfupload上传组件
            $this->display('cover.info.html');
        }
        else
        {
            $data = array();
            $data['add_time']      =   time();
            //查询封面位置是否已有数据 有的话返回
            $exsit = $this->_cover_mod->find();
            if($exsit){
                $this->show_warning('封面已有数据,不能重复添加','back');
                return;
            }
            $path="../upload/cover/";
            if(!file_exists($path)){
                mkdir("$path", 0777);
            }
            $tp = array("image/gif","image/png","image/jpeg",'image/jpg');
            if(!in_array($_FILES["img"]["type"],$tp)){
                $this->show_warning('格式不对','<<返回');
                exit;
            }
            if($_FILES["img"]["name"]){
                $file1=$_FILES["img"]["name"];
                $file2 = $path.time().$file1;
                $flag=1;
            }
            if($flag){
                $result=@move_uploaded_file($_FILES["img"]["tmp_name"],$file2);
                $data['img_url'] = $file2;
            }else{
                $this->show_warning('文件传输失败','back');
                return ;
            }
            if($result){
                $this->_cover_mod->add($data);
            }else{
                $this->show_warning('文件传输失败','back');
                return ;
            }

            $this->show_message('add_cover_successed',
                'back_list', 'index.php?app=elephotos&amp;act=cover',
                'continue_add', 'index.php?app=elephotos&amp;act=add_cover'
            );
        }
    }
    /**
     *    编辑封面
     *
     *    @author    Hyber
     *    @return    void
     */
    function edit_cover(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$id)
        {
            $this->show_warning('no_such_cover');
            return;
        }
        if(IS_POST) {
            $data = array();
            if ($_FILES['img']['name']) {
                $path = "../upload/cover/";
                if (!file_exists($path)) {
                    mkdir("$path", 0777);
                }
                $tp = array("image/gif", "image/png", "image/jpeg", 'image/jpg');
                if (!in_array($_FILES["img"]["type"], $tp)) {
                    $this->show_warning('格式不对', '<<返回');
                    exit;
                }
                if ($_FILES["img"]["name"]) {
                    $file1 = $_FILES["img"]["name"];
                    $file2 = $path . time() . $file1;
                    $flag = 1;
                }

                if ($flag) {
                    $result = @move_uploaded_file($_FILES["img"]["tmp_name"], $file2);
                    $data['img_url'] = $file2;
                } else {
                    $this->show_warning('文件传输失败', 'back');
                    return;
                }
            }
            $rows = $this->_cover_mod->edit($id, $data);
            if ($this->_cover_mod->has_error()) {
                $this->show_warning($this->_cover_mod->get_error());
                return;
            }
            $this->show_message('edit_cover_successed',
                'back_list', 'index.php?app=elephotos&amp;act=cover',
                'edit_again', 'index.php?app=elephotos&amp;act=edit_cover&amp;id=' . $id);
        }else{
            $cover = $this->_cover_mod->get($id);
            $cover['img_url'] = LOCALHOST1.'/'.$cover['img_url'];
            $this->assign('cover',$cover);
            $this->display('cover.info.html');
        }

    }
    /**
     *    删除封面
     *
     *    @author    Hyber
     *    @return    void
     */
    function drop_cover(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id)
        {
            $this->show_warning('no_such_cover');

            return;
        }
        $ids=explode(',', $id);
        $message = 'drop_ok';
        foreach ($ids as $key=>$val){
            if(!$this->_cover_mod->drop($val)){
                $this->show_warning($this->_cover_mod->get_error());
                return;
            }
        }

        $this->show_message($message,'back_list');

    }


    //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();

       if (in_array($column ,array('if_show', 'sort_order')))
       {
           $data[$column] = $value;
           $this->_article_mod->edit($id, $data);
           if(!$this->_article_mod->has_error())
           {
               echo ecm_json_encode(true);
           }
       }
       else
       {
           return ;
       }
       return ;
   }
    function drop_series()
    {
        $ids = isset($_GET['id']) ? trim($_GET['id']) : '';

        if (!$ids)
        {
            $this->show_warning('no_such_article');

            return;
        }
        $ids=explode(',', $ids);
        $message = 'drop_ok';
        foreach ($ids as $key=>$val){
            if(!$this->_series_mod->drop($val)){
                $this->show_warning($this->_series_mod->get_error());

                return;
            }
        }

        $this->show_message($message,'back_list');
    }

    /* 更新排序 */
    function update_order()
    {
        if (empty($_GET['id']))
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        $ids = explode(',', $_GET['id']);
        $sort_orders = explode(',', $_GET['sort_order']);
        foreach ($ids as $key => $id)
        {
            $this->_article_mod->edit($id, array('sort_order' => $sort_orders[$key]));
        }

        $this->show_message('update_order_ok');
    }

        /* 构造并返回树 */
    function &_tree($acategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($acategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree;
    }
        /* 取得可以作为上级的文章分类数据 */
    function _get_options()
    {
        $mod_acategory = &m('acategory');
        $acategorys = $mod_acategory->get_list();
        $tree =& $this->_tree($acategorys);
        return $tree->getOptions();
    }

    /* 异步删除附件 */
    function drop_uploadedfile()
    {
        $file_id = isset($_GET['file_id']) ? intval($_GET['file_id']) : 0;
        if ($file_id && $this->_uploadedfile_mod->drop($file_id))
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
    /* 文章标记为唯一标识不可重复 */
    function ajax_code_unique()
    {
    	$id=empty($_POST['id'])?'':trim($_POST['id']);
    	 $code=empty($_POST['code'])?'':trim($_POST['code']);
    	 if($code){
    	 	$article=$this->_article_mod->find(
    	 			array("conditions"=>"code='$code'")
    	 	);   	 	
    	 	if($id){
    	 		if($article && !$article[$id]){  	 			
    	 			echo ecm_json_encode(false);
    	 			return ;
    	 		}else{
    	 			echo ecm_json_encode(true);
    	 			return ;
    	 		}
    	 	}else{
    	 		if($article){
    	 			echo ecm_json_encode(false);
    	 			return ;
    	 		}else{
    	 			echo ecm_json_encode(true);
    	 			return ;
    	 		}
    	 	}   	
    	 }else{
    	 		return ;
    	 }
    	 return ;
    }
}

?>