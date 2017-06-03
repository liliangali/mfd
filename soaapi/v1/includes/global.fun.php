<?php
/**
 * 所有项目公共方法集
 *
 * 想在多个子项目中做同样的操作太难了 所以加了这个文件  在入口文件中引入即可
 *
 * @author Ruesin
 */







/**
 *  导入根目录下的一个类
 *
 * @author Ruesin
 */
function imports()
{
    $c = func_get_args();
    if (empty($c))
    {
        return;
    }
    array_walk($c, create_function('$item, $key', 'include_once(ROOT_PATH . \'/includes/libraries/\' . $item . \'.php\');'));
}


/**
 * 获取根目录下的一个模型
 *
 * @author Ruesin
 */
function &mr($model_name, $params = array(), $is_new = false)
{
    static $models = array();
    $model_hash = md5($model_name . var_export($params, true));
    if ($is_new || !isset($models[$model_hash]))
    {
        $model_file = ROOT_PATH . '/includes/models/' . $model_name . '.model.php';
        if (!is_file($model_file))
        {
            /* 不存在该文件，则无法获取模型 */
            return false;
        }
        include_once($model_file);
        $model_name = ucfirst($model_name) . 'Model';
        if ($is_new)
        {
            return new $model_name($params, db());
        }
        $models[$model_hash] = new $model_name($params, db());
    }

    return $models[$model_hash];
}

/**
 * 获得用户的真实IP地址
 *
 * @return  string
 */
function real_ips()
{
    static $realip = NULL;

    if ($realip !== NULL)
    {
        return $realip;
    }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip)
            {
                $ip = trim($ip);

                if ($ip != 'unknown')
                {
                    $realip = $ip;

                    break;
                }
            }
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            if (isset($_SERVER['REMOTE_ADDR']))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $realip = '0.0.0.0';
            }
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_CLIENT_IP'))
        {
            $realip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $realip = getenv('REMOTE_ADDR');
        }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
}

