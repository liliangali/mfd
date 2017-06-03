<?php

/* 购物车 cart */
class CartModel extends BaseModel
{
    var $table  = 'cart';
    var $prikey = 'rec_id';
    var $_name  = 'cart';
 
    var $_relation = array(
        'belongs_to_store'  => array(
            'type'      =>  BELONGS_TO,
            'model'     =>  'store',
            'reverse'   =>  'has_cart',
        ),
        'belongs_to_goodsspec'  => array(
            'type'      =>  BELONGS_TO,
            'model'     =>  'goodsspec',
            'foreign_key' => 'spec_id',
            'reverse'   =>  'has_cart_items',
        ),
    );

    /**
     *    获取商品数量
     *
     *    @author    Garbin
     *    @return    void
     */
    function get_kinds($sess_id, $user_id = 0)
    {
        $where_user_id = $user_id ? " AND user_id={$user_id}" : '';
        $kinds = $this->db->getOne("SELECT SUM(quantity) as c FROM {$this->table} WHERE session_id='{$sess_id}'{$where_user_id}");

        return $kinds;
    }
    
    
    /**
     * 获取已享受的首推
     *
     * @author Ruesin
     */
    function _get_order_first($user_id = 0)
    {
        if (!$user_id) return false;
    
        $mOfl = &m('orderfirstlog');
    
        $logs = $mOfl->find("user_id = '{$user_id}' ");
        foreach ((array)$logs as $row){
            $res[$row['cloth']] = $row;
        }
        //$this->_firsts = $res;
        return $res;
    }
    /**
     * 获取所有品类的刺绣信息
     *
     * @author Ruesin
     */
    function _format_embs($type = 'code'){

        $mEp  = &m('dict_embs_parent');
        $p = $mEp->find(array(
            'conditions' => " is_show = '1' ",
            'order'      => ' rank ASC',
        ));


        foreach ((array)$p as $row){
            $pIds[$row['id']] = $row['id'];
            $res[$row['cCode']][$row['id']] = $row;
            $resId[$row['cId']][$row['id']] = $row;
        }
        if($pIds){
            $mDict = &m('dict');
            $e = $mDict->find(array(
                'conditions' => " is_display = '1' AND ".db_create_in($pIds,'parentid'),
            ));
        }

        foreach ((array)$e as $row){

            if($p[$row['parentid']]){
                $row['image'] = "http://img.diy.mfd.cn/process/{$row['parentid']}/{$row['id']}_S.png";
                //$row['parentid'];
                $res[$p[$row['parentid']]['cCode']][$row['parentid']]['list'][$row['id']] = $row;
                $resId[$p[$row['parentid']]['cId']][$row['parentid']]['list'][$row['id']] = $row;
            }
        }

        if($type == 'id'){
            return $resId;
        }
        return $res;
    }
    
    public static $foCloth = array(
        
        '0016' => array(
            'fabric' => array(
                'SAK125A' => 'SAK125A',  //两件套只匹配西服
//                 'DBM739A' => 'DBM739A',
                'SAM212A' => 'SAM212A',
            ),
            'price' => 199,
        ),
        
    );
    
	public static $_CUSTOMS = array(
    		//lining 313 6291 103136
    		'0003' => array(
    				'cate_id' => '3',
    				'cate_name' => '西服',
    				'fabric_m' => '2.1', // 面料单号基数
    				'lining_m' => '2', // 里料单号基数
    				'craft' => array(
    						'id' => '435',
    						'name' => '工艺类型',
    						'son' => array(
    								'1230' => '手工艺',
    								'431' => '衬类型'
    						)
    				)
    		),
    		'0004' => array(
    				'cate_id' => '2000',
    				'cate_name' => '西裤',
    				'fabric_m' => '1.5',
    				'lining_m' => '0',
    				'craft' => array(
    						'id' => '2224',
    						'name' => '工艺选择'
    				)
    		),
    		'0005' => array(
    				'cate_id' => '4000',
    				'cate_name' => '马夹',
    				'fabric_m' => '0.8',
    				'lining_m' => '0'
    		)
    		,
    		'0006' => array(
    				'cate_id' => '3000',
    				'cate_name' => '衬衣',
    				'fabric_m' => '2.8',
    				'lining_m' => '0'
    		),
    		'0007' => array(
    				'cate_id' => '6000',
    				'cate_name' => '大衣',
    				'fabric_m' => '1.68',
    				'lining_m' => '2',
    				'craft' => array(
    						'id' => '6409',
    						'name' => '工艺类别'
    				)
    		)
    );
    
    
}

?>