<?php
/**
* 多语言配置
* @author xiao5 <xiao5.china@gmail.com>
* @version $Id: config.php 4291 2015-05-30 02:41:45Z gaofei $
* @copyright Copyright 2014 cotte.com
* @package confing 
*/
/**
 * 定义配置返回格式.
 * 以语言项key为下标返回所需要数组
 */
return array (
   'sc-utf-8'=>array('db'=>'mysql://root:root@127.0.0.1:3306/mfd','tempdir'=>''),
   'tc-utf-8'=>array('db'=>'mysql://root:root@127.0.0.1:3306/local_alicaifeng_tc','tempdir'=>''),
   'en-utf-8'=>array('db'=>'mysql://root:root@127.0.0.1:3306/local_alicaifeng_en','tempdir'=>''),
);

?>