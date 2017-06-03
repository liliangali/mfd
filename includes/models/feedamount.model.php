<?php

/* 会员 member */
class FeedamountModel extends BaseModel
{
    var $table  = 'feed_amount';
    var $prikey = 'id';
    var $_name  = 'feedamount';
    
    var $_relation = array(
            
    );

    /**
     *
     *@author liang.li <1184820705@qq.com>
     *@2015年4月29日
     */
    function getType()
    {
        $arr = array(
            1=>'大型犬',
            2=>'中型犬',
            3=>'小型犬',

        );
        return $arr;
    }
    
    /**
    *获得ask数组
    *@author liang.li <1184820705@qq.com>
    *@2015年4月29日
    */
    function getTime()
    {
        $arr = array(
            1=>'1-6周',
            2=>'7-9周',
            3=>'1-2月',
            4=>'3-4月',
            5=>'5-7月',
            6=>'8-10月',
            7=>'11月到8岁',
            8=>'8岁以上',
            9=>'5-8月',
            10=>'9-12月',
            11=>'9-12月',
            12=>'1岁到7岁',
            13=>'7岁以上',
            14=>'3-5月',
            15=>'6-10月',
            16=>'11-15月',
            17=>'16月到6岁',
            18=>'6岁以上',
        );
        return $arr;
    }

    /**
     *
     *@author liang.li <1184820705@qq.com>
     *@2015年4月29日
     */
    function getBody()
    {
        $arr = array(
            1=>'过瘦',
            2=>'稍瘦',
            3=>'正常',
            4=>'稍胖',
            5=>'狗胖子',

        );
        return $arr;
    }

    /**
     *
     *@author liang.li <1184820705@qq.com>
     *@2015年4月29日
     */
    function getRun()
    {
        $arr = array(
            1=>'0-0.5h',
            2=>'0.5-1h',
            3=>'1-3h',
            4=>'＞3h',
        );
        return $arr;
    }
}

?>