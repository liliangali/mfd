<?php


class UserphotogalleryModel extends BaseModel
{
    var $table  = 'photo_gallery';
    var $prikey = 'img_id';
    var $_name  = 'photo_gallery';
	
    var $_relation  = array(
        'belongs_to_photocomment' => array(
            'model'             => 'userphotocomment',
            'type'              => BELONGS_TO,
            'foreign_key'       => 'id',
            'reverse'           => 'has_photo_gallery',
        ),
    );
}

?>