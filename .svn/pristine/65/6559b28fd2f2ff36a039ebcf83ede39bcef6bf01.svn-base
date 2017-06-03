<?php
include_once 'SDKConfig.formal.php';
include_once 'PublicEncrypte.php';
include_once 'common.php';
include_once 'log.class.php';
class SecureUtil {
    protected static $instance = null;
    /**
     * Gets singleton instance of FirePHP
     *
     * @param boolean $AutoCreate
     * @return FirePHP
     */
    public static function getInstance($AutoCreate = false)
    {
        if ($AutoCreate===true && !self::$instance) {
            self::init();
        }
        return self::$instance;
    }

    /**
     * Creates FirePHP object and stores it for singleton access
     *
     * @return FirePHP
     */
    public static function init()
    {
        return self::setInstance(new self());
    }

// 初始化日志
    private  $log ;
    public function __construct(){
        $this->log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
    }
    /**
     * 签名
     *
     * @param String $params_str
     */
    public function sign(&$params,$type="bank") {


        $this->log ->LogInfo ( '=====签名报文开始======' );
        if(isset($params['transTempUrl'])){
            unset($params['transTempUrl']);
        }
        // 转换成key=val&串
        $params_str = coverParamsToString ( $params );
        $this->log ->LogInfo ( "签名key=val&...串 >" . $params_str );

        $params_sha1x16 = sha1 ( $params_str, FALSE );
        $this->log ->LogInfo ( "摘要sha1x16 >" . $params_sha1x16 );
        // 签名证书路径
        if ($type == "bank") {
            $cert_path = SDK_SIGN_CERT_PATH_BANK;
        } else{
            $cert_path = SDK_SIGN_CERT_PATH;
        }
        $private_key = $this->getPrivateKey ( $cert_path );
        // 签名
        $sign_falg = openssl_sign ( $params_sha1x16, $signature, $private_key, OPENSSL_ALGO_SHA1 );
        if ($sign_falg) {
            $signature_base64 = base64_encode ( $signature );
            $this->log ->LogInfo ( "签名串为 >" . $signature_base64 );
            $params ['signature'] = $signature_base64;
        } else {
            $this->log ->LogInfo ( ">>>>>签名失败<<<<<<<" );
        }
        $this->log ->LogInfo ( '=====签名报文结束======' );
    }

    /**
     * 验签
     *
     * @param String $params_str
     * @param String $signature_str
     */
    public function verify($params) {
        // 公钥
        $public_key = $this->getPulbicKeyByCertId ( $params ['certId'] );
    //	echo $public_key.'<br/>';
        // 签名串
        $signature_str = $params ['signature'];
        unset ( $params ['signature'] );
        $params_str = coverParamsToString ( $params );
        $this->log ->LogInfo ( '报文去[signature] key=val&串>' . $params_str );
        $signature = base64_decode ( $signature_str );
        $this->log ->LogInfo ( 'base64_decode[signature]>' . $signature );
    //	echo date('Y-m-d',time());
        $params_sha1x16 = sha1 ( $params_str, FALSE );
        $this->log ->LogInfo ( '摘要shax16>' . $params_sha1x16 );
        $isSuccess = openssl_verify ( $params_sha1x16, $signature,$public_key, OPENSSL_ALGO_SHA1 );
        $this->log ->LogInfo ( $isSuccess ? '验签成功' : '验签失败' );
        return $isSuccess;
    }

    /**
     * 根据证书ID 加载 证书
     *
     * @param unknown_type $certId
     * @return string NULL
     */
    public function getPulbicKeyByCertId($certId) {

        $this->log ->LogInfo ( '报文返回的证书ID>' . $certId );
        // 证书目录
        $cert_dir = SDK_VERIFY_CERT_DIR;
        $this->log ->LogInfo ( '验证签名证书目录 :>' . $cert_dir );
        $handle = opendir ( $cert_dir );
        if ($handle) {
            while ( $file = readdir ( $handle ) ) {
                clearstatcache ();
                $filePath = $cert_dir . '/' . $file;
                if (is_file ( $filePath )) {
                    if (pathinfo ( $file, PATHINFO_EXTENSION ) == 'cer') {
                        if ($this->getCertIdByCerPath ( $filePath ) == $certId) {
                            closedir ( $handle );
                            $this->log ->LogInfo ( '加载验签证书成功' );
                            return $this->getPublicKey ( $filePath );
                        }
                    }
                }
            }
            $this->log ->LogInfo ( '没有找到证书ID为[' . $certId . ']的证书' );
        } else {
            $this->log ->LogInfo ( '证书目录 ' . $cert_dir . '不正确' );
        }
        closedir ( $handle );
        return null;
    }

    /**
     * 取证书ID(.pfx)
     *
     * @return unknown
     */
    public function getCertId($cert_path) {
        $pkcs12certdata = file_get_contents ( $cert_path );

        openssl_pkcs12_read ( $pkcs12certdata, $certs, SDK_SIGN_CERT_PWD );
        $x509data = $certs ['cert'];
        openssl_x509_read ( $x509data );
        $certdata = openssl_x509_parse ( $x509data );
        $cert_id = $certdata ['serialNumber'];
        return $cert_id;
        
    }

    /**
     * 取证书ID(.cer)
     *
     * @param unknown_type $cert_path
     */
    public function getCertIdByCerPath($cert_path) {
        $x509data = file_get_contents ( $cert_path );
        openssl_x509_read ( $x509data );
        $certdata = openssl_x509_parse ( $x509data );
        $cert_id = $certdata ['serialNumber'];
        return $cert_id;
    }

    /**
     * 签名证书ID
     *
     * @return unknown
     */
    public function getSignCertId($type = 'bank') {
        // 签名证书路径
//         if ($type == "bank") {
            return $this->getCertId ( SDK_SIGN_CERT_PATH_BANK );
//         } else {
//             return $this->getCertId ( SDK_SIGN_CERT_PATH );
//         }
    }
    public function getEncryptCertId() {
        // 签名证书路径
        return $this->getCertIdByCerPath ( SDK_ENCRYPT_CERT_PATH );
    }

    /**
     * 取证书公钥 -验签
     *
     * @return string
     */
    public function getPublicKey($cert_path) {
        return file_get_contents ( $cert_path );
    }
    /**
     * 返回(签名)证书私钥 -
     *
     * @return unknown
     */
    public function getPrivateKey($cert_path) {
        $pkcs12 = file_get_contents ( $cert_path );
        openssl_pkcs12_read ( $pkcs12, $certs, SDK_SIGN_CERT_PWD );
        return $certs ['pkey'];
    }

    /**
     * 加密 卡号
     *
     * @param String $pan
     *        	卡号
     * @return String
     */
    public function encryptPan($pan) {
        $cert_path = MPI_ENCRYPT_CERT_PATH;
        $public_key = $this->getPublicKey ( $cert_path );

        openssl_public_encrypt ( $pan, $cryptPan, $public_key );
        return base64_encode ( $cryptPan );
    }
    /**
     * pin 加密
     *
     * @param unknown_type $pan
     * @param unknown_type $pwd
     * @return Ambigous <number, string>
     */
    public function encryptPin($pan, $pwd) {
        $cert_path = SDK_ENCRYPT_CERT_PATH;
        $public_key = $this->getPublicKey ( $cert_path );

        return EncryptedPin ( $pwd, $pan, $public_key );
    }
    /**
     * cvn2 加密
     *
     * @param unknown_type $cvn2
     * @return unknown
     */
    public function encryptCvn2($cvn2) {
        $cert_path = SDK_ENCRYPT_CERT_PATH;
        $public_key = $this->getPublicKey ( $cert_path );

        openssl_public_encrypt ( $cvn2, $crypted, $public_key );

        return base64_encode ( $crypted );
    }
    /**
     * 加密 有效期
     *
     * @param unknown_type $certDate
     * @return unknown
     */
    public function encryptDate($certDate) {
        $cert_path = SDK_ENCRYPT_CERT_PATH;
        $public_key = $this->getPublicKey ( $cert_path );

        openssl_public_encrypt ( $certDate, $crypted, $public_key );

        return base64_encode ( $crypted );
    }

    /**
     * 加密 数据
     *
     * @param unknown_type $certDatatype
     * @return unknown
     */
    public function encryptDateType($certDataType) {
        $cert_path = SDK_ENCRYPT_CERT_PATH;
        $public_key = $this->getPublicKey ( $cert_path );

        openssl_public_encrypt ( $certDataType, $crypted, $public_key );

        return base64_encode ( $crypted );
    }
}