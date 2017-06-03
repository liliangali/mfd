<?php
/* 用户复购
 * 2015-12-14 by shao
 *  */
class Numerical_statApp  extends BackendApp
{
	var $_member_mod;
	var $_generalize_mod;
	var $_g_mod;
	var $_invite_mod;
	var $_order_mod;
	var $_member_lv;
	function __construct(){
		$this->_member_mod = &m("member");
		$this->_generalize_mod =& m('generalize_member');
		$this->_g_mod = & m('generalize');
		$this->_invite_mod = & m('memberinvite');
		$this->_order_mod = & m('order');
		 $this->_member_lv = & m('memberlv'); 
           parent::__construct();
    }

    function index(){
    
    	$this->display("numerical_stat/index.html");
    }
    function export(){
  
    	
    
    	$order_mod=m("order");
    $order_figure=m("orderfigure");
    $order_goods=m("ordergoods");
    $member_lvids=$this->_member_lv->find(array(
    		'conditions' =>"1=1",
    		'fields' =>'member_lv_id,name',
    ));
    if($member_lvids)
    {
    	foreach($member_lvids as $key=>$val)
    	{
    		$member_lvs[$val['member_lv_id']]=$val['name'];
    	}
    }
    
    	$conditions="";
    	if($_GET['time_from']){
    		$addtimef=strtotime($_GET['time_from']);
    		$conditions.=" AND order_alias.add_time >='{$addtimef}'";
    	}
    	if($_GET['time_to']){
    		$addtimet=strtotime($_GET['time_to']);
    		$conditions.=" AND order_alias.add_time <='{$addtimet}'";
    	}
    	if($addtimef && $addtimet){
    		if($addtimef>$addtimet){
    			/* $this->show_message("搜索起始时间不能大于终止时间");
    			return false; */
    			$this->show_message('搜索起始时间不能大于终止时间');
    			return;
    		}
    	}else{
    		$this->show_message("必须选择起始时间");
    		return false;
    	}
    	if(($addtimef+3*30*24*3600)<$addtimet)
    	{
    		$this->show_message("时间段不能大于3个月");
    		return false;
    	}
    	$orderlists=$order_mod->find(array(
    			'conditions' =>"order_alias.extension= 'news' AND order_alias.status in (30,40)".$conditions,
    			//'join' =>'has_members',
    			'fields' =>"order_alias.user_id,order_alias.ship_name,order_alias.has_measure,order_alias.user_name,order_alias.ship_mobile,order_alias.add_time",
    			'order'=>"add_time DESC",
    	));
    	$or_user="";
    	if($orderlists)
    	{
    		foreach($orderlists as $okey=>$oval){
    			if($oval['has_measure'] ==1)
    			{
    				$or_figulists=$order_figure->find(array(
    						'conditions' =>"order_id='{$oval['order_id']}'",
    						'fields' =>"id,realname AS ship_name,phone AS ship_mobile,order_id,userid AS user_id",
    				));
    				if($or_figulists){
    					foreach($or_figulists as $key=>$val){
    						$or_user[$val['ship_mobile']][$val['id'].$val['ship_mobile']]=$val;
    					}
    				}
    			}else{
    				$or_user[$oval['ship_mobile']][]=$oval;
    			}
    		
    		}
    		$count_zo=count($or_user);
    		 if($or_user)
    		 {
    		 	foreach($or_user as $key=>$val)
    		 	{
    		 		$or_user[$key]['count']=count($val);
    		 	}
    		 	$ins='';
    		 	foreach($or_user as $key=>$val){
    		 	
    		 		$ins +=1;
    		 		$orusers[$ins]=$val;
    		 		$count=$val['count'];
    		 		unset($val['count']);
    		 		$orderids=i_array_column($val,'order_id');
    		 		$orderid_s=db_create_in($orderids,'order_id');
    		 		$cloths=$order_goods->find(array(
    		 				'conditions' =>$orderid_s,
    		 				'fields'=>"order_id,goods_name,cloth,price,quantity,subtotal",
    		 		));
    		 		$cy_num='';//衬衣数量
    		 		$dy_num='';//大衣数量
    		 		$xf_num='';//西服数量
    		 		$xk_num='';//西裤数量
    		 		$mj_num='';//马甲数量
    		 		$cy_price='';//衬衣消费合计
    		 		$dy_price='';//大衣消费合计
    		 		$xf_price='';//西服消费合计
    		 		$xk_price='';//西裤消费合计
    		 		$mj_price='';//马甲消费合计
    		 		if($cloths){
    		 			foreach($cloths as $k=>$v){
    		 				if($v['cloth']=='0006'){
    		 					$cy_num +=1;
    		 					$cy_price +=$v['subtotal'];
    		 				}
    		 				if($v['cloth']=='0007'){
    		 					$dy_num +=1;
    		 					$dy_price +=$v['subtotal'];
    		 				}
    		 				if($v['cloth']=='0003'){
    		 					$xf_num +=1;
    		 					$xf_price +=$v['subtotal'];
    		 				}
    		 				if($v['cloth']=='0004'){
    		 					$xk_num +=1;
    		 					$xk_price +=$v['subtotal'];
    		 				}
    		 				if($v['cloth']=='0005'){
    		 					$mj_num +=1;
    		 					$mj_price +=$v['subtotal'];
    		 				}
    		 			}
    		 		}
    		 		 
    		 		$all_price=$cy_price+$dy_price+$xf_price+$xk_price+$mj_price;
    		 		foreach($val as $kk=>$vv){
    		 			$members=$this->_member_mod->get(array(
    		 					'conditions'=>"(user_name='{$vv['ship_mobile']}' or phone_mob='{$vv['ship_mobile']}') AND serve_type=1",
    		 					'fields'=>"member_lv_id,user_id",
    		 			));
    		 			if($members['user_id']){
    		 				$member_lv_id=$member_lvs[$members['member_lv_id']];
    		 			}else{
    		 				$member_lv_id='会员';
    		 			}
    		 			if($members['member_lv_id'] > 4 or !$all_price){
    		 				$del[]=$ins;
    		 			}
    		 				
    		 			$order[$ins] = array(
    		 					'id'=>$ins,//序号
    		 					'user_id'=>$members['user_id'],//用户ID
    		 					'member_lv_id'=>$member_lv_id,//等级
    		 					'ship_name'=>$vv['ship_name'],    //姓名
    		 					'phone'=>$vv['ship_mobile'],//联系电话
    		 					'count'=>$count?$count:'0',//购买次数
    		 					'cy_num'=>$cy_num?$cy_num:'0',//衬衣数量
    		 					'cy_price'=>$cy_price?$cy_price:'0',//衬衣消费合计
    		 					'dy_num'=>$dy_num?$dy_num:'0',//大衣数量
    		 					'dy_price'=>$dy_price?$dy_price:'0',//大衣消费合计
    		 					'xf_num'=>$xf_num?$xf_num:'0',//西服数量
    		 					'xf_price'=>$xf_price?$xf_price:'0',//西服消费合计
    		 					'xk_num'=>$xk_num?$xk_num:'0',//西裤数量
    		 					'xk_price'=>$xk_price?$xk_price:'0',//西裤消费合计
    		 					'mj_num'=>$mj_num?$mj_num:'0',//马甲数量
    		 					'mj_price'=>$mj_price?$mj_price:'0',//马甲消费合计
    		 					'all_price'=>$all_price?$all_price:'0',//合计金额
    		 					'aga_buy'=>0,
    		 			);
    		 		}
    		 	
    		 	}
    		 }	
    		if($del){
    			foreach($del as $dk=>$dv)
    			{
    				unset($order[$dv]);
    			}
    		}
    		$iny='';
    		$oncount='';
    		if($order){
    			foreach($order as $k=>$v)
    			{
    				if($v['count'] >1){
    					$oncount +=1;
    				}
    				$iny +=1;
    				$order[$k]['id']=$iny;
    			}
    			foreach($order as $kl=>$vl)
    			{
    				$order[$kl]['aga_buy']=round($oncount/$iny,2)*100;
    			}
    			$fields_name = array('序号','用户ID','等级','姓名','联系电话','购买次数','衬衣数量','衬衣消费合计','大衣数量','大衣消费合计','西服数量','西服消费合计','西裤数量','西裤消费合计','马甲数量','马甲消费合计','合计金额','复购率');
    			array_unshift($order,$fields_name);
    			$this->export_to_csv($order, 'order', 'gbk');
    		}
    	}else{
    		$this->show_message("没有符合条件的数据");
    		return false;
    	}
    
    }
    
    /* 	include(ROOT_PATH.'/includes/libraries/PHPExcel.php');
     include(ROOT_PATH.'/includes/libraries/PHPExcel/Writer/Excel2007.php');
     
    $objPHPExcel = new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save("xxx.xlsx");
     
    //设置当前的sheet
    $objPHPExcel->setActiveSheetIndex(0);
    //设置sheet的name
    $objPHPExcel->getActiveSheet()->setTitle('numerical');
    //设置单元格的值
    $objPHPExcel->getActiveSheet()->setCellValue('A1', '用户复购汇总表');
    $objPHPExcel->getActiveSheet()->setCellValue('A2', '序号');
    $objPHPExcel->getActiveSheet()->setCellValue('B2', '用户ID');
    $objPHPExcel->getActiveSheet()->setCellValue('C2', '等级');
    $objPHPExcel->getActiveSheet()->setCellValue('D2', '姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('E2', '联系电话');
    $objPHPExcel->getActiveSheet()->setCellValue('F2', '购买次数');
    $objPHPExcel->getActiveSheet()->setCellValue('G2', '品类');
    $objPHPExcel->getActiveSheet()->setCellValue('G3', '衬衣');
    $objPHPExcel->getActiveSheet()->setCellValue('I3', '大衣');
    $objPHPExcel->getActiveSheet()->setCellValue('K3', '西服');
    $objPHPExcel->getActiveSheet()->setCellValue('M3', '西裤');
    $objPHPExcel->getActiveSheet()->setCellValue('O3', '马甲');
    $objPHPExcel->getActiveSheet()->setCellValue('Q3', '合计金额（元）');
    $objPHPExcel->getActiveSheet()->setCellValue('R3', '复购率');
    $objPHPExcel->getActiveSheet()->setCellValue('G4', '数量');
    $objPHPExcel->getActiveSheet()->setCellValue('H4', '消费合计');
    $objPHPExcel->getActiveSheet()->setCellValue('I4', '数量');
    $objPHPExcel->getActiveSheet()->setCellValue('J4', '消费合计');
    $objPHPExcel->getActiveSheet()->setCellValue('K4', '数量');
    $objPHPExcel->getActiveSheet()->setCellValue('L4', '消费合计');
    $objPHPExcel->getActiveSheet()->setCellValue('M4', '数量');
    $objPHPExcel->getActiveSheet()->setCellValue('N4', '消费合计');
    $objPHPExcel->getActiveSheet()->setCellValue('O4', '数量');
    $objPHPExcel->getActiveSheet()->setCellValue('P4', '消费合计');
    //合并单元格
    $objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
    $objPHPExcel->getActiveSheet()->mergeCells('A2:A4');
    $objPHPExcel->getActiveSheet()->mergeCells('B2:B4');
    $objPHPExcel->getActiveSheet()->mergeCells('C2:C4');
    $objPHPExcel->getActiveSheet()->mergeCells('D2:D4');
    $objPHPExcel->getActiveSheet()->mergeCells('E2:E4');
    $objPHPExcel->getActiveSheet()->mergeCells('F2:F4');
    $objPHPExcel->getActiveSheet()->mergeCells('G2:R2');
    $objPHPExcel->getActiveSheet()->mergeCells('G3:H3');
    $objPHPExcel->getActiveSheet()->mergeCells('I3:J3');
    $objPHPExcel->getActiveSheet()->mergeCells('K3:L3');
    $objPHPExcel->getActiveSheet()->mergeCells('M3:N3');
    $objPHPExcel->getActiveSheet()->mergeCells('Q3:Q4');
    $objPHPExcel->getActiveSheet()->mergeCells('R3:R4');
    */
   

    
    
 
    
   
}
?>