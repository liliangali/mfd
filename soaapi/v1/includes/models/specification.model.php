<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
class SpecificationModel extends BaseModel
{
    var $table  = 'specification';
    var $prikey = 'spec_id';
    var $_name  = 'specification';
    var $_prefix = "cf_";

    
    var $_relation  = array(
        'has_goodsspec' => array(
            'model'         => 'specvalues',
            'type'          => HAS_MANY,
            'foreign_key'   => 'spec_id',
            'dependent'     => true
        ),
    );
    
    
}

?>