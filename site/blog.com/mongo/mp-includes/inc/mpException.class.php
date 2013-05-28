<?php
/* TODO - make this a pretty printed exception! - see installException for reference */

class mpException extends Exception
{

public function errorMessage()
{
    //error message
    $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
    .': <b>'.$this->getMessage().'</b> error raised';
    return $errorMsg;
}

}

?>
