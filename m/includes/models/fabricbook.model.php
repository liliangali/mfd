<?php

class FabricbookModel extends BaseModel
{
    var $table  = 'fabricbook';
    var $prikey = 'id';
    var $_name  = 'fb';
    
    function getCategory(){
        return array(
            "1" => "料册区",
            "2" => "辅料展示",
            "3" => "配饰区",
        	"4" => "量体工具",
        	"5"=>"样衣展示",
        	"6"=>"创业辅导",
        );
    }
}

?>