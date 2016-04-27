<?php
class cryptChain {
    private $skey = 'vu3DH{z|qQx6ROcK)s6?"1+?c3g(^#E38$Vd8kaJa2MG4A9C4XmuhkP540`nN0X';
    public $charChain = null;

    private  function encode($salt = null){ 
        if($salt){
            $text = $salt;
        }else {
            $text = $this->charChain;
        }
        if(!$text){return false;}
        //$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv_size = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_3DES, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        return base64_encode($crypttext); 
    }

    private function decode($salt = null){
        if($salt){
            $text = $salt;
        }else {
            $text = $this->charChain;
        }
        if(!$text){return false;}
        $crypttext = base64_decode($text); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_3DES, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return $decrypttext;
    }
    
    public function pwdHash($salt = null){
        $result = null;
        if(!$salt){
            $salt = openssl_random_pseudo_bytes(16,$cstrong);
            $result = Array("pwd_hash" => hash_hmac(sha512, $this->charChain, $salt),
                            "iterativechain" => $this->encode($salt));
        }else {
            $salt = $this->decode($salt);
            $this->charChain = $this->decode();
            $result = hash_hmac(sha512, $this->charChain, $salt);
        }
        return $result;
    }
}
?>
