<?php

/* 会员量体数据 */
class Member_figureModel extends BaseModel
{
    var $table  = 'member_figure';
    var $prikey = 'figure_sn';
    var $_name  = 'member_figure';

    /* 量体信息各部位数据 */
    function _positions(){
    
        $return = array(
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
        );
        return $return;
    }

}

?>