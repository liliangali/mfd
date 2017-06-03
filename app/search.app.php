<?php
class SearchApp extends MallbaseApp
{
    var $sto_mod;
    var $ctm_mod;
    var $dis_mod;
    var $fab_mod;
    var $dmd_mod;
    var $cds;
    var $titles;
    function __construct()
    {
      
      parent::__construct();
      $this->ctm_mod =& m('customs');
      $this->sto_mod =& m('store');
      $this->dis_mod =& m('dissertation');
      $this->fab_mod =& m('part');
      $this->dmd_mod =& m('demand');
      
      $this->cds = $this->conditions();
      $ctmNum = $this->ctm_mod->_count($this->cds['ctm']);
      $stoNum = $this->sto_mod->_count($this->cds['sto']);
      $disNum = $this->dis_mod->_count($this->cds['dis']);
      $fabNum = $this->fab_mod->_count($this->cds['fab']);
      $dmdNum = $this->dmd_mod->_count($this->cds['dmd']);
      
      $keyword = htmlspecialchars($_GET['keyword']);
      $this->titles = array(
          'sto' => array(
                'name' => '裁缝',
                'url'  => "search-index-sto.html?keyword={$keyword}",
                'num'  => $stoNum,
          ),
          'ctm' => array(
              'name' => '样衣',
              'url'  => "search-index-ctm.html?keyword={$keyword}",
              'num'  => $ctmNum,
          ),
          'dis' => array(
              'name' => '套装',
              'url'  => "search-index-dis.html?keyword={$keyword}",
              'num'  => $disNum,
          ),
          'fab' => array(
              'name' => '面料',
              'url'  => "search-index-fab.html?keyword={$keyword}",
              'num'  => $fabNum,
          ),
          'dmd' => array(
              'name' => '需求',
              'url'  => "search-index-dmd.html?keyword={$keyword}",
              'num'  => $dmdNum,
          ),
      );

      $this->assign("url", "?keyword={$keyword}");
      $this->assign("keyword", $keyword);

    }
    
    function index(){
        
        $arg = $this->get_params();
        foreach($this->titles as $k => $v){
            if($v['num'] == 0){
                unset($this->titles[$k]);
            }
        }
        
        if(empty($this->titles)){
            $this->display("search.warning.html");
            return;
        }
        
        $keys = array_keys($this->titles);
        $this->assign("titles", $this->titles);
       
        $action = isset($arg[0]) ? $arg[0] : $keys[0];
        if(!in_array($action,$keys)) $action = $keys[0];
        $this->assign("cur", $action);
        $this->$action();
    }
    
    function sto(){
        $arg = $this->get_params();
        $arg[0]='sto';
        $params = array('args'=>$arg,'pagekey'=>1);
        $page = $this->_get_page(16, $params);
        $list = $this->sto_mod->find(array(
            'conditions' => $this->cds['sto'],
            'count' => true,
            'limit' => $page['limit'],
            'fields' => '*',
            'order' => "add_time DESC",
        ));
        
        $page['item_count'] = $this->sto_mod->getCount();
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        foreach($list as $k=>$val){
            $list[$k]['member_logo'] = $objAvatar->avatar_show($val['store_id'],'big',1);
        }
        $this->_format_page($page,7 ,$params);
        $this->assign('page_info', $page);
        $this->assign('list', $list);
        $this->display("search.sto.html");
    }
    
    function dis(){
        $arg = $this->get_params();
        $arg[0]='dis';
        $params = array('args'=>$arg,'pagekey'=>1);
        $page = $this->_get_page(15, $params);
        $dislist = $this->dis_mod->find(array(
            'conditions' => $this->cds['dis'],
            'count' => true,
            'limit' => $page['limit'],
            'fields' => '*',
            'order' => "sort_order DESC",
        ));
        
        $disids = array();
        
        foreach($dislist as $key => $val){
            $cstids[] = $val['id'];
        }
        
        $links_mod = &m("links");
        $links = $links_mod->find(array(
            'conditions' => "is_active=1 AND d_id ".db_create_in($cstids),
            'order' => "lorder ASC, c_id DESC",
            'join' => 'has_custom',
            'fields' => "d_id,cst_dis_image,cst_image",
        ));
        
        
        
        $append = array();
        foreach($links as $key => $val){
            $append[$val['d_id']][$val['id']] = $val;
        }
        
        $region_mod = &m('region');
        $regions = $region_mod->get_list(0);
        if ($regions)
        {
            $tmp  = array();
            foreach ($regions as $key => $value)
            {
                $tmp[$key] = $value['region_name'];
            }
            $regions = $tmp;
        }
        
        $dmd_item = &m('demanditem');
        
        $items = $dmd_item->find(array(
            'conditions' => 'cate=4',
        ));
        
        $opts = array();
        foreach((array)$items as $key => $val){
            $opts[$val['id']] = $val['name'];
        }
        $page['item_count'] = $this->dis_mod->getCount();
        $this->assign("regions", $regions);
        $this->assign("opts", $opts);
        $this->assign('dislist', $dislist);
        $this->assign('append', $append);

        $this->_format_page($page,7 ,$params);
        $this->assign('page_info', $page);
        $this->display("search.dis.html");
    }
    
    function ctm(){
        $arg = $this->get_params();
        $arg[0]='ctm';
        $params = array('args'=>$arg,'pagekey'=>1);
        $page = $this->_get_page(15, $params);
        
        $list = $this->ctm_mod->find(array(
            'conditions' => $this->cds['ctm'],
            'count' => true,
            'limit' => $page['limit'],
            'fields' => '*',
            'order' => "cst_id DESC",
        ));
        
        $page['item_count'] = $this->ctm_mod->getCount();
        $this->_format_page($page,7 ,$params);
        $this->assign('page_info', $page);
        
        $cstids = array();
        foreach((array)$list as $key => $val){
            $cstids[] = $val['cst_id'];
        }
        $gallery_mod = &m("gallery");
        $imgs = $gallery_mod->find(array(
            'conditions' => "cst_id ".db_create_in($cstids),
        ));
        
        $append = array();
        foreach($imgs as $key => $val){
            $append[$val['cst_id']][] =$val;
        }
        
        $fab_mod = &m("customsparts");
        
        $fabs = $fab_mod->find(array(
            'conditions' => "fabric_id != 0 AND cst_id ".db_create_in($cstids),
            'join' => 'has_parts',
        ));
        
        $fabsArray = array();
        $fabid = array();
        foreach($fabs as $key => $val){
            $fabsArray[$val['cst_id']] = $val;
            $fabid[] = $val['part_id'];
        }
        
        $attr_mod = &m("partattr");
        
        $eles = $attr_mod->find(array(
            'conditions' => 'attr_id = 25 AND part_id' .db_create_in($fabid),
        ));
        
        $eleArray = array();
        foreach($eles as $key => $val){
            $eleArray[$val['part_id']] = $val;
        }
        
        
        foreach($list as $key => $val){
            $list[$key]['fabric_sn'] = @$fabsArray[$key]['goods_sn'];
            $list[$key]['ele']       = @$eleArray[$fabsArray[$key]['part_id']]['attr_value'];
        }
        
        $this->assign("append", $append);
        $this->assign('list', $list);
        $this->display("search.ctm.html");
    }
    
    
    function fab(){
        $arg = $this->get_params();
        $arg[0]='fab';
        $params = array('args'=>$arg,'pagekey'=>1);
        $page = $this->_get_page(20, $params);

        $list = $this->fab_mod->find(array(
            'conditions' => $this->cds['fab'],
            'count' => true,
            'limit' => $page['limit'],
            'fields' => '*',
            'order' => "part_id DESC",
        ));
        
        $page['item_count'] = $this->fab_mod->getCount();
        $this->_format_page($page,7 ,$params);
        $this->assign('page_info', $page);
        $this->assign('list', $list);
        $this->display("search.fab.html");
    }
    
    function dmd(){
        $arg = $this->get_params();
        $arg[0]='fab';
        $params = array('args'=>$arg,'pagekey'=>1);
        $page = $this->_get_page(20, $params);
        
        $list = $this->dmd_mod->find(array(
            'conditions' => $this->cds['dmd'],
            'count' => true,
            'limit' => $page['limit'],
            'fields' => '*',
            'order' => "md_id DESC",
        ));
        //会员头像基类
        require(ROOT_PATH . '/includes/avatar.class.php');
        $objAvatar = new Avatar();
        
        foreach($list as &$row){
            $row['params'] = unserialize($row['params']);
            $row['avatar'] = $avatar = $objAvatar->avatar_show($row['user_id'],'middle');
        }
        
        $page['item_count'] = $this->dmd_mod->getCount();
        $this->_format_page($page,7 ,$params);
        $this->assign('page_info', $page);
        $this->assign('list', $list);
        $this->display("search.dmd.html");
    }
    
    private function conditions(){
        $keyword = trim(htmlspecialchars($_GET['keyword']));
        if(empty($keyword)){
            $this->show_warning('请填写要搜索的关键字!');
            return;
        }
        $this->searchHistory($keyword);
        $cds = array(
            'sto' => "store_name LIKE '%{$keyword}%' AND state=1",
            'ctm' => "(cst_name LIKE '%{$keyword}%' || cst_description LIKE '%{$keyword}%') AND is_active=1",
            'dis' => "title LIKE '%{$keyword}%' || brief LIKE '%{$keyword}%'",
            'fab' => "fabric_id !=0 AND state=1 AND is_on_sale=1 AND part_name LIKE '%{$keyword}%'", 
            'dmd' => "(md_title LIKE '%{$keyword}%' || remark LIKE '%{$keyword}%') AND status = 2 AND region_code = '{$_COOKIE['cityCode']}'"
        );
        
        return $cds;
    }
    
    function get_params(){
        
        $arg = parent::get_params();
        
        $page = intval($_POST['page']);
        if($page){
            $arg[1] = $page;
        }
        return $arg;
    }
    
    private function searchHistory($keyword){
        $sh = @unserialize(stripslashes($_COOKIE['sh']));
        if(!$sh){
            $sh = array();
        }
        
        if(!in_array($keyword, $sh)){
            $sh[] = $keyword;
        }
        
        if(count($sh) > 6){
            array_shift($sh);
        }
        sort($sh);
        setcookie("sh", serialize($sh), gmtime()+3600*24*30);
    }
}

?>