<?php

/**
* 童装频道设置项
* @author yhao.bai <baiyunhao@hotmail.com>
* @version $Id: children.arrayfile.php 4291 2015-05-30 02:41:45Z gaofei $
* @copyright Copyright 2014 mfd.com
* @package man
*/
class ChildrenArrayfile extends BaseArrayfile
{
    function __construct()
    {
        $this->_filename = ROOT_PATH . '/data/children.inc.php';
    }
    
    /**
    * 描述
    * @return array
    * @access public
    * @see get_default
    * @version 1.0.0 (2014-11-19)
    * @author yhao.bai
    */
    function get_default()
    {
        return array(
            'cat_id'     => 0,
            'keywords'   => 0,
            'desc'       => 0,
            'title'      => 0,
            'goodsnum' => '8',
        );
    }
}
?>