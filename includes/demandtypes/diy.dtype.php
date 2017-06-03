<?php
/**
 *   定制商品过来需求
 */
class DiyDemand extends BaseDemand
{
    public $_type;
    function __construct()
    {
        parent::__construct();
        $this->DiyDemand();
    }
    function DiyDemand(){
        $this->_type = 'diy';
    }
    
    function _check_param($arg = array()){
        $gid = intval($arg['post']['id']);
        if(!$gid) return 0;
        $mCst = &m('customs');
        $goods = $mCst->get($gid);
        if(empty($goods)) return 0;

        $arr = @explode('|||', $arg['post']['code']);
        if(count($arr) >= 2 ){
            $fabric = @explode(':', $arr[0]);
            if($fabric[0] == 3){
                $goods['diyCode']['fabric'] = $fabric[1];
            }
            $goods['diyCode']['craft'] =  $arr[1];
        }else{
            $fabric = @explode(':', $arr[0]);
            if($fabric[0] == 3){
                $goods['diyCode']['fabric'] = $fabric[1];
            }else{
                $goods['diyCode']['craft'] =  $arr[0];
            }
        }
        
        return $goods;
    }
    
    function _get_item_data($arg = array()){
        
        $data = parent::_get_item_data();
        
        if($arg['diyCode']['fabric'] != ''){

            $ml['list'][-1]['id']   = -3;
            $ml['list'][-1]['name'] = $arg['diyCode']['fabric'];
            $ml['list'][-1]['cate'] = 3;
            $data[3] = $ml;
        }

        $mCate = &m('gcategory');
        $cate  = $mCate->get($arg['cst_cate']);
        //品类
        $pl['list'][-1]['id']   = -$arg['cst_cate'];
        $pl['list'][-1]['name'] = $cate['cate_name'];
        $pl['list'][-1]['cate'] = 2;
        $data[2] = $pl;
        return $data;
    }
    
    function add($post)
    {
        $params = $this->_get_params($post['params'],$post['fabric']);
        $post['params']  = $params;
        $post['md_type'] = $this->_type;
        $post['md_type_id']  = $post['type_id'];
        $res = parent::add($post);
        return $res;
    }
    
    function _get_params($post,$fabric=''){
        $items = $this->_mod_demanditem->find(array(
                'conditions' => 'id'.db_create_in($post),
        ));
        
        foreach ($items as $row){
            $res[$row['cate']]['cat'] = $this->item_cate[$row['cate']];
            $res[$row['cate']]['val'] = $row['name'];
            $res[$row['cate']]['id']  = $row['id'];
        }
        
       
        $mCate = &m('gcategory');
        $cate  = $mCate->get(abs($post[2]));
        
        $res[2]['cat'] = $this->item_cate[2];
        $res[2]['val'] = $cate['cate_name'];
        $res[2]['id']  = -1;
        
        $res[3]['cat'] = $this->item_cate[3];
        $res[3]['val'] = $fabric;
        $res[3]['id']  = -1;
        asort($res);
        return serialize($res);
    }
    
    function _format_info($info = array()){

        $mGoods = &m('customs');
        $goods = $mGoods->get($info['md_type_id']);
        $info['goods'] = $goods;
        parent::_format_info($info);

        return $info;
    }
    
    //======================================== 私有方法 ==============================================//
    /**
     * 解析定制code
     *
     * 格式说明：一级分类id:组件id
     * 格式备注：款式设计一级分类有重复;个性签名位置会有多个用","连接
     *
     * 事例：8001:609997|24:36|24:100013|24:100037|298:31655|298:376|298:450|298:528|298:427,485
     * 备注  面料:DBL652A
     * 		款式设计:前门扣(单排一粒扣) 		款式设计:胸口袋(正常胸袋1(单1）) 	款式设计:下口袋(标准1)
     * 		里料:FLLDL009					纽扣搭配:奶白色果壳扣KG051
     * 		个性签名:字体颜色(102#--白色) 		个性签名:字体(宋体)				个性签名:位置(领底呢，左里袋上方的面料上（直过面除外）)
     *
     *
     * 返回：process ：工艺
     * 		consumption：单耗
     * 		customs-service_fee:服务费
     * 		data ：is_inventory ：1 检查库存	；is_price ：1 计算价格
     */
    function parsing_code($id,$code){
        //     	var_dump($expression);
        $return = array('error'=>0,'msg'=>'','data'=>array());
        $codearr = array();
        $codedata = array();
        if (!$id || !$code) return array('error'=>1,'msg'=>'id or code empty','data'=>array());
         
        $custom = $this->get_basis_info($id,1);	//生成嵌套格式的树形数组
         
        if (!$custom) return array('error'=>1,'msg'=>'custom info empty','data'=>array());
         
        $codearr = array_filter(explode('|', $code));
        if ($codearr){
            //规整数组 一级分类=>组件
            foreach($codearr as $r) {
                $ex = array();
                $ex = explode(':', $r);
                $id = $ex[0];
                $exs = explode(',', $ex[1]);
                if(!isset($codedata[$id])) {
                    $codedata[$id] = array($exs[0]);
                    if (isset($exs[1])) $codedata[$id] = array($exs[1]);
                }else{
                    $codedata[$id][] = $exs[0];
                    if (isset($exs[1])) $codedata[$id][] = $exs[1];
                }
            }
    
            //检查改定制是否存在这些组件
            foreach ($codedata as $key=>$val){
                foreach ($val as $pid){
                    $this->array_search_key(intval($pid), $custom['data']);
                    if (!$this->_nodes_found[$pid]){
                        $return['error'] = 1;
                        $return['msg'] = $pid.' is empty';
                        break;
                    }
                    $this->_nodes_found[$pid]['t_id'] = $key;
                }
            }
    
            //错误 包含定制款之外的组件
            if ($return['error']){
                $return['data'] = array();
                return $return;
            }
    
            $return['data'] = $this->_nodes_found;
    
            /* 释放 */
            unset($this->_nodes_found);
    
            foreach ($return['data'] as $k=>$v){
                $return['data'][$k]['is_inventory'] = 0;		//是否需要检查库存
                $return['data'][$k]['is_price'] = 0;			//是否计算价格
                /* 现在组件 只有面料、里料涉及到库存价格 */
                if (in_array($v['t_id'], Constants::$fabricsParent) || in_array($v['t_id'], Constants::$materialParent)){//面料
                    $return['data'][$k]['is_price'] = 1;
                }
                if (in_array($v['t_id'], Constants::$fabricsParent)){//只有面料减库存
                    $return['data'][$k]['is_inventory'] = 1;
                }
                 
            }
            $return['process'] = $custom['process'];
            $return['consumption'] = $custom['consumption'];
        }
         
        return $return;
    }
    /**
     * 通过基本款id 解析出code
     *
     * return array(3) {
     *  				["error"]=>int(0)
     * 					["msg"]=>string(0) ""
     *					["data"]=>string(119) "8001:612224|24:38|24:100013|24:100015|24:100017|24:100037|24:100039|24:100041|298:31655|298:376|298:450|298:528|298:427"
     * }
     */
    function parsing_code_base($id){
        $return = array('error'=>0,'msg'=>'','data'=>array());
        $codearr = array();
        $codedata = array();
        if (!$id) return array('error'=>1,'msg'=>'id or code empty','data'=>array());
         
        $custom = $this->get_basis_info($id,1);	//生成嵌套格式的树形数组
    
         
        //必须有组件
        if(!$custom['key_sequence']) return array('error'=>1,'msg'=>'key_sequence info empty','data'=>array());
    
         
        if (!$custom) return array('error'=>1,'msg'=>'custom info empty','data'=>array());
         
        $cs =& cs();
        $gcategories = $cs->_get_gcategory();
        $tree = $cs->_tree($gcategories);
         
        $this->array_search_val('is_dft', $custom['data'],$tree,1);
         
         
         
         
        if (!$this->_nodes_found_val)  if (!$custom) return array('error'=>1,'msg'=>'default empty','data'=>array());
         
        foreach (Constants::$fabricsParent as $fabrics){
            $this->array_search_key(intval($fabrics), $this->_nodes_found_val,1);
        }
         
        //每个基本款必须有默认面料
        if (!$this->_nodes_found) return array('error'=>1,'msg'=>'fabrics default empty','data'=>array());
         
        $code = '';
        foreach ($this->_nodes_found_val as $k=>$v){
            $tpatid = array();
            $patid = array();
            $tpatid = array_keys($v);
            $patid = array_values($v);
            $code .= $tpatid[0].':'.$patid[0]."|";
        }
         
        if (!$code) return array('error'=>1,'msg'=>'code err','data'=>array());
         
        //     	echo $code."\n";
        /* 释放 */
        unset($this->_nodes_found_val);
        unset($this->_nodes_found);
         
        return array('error'=>0,'msg'=>'','data'=>array(substr($code,0,-1)));
         
    }
    
    
}

