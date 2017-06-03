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
            //             'fw' => array('zname'=>'腹围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),//
            'zyw' => array('zname'=>'中腰围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            //'stw' => array('zname'=>'上臀围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'stw' => array('zname'=>'上臂围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'zjk' => array('zname'=>'肩宽','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'qjk' => array('zname'=>'前肩宽','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'part_label_10130' => array('zname'=>'左腕围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),//
            'part_label_10131' => array('zname'=>'右腕围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),//
            'zxc' => array('zname'=>'左袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'yxc' => array('zname'=>'右袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'hyc' => array('zname'=>'后衣长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            //
            'yw' => array('zname'=>'腰围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'tw' => array('zname'=>'臀围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'tgw' => array('zname'=>'腿根围','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'td' => array('zname'=>'通裆','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'qyg' => array('zname'=>'前腰高','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'hyg' => array('zname'=>'后腰高','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'qyj' => array('zname'=>'前腰节长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'hyjc' => array('zname'=>'后腰节长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'zkc' => array('zname'=>'左裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'ykc' => array('zname'=>'右裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'xiw' => array('zname'=>'膝围','isshow'=>0,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'jk' => array('zname'=>'脚口','isshow'=>0,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'dxzxc' => array('zname'=>'短袖左袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'dxyxc' => array('zname'=>'短袖右袖长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'dkzkc' => array('zname'=>'短裤左裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
            'dkykc' => array('zname'=>'短裤右裤长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
        
        
        
        
             
            /* 'part_label_10726' => array('zname'=>'马甲后衣长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
             'part_label_10725' => array('zname'=>'大衣后衣长','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'),
        'hd' => array('zname'=>'横档','isshow'=>1,'ename'=>'英文名称扩展值','logo'=>'图标扩展值'), */
        );
        
        return $return;
    }

}

?>