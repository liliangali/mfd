<?php

/**
 *基础材料模型
 * @author cyrus <2621270755@qq.com>
 * @copyright Copyright 2016 cotte.com
 * @package basematerial.model.php
 */
class StandardPackageModel extends BaseModel
{
    var $table  = 'standard_package';
    var $prikey = 'sp_id';
    var $alias  = 'sp';
    var $_name  = 'standardpackage';
    
    
    /* 表单自动验证 */
    var $_autov = array(
        
    );

    
    
}

?>