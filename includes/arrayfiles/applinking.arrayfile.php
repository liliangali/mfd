<?php
class ApplinkingArrayfile extends BaseArrayfile 
{
    function __construct()
    {
        $this->DatacallArrayfile();
    }
    
    function DatacallArrayfile()
    {
        $this->_filename = ROOT_PATH . '/data/settings.inc.php';
    }
    
}
?>