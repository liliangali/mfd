<?php

/* 流行趋势相册模型 */
class TrendsGalleryModel extends BaseModel
{
    var $table  = 'trends_gallery';
    var $prikey = 'img_id';
    var $alias  = 'tg';
    var $_name  = 'trendsgallery';
    var $temp; // 临时变量
    var $_relation = array(
    		
    	 // 一个图片只能属于一个数据
        'belongs_to_goods' => array(
            'model'         => 'trends',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'trends_id',
            'reverse'       => 'has_goodsspec',
        ),
    		
    		'be_collect' => array(
    				'model'         => 'customs',
    				'type'          => HAS_AND_BELONGS_TO_MANY,
    				'middle_table'  => 'customs_parts',
    				'foreign_key'   => 'pt_id',
    				
    				'reverse'       => 'collect_customs',
    		),
    		
    );

    var $_autov = array(
        'trends_id' => array(
            'required'  => true,
            'filter'    => 'trim',
        ),
    );
    
    /**
     * 删除数据
     */
    public function drop($conditions)
    {
    	$this->drop_data($conditions);
    	parent::drop("trends_id".db_create_in($conditions));
    }
    
    /**
     * 删除相册文件对应的图片
     */
    public function drop_data($conditions)
    {
    	$tg_list = $this->find(array(
    			"conditions"	=> "1=1 AND trends_id".db_create_in($conditions),
    			"fields"		=> "img_url,thumb1,thumb2",
    			));
    	foreach ($tg_list as $v)
    	{
    		$img = $v['img_url'];
	    	$img_url = ROOT_PATH.'/upload_user_photo/liuxing/original/'.$img;
	    	if (file_exists($img_url))
	    	{
	    		@unlink($img_url);
	    	}
	    	
	    	$thumb1 = ROOT_PATH.'/upload_user_photo/liuxing/225x305/'.$img;
	    	if (file_exists($thumb1))
	    	{
	    		@unlink($thumb1);
	    	}
	    	
	    	$thumb2 = ROOT_PATH.'/upload_user_photo/liuxing/200x250/'.$img;
	    	if (file_exists($thumb2))
	    	{
	    		@unlink($thumb2);
	    	}
	    	
	    	$thumb3 = ROOT_PATH.'/upload_user_photo/liuxing/236x353/'.$img;
	    	if (file_exists($thumb3))
	    	{
	    		@unlink($thumb3);
	    	}
    	}
    }

  	/**
  	 * 根据img_id 删除对应的图片
  	 */
    public function drop_gallery($img_id)
    {
    	$img_info = $this->get($img_id);
    	$img = $img_info['img_url'];
    	$img_url = ROOT_PATH.'/upload_user_photo/liuxing/original/'.$img;
    	if (file_exists($img_url))
    	{
    		@unlink($img_url);
    	}
    	
    	$thumb1 = ROOT_PATH.'/upload_user_photo/liuxing/225x305/'.$img;
    	if (file_exists($thumb1))
    	{
    		@unlink($thumb1);
    	}
    	
    	$thumb2 = ROOT_PATH.'/upload_user_photo/liuxing/200x250/'.$img;
    	if (file_exists($thumb2))
    	{
    		@unlink($thumb2);
    	}
    	
    	$thumb3 = ROOT_PATH.'/upload_user_photo/liuxing/236x353/'.$img;
    	if (file_exists($thumb3))
    	{
    		@unlink($thumb3);
    	}
    	
    	parent::drop($img_id);
    	return true;
    }
    
}


?>