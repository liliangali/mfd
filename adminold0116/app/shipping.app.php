<?php

use Cyteam\Goods\Region;
use Cyteam\Goods\Shipping;
/**
 *    配送方式管理控制器
 *
 *    @author    yhao.bai
 *    @usage    none
 */
class ShippingApp extends BackendApp
{
	var $model_shipping;
	var $model_shiping_area;
	function __construct(){

		$this->model_shipping =& m('shipping');
		$this->model_shiping_area =& m('shippingarea');
		parent::__construct();
	}
    function index()
    {
    	$shippings = $this->model_shipping->find(array(
	        'order'   => 'shipping_id DESC',
        ));
    	
    	/* 读取已安装的支付方式 */
        $this->assign('shippings', $shippings);
        $this->display('shipping.index.html');
    }
    
    /**
    *content
    *@author  liang.li
    */
    function saveShipping($post,$shippingId = 0) 
    {
        $shippingMdl = m('shipping');
        $regionLib   = new Region();
        $shiipingLib = new Shipping();
        $regionList = $regionLib->lists();
        $areaList = $shiipingLib->areaList();
        $fregionList = $this->fregion($regionList);
        $sdata['shipping_name'] = $post['shipping_name'];
        $sdata['first_weight'] = $post['first_weight'];
        $sdata['step_weight'] = $post['step_weight'];
        $sdata['enabled'] = $post['enabled'];
        $sdata['is_fress'] = $post['is_fress'];
        $sdata['sort_order'] = $post['sort_order'];
        $sdata['code'] = $post['code'];
        $sdata['first_money'] = $post['first_price'];
        $sdata['step_money'] = $post['step_price'];
        //$shippingId = 1;
       
        
        if ($shippingId) 
        {
            $shiipingLib->edit($shippingId,$sdata);
            $shiipingLib->del("shipping_id = $shippingId");
        }
        else 
        {
            $shippingId = $shippingMdl->add($sdata);
            if (!$shippingId)
            {
                $this->show_warning('ERROR!');
                return;
            }
        }
        
        
        
        if ($post['fee_conf'])
        {
            $shiipAreaData['shipping_id'] = $shippingId;
            foreach ($_POST['fee_conf'] as $key => $value)
            {
                $parea = [];
                $sarea = [];
        
                //         	   echo '<pre>';print_r($value);exit;
                if ($value['area'])
                {
                    $area = explode(",", $value['area']);
                    //         	    echo '<pre>';print_r($area);exit;
                    foreach ($area as $key1 => $value1)
                    {
                        if ($value1)
                        {
                            if ($fregionList[$value1])  //===== 父级分类  =====
                            {
                                $pre = i_array_column($fregionList[$value1]['children'], "id");
                                $parea = array_merge($parea,$pre);
                                // echo '<pre>';print_r($fregionList);exit;
                            }
                            else
                            {
                                //         	         echo '<pre>';print_r($value1);exit;
                                $sarea[] = $value1;
                            }
                        }
                    }
                    $sarea = array_merge($sarea,$parea);
        
                    $shiipAreaData['first_weight'] = $value['start_standard'];
                    $shiipAreaData['step_weight'] = $value['add_standard'];
                    $shiipAreaData['first_price'] = $value['start_fee'];
                    $shiipAreaData['step_price'] = $value['add_fee'];
                    $shiipAreaData['fr'] = $shiipAreaData['first_weight'].",".$shiipAreaData['first_price'].",".$shiipAreaData['step_weight'].",".$shiipAreaData['step_price'];
                    //         	  echo '<pre>';print_r($sarea);exit;
                    foreach ($sarea as $key2 => $value2)
                    {
                        if (!$value2)
                        {
                            continue;
                        }
                        $arconditions = " shipping_id = $shippingId AND region_id = $value2 ";
                        if ($shiipingLib->areaInfo($arconditions))
                        {
                            $shiipingLib->del($arconditions);
                        }
                        $shiipAreaData['region_id'] = $value2;
                        // echo '<pre>';print_r($shiipAreaData);exit;
                        $shiipingLib->add($shiipAreaData);
                         
                    }
                    // echo '<pre>';print_r($sarea);exit;
                }
            }
        }
    }

    /**
     *   添加配送方式
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function add()
    {
    	if(!IS_POST)
    	{
    		$this->import_resource(array(
    				'script' => 'jquery.plugins/jquery.validate.js',
    				'style'  => 'res:style/jqtreetable.css'
    		));
    		$this->assign('yes_or_no', array(Lang::get('no'), Lang::get('yes')));
    		$this->display('shipping.form.html');
    	}
    	else
    	{
    	    $this->saveShipping($_POST);
//         	echo '<pre>';print_r($_POST);exit; 
        	$this->show_message("操作成功", '返回配送方式列表','index.php?app=shipping');
    	    return;
    	}
    }
    
    /**
     *   编辑配送方式
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function edit()
    {
    	$shipping_id = isset($_REQUEST["shipping_id"]) ? intval($_REQUEST['shipping_id']) : 0;
    	
    	if(!$shipping_id)
    	{
    		$this->show_warning('Hacking Attempt');
    		return ;
    	}
    	
    	if(!IS_POST)
    	{
    	    $shippingLib = new Shipping();
    	    $regionLib   = new Region();
    	    $shippingInfo = $shippingLib->info("shipping_id=$shipping_id");
    	    $areaList = $shippingLib->areaList("shipping_id=$shipping_id","region_id");
    	    $regionList = $regionLib->lists();
    	    $fr = [];
//    echo '<pre>';print_r($areaList);exit; 
            if ($areaList) 
            {
                foreach ($areaList as $key => $value)
                {
                    // $fr[$value['fr']] = $value['fr'];
                    $value['p_region_id'] = $regionList[$value['region_id']]['parent_id'];
                    $value['region_name'] = $regionList[$value['region_id']]['region_name'];
                    $fr[$value['fr']]['val_list'] = $value;
                    if ($fr[$value['fr']])
                    {
                        $fr[$value['fr']]['p_list'][$value['p_region_id']]['item'][$value['region_id']] = $value;
                        //$fr["{$value['fr']}"][$value['p_region_id']]['name'] = $regionList[$value['p_region_id']];
                        $fr["{$value['fr']}"]['p_list'][$value['p_region_id']]['name'] = $regionList[$value['p_region_id']]['region_name'];
                        //     	    echo '<pre>';var_dump($fr);exit;
                    }
                    else
                    {
                        //   echo '<pre>';print_r($value);exit;
                        $fr["{$value['fr']}"]['p_list'][$value['p_region_id']]['item'][$value['region_id']] = $value;
                        $fr["{$value['fr']}"]['p_list'][$value['p_region_id']]['name'] = $regionList[$value['p_region_id']]['region_name'];
                        //     	 echo '<pre>';print_r($fr);exit;
                    }
                }
//    echo '<pre>';print_r($fr);exit;              	
                foreach ($fr as $key => $value)
                {
                    $regionf = [];
                    foreach ($value['p_list'] as $key1 => $value1)
                    {
                        $regionf = array_merge($regionf,i_array_column($value1['item'], "region_id"));
                        //$fr[$key][$key1]['f_region'] = implode(",", );
                        $fr[$key]['p_list'][$key1]['f_region_name'] = implode(",", i_array_column($value1['item'], "region_name"));
                    }
//                echo '<pre>';print_r($regionf);exit; 
                    $fr[$key]['f_region'] = implode(",", $regionf);
                }
//           echo '<pre>';print_r($fr);exit; 
                $this->assign('fr',$fr);
            }
    	    
//     echo '<pre>';print_r($fr);exit; 	    
//    echo '<pre>';print_r($areaList);exit; 
//     echo '<pre>';print_r($shipping_id);exit; 
// echo '<pre>';print_r($shippingInfo);exit; 
    		$this->assign('info',$shippingInfo);
    		$this->display('shipping.form.html');
    	}
    	else
    	{
    	    $this->saveShipping($_POST,$shipping_id);
    		$data = array(
    				'shipping_name' => $_POST['shipping_name'],
    				'code'          => $_POST['code'],
    				'first_weight'  => $_POST['first_weight'],
    				'step_weight'   => $_POST['step_weight'],
    				'shipping_desc' => $_POST['shipping_desc'],
    				'enabled'       => $_POST['enabled'],
    		        'is_fress'       => $_POST['is_fress'],
    				'sort_order'    => $_POST['sort_order']
    		);
    		$res = $this->model_shipping->edit($shipping_id,$data);
    
    		if(!$res)
    		{
    			$msg = $this->model_shipping->get_error();
    			$this->show_warning($msg);
    			return;
    		}
    		$this->show_message("操作成功", '返回配送方式列表','index.php?app=shipping');
    	}
    }
    
    
    /**
    *设默认物流
    *@author  liang.li
    */
    function setdefault() 
    {
        $shippingId = isset($_GET["shipping_id"]) ? intval($_GET['shipping_id']) : 0;
        if (!$shippingId) 
        {
            $this->show_warning('缺少参数');
            return;
        }
        $shippingLib = new Shipping();
        $shippingLib->setdefault($shippingId);
        $this->show_message("操作成功", '返回配送方式列表','index.php?app=shipping');
    }
    
    function drop()
    {
    	$shippingId = isset($_GET["shipping_id"]) ? intval($_GET['shipping_id']) : 0;
    	$shippingLib = new Shipping();
    	$shippingLib->delShippingByPk($shippingId);
    	$this->show_message("操作成功", '返回配送方式列表','index.php?app=shipping');
    }
    
    
    function region() 
    {
       $regionLib = new Region();
       $regionList = $regionLib->lists();
       $ret = $this->fregion($regionList);
       $ret = array_values($ret);
       echo json_encode($ret);exit;
    echo '<pre>';print_r($ret);exit;    
    }
    
    function fregion($regionList) 
    {
        $ret = [];
        foreach ($regionList as $key => $value)
        {
            if ($value['parent_id'] == 2)
            {
                $val['id'] = $key;
                $val['value'] = $value['region_name'];
                $val['parentId'] = 2;
                $ret[$value['region_id']] = $val;
            }
            else
            {
                $sval['id'] = $key;
                $sval['value'] = $value['region_name'];
                $sval['parentId'] = $value['parent_id'];
                $ret[$value['parent_id']]['children'][] = $sval;
            }
        }
        return $ret;
    }
    
    
    
}

?>