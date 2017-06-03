<?php

/* 多级选择：地区选择，分类选择 */
class MlselectionApp extends MallbaseApp
{
    function index()
    {
        in_array($_GET['type'], array('region', 'gcategory')) or $this->json_error('invalid type');
        $pid = empty($_GET['pid']) ? 0 : $_GET['pid'];
		
        switch ($_GET['type'])
        {
            case 'region':
                $mod_region =& m('region');
                $regions = $mod_region->get_list($pid);
                foreach ($regions as $key => $region)
                {
                    $regions[$key]['region_name'] = htmlspecialchars($region['region_name']);
                }
                $this->json_result(array_values($regions));
                break;
            case 'gcategory':
                $mod_gcategory =& m('gcategory');
                $cates = $mod_gcategory->get_list($pid, true);
                foreach ($cates as $key => $cate)
                {
                    $cates[$key]['cate_name'] = htmlspecialchars($cate['cate_name']);
                }
                $this->json_result(array_values($cates));
                break;
        }
    }
    
	function region_serve(){
    	if($_GET['regionname'])
    	{
    		$region_name=$_GET['regionname'];
    		$region_name=str_replace('市','',$region_name);
    		//var_dump($region_name);
    		$region_mod =m('region');
    		//$res=$region_mod->get(array('conditions'=>"region_name = '$region_name' "));
    		//$res=$region_mod->get(array('conditions'=>"1=1"));
    		//if($res['region_id']){
    			$serve=m('serve');
    			$res_serve=$serve->find(array(
    			'conditions' => '1=1 and region_name like \'%'.$region_name.'%\' and serve_type=2 and serve_detail.longitude  is not null ',
        		'limit' => '0,10',
				'join' => 'has_serve_detai',
    			'fields'=>'longitude,latitude,portrait,serve_address,mobile,synopsis,company_name'));
    			//var_dump($region_name);exit;
    			$this->json_result(array_values($res_serve));
    			return ;
    		//}
    	}
    	$this->json_result(array_values(array()));
    }
    
}

?>