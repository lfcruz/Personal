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
