<?php
/**
* 根据ip查询地理位置
* @author xiao5 <xiao5.china@gmail.com>
* @version $Id: ipCity.class.php 4291 2015-05-30 02:41:45Z gaofei $
* @copyright Copyright 2014 mfd.com
* @package libraries 
*/

/**
* @deprecated 根据ip查询地理位置
* @see ipCity
* @version 1.0.0 (2014-11-13)
* @author xiao5 <xiao5.china@gmail.com>
* @package libraries
*/
class ipCity {
    /**
     * 百度申请ak
     * @access private
     * @var integer|string
     */
    var $_ak = '3qX7yqbTKVlRMQLUkZNDgVTH';
    
    /**
     * 启用签名生成的sk
     * @access private
     * @var integer|string
     */
    var $_sk = '4E51D4Dd25a61946e57e105a3ebe840f';
    
    /**
     * @deprecated 构造函数
     * @return void
     * @access public
     */
    function __construct()
    {
    
    }
    /**
    * @deprecated 计算SN签名算法
    * @param string $ak access key
    * @param string $sk secret key
    * @param string $url url值，例如: /geosearch/nearby 
    * @param array  $querystring_arrays 参数数组，key=>value形式。在计算签名后不能重新排序，也不能添加或者删除数据元素
    * @param string $method 只能为'POST'或者'GET'
    * @return string
    * @access public
    * @see caculateAKSN
    * @version 1.0.0 (2014-11-13)
    * @author Xiao5
    */
    public function caculateAKSN($ak, $sk, $url, $querystring_arrays, $method = 'GET'){
        if ($method === 'POST'){
            ksort($querystring_arrays);
        }
        $querystring = http_build_query($querystring_arrays);
        return md5(urlencode($url.'?'.$querystring.$sk));
    }
    /**
    * @deprecated 根据ip地址获取对应所在城市
    * @param type $userip 用户IP地址
    * @return string
    * @access public
    * @see getCity
    * @version 1.0.0 (2014-11-13)
    * @author Xiao5
    */
    public function getCity($userip=''){
        $data = array();

        /* 判断IP地址是否有效 */
        if ($userip)
        {
            if ( preg_match( "/^([0-9]{1,3}.){3}[0-9]{1,3}$/", $userip ) == 0 )
            {
                return $data;
            }
        }
       
        /* IP查询的完整URL */
        $url = 'http://api.map.baidu.com/location/ip?ak=%s&ip=%s&coor=%s';
        
        /* 这个就是用来计算SN用的URI，根据wiki说的，不带域名和参数 */
        $uri = 'location/ip';
        
        /* 参数coor的值 */
        $coor = 'bd09ll';
        
        /* 这一步相当重要，上面提供的那个计算SN的函数用的URI是不带参数的，但是GET方式却是需要用带参的URI进行SN计算的 */
        $querystring_arrays = array(
            'ak' => $this->_ak,
            'ip' => $userip,
            'coor' => $coor
        );
        /* 用上面的函数计算一个SN */
        $sn = $this->caculateAKSN($this->_ak, $this->_sk, $uri, $querystring_arrays);
        
        /* 构造完整的访问URL */
        $target = sprintf($url,$this->_ak, urlencode($userip), $coor);
        $data = file_get_contents($target);
        $data = json_decode($data,1);
        
        return $data;
    }
    /**
     * 利用QQ地址库
     * 根据ip地址获取对应所在城市
     * @param type $userip 用户IP地址
     * @return string
     */
    public function getGeoip( $userip, $dat_path = '' ) 
    {
        //IP数据库路径，这里用的是QQ IP数据库 2014年11月05日 纯真版
        empty( $dat_path ) && $dat_path = ROOT_PATH . 'includes/codetable/QQWry.dat';
        //判断IP地址是否有效
        if ( preg_match( "/^([0-9]{1,3}.){3}[0-9]{1,3}$/", $userip ) == 0 ) 
        {
            return 'IP Address Invalid';
        }
        //打开IP数据库
        if ( !$fd = @fopen( $dat_path, 'rb' ) ) 
        {
            return 'IP data file not exists or access denied';
        }
        //explode函数分解IP地址，运算得出整数形结果
        $userip = explode( '.', $userip );
        $useripNum = $userip[0] * 16777216 + $userip[1] * 65536 + $userip[2] * 256 + $userip[3];
        //获取IP地址索引开始和结束位置
        $DataBegin = fread( $fd, 4 );
        $DataEnd = fread( $fd, 4 );
        $useripbegin = implode( '', unpack( 'L', $DataBegin ) );
        if ( $useripbegin < 0 )
            $useripbegin += pow( 2, 32 );
        $useripend = implode( '', unpack( 'L', $DataEnd ) );
        if ( $useripend < 0 )
            $useripend += pow( 2, 32 );
        $useripAllNum = ($useripend - $useripbegin) / 7 + 1;
        $BeginNum = 0;
        $EndNum = $useripAllNum;
        //使用二分查找法从索引记录中搜索匹配的IP地址记录
        while ( $userip1num > $useripNum || $userip2num < $useripNum ) 
        {
            $Middle = intval( ($EndNum + $BeginNum) / 2 );
            //偏移指针到索引位置读取4个字节
            fseek( $fd, $useripbegin + 7 * $Middle );
            $useripData1 = fread( $fd, 4 );
            if ( strlen( $useripData1 ) < 4 ) 
            {
                fclose( $fd );
                return 'File Error';
            }
            //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
            $userip1num = implode( '', unpack( 'L', $useripData1 ) );
            if ( $userip1num < 0 )
                $userip1num += pow( 2, 32 );
            //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
            if ( $userip1num > $useripNum ) 
            {
                $EndNum = $Middle;
                continue;
            }
            //取完上一个索引后取下一个索引
            $DataSeek = fread( $fd, 3 );
            if ( strlen( $DataSeek ) < 3 ) 
            {
                fclose( $fd );
                return 'File Error';
            }
            $DataSeek = implode( '', unpack( 'L', $DataSeek . chr( 0 ) ) );
            fseek( $fd, $DataSeek );
            $useripData2 = fread( $fd, 4 );
            if ( strlen( $useripData2 ) < 4 ) 
            {
                fclose( $fd );
                return 'File Error';
            }
            $userip2num = implode( '', unpack( 'L', $useripData2 ) );
            if ( $userip2num < 0 )
                $userip2num += pow( 2, 32 );
            //找不到IP地址对应城市
            if ( $userip2num < $useripNum ) 
            {
                if ( $Middle == $BeginNum ) 
                {
                    fclose( $fd );
                    return 'No Data';
                }
                $BeginNum = $Middle;
            }
        }
    
        $useripFlag = fread( $fd, 1 );
        if ( $useripFlag == chr( 1 ) ) 
        {
            $useripSeek = fread( $fd, 3 );
            if ( strlen( $useripSeek ) < 3 ) 
            {
                fclose( $fd );
                return 'System Error';
            }
            $useripSeek = implode( '', unpack( 'L', $useripSeek . chr( 0 ) ) );
            fseek( $fd, $useripSeek );
            $useripFlag = fread( $fd, 1 );
        }
        if ( $useripFlag == chr( 2 ) ) 
        {
            $AddrSeek = fread( $fd, 3 );
            if ( strlen( $AddrSeek ) < 3 ) 
            {
                fclose( $fd );
                return 'System Error';
            }
            $useripFlag = fread( $fd, 1 );
            if ( $useripFlag == chr( 2 ) ) 
            {
                $AddrSeek2 = fread( $fd, 3 );
                if ( strlen( $AddrSeek2 ) < 3 ) 
                {
                    fclose( $fd );
                    return 'System Error';
                }
                $AddrSeek2 = implode( '', unpack( 'L', $AddrSeek2 . chr( 0 ) ) );
                fseek( $fd, $AddrSeek2 );
            } else {
                fseek( $fd, -1, SEEK_CUR );
            }
            while ( ($char = fread( $fd, 1 )) != chr( 0 ) )
                $useripAddr2 .= $char;
            $AddrSeek = implode( '', unpack( 'L', $AddrSeek . chr( 0 ) ) );
            fseek( $fd, $AddrSeek );
            while ( ($char = fread( $fd, 1 )) != chr( 0 ) )
                $useripAddr1 .= $char;
        } else {
             
            fseek( $fd, -1, SEEK_CUR );
            while ( ($char = fread( $fd, 1 )) != chr( 0 ) )
                $useripAddr1 .= $char;
            $useripFlag = fread( $fd, 1 );
            if ( $useripFlag == chr( 2 ) ) 
            {
                $AddrSeek2 = fread( $fd, 3 );
                if ( strlen( $AddrSeek2 ) < 3 ) 
                {
                    fclose( $fd );
                    return 'System Error';
                }
                $AddrSeek2 = implode( '', unpack( 'L', $AddrSeek2 . chr( 0 ) ) );
                fseek( $fd, $AddrSeek2 );
            } else {
                fseek( $fd, -1, SEEK_CUR );
            }
            while ( ($char = fread( $fd, 1 )) != chr( 0 ) ) {
                $useripAddr2 .= $char;
            }
        }
        fclose( $fd );
    
        //返回IP地址对应的城市结果
        if ( preg_match( '/http/i', $useripAddr2 ) ) 
        {
            $useripAddr2 = '';
        }
        //         $useripaddr = "$useripAddr1 $useripAddr2";
        $useripaddr = "$useripAddr1";
        $useripaddr = preg_replace( '/CZ88.Net/is', '', $useripaddr );
        $useripaddr = preg_replace( '/^s*/is', '', $useripaddr );
        $useripaddr = preg_replace( '/s*$/is', '', $useripaddr );
        if ( preg_match( '/http/i', $useripaddr ) || $useripaddr == '' ) 
        {
            $useripaddr = 'No Data';
        } 
        elseif ( !$this->isUtf8( $useripaddr ) ) 
        {
            $useripaddr = iconv( 'GBK', 'UTF-8', $useripaddr );
        }
        return $useripaddr;
    }
    /**
     * 判断是否我utf-8编码的字符串
     * @param type $string
     * @return boolean
     */
    private function isUtf8( $string ) 
    {
        if ( preg_match( "/^([" . chr( 228 ) . "-" . chr( 233 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}){1}/", $string ) == true || preg_match( "/([" . chr( 228 ) . "-" . chr( 233 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}){1}$/", $string ) == true || preg_match( "/([" . chr( 228 ) . "-" . chr( 233 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}){2,}/", $string ) == true ) 
        {
            return true;
        } 
        else 
        {
            return false;
        }
    }
    function getIp()
    {
        $realip = '';
        $unknown = 'unknown';
        if (isset($_SERVER))
        {
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown))
            {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach($arr as $ip)
                {
                    $ip = trim($ip);
                    if ($ip != 'unknown')
                    {
                        $realip = $ip;
                        break;
                    }
                }
            }
            else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown))
            {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }
            else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }else
            {
                $realip = $unknown;
            }
        }
        else
        {
            if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown))
            {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            }
            else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown))
            {
                $realip = getenv("HTTP_CLIENT_IP");
            }
            else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown))
            {
                $realip = getenv("REMOTE_ADDR");
            }
            else
            {
                $realip = $unknown;
            }
        }
        $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;
        return $realip;
    }
    /**
    * @deprecated 通过新浪API接口根据ip获取地理位置
    * @param string $ip 查询的ip地址
    * @return array
    * @access public
    * @see GetIpLookup
    * @version 1.0.0 (2014-11-14)
    * @author Xiao5
    */
    public function GetIpLookup($ip = '')
    {
        if(empty($ip))
        {
            $ip = $this->getIp();
            
            if ($ip == '127.0.0.1')
            {
                /* debug 公司外网ip： 本地环境取得ip 是127.0.0.1 */
//                 $ip = $this->getLocalIp();
                $ip = '218.58.54.237';
            }
        }
        $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
        if(empty($res))
        { 
            return false; 
        }
        $jsonMatches = array();
        preg_match('#\{.+?\}#', $res, $jsonMatches);
        if(!isset($jsonMatches[0]))
        {
            return false; 
        }
        $json = json_decode($jsonMatches[0], true);
        if(isset($json['ret']) && $json['ret'] == 1)
        {
            $json['ip'] = $ip;
            unset($json['ret']);
        }
        else
        {
            return false;
        }
        return $json;
    }
    /**
    * @deprecated php获取本机ip : 本地开发环境的 内网ip
    * @return string
    * @access public
    * @see getLocalIp
    * @version 1.0.0 (2014-11-14)
    * @author Xiao5
    */
   public function getLocalIp()
    {
        $preg = "/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
        //获取操作系统为win2000/xp、win7的本机IP真实地址
        exec("ipconfig", $out, $stats);
        if (!empty($out)) 
        {
            foreach ($out AS $row) 
            {
                if (strstr($row, "IP") && strstr($row, ":") && !strstr($row, "IPv6")) 
                {
                    $tmpIp = explode(":", $row);
                    if (preg_match($preg, trim($tmpIp[1]))) 
                    {
                        return trim($tmpIp[1]);
                    }
                }
            }
        }
        //获取操作系统为linux类型的本机IP真实地址
        exec("ifconfig", $out, $stats);
        if (!empty($out)) 
        {
            if (isset($out[1]) && strstr($out[1], 'addr:')) 
            {
                $tmpArray = explode(":", $out[1]);
                $tmpIp = explode(" ", $tmpArray[1]);
                if (preg_match($preg, trim($tmpIp[0]))) 
                {
                    return trim($tmpIp[0]);
                }
            }
        }
        return '127.0.0.1';
    }
    
    
    /**
     * $splitChar 字段分隔符
     * $file 数据文件文件名
     * $insertType 插入操作类型，包括INSERT,REPLACE
     */
    function loadTxtDataIntoDatabase($splitChar,$file)
    {
        $splitChar = '|';
        $file =  ROOT_PATH . '/includes/textlibrary/BaiduMap_cityCode.txt';
        $sqldata = trim(file_get_contents($file));
        $array = explode("\r\n", $sqldata);
        if ($array)
        {
            $mod_region =& m('region');
            $regions = $mod_region->find();
            foreach ($array as $key=>$r){
                $row[$key] = explode($splitChar, $r);
                $is = 0;
                foreach ($regions as $v)
                {
                    if ($v['region_name'] == $row[$key][0])
                    {
                        $is = 1;
                        $mod_region->edit(array('region_id'=>$v['region_id']),array('citycode'=>$row[$key][1]));
//                         echo 'id=>'.$v['region_id'].'------region_name=>'.$v['region_name'].'-----------'.$row[$key][0]."<br/>";
                    }elseif (strstr($v['region_name'],$row[$key][0])){
                        $is = 1;
                        $mod_region->edit(array('region_id'=>$v['region_id']),array('citycode'=>$row[$key][1]));
                    }
                }

                if (!$is)
                {
                    /* 暂时没有的城市
                                                            襄阳       恩施土家族苗族自治州       神农架林区      湘西土家族苗族自治州      五指山     琼海     儋州      文昌        万宁       东方       定安       屯昌        澄迈       临高
                                                            白沙黎族自治         昌江黎族自治         乐东黎族自治        陵水黎族自治          保亭黎族苗族自治      琼中黎族苗族自治        阿坝藏族羌族自治州      甘孜藏族自治州
                                                            凉山彝族自治州        黔西南布依族苗族自治州        黔东南苗族侗族自治州          黔南布依族苗族自治州     普洱                                    
                    */
//                     echo $row[$key][0]."<br/>";
                }
                
            }
        }
    }
    
    /**
     * 查找父id 逐级返回. 不包括本身
     *
     * @param mix $id
     * @return array
     */
    function getRegionsParents($id){
        $mod_region =& m('region');
        $regions = $mod_region->find();
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($regions, 'region_id', 'parent_id', 'region_name');
        return $tree->getParents($id);
    }
    
    /**
     * 查找父id 逐级返回
     * 
     * 根据cookie中的citycode 获取 省市区级联的 id 字符串形式返回
     *
     * @param mix $id
     * @return sting
     */
    function getRegionsByCode($code=''){
        if (!$code)
        {
            $code = $_COOKIE['cityCode'] ? $_COOKIE['cityCode'] : 236;
        }
        $mod_region =& m('region');
        $conditions = ' citycode ='.$code;
        $regions_info = $mod_region->get(array('conditions' => $conditions));
        if ($regions_info)
        {
            $p = $this->getRegionsParents($regions_info['region_id']);
            array_push($p,$regions_info['region_id']);
            $r = array_filter($p);
            return implode(",",$r);
        }
        
        return '';
       
    }
}