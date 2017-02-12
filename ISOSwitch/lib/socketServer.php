<?php
class socketServer {
    private $sockAddress;
    private $sockPort;
    private $sockResource;
    private $sockClient;
    
    function __construct($vAddress, $vPort) {
        $this->sockAddress = $vAddress;
        $this->sockPort = $vPort;
        
        $this->sockResource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        try {
            socket_bind($this->sockResource, $this->sockAddress, $this->sockPort);
            socket_listen($this->sockResource);
        } catch (Exception $e) {
            echo "Caught exception: ".$e->getMessage()."\n";
            $this->sockResource = null;
        }
    }
    
    function __destruct() {
        socket_close($this->sockResource);
    }
    
    public function openStream() {
        $this->sockClient = socket_accept($this->sockResource);
        return true;
    }
    
    public function inputStream() {
        return socket_read($this->sockClient, 1024);
    }
    
    public function outputStream($vStream) {
        $vresult = true;
        try {
            socket_write($this->sockClient, $vStream);
        } catch (Exception $e) {
            echo "Caught exception: ".$e->getMessage()."\n";
            $vresult = false;
        }   
        return $vresult;
    }
    
    public function closeStream() {
        socket_close($this->sockClient);
        return true;
    }
    
}

class socketProcessor {
    private $ipAddress;
    private $port;
    private $socket;
    private $client;
    private $socketType;
    
    function __construct($vIP,$vPort,$vType) {
        $status = false;
        $this->ipAddress = $vIP;
        $this->port = $vPort;
        $this->socketType = $vType;
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket!!!!");
        switch ($this->socketType){
            case "C":
                if($this->socket){
                $status = true;
                }
                break;
            case "S":
                if($this->socket and socket_bind($this->socket, $this->ipAddress, $this->port)){
                    socket_listen($this->socket);
                    $status = true;
                }
                break;
            default:
                break;
        }
        return $status;
     }
    
    /*function __destruct() {
        socket_set_block($this->socket);
        socket_set_option($this->socket, SOL_SOCKET, SO_LINGER, Array("l_onoff" => 1,"l_linger" => 0));
        socket_close($this->socket);
    }*/

    public function receiveMessage(){
        $this->client = socket_accept($this->socket);
        $inMSG = socket_read($this->client, 1024);
        return $inMSG;
    }
    
    public function returnMessage($outMSG){
        $result = true;
        $result = socket_write($this->client, $outMSG);
        socket_close($this->client);
        return $result;
    }
    
    public function sendMessage($outMSG){
        socket_connect($this->socket, $this->ipAddress, $this->port) or die("Could not open server!!!!");
        socket_write($this->socket, $outMSG);
        $inMSG = socket_read($this->socket, 1024);
        socket_close($this->socket);
        return $inMSG;
    }
}
    
?>
