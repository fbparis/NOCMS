<?php
define('SESSION_SECRET', '1234');

class Session {
    public $valid = true;
    protected $secret = null;
    protected $encrypt = null;
    
    function __construct($encrypt=false) {
        $this->secret = defined('SESSION_SECRET') ? SESSION_SECRET : '';
        $this->secret .= session_id() . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'];
        $this->encrypt = $encrypt;
        session_start();
        $this->valid = $this->check();
    }
    
    function __destruct() {
        $this->lock();
    }
    
    protected function lock() {
        $_SESSION = array('dat'=>$this->session_values());
        $_SESSION['chk'] = $this->hash($_SESSION['dat']);        
    }
    
    protected function session_values() {
        $sess = serialize($_SESSION);
        if ($this->encrypt) {
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size,MCRYPT_RAND);
            $sess = mcrypt_encrypt(MCRYPT_RIJNDAEL_256,sha1($this->secret,true),$sess,MCRYPT_MODE_ECB,$iv);            
        }
        return $sess;
    }
    
    protected function check() {
        if (!@count($_SESSION)) return true;
        if (@$_SESSION['chk'] && ($_SESSION['chk'] == $this->hash(@$_SESSION['dat']))) {
            if ($this->encrypt) {
                $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,MCRYPT_MODE_ECB);
                $iv = mcrypt_create_iv($iv_size,MCRYPT_RAND);
                $_SESSION['dat'] = mcrypt_decrypt(MCRYPT_RIJNDAEL_256,sha1($this->secret,true),$_SESSION['dat'],MCRYPT_MODE_ECB,$iv);
            }
            $_SESSION = unserialize($_SESSION['dat']);
            return true;
        }
        session_destroy();
        session_start();
        session_regenerate_id(true);
        return false;
    }
    
    protected function hash($s) {
        return sha1($this->secret . $s,false);
    }
}

$Sess = new Session;
?>