<?php
namespace App;
use App\Models\WxConfig;
use EasyWeChat\Foundation\Application;

/**
 * Created by PhpStorm.
 * User: tandi
 * Date: 17/5/26
 * Time: 下午5:02
 */
class Helper
{
    /**
     * array_column()函数兼容低版本
     *
     * 获取二维数组中的元素
     *
     * @return void
     */
    public static function i_array_column($input, $columnKey, $indexKey = null)
    {
        if (! function_exists('array_column')) {
            $columnKeyIsNumber = (is_numeric($columnKey)) ? true : false;
            $indexKeyIsNull = (is_null($indexKey)) ? true : false;
            $indexKeyIsNumber = (is_numeric($indexKey)) ? true : false;
            $result = array();
            foreach ((array) $input as $key => $row) {
                if ($columnKeyIsNumber) {
                    $tmp = array_slice($row, $columnKey, 1);
                    $tmp = (is_array($tmp) && ! empty($tmp)) ? current($tmp) : null;
                } else {
                    $tmp = isset($row[$columnKey]) ? $row[$columnKey] : null;
                }
                if (! $indexKeyIsNull) {
                    if ($indexKeyIsNumber) {
                        $key = array_slice($row, $indexKey, 1);
                        $key = (is_array($key) && ! empty($key)) ? current($key) : null;
                        $key = is_null($key) ? 0 : $key;
                    } else {
                        $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                    }
                }
                $result[$key] = $tmp;
            }
            return $result;
        } else {
            return array_column($input, $columnKey, $indexKey);
        }
    }

    /**
     * array_column()函数兼容低版本
     *
     * 二维数组的值作为建
     *
     * @return void
     */
    public static function v_array_column($array,$input)
    {
        return array_reduce($array,function(&$newArray,$v) use($input){
            $newArray[$v[$input]] = $v;
            return $newArray;
        });
    }

    /**
     *
     * 生成二维码
     * @type:
     * @return void
     */
    public static function qrcode($qid,$qtype=1,$limit=30 * 24 * 3600)
    {
        $application = new Application(config("wechat.open_app_config"));
        $app = $application->open_platform->createAuthorizerApplication(config("wechat.app_id"), WxConfig::getRefreshToken());
        $qrcode = $app->qrcode;
        if($qtype == 1)
        {
            $result = $qrcode->forever($qid);
        }
        else
        {
            $result =  $qrcode->temporary($qid, $limit);
        }
        return $qrcode->url($result->ticket);
    }
}