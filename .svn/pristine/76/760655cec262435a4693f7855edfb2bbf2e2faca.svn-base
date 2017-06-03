<?php

class PaynotifysApp extends FrontendApp
{
    public function weixin(){
        
        $data = file_get_contents("php://input");
        if (is_array($data)){
            file_put_contents(ROOT_PATH.'/upload/sin1.txt', serialize($data));
            file_put_contents(ROOT_PATH.'/upload/sin2.txt', serialize($GLOBALS['HTTP_RAW_POST_DATA']));
        }
        file_put_contents(ROOT_PATH.'/upload/sin3.txt', $data);
        file_put_contents(ROOT_PATH.'/upload/sin4.txt', $GLOBALS['HTTP_RAW_POST_DATA']);
        
    }
    
    function sintest(){
        $cs1 = file_get_contents(ROOT_PATH.'/upload/sin1.txt');
        $cs2 = file_get_contents(ROOT_PATH.'/upload/sin2.txt');
        $cs3 = file_get_contents(ROOT_PATH.'/upload/sin3.txt');
        $cs4 = file_get_contents(ROOT_PATH.'/upload/sin4.txt');
        print_r($cs1);
        echo '<br>';
        print_r($cs2);
        echo '<br>';
        print_r($cs3);
        echo '<br>';
        print_r($cs4);
    }
}
