<?php
//ns add 尺码维护
class SizeTableApp extends BackendApp
{
    var $_sizeTable_mod;
    var $_sizeStyle_mod;
    function __construct()
    {
         parent::__construct();
         $this->_sizeTable_mod =& m('size_table');
         $this->_sizeStyle_mod =& m('size_style');
    }

    function index(){
      $page = $this->_get_page(30);
      $conditions = $this->_get_query_conditions(array(
          array(
              'field' => 'name',
              'name'  => 'name',
              'equal' => 'like',
          ),array(
              'field' =>'gender',
              'name'  => 'gender',
              'equal' => '=',
              //'handler'=> 'gmstr2time',
          ),
      ));
      $table_list = $this->_sizeTable_mod->find(array(
            'conditions' => '1=1'.$conditions,
            'limit' => $page['limit'],
            //'order' => "$sort $order",
            'count' => true,
      ));
      $page['item_count'] = $this->_sizeTable_mod->getCount();
      $this->_format_page($page);
      $this->assign('page_info', $page);
      $this->assign('table_list',$table_list);
      $this->display("sizeTable.index.html");
    }
    //添加
    function add(){
      if(!IS_POST){
        $style_list = $this->_sizeStyle_mod->find(array(
            'conditions' => 'is_show = 1'
        ));
        $this->assign('style_list',$style_list);
        $this->display("sizeTable.from.html");
      }else{
        $data = array(
          //'name' => $_POST["name"],
          'name_id' => $_POST['name_id'],
          'standard_code' => $_POST["standard_code"],
          'gender' => $_POST["gender"],
          'xw' => $_POST["xw"],
          'zyw' => $_POST["zyw"],
          'tw' => $_POST["tw"],
          'xf' => $_POST["xf"],
          'zjk' => $_POST["zjk"],
          'xc' => $_POST["xc"],
          'hyc' => $_POST["hyc"],
          'lheight' => $_POST["lheight"],
          'jxw' => $_POST["jxw"],
          'remarks' => $_POST['remarks'],
          'add_time' => time()
        );
        if($_POST['name_id']){
          $name_info = $this->_sizeStyle_mod->get(array(
              'conditions' => 'id='.$_POST['name_id']
          ));
          $data['name'] = $name_info['name'];
        }
        $this->_sizeTable_mod->add($data);
        $this->show_message('添加成功',
            'back_list',    'index.php?app=sizeTable',
            'continue_add', 'index.php?app=sizeTable&amp;act=add'
        );
      }

    }
    //修改
    function edit(){
        $id=intval($_GET['id']);
        $if_page = $_GET['if_page'];
        if(empty($id)){
          $this->show_message("修改的信息不存在！");
          return false;
        }
        if(!IS_POST){
            $size_info = $this->_sizeTable_mod->get(array(
                'conditions'=> 'id='.$id
            ));
            if(!$size_info){
              $this->show_message("修改的信息不存在！");
              return false;
            }
            $style_list = $this->_sizeStyle_mod->find(array(
                'conditions' => 'is_show = 1'
            ));
            $this->assign('style_list',$style_list);
            $this->assign('size_info',$size_info);
            $this->assign('if_page',$if_page);
            $this->display("sizeTable.from.html");
      }else{
        $data = array(
          // 'name' => $_POST["name"],
          'name_id' => $_POST['name_id'],
          'standard_code' => $_POST["standard_code"],
          'gender' => $_POST["gender"],
          'xw' => $_POST["xw"],
          'zyw' => $_POST["zyw"],
          'tw' => $_POST["tw"],
          'xf' => $_POST["xf"],
          'zjk' => $_POST["zjk"],
          'xc' => $_POST["xc"],
          'hyc' => $_POST["hyc"],
          'lheight' => $_POST["lheight"],
          'jxw' => $_POST["jxw"],
          'remarks' => $_POST['remarks'],
          //'add_time' => time()
        );
        if($_POST['name_id']){
          $name_info = $this->_sizeStyle_mod->get(array(
              'conditions' => 'id='.$_POST['name_id']
          ));
          $data['name'] = $name_info['name'];
        }
        $this->_sizeTable_mod->edit($id,$data);
        $this->show_message('修改成功',
            'back_list',    'index.php?app=sizeTable&page='.$_POST['if_page']
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
      if (!$this->_sizeTable_mod->drop($id))
      {
          $this->show_warning('删除失败！');
          return;
      }
      $this->show_message('删除成功');
    }


}

?>
