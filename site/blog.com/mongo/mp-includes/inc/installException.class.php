<?php

class installException extends Exception
{

public $vars = array();

public function os() {
    $myos = strtolower(php_uname('s'));
    if (strstr($myos,'linux')) return 'linux';
    if (strstr($myos,'windows')) return 'windows';
    // extend for support os's
    return 'unknown';
}


public function __construct($error,$message,$linux_action='',$win_action='',$fix_function='__a_function_that_cannot_exist__') {
    parent::__construct($error);
    $this->vars['messsage'] = $message;
    $this->vars['linux_action'] = $linux_action;
    $this->vars['win_action'] = $win_action;
    $this->vars['fix_function'] = $fix_function;
}

public function debugMessage()
{
    //error message
    $errorMsg = '<div style="font-size:13px;color:#666;">Debug: Error on line '.$this->getLine().' in '.$this->getFile()
    .': <b>'.$this->getMessage().'</b> error raised</div>';
    return $errorMsg;
}


public function printMessage()
{   
    $debug='';
    if ($GLOBALS['_debug']) $debug = $this->debugMessage();

    $title = $this->getMessage();
    $html = 'An installation error has occured: <b>'.$title
            . '</b><br>We suggest you:<br><pre>';
    if ($this->os() == 'linux') $html .= $this->vars['linux_action'];
    else $html .= $this->vars['win_action'];
    $html .= '</pre>'.$debug;

    if (function_exists('mongopress_simple_page')) {
        // die pretty with a refresh or next
        mongopress_simple_page($title,$html,$_SERVER['REQUEST_URI']);
        die();
    } else {
        // die ugly with text
        print "<h1>$title</h1>$html";
        die();
    }

}

public function jsonMessage()
{   
    $debug='';
    if ($GLOBALS['_debug']) $debug = $this->debugMessage();

    $title = $this->getMessage();
    $text = 'An installation error has occured: '.$title
            . ' We suggest you: ';
    if ($this->os() == 'linux') $html .= $this->vars['linux_action'];
    else $html .= $this->vars['win_action'];
    $html .= $debug;

	$progress['message']=__($title);
	$progress['success']=false;
	echo json_encode($progress);

	die();
}



public function autofix() {
    $fix = $this->vars['fix_function'];
    if ($GLOBALS['_debug'] == true) utils_dump('Autofix function attempted: '.$fix);

    if (function_exists($fix)) return $fix();
    
    return false;
}


}

?>
