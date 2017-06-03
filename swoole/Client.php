<?php
class Client
{
    private $client;

    public function __construct() {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);
    }

    public function connect() {
        if( !$this->client->connect("127.0.0.1", 9501 , 1) ) {
            echo "Error: {$this->client->errMsg}[{$this->client->errCode}]\n";
        }

//        fwrite(STDOUT, "请输入消息：");
//        $msg = trim(fgets(STDIN));
//        $this->client->send( $msg );

        $this->client->send(str_repeat("A", 600));
        $data = $this->client->recv(700, 0) or die("recv failed\n");
        echo "recv: " . $data . "\n";

    }
}

$client = new Client();
$client->connect();