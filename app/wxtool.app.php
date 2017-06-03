<?php
class Wxtoolapp extends MallbaseApp
{
    public $mpObj;
    function __construct()
    {
        parent::__construct();
        $this->mpObj = mpObj();
    }

    /**
     * 发送客服消息
     * @param array $data 消息结构{"touser":"OPENID","msgtype":"news","news":{...}}
     * @return boolean|array
     */
    public function sendCustomMessage()
    {

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if(!$id)
        {
            return false;
        }
        $order = m('order');
        $order_info = $order->get_info($id);
        if(!$order_info)
        {
            return false;
        }

        $res = $this->mpObj->valid(1);
        $dir = ROOT_PATH."/static/img/wx2.png";
        $update['media'] = "@$dir";//{"media":'@Path\filename.jpg'}
        $sres = $this->mpObj->uploadMedia($update,'image');
        if(isset($sres['media_id']) && $sres['media_id'])
        {
            $data['touser'] = "oDeaw0QHJEA-jtM2_E2TF5fg7M9E";
            $data['msgtype']  = "image";
            $data['image']['media_id'] = $sres['media_id'];
            $json = $this->mpObj->sendCustomMessage($data);
            return $json;
        }
        return $sres;
    }

}

?>