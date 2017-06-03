<?php


class UserphotocommentModel extends BaseModel
{
    var $table  = 'comments_photo';
    var $prikey = 'id';
    var $_name  = 'comments_photo';

    
    var $_relation = array(
	        'has_photo_gallery' => array(
	            'model' => 'userphotogallery', //模型的名称
	            'type' => HAS_MANY, //关系类型
	            'foreign_key' => 'project_id', //外键名
	    		'dependent' => true
	        ),
	        
	        'belongs_to_userphoto' => array(
	            'model'             => 'userphoto',
	            'type'              => BELONGS_TO,
	            'foreign_key'       => 'id',
	            'reverse'           => 'has_photo_comment',
	        ),
	        
        );
    
}

?>