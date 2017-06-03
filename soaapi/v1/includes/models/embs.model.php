<?php
/**
 * 刺绣数据操作类
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: embs.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 redcollar
 */
class EmbsModel extends BaseModel
{
    var $table  = 'embs';
    var $prikey = 'id';
    var $_name  = 'embs';

    /**
     * 刺绣数据
     * 
     * @version 1.0.0 (Jan 22, 2015)
     * @author Ruesin
     */
    function _embs($condition){
    	
        $arr = $this->find(array(
                'conditions' =>$condition." AND is_active=1",
        		'index_key' => '',
        ));
        
        foreach ($arr as $key=>$row){
//             $res[$row['emb']]['name'] = $row['value'];
            if ($row['src']) 
            {
            	$arr[$key]['src'] = SITE_URL.$row['src'];
            }
          /*   $res[$row['emb']]['src']  = $row['src'];
            $res[$row['emb']]['key']  = $row['src']; */
        }
        return $arr;
    }
    
    /**
    *  根据Key获得emb
    * @version 1.0.0
    * @author liang.li <1184820705@qq.com>
    * @2015-3-3
    */
    function getByKey($key) 
    {
    	$key = intval($key);
    	$emb_info = $this->get(" `key` = $key ");
    	if ($emb_info['src']) 
    	{
    		$emb_info['src'] = SITE_URL.$emb_info['src'];
    	}
    	return $emb_info;
    }
}
