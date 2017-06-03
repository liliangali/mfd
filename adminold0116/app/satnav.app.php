<?php

use Cyteam\Config\Config;
define('MAX_LAYER', 3);
/**
 *    主导航管理控制器
 *
 *    @author    tangsj
 *    @usage    none
 */
class SatnavApp extends BackendApp
{
    var $_satnav_mod;
    var $_satnavcat_mod;
    var $url_rule_values;
    var $url_rule_names;
    private $type = array(
        0=>'无',
        1 => '最新',
        2 => '最热',
    );

    function __construct()
    {
        $this->SatnavApp();
    }

    function SatnavApp()
    {
        parent::BackendApp();
        $this->_satnav_mod =& m('satnav');
        $this->_satnavcat_mod = & m('satnavcat');
        $this->if_show = array(
            0 => Lang::get('no'),
            1 => Lang::get('yes'),
        );
        $config=new Config();
        $setting = $config->get_config();
        $this->url_rule_values=i_array_column($setting['url_rule'], 'value');
        $this->url_rule_names=i_array_column($setting['url_rule'],'name');
        
    }

    /**
     *   导航首页
     *
     *    @author    tangsj
     *    @return    void
     */
    function index()
    {
        //
        $acategories = $this->_satnav_mod->get_list();
        $tree =& $this->_tree($acategories);
       
        /* 先根排序 */
        $sorted_acategories = array();
        $cate_ids = $tree->getChilds();
     
        foreach ($cate_ids as $id)
        {
            $parent_children_valid = $this->_satnav_mod->parent_children_valid($id);
            $sorted_acategories[] = array_merge($acategories[$id], array('layer' => $tree->getLayer($id), 'parent_children_valid'=>$parent_children_valid));
        }
        
        $lcon = array(
            0 => '无',
            1 => '最新',
            2 =>'最热',
        );
        
        foreach ($sorted_acategories as $k=>$v){
            $sorted_acategories[$k]['lcon'] = $lcon[$v['lcon']];
        }
        
        $this->assign('acategories', $sorted_acategories);
        
        /* 构造映射表（每个结点的父结点对应的行，从1开始） */
        $row = array(0 => 0);   // cate_id对应的row
        $map = array();         // parent_id对应的row
        foreach ($sorted_acategories as $key => $sat_nav)
        {
            $row[$sat_nav['satnav_id']] = $key + 1;
            $map[] = $row[$sat_nav['parent_id']];
        }
        $this->assign('map', ecm_json_encode($map));
        
        $this->assign('max_layer', MAX_LAYER);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css')
        );
        
        
     
      /*   $page   =   $this->_get_page(30);   
        $satnavs= $this->_satnav_mod->find(array(
        'limit'   => $page['limit'],
        'order'   => 'sort_order DESC,add_time DESC',
        'count'   => true   
        ));   
        $page['item_count']=$this->_satnav_mod->getCount();   */ 
 
     /*    foreach ($satnavs as $k=>$v){
            $satnavs[$k]['lcon'] = $lcon[$v['lcon']];
        } */
     
       /*  $this->_format_page($page);
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
     
        $this->assign('page_info', $page);   
        $this->assign('satnavs', $satnavs); */
        $this->display('satnav.index.html');
    }
     /**
     *    新增 导航
     *
     *    @author    tangsj
     *    @return    void
     */
    function add()
    {
       
        if (!IS_POST)
        {
           
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $this->assign('type', $this->type);
            $this->assign('url_rule_values',$this->url_rule_values);//url规则值
            $this->assign('url_rule_names',$this->url_rule_names);//url规则名
            $this->assign('parents', $this->_get_options()); //分类树
            $this->assign('build_upload', $this->_build_upload(array('belong' => BELONG_ARTICLE, 'item_id' => 0))); // 构建swfupload上传组件
            $this->display('satnav.form.html');
        }
        else
        {   
            
            $data = array();
            $data['name']      =   $_POST['satnavname'];
            $data['title']    =   $_POST['title'];
            $data['parent_id']    =   $_POST['parent_id'];
            $data['link']       =   $_POST['link'] == 'http://' ? '' : $_POST['link'];
            $data['if_show']    =   $_POST['if_show'];
            $data['sort_order'] =   $_POST['sort_order'];
            $data['lcon'] =   $_POST['lcon'];
            $data['alone'] =   $_POST['alone'];
            $data['add_time']   =   gmtime();
            
            if (!$satnav_id = $this->_satnav_mod->add($data))  //获取article_id
            {
                $this->show_warning($this->_satnav_mod->get_error());

                return;
            }
       
             $this->show_message('添加导航成功!',
                'back_list', 'index.php?app=satnav&amp;act=index&amp;title='.$_POST['title'].'&amp;cate_id='.$_POST['cate_id'],
                'continue_add', 'index.php?app=satnav&amp;act=add'
            ); 
        }
    }
    
    
    /* 异步去商品分类子元素 */
    function ajax_cate()
    {
        if(!isset($_GET['id']) || empty($_GET['id']))
        {
            echo ecm_json_encode(false);
            return;
        }
        $cate = $$this->_satnav_mod->get_list($_GET['id']);
        foreach ($cate as $key => $val)
        {
            $child = $this->_satnav_mod->get_list($val['satnav_id']);
            $lay = $this->_satnav_mod->get_layer($val['satnav_id']);
            if ($lay >= MAX_LAYER)
            {
                $cate[$key]['add_child'] = 0;
            }
            else
            {
                $cate[$key]['add_child'] = 1;
            }
            if (!$child || empty($child) )
            {
    
                $cate[$key]['switchs'] = 0;
            }
            else
            {
                $cate[$key]['switchs'] = 1;
            }
        }
        header("Content-Type:text/html;charset=" . CHARSET);
        echo ecm_json_encode(array_values($cate));
        return ;
    }
    
    

     /**
     *    编辑导航
     *
     *    @author    tangsj
     *    @return    void
     */
    function edit()
    {

        $satnav_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$satnav_id)
        {
            $this->show_warning('导航不存在');
            return;
        }
         if (!IS_POST)
        {
        
            $find_data     = $this->_satnav_mod->get($satnav_id);
          
            if (empty($find_data))
            {
                $this->show_warning('没有该导航');

                return;
            }
            $this->assign('type', $this->type);
            $this->assign("id", $satnav_id);
            $this->assign('find_data', $find_data);
            $this->assign('parents', $this->_get_options()); //分类树
            $this->assign('url_rule_values',$this->url_rule_values);//url规则值
            $this->assign('url_rule_names',$this->url_rule_names);//url规则名
            $this->display('satnav.form.html');
        }
        else
        {
            $data = array();
            $data['name']          =   $_POST['satnavname'];
            $data['title']          =   $_POST['title'];
            if (!empty($_POST['cate_id']))
            {
                $data['cate_id']        =   $_POST['cate_id'];
            }
            $data['link']           =   $_POST['link'] == 'http://' ? '' : $_POST['link'];
            $data['if_show']        =   $_POST['if_show'];
            $data['sort_order']     =   $_POST['sort_order'];
            $data['if_show']     =   $_POST['if_show'];
            $data['lcon']     =   $_POST['lcon'];
            $data['alone'] =   $_POST['alone'];
          
            

            $rows=$this->_satnav_mod->edit($satnav_id, $data);
            if ($this->_satnav_mod->has_error())
            {
                $this->show_warning($this->_satnav_mod->get_error());

                return;
            }
           
            $this->show_message('修改成功',
                '返回',    'index.php?app=satnav&amp;act=index&amp;title='.$_POST['title'].'&amp;cate_id='.$_POST['cate_id'],
                '重新编辑',    'index.php?app=satnav&amp;act=edit&amp;id=' . $satnav_id);
        }
    }
    

    /**
     *    添加下级
     *
     *    @author    tangsj
     *    @return    void
     */
    
    
    function add_satnavcat(){
        $satnav_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$satnav_id)
        {
            $this->show_warning('导航不存在');
            return;
        }
        
        if (!IS_POST)
        {
        
            $find_data     = $this->_satnav_mod->get($satnav_id);
            if (empty($find_data))
            {
                $this->show_warning('没有该导航');

                return;
            }
           
            $this->assign('type', $this->type);
            $this->assign("id", $satnav_id);
            $this->assign("parent_id", $satnav_id);
           
            $this->assign('parents', $this->_get_options()); //分类树
            $this->display('satnavcat.form.html');
        }else{

            $data = array();
            $data['name']      =   $_POST['satnavname'];
            $data['title']    =   $_POST['title'];
            $data['parent_id']    =   $_POST['parent_id'];
            $data['link']       =   $_POST['link'] == 'http://' ? '' : $_POST['link'];
            $data['if_show']    =   $_POST['if_show'];
            $data['sort_order'] =   $_POST['sort_order'];
            $data['lcon'] =   $_POST['lcon'];
            $data['alone'] =   $_POST['alone'];
            $data['add_time']   =   gmtime();
            
            if (!$satnav_id = $this->_satnav_mod->add($data))  //获取article_id
            {
                $this->show_warning($this->_satnav_mod->get_error());
            
                return;
            }
            $this->show_message('添加导航成功!',
                'back_list', 'index.php?app=satnav&amp;act=index&amp;title='.$_POST['title'].'&amp;cate_id='.$_POST['cate_id'],
                'continue_add', 'index.php?app=satnav&amp;act=add'
            );
            
        }
        
        
        
        
    
    }

     /**
     *    修改异步数据
     *    @author    tangsj
     *    @return    void
     */
    
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();

       if (in_array($column ,array('if_show', 'sort_order')))
       {
           $data[$column] = $value;
           $this->_satnav_mod->edit($id, $data);
           if(!$this->_satnav_mod->has_error())
           {
               echo ecm_json_encode(true);
           }
       }elseif ($column == 'name'){
           $data[$column] = $value;
           $this->_satnav_mod->edit($id, $data);
           if(!$this->_satnav_mod->has_error())
           {
               echo ecm_json_encode(true);
           }
      }elseif ($column == 'title'){
           $data[$column] = $value;
           $this->_satnav_mod->edit($id, $data);
           if(!$this->_satnav_mod->has_error())
           {
               echo ecm_json_encode(true);
           }
      }elseif ($column == 'link'){
           $data[$column] = $value;
           $this->_satnav_mod->edit($id, $data);
           if(!$this->_satnav_mod->has_error())
           {
               echo ecm_json_encode(true);
           }
      }elseif ($column == 'if_show'){
           $data[$column] = $value;
           $this->_satnav_mod->edit($id, $data);
           if(!$this->_satnav_mod->has_error())
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
   /**
    *    删除 导航
    *
    *    @author    tangsj
    *    @return    void
    */
   
   
    function drop()
    {
       
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
        
        if (!$id) {
            $this->show_warning('请选择要删除的数据!');
            return;
        }
        
        $this->_satnav_mod->drop($id);
        $this->show_message('删除成功!', 'back_list', 'index.php?app=satnav&act=index&page=' . $ret_page);
  
    }

        /**
     *    更新排序
     *
     *    @author    tangsj
     *    @return    void
     */
    
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

     /**
     *    构造并返回树
     *
     *    @author    tangsj
     *    @return    void
     */
    
    function &_tree($acategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($acategories, 'satnav_id', 'parent_id', 'name');
        return $tree;
    }
  
    /**
     *    返回 树形结构 数据
     *
     *    @author    tangsj
     *    @return    void
     */
    function _get_options()
    {
        $mod_acategory = &m('satnav');
        $acategorys = $mod_acategory->get_list();
        $tree =& $this->_tree($acategorys);
        return $tree->getOptions();
    }
    
    function check_sat_name()
    {
        $satnavname = $_GET['satnavname'];
        $id = $_GET['id'];
        if (!$serve_name)
        {
            echo 0;
        }
    
        $conditions = "name = '$satnavname' ";
        if ($id)
        {
            $conditions .= " AND satnav_id != $id";
        }
    
        if ($this->_satnav_mod->get($conditions))
        {
            echo 0;
        }
        else
        {
            echo 1;
        }
    
    }

}

?>