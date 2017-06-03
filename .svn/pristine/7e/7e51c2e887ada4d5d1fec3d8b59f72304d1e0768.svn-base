<?php

/* 会员量体数据 */
class Customer_figureModel extends BaseModel
{
    var $table  = 'customer_figure';
    var $prikey = 'figure_sn';
    var $_name  = 'customer_figure';
    /*
     * 判断用户手机号是否唯一
    */
    function unique($customer_mobile,$storeid,$type_cus,$figure_sn = 0)
    {
    	$conditions = "customer_mobile = '" . $customer_mobile . "'";
    	if($storeid){
    		$conditions .= " AND storeid = '".$storeid."'";
    	}
    	if($type_cus){
    		$conditions .= " AND type_cus in (1,2,3)";
    	}
    	if($figure_sn){
    		$conditions .= " AND figure_sn <> '" . $figure_sn . "' AND storeid = '".$storeid."'";
    	}
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
    /* 量体信息各部位数据 */
    function _positions(){
    
    	$return = array(
    	//            量体信息
    			'lw' => array('zname'=>'颈围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'xw' => array('zname'=>'胸围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'zyw' => array('zname'=>'中腰','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'tw' => array('zname'=>'臀围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'zww' => array('zname'=>'左腕围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'yww' => array('zname'=>'右腕围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'sbw' => array('zname'=>'上臂围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'zjk' => array('zname'=>'肩宽','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'zxc' => array('zname'=>'左袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'yxc' => array('zname'=>'右袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'qjk' => array('zname'=>'前肩宽','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'hyjc' => array('zname'=>'后腰节长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'hyc' => array('zname'=>'后衣长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'qyj' => array('zname'=>'前腰节','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'yw' => array('zname'=>'腰围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'tgw' => array('zname'=>'腿根围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'td' => array('zname'=>'通档','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'hyg' => array('zname'=>'后腰高','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'qyg' => array('zname'=>'前腰高','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'zkc' => array('zname'=>'左裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'ykc' => array('zname'=>'右裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'xiw' => array('zname'=>'膝围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'jk' => array('zname'=>'脚口','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'dkzkc' => array('zname'=>'短裤左裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'dkykc' => array('zname'=>'短裤右裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'lheight' => array('zname'=>'身高','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'lweight' => array('zname'=>'体重','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'part_label_10726' => array('zname'=>'马甲后衣长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'part_label_10725' => array('zname'=>'大衣后衣长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'hd' => array('zname'=>'横档','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'syzxc' => array('zname'=>'上衣左袖长','cate'=>array('0003'=>'0003','0011'=>'0011'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'cyzxc' => array('zname'=>'衬衣左袖长','cate'=>array('0006'=>'0006','0016'=>'0016'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'dyzxc' => array('zname'=>'大衣左袖长','cate'=>array('0007'=>'0007','0021'=>'0021'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'syyxc' => array('zname'=>'上衣右袖长','cate'=>array('0003'=>'0003','0011'=>'0011'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'cyyxc' => array('zname'=>'衬衣右袖长','cate'=>array('0006'=>'0006','0016'=>'0016'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'dyyxc' => array('zname'=>'大衣右袖长','cate'=>array('0007'=>'0007','0021'=>'0021'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'syhyc' => array('zname'=>'上衣后衣长','cate'=>array('0003'=>'0003','0011'=>'0011'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'cyhyc' => array('zname'=>'衬衣后衣长','cate'=>array('0006'=>'0006','0016'=>'0016'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'dyhyc' => array('zname'=>'大衣后衣长','cate'=>array('0007'=>'0007','0021'=>'0021'),'isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			
    
    			//着装风格
    			'body_type_3' => array('zname'=>'上衣','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'body_type_2000' => array('zname'=>'西裤','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'body_type_3000' => array('zname'=>'衬衣','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'body_type_4000' => array('zname'=>'马夹','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'body_type_6000' => array('zname'=>'大衣','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'styleDY' => array('zname'=>'大衣着装习惯','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'body_type_90000' => array('zname'=>'礼服','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    
    
    			//特体
    			'body_type_19' => array('zname'=>'左肩','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'body_type_20' => array('zname'=>'右肩','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'body_type_24' => array('zname'=>'肚','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'body_type_25' => array('zname'=>'手臂','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    			'body_type_26' => array('zname'=>'臀','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
    
    	);
    	return $return;
    }
    
    
    
}

?>