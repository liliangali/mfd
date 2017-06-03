<?php

/* 流行趋势模型 */
class TrendsModel extends BaseModel
{
    var $table  = 'trends';
    var $prikey = 'trends_id';
    var $alias  = 't';
    var $_name  = 'trends';
    var $temp; // 临时变量
    var $_relation = array(
        // 一个数据对应多个图片
        'has_goodsspec' => array(
            'model'         => 'trendsgallery',
            'type'          => HAS_MANY,
            'foreign_key'   => 'trends_id',
            'dependent'     => true
        ),
    		
    );

    var $_autov = array(
        'title' => array(
            'required'  => true,
            'filter'    => 'trim',
        ),
    );

   
    function edit($conditions, $edit_data)
    {
        return parent::edit($conditions, $edit_data);
    }

    function drop($conditions, $fields = '')
    {
        return parent::drop($conditions, $fields);
    }
	
    /**
     * 酷客基地获取图片列表
     */
    function get_img_kuke($limit)
    {
    	$trends_list = $this->find(array(
    			"conditions"	=>	"1=1 AND is_fab=1",
    			"order"			=> "sort_order asc,trends_id desc",
    			"fields"		=> "trends_id",
    			"limit"			=> "$limit",
    	));
    	foreach ($trends_list as $k=>$v)
    	{
    		$trends_id = $v['trends_id'];
    		$trends_gallery_mod = & m("trendsgallery");
    		$trends_gallery_list = $trends_gallery_mod->get(array(
    				"conditions"	=>	"trends_id= $trends_id",
    				"fields"		=>	"img_url,img_desc",
    				"limit"			=> "12"
    		));
    		if ($trends_gallery_list)
    		{
    			$trends_gallery_list['img_url'] = site_url().'/upload_user_photo/liuxing/236x353/'.$trends_gallery_list['img_url'];
    		}
    	
    		$trends_list[$k]['img'] = $trends_gallery_list;
    	}
    	return $trends_list;	 
    }
    
    /**
     * 面料供方获取图片列表
     */
    function get_img_fabric()
    {
    	$trends_list = $this->find(array(
    			"conditions"	=>	"1=1 AND is_fab=1",
    			"order"			=> "sort_order asc",
    			"fields"		=> "trends_id",
    			"limig"			=> "12",
    	));
    	foreach ($trends_list as $k=>$v)
    	{
    		$trends_id = $v['trends_id'];
    		$trends_gallery_mod = & m("trendsgallery");
    		$trends_gallery_list = $trends_gallery_mod->find(array(
    				"conditions"	=>	"trends_id= $trends_id",
    				"fields"		=>	"img_url,img_desc",
    				"limit"			=> "3"
    		));
    		if ($trends_gallery_list)
    		{
    			foreach ($trends_gallery_list as $k1=>$v)
    			{
    				$trends_gallery_list[$k1]['img_url'] = site_url().'/upload_user_photo/liuxing/225x305/'.$v['img_url'];
    			}
    		}
    	
    		$trends_list[$k]['img'] = $trends_gallery_list;
    	}
    	return $trends_list;
    }
    
    /**
     * 
     */
    function edit_img()
    {
    	$trends_gallery_mod = & m("trendsgallery");
    	$trends_gallery_list = $trends_gallery_mod->find(array(
    			"conditions"	=>	"1=1 ",
    			"fields"		=>	"thumb1",
    	));
    	foreach ($trends_gallery_list as $v)
    	{
    		if (substr($v['thumb1'],0,34) == 'upload_user_photo/liuxing/225x305/')
    		{
    			$new_name = substr($v['thumb1'], -36);
    			$trends_gallery_mod->edit($v['img_id'],array('img_url'=>$new_name));
    		}
    	}
    }
    
    
    
    
    
    
    
    
    
    
    
}


?>