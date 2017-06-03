<?php

class IndexadModel extends BaseModel
{
    var $table  = 'index_ad';
    var $prikey = 'id';
    var $_name  = 'ad';
    
    var $_autov = array(
        'city_id' => array(
            'required'  => true,
        ),
        'img' => array(
            'required'  => true,
        ),
        'href' => array(
            'required'  => true,
        ),
    );
}

?>