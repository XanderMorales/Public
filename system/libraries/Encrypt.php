<?
/*
yum install mhash
yum install mcrypt
yum install php-mhash
yum install php-mcrypt
*/
Class Encrypt
{
    private $mykey;
    private $create_iv;
    public $mystring;
    /**
    * @desc $action valid settings: encrypt or decrypt, $string_in is data to convert;
    */
    function __construct($action, $string_in)
    {
        $this->mykey = FrameworkConfig::$setting['encryption.key'];
        $this->create_iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $this->mystring = ($action == 'decrypt') ? $this->decrypt_text(base64_decode($string_in)) : base64_encode($this->encrypt_text($string_in));
    }
    private function decrypt_text($string_in)
    {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->mykey, $string_in, MCRYPT_MODE_ECB, $this->create_iv));
    }
    private function encrypt_text($string_in)
    {
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->mykey, $string_in, MCRYPT_MODE_ECB, $this->create_iv);
    }
}
?>