<?php
//ns add 尺码名称
class SizeStyleApp extends BackendApp
{
    var $_sizeStyle_mod;
    function __construct()
    {
         parent::__construct();
         $this->_sizeStyle_mod =& m('size_style');
    }

    function index(){
      $page = $this->_get_page(30);
      $style_list = $this->_sizeStyle_mod->find(array(
            'conditions' => '1=1',
            'limit' => $page['limit'],
            //'order' => "$sort $order",
            'count' => true,
      ));
      $page['item_count'] = $this->_sizeStyle_mod->getCount();
      $this->_format_page($page);
      $this->assign('page_info', $page);
      $this->assign('style_list',$style_list);
      $this->display("sizeStyle.index.html");
    }
    //添加
    function add(){
      if(!IS_POST){
          $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
        $this->display("sizeStyle.from.html");
      }else{
        $data = array(
          'name' => $_POST["name"],
          'is_show' => $_POST['is_show'],
            'img'    => $_POST['images'],
        );
        $this->_sizeStyle_mod->add($data);
        $this->show_message('添加成功',
            'back_list',    'index.php?app=sizeStyle',
            'continue_add', 'index.php?app=sizeStyle&amp;act=add'
        );
      }

    }
    //修改
    function edit(){
        $id=intval($_GET['id']);
        if(empty($id)){
          $this->show_message("修改的信息不存在！");
          return false;
        }
        if(!IS_POST){
            $info = $this->_sizeStyle_mod->get(array(
                'conditions'=> 'id='.$id
            ));
            if(!$info){
              $this->show_message("修改的信息不存在！");
              return false;
            }
            $this->assign('info',$info);
            $this->display("sizeStyle.from.html");
      }else{
        $data = array(
          'name' => $_POST["name"],
          'is_show' => $_POST['is_show'],
           'img'    => $_POST['images'],
        );
        $this->_sizeStyle_mod->edit($id,$data);
        $this->show_message('修改成功',
            'back_list',    'index.php?app=sizeStyle'
        );
      }
    }
    //删除
    function drop(){
      $id = isset($_GET['id']) ? trim($_GET['id']) : '';
      if (!$id)
      {
          $this->show_warning('删除失败！');
          return;
      }
      if (!$this->_sizeStyle_mod->drop($id))
      {
          $this->show_warning('删除失败！');
          return;
      }
      $this->show_message('删除成功');
    }


}

?>
