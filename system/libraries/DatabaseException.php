<?php
/**
*
*/
final class DatabaseException extends Exception
{
    public $message;
    public $errno;
    /**
    * 
    */
    public function __construct($message, $errorno, $ref_file, $ref_line)
    {
        $this->message = $message;
        $this->errno = $errorno;
    }
    /**
    * override __tostring
    */
    public function __toString()
    {
        return ("Error: $this->code - $this->message");
    }
}