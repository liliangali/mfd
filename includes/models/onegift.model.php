<?php

class OnegiftModel extends BaseModel
{
    var $table  = 'onegift';
    var $prikey = 'one_id';
    var $_name  = 'onegift';
    
    public function algorithms(){
        
        return array(
                '1' => '去低取高',
                '2' => '折中对半',
                '3' => '去高取低',
        );
        
    }
    
}
