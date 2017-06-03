<?php

/**
 *    语言项管理
 *
 *    @author    Garbin
 *    @param    none
 *    @return    void
 */
class Lang
{
    /**
     *    获取指定键的语言项
     *
     *    @author    Garbin
     *    @param     none
     *    @return    mixed
     */
    static function &get($key = '')
    {
        if (Lang::_valid_key($key) == false)
        {
            return $key;
        }
        $vkey = $key ? strtokey("{$key}", '$GLOBALS[\'__ECLANG__\']') : '$GLOBALS[\'__ECLANG__\']';
        $tmp = eval('if(isset(' . $vkey . '))return ' . $vkey . ';else{ return $key; }');

        return $tmp;
    }

    /**
     * 验证key的有效性
     *
     * @author Hyber
     * @param string $key
     * @return bool
     */
    static function _valid_key($key)
    {
        if (strpos($key, ' ') !== false)
        {
            return false;
        }
        #todo 暂时只判断是否含有空格
        return true;
    }


    /**
     *    加载指定的语言项至全局语言数据中
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    static function load($lang_file)
    {
        static $loaded = array();
        $old_lang = $new_lang = array();
        $file_md5 = md5($lang_file);
        if (!isset($loaded[$file_md5]))
        {
            $new_lang = Lang::fetch($lang_file);
            $loaded[$file_md5] = $lang_file;
        }
        else
        {
            return;
        }
        $old_lang =& $GLOBALS['__ECLANG__'];
        if (is_array($old_lang))
        {
            $new_lang = array_merge($old_lang, $new_lang);
        }

        $GLOBALS['__ECLANG__'] = $new_lang;
    }

    /**
     *    获取一个语言文件的内容
     *
     *    @author    Garbin
     *    @param     string $lang_file
     *    @return    array
     */
    static function fetch($lang_file)
    {
        return is_file($lang_file) ? include($lang_file) : array();
    }
}

/**
 *    将default.abc类的字符串转为$default['abc']
 *
 *    @author    Garbin
 *    @param     string $str
 *    @return    string
 */
function strtokey($str, $owner = '')
{
    if (!$str)
    {
        return '';
    }
    if ($owner)
    {
        return $owner . '[\'' . str_replace('.', '\'][\'', $str) . '\']';
    }
    else
    {
        $parts = explode('.', $str);
        $owner = '$' . $parts[0];
        unset($parts[0]);
        return strtokey(implode('.', $parts), $owner);
    }
}

?>