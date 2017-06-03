<?php/** * Cotte licence * * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn) * @license  http://license.cotte.cn/ cotte License */class DictCApp extends BackendApp{	private $_mod_fcrossrule;	// private $_mod_fdict;    var $sw = 80;    var $sh = 80;    var $mw = 300;    var $mh = 300;    var $coefficient = 5.5;    function __construct()    {         $this->_mod_fcrossrule = &m("fdiy_crossrule");         // $this->_mod_fdict = &m("fdiy_dict");         $this->_fbcategory_mod =& m('fbcategory');                  $this->style = array(             "1" => "正装",             "2" => "休闲",             "3" => "礼服"         );                  // $this->init  = $this->_mod_fdict->init;         parent::__construct();    }    function index()    {        $conditions = ' 1 ';        $query['cid']  = isset($_GET['cid']) ? trim($_GET['cid']) : '';        if($query["cid"]){            $conditions .= " AND cid = '{$query["cid"]}'";        }        $page = $this->_get_page(30);        setcookie('curr_page',$page["curr_page"]);        $_list = $this->_mod_fcrossrule->find(array(            'conditions' => $conditions,            'limit' => $page['limit'],            'count' => true,            'order' => "id DESC"        ));        /* 所有工艺 memcache缓存 */        $_data = $this->_fbcategory_mod->_get_data();        foreach ((array)$_list as $k=>$v){        	foreach ((array)explode(',',$v['rules']) as $r){        		if (isset($_data[$r])){        			$_list[$k]['rn'] .= $_data[$r]['cate_name'].'<br/>';        		}        	}        	foreach ((array)explode(',',$v['collection']) as $c){        		if (isset($_data[$c])){        			$_list[$k]['cn'] .= $_data[$c]['cate_name']."<br/>";        		}        	}        }        $page['item_count'] = $this->_mod_fcrossrule->getCount();        $this->_format_page($page);        $this->assign('page_info', $page);        $this->assign('query', $query);        $this->assign("_list", $_list);        // $this->assign("cat_list",   $this->init);        $this->display('fdiy.crossrile.index.html');    }        function add(){                if(IS_POST){           if (empty($_POST['item']) || !is_array($_POST['item'])){               $this->show_warning('必须填写主属性！');               return;           }            $fcData = array(                'cid'     =>trim($_POST['category']),                'collection'    => trim(implode(array_keys($_POST['item']), ',')),                'rules'    => trim(($_POST['linkid']))            );                        if ($this->_mod_fcrossrule->add($fcData)){            	$this->show_message("互斥属性发布成功",'back_list', 'index.php?app=dictC');            }        }else{            $this->import_resource(array(                'script' => 'jquery.plugins/jquery.validate.js'            ));            $this->assign("cat_list",   $this->_fbcategory_mod->get_children(0));            $this->display("fdiy.crossrile.info.html");        }    }    function showItems(){                $ids   = $_GET['ids'];        if(!$_COOKIE['fdiys']) {            setcookie("fdiys", $ids);        }else{            $ids = $_COOKIE['fdiys'];        }                $name    = trim($_GET['name']);        $to_site = trim($_GET['to_site']);        $condition = " if_show = 1 and parent_id > 0 ";        if($name){            $condition .= " AND cate_name LIKE '%{$name}%'";        }                if($to_site){            $condition .= " AND to_site = '{$to_site}'";        }           $page = $this->_get_page(10);        $list = $this->_fbcategory_mod->find(array(            "conditions" => $condition,            'limit'  => $page['limit'],            'count'  => true,        ));        $ids = explode(",", $ids);        foreach($list as $key => $val){            $list[$key]['checked'] = in_array($val["cate_id"], $ids) ? 1 : 0;        }        $page['item_count'] = $this->_fbcategory_mod->getCount();        $this->_format_page($page);        $this->assign("list", $list);        $this->assign("to_site", $this->to_site);        $this->assign('page_info', $page);        $this->display("fdiy.crossrile.items.html");    }        function loadItems(){        $ids = trim($_GET['ids']);        $did = intval($_GET["did"]);        if(!$ids){            $this->json_error('非法操作');            return;        }        $list = $this->_fbcategory_mod->find(array(            "conditions" => "cate_id IN ($ids)",            'fields'     => "cate_id as id,cate_name as name"        ));            $retArr = array();            $lorder = array();        foreach((array)$list as $key => $val){            $list[$key]['lorder'] = isset($lorder[$val['id']]) ? $lorder[$val["id"]] : 0;        }        foreach((array)$list as $key => $val){            $retArr[] = $val;        }            $this->json_result($retArr);        die();    }        function loadItem(){        $pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;        if (isset($_GET['node']) && $_GET['node'] ==1){            if (empty($pid)) die($this->json_result(array()));        }        $this->_fbcategory_mod =& m('fbcategory');        $list = $this->_fbcategory_mod->find(array(            "conditions" => "parent_id = '".$pid."' AND if_show =1 ",            //'limit'      => "10",            'fields'     => "cate_id,cate_name,parent_id"        ));                        foreach($list as $key => $val){                    $data = array(                "name" => $val["cate_name"],                "id"   => $val["cate_id"],                'code' => $val["cate_id"],                'pid'  => $val["parent_id"],                'sid'  => $val["cate_id"],            );            $ret[] = $data;        }        die($this->json_result($ret));    }            function edit(){        if(IS_POST){                        if (empty($_POST['item']) || !is_array($_POST['item'])){                $this->show_warning('必须填写主属性！');                return;            }                        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);            $fcData = array(                'cid'     =>trim($_POST['category']),                'collection'    => trim(implode(array_keys($_POST['item']), ',')),                'rules'    => trim(($_POST['linkid']))            );            if ($this->_mod_fcrossrule->edit($id,$fcData)){                $this->show_message("互斥属性修改成功",'back_list', 'index.php?app=dictC&page='.$_COOKIE['curr_page']);            }        }else{            $this->import_resource(array(                'script' => 'jquery.plugins/jquery.validate.js'            ));            $id = intval($_GET["id"]);            $data = $this->_mod_fcrossrule->get($id);            $data['category'] = $data['cid'];            $items = $this->_fbcategory_mod->find(array(                'conditions' => db_create_in($data['collection'],'cate_id'),            ));            $collection = $this->_fbcategory_mod->find(array(                'conditions' => db_create_in($data['rules'],'cate_id'),            ));            $this->assign("data", $data);            $this->assign("items", $items);            $this->assign("crossrile", $collection);            $this->assign("cat_list",   $this->_fbcategory_mod->get_children(0));            $this->display("fdiy.crossrile.info.html");        }    }        function formatData($sData, $adData){        $upData = array(); //设置默认        $dnData = array(); //取消默认        $deData = array();        $asData = array();        foreach((array)$sData as $key => $val){            if(isset($adData[$val["item_id"]])){                $is_defalut = $adData[$val['item_id']]['is_default'];                if($val['is_default'] < $is_defalut){                    $upData[] = $val["id"];                }                        if($val["is_default"] > $is_defalut){                    $dnData[] = $val["id"];                }                                if($val["assign"] != $adData[$val["item_id"]]["assign"]){                    $asData[] = array(                        'id'     => $val["id"],                        'assign' => $adData[$val["item_id"]]["assign"],                    );                }                                unset($adData[$val["item_id"]]);            }else{                $deData[] = $val["id"];            }        }             $newArr = array();       foreach($adData as $key => $val){           $newArr[] = $val;       }       return array(           'adData' => $newArr,           'upData' => $upData,           'dnData' => $dnData,           'deData' => $deData,           'asData' => $asData,       );    }            /* 取得可以作为上级的商品分类数据 */    function _get_options($except = NULL)    {        $_cate_mod = & bm('gcategory', array('_store_id' => 0));                $gcategories = $_cate_mod->get_list();        $tree =& $this->_tree($gcategories);            return $tree->getOptions(MAX_LAYER - 1, 0, $except);    }        /* 构造并返回树 */    function &_tree($gcategories)    {        import('tree.lib');        $tree = new Tree();        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');        return $tree;    }        function drop(){        $id = intval($_GET['id']);        $data = $this->_mod_fcrossrule->get($id);                if(empty($data)){            $this->show_message("请编辑冲突规则,再执行删除操作!");            return false;        }        $this->_mod_fcrossrule->drop($id);        $this->show_message("互斥属性删除成功!");    }    }