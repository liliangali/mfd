<?php
/**
 * 移动客户端
 * @author lirp
 *
 */
class ClientApp extends MallbaseApp
{
    function __construct()
    {
        $this->ClientApp();
    }

    function ClientApp()
    {
        parent::__construct();
    }

    function index()
    {
        $this->assign('title', '衣尚APP下载');

        $this->display('client.html');
    }
}

?>
