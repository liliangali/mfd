<?php
/**
 * 消费者需求后台管理控制器
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: demand.app.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 redcollar
 */
class DemandApp extends BackendApp
{
    var $_mod_demanditem;
    var $_mod_demand;
    public $_types;
    public $_status;
    public $item_cate;
    public $item_catte;
    function __construct()
    {
        $this->DemandApp();
    }

    function DemandApp()
    {
        parent::BackendApp();
        $this->_mod_demanditem = & m('demanditem');
        $this->_mod_demand = & m('demand');
        $this->_types = $this->_mod_demand->_types();
        $this->_status = $this->_mod_demand->_status();
        $this->item_cate = $this->_mod_demanditem->_item_cates();
        $this->item_catte = array(
        		1 => '主题风格',
        		2 => '品类',
        		3 => '面料',
        		4 => '定制预算',
        		5 => '尺寸号码',
        );
    }

    function index()
    {
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'md_sn',
                'equal' => '=',
            ),
        ));
        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order, array('asc', 'desc'))) {
                $sort = 'md_id';
                $order = 'desc';
            }
        } else {
            $sort = 'md_id';
            $order = 'desc';
        }
        $page = $this->_get_page(15);
        $list = $this->_mod_demand->find(array(
            'conditions' => "1 = 1 " . $conditions,
            'count' => true,
            'order' => "$sort $order",
            'limit' => $page['limit'],
        ));

        $page['item_count'] = $this->_mod_demand->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('list', $list);
        $this->assign('cate', $this->item_cate);
        $this->display('demand/index.html');
    }

    function edit()
    { 
        
        if (!IS_POST) {
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style' => 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            if (!$id) {
                $this->show_message('参数错误!', 'back_list', 'index.php?app=demand&act=index');
                return;
            }
            $data = $this->_mod_demand->get($id);
            if (empty($data)) {
                $this->show_message('参数错误!', 'back_list', 'index.php?app=demand&act=index');
                return;
            }

           $itemss = $this->_mod_demanditem->find(array(
                'conditions' => '1 = 1 ',
        ));
        
        foreach ($itemss as $key=>$item){
            $items[$item['cate']]['list'][$key] = $item;
        }
     
      
            //$this->_mod_demanditem
            $data['params'] = unserialize($data['params']);
foreach($data['params'] as $key=>$val){
	//$params['$key']=$val[''];
}
            
            $this->assign('items', $items);
            $this->assign('status', $this->_status);
            $this->assign('types', $this->_types);
            $this->assign('data', $data);
            $this->assign('cates',$this->item_catte);
            $this->display('demand/edit.html');
        } else {
        	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        	$status = $_POST['status'] = isset($_POST['status']) ? intval($_POST['status']) : 2;
        	$data = array(
        			'md_sn'  => intval($_POST['md_sn']),
        			'md_title' => trim($_POST['md_title']),
        			'md_type'  => 'normal',
        			'status'   => $status,
        			'region_name'   => trim($_POST['region_name']),
        			'uname'    => trim($_POST['uname']),
        			'mobile'  => $_POST['mobile'],
        			
        	);
        	if($_POST['add_time']){
        		$data1=array(
        		'add_time'  => strtotime($_POST['add_time']),
        		);
        		
        	}
        $this->_mod_demand->edit($id,$data);
        $this->_mod_demand->edit($id,$data1);
            $status = trim($_POST['status']);
            $id = intval($_POST['id']);
            if (!$id) {
                $this->show_warning('操作失败!');
                return;
            }
            $data = array(
                'status' => $status,
            );

            $res = $this->_mod_demand->edit($id, $data);

            if (!$res) {
                $this->show_warning('操作失败!');
                return;
            } else {
                $this->show_message('操作成功!',
                    'back_list', 'index.php?app=demand&act=index'
                );
            }
        }
    }
    
    function drop(){
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
        if (!$id) {
            $this->show_warning('请选择要删除的数据!');
            return;
        }
        
        $this->_mod_demand->drop(db_create_in($id, 'md_id'));
        $this->show_message('删除成功!', 'back_list', 'index.php?app=demand&act=index&page=' . $ret_page);
    }

    function dropItem()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
        if (!$id) {
            $this->show_warning('请选择要删除的数据!');
            return;
        }

        $this->_mod_demanditem->drop(db_create_in($id, 'id'));
        $this->show_message('删除成功!', 'back_list', 'index.php?app=demand&act=items&page=' . $ret_page);
    }

    ////****************************************////
    /* 需求项 */
    function items()
    {

        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'cate',
                'equal' => '=',
            ),
            array(
                'field' => 'name',
                'equal' => 'like',
            ),
        ));
        //update order
        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order, array('asc', 'desc'))) {
                $sort = 'id';
                $order = 'desc';
            }
        } else {
            $sort = 'id';
            $order = 'desc';
        }

        $page = $this->_get_page(15);
        $list = $this->_mod_demanditem->find(array(
            'conditions' => "1 = 1 " . $conditions,
            'count' => true,
            'order' => "$sort $order",
            'limit' => $page['limit'],
        ));

        $page['item_count'] = $this->_mod_demanditem->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('list', $list);
        $this->assign('cate', $this->item_cate);
        $this->display('demand/item/index.html');

    }

    /* 添加需求项 */
    function addItem()
    {
        $this->assign('action', 'add');
        $this->_editorItem();
    }

    /* 编辑需求项 */
    function editItem()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $data = $this->_mod_demanditem->get($id);
        if (!empty($data)) {
            $this->assign('data', $data);
            $this->assign('action', 'edit');
        } else {
            $this->assign('action', 'add');
        }
        $this->_editorItem();
    }

    //_editorItem
    function _editorItem()
    {
        if (!IS_POST) {
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style' => 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            $this->assign('cate', $this->item_cate);
            $this->display('demand/item/add.html');
        } else {
            $name = trim($_POST['name']);
            $cate = trim($_POST['cate']);
            $rank = intval(trim($_POST['rank']));
            $id = intval($_POST['id']);

            $data = array(
                'name' => $name,
                'cate' => $cate,
                'rank' => $rank,
            );

            if ($id === 0) {
                $res = $this->_mod_demanditem->add($data);
            } else {
                $res = $this->_mod_demanditem->edit($_POST['id'], $data);
            }

            if (!$res) {
                $this->show_warning('操作失败!');
                return;
            } else {
                $this->show_message('操作成功!',
                    //'continue_add', 'index.php?app=customs&amp;act=add',
                    'back_list', 'index.php?app=demand&act=items'
                );
            }
        }
    }

    //设置默认项
    function setdft()
    {
        if (!IS_POST) {
            $this->assign('cate', $this->item_cate);
            $this->display('demand/item/setdft.html');
        } else {
            $id = $_POST['item'];
            $cate = $_POST['cate'];
            $r1 = $this->_mod_demanditem->edit("cate = {$cate}", 'dft = 0');
            if ($r1) {
                $r2 = $this->_mod_demanditem->edit($id, 'dft = 1');
                if ($r2) {
                    $this->show_message('操作成功!',
                        'back_list', 'index.php?app=demand&act=items'
                    );
                    return;
                }
            }
            $this->show_warning('操作失败!');
            return;
        }
    }

    function ajaxdft()
    {
        $cate = $_GET['cate'];
        $items = $this->_mod_demanditem->find(array(
            'conditions' => " cate = {$cate}",
        ));
        foreach ($items as $item) {
            if ($item['dft']) {
                $con .= "<option value='{$item['id']}' selected >{$item['name']}</option>";
            } else {
                $con .= "<option value='{$item['id']}'>{$item['name']}</option>";
            }
        }
        echo $con;
    }


    /* 添加了价格排序,而之前的没有,用此方法刷数据，用完即费 */
    function upPriceRank()
    {
        $all = $this->_mod_demand->find();
        echo '<pre>';
        print_r($all);
        exit;
        foreach ($all as $row) {
            $par = unserialize($row['params']);
            //explode('~', $par[4]['val']);//可以通过ID修复，就不要直接操作字符了。//不行啊，之前有套装发布的，那是后存的id都是-1
            $p = explode('~', $par[4]['val']);
            if (count($p) == 2) {
                $s = $p[0] + 1;
            } else {
                $f = substr($p[0], 0, 1);
                if ($f == 1) {
                    $s = 999;
                } elseif ($f == 5) {
                    $s = 5001;
                }
            }
            $this->_mod_demand->edit("md_id = '{$row['md_id']}'", array('price_rank' => $s));
        }
    }

}


