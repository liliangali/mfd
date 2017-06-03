<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */


/**
 |--------------------------------------------------------------------------
 | 属性diy
 |--------------------------------------------------------------------------
 |
 |
 | @author 小五 <xiao5.china@gmail.com>
 |
 */
    
class FdiyApp extends MallbaseApp
{
    function __construct(){
        parent::__construct();
        header("Content-Type:text/html;charset=" . CHARSET);
        $this->_fdiy_management_mod =& m('fdiy_management');
        $this->_mod_fdict = &m("fdiy_dict");
        $this->_mod_fcrossrule = &m("fdiy_crossrule");
        $this->_mod_fbcategory = &m("fbcategory");
        /* 读取后台树形配置 层级id只能手动定义 */
        $this->_conflictCate = ['0001'=>2,'0003'=>3,'0004'=>4,'0005'=>346,'0006'=>340,'0007'=>347,'0018'=>781];

    }
 
    /**
     * http://local.mfd.com/fdiy-1-3.html
     * 属性diy入口
     * @param 1            品类
     */
    function index()
    {
		$user = $this->visitor->get();
		$this->assign('mid', empty($user) ? 0 : $user['user_id']);

    	$args = $this->get_params();
    	$cid = empty($args[0]) ? '1' : $args[0]; //配置一级id
    	
    	
    	/* 取得diy配置 */
    	$acategories = $this->_fdiy_management_mod->get_list($cid);
    	
    	if (empty($acategories)){
    	    $this->show_warning('！该品类下还没有关联定制属性');
    		return;
    	}
    	
    	$sid = empty($args[1]) ? current($acategories)['cate_id'] : $args[1]; //配置二级id
    	$dids= array_unique(explode(',', $acategories[$sid]['did']));
    	
    	if (empty($dids)){
    	    $this->show_warning('！该品类下还没有录入定制属性');
    	    return;
    	}

    	
    	$plist = $this->_mod_fbcategory->get_descendant_ids($dids);
    	unset($plist[0]);
    	
    	
    	/* 所有工艺 memcache缓存 */
    	$_data = $this->_mod_fbcategory->_get_data(db_create_in($plist,'cate_id'),1);
    	$_list =  $this->_mod_fbcategory->deep_tree($_data);	//生成嵌套格式的树形数组

    	//按照后台关联顺序排序
        $tmp = [];
        foreach ($dids as $key){
            $tmp[] = $_list[$key];
        }

		/* 取得冲突 */
		$_clist = $this->_mod_fcrossrule->find(array(
				'conditions' => db_create_in($dids,'cid'),
				'order' => "id DESC"
		));
                
                
        // 系统后台diy相关配置
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); 
        
        $aprice = isset($setting["diy_aprice"]) ? $setting["diy_aprice"] : 1.5;     //功能料均价
        $ratio = isset($setting["diy_ratio"]) ? $setting["diy_ratio"] : 25;         //单个功能料占比 /100 
        $maxnum = isset($setting["diy_maxnum"]) ? $setting["diy_maxnum"] : 3;       //功能料种数上限 
//print_exit($tmp);
    	$this->assign('cid', $cid);
    	$this->assign('sid', $sid);
    	$this->assign('aprice', $aprice);
    	$this->assign('ratio', $ratio);
    	$this->assign('maxnum', $maxnum);
    	$this->assign('basedata', json_encode($tmp));
    	$this->assign('rule', json_encode(array_values($_clist)));
         $this->display('fdiy/fdiy.dict.html');
    }

	function __destruct()
	{
		// TODO: Implement __destruct() method.
	}

	/**
     * 获取面料信息        品类｜面料code
     * http://local.mfd.com/fdiy-agf-0003-DSA605A.html
     */
    function agf()
    {
    	$res = ['err'=>1,'msg'=>'请选择品类!'];
    	$args = $this->get_params();
    	$cid = empty($args[0]) ? '0003' : $args[0];
    	$fcode = empty($args[1]) ? 'DSA605A' : $args[1];
    	if (!$cid || !$fcode){
    		echo json_encode($res);
    		exit();
    	}
    	/* 获取面料详细信息 供diy数据展示 */
    	$finfo = $this->_mod_fdict->_get_finfo($fcode,$cid);
    	echo json_encode($finfo);
    }
    
    /**
     * 获取客户指定数据信息        品类｜工艺code
     * http://local.mfd.com/fdiy-agf-0003-0638.html
     *
     */
    function agc(){
    	$res = ['err'=>1,'msg'=>'请选择品类!'];
    	$args = $this->get_params();
    	$cid = empty($args[0]) ? '0003' : $args[0];
    	$code = empty($args[1]) ? '0638' : $args[1];
    	if (!$cid || !$code){
    		echo json_encode($res);
    		exit();
    	}
    	$_fdiy_comm = &m('fdiy_dcomm');
    	$_clist = $_fdiy_comm->find(array(
//    			'conditions' => "cid = '".$cid."' and ecode= '".$code."'",
    			'conditions' => "ecode= '".$code."'",
    			'order' => "id DESC"
    	));
    	echo json_encode(array_values($_clist));
    }
    
    /* 构造并返回树 */
    function &_tree($acategories)
    {
    	import('tree.lib');
    	$tree = new Tree();
    	$tree->setTree($acategories, 'cate_id', 'parent_id', 'cate_name');
    	return $tree;
    }


}

?>