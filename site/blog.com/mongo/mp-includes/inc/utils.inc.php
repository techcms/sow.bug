<?php

function utils_os() {
utils_dump(php_uname('s'));
}

function utils_get_os() {
    $myos = strtolower(php_uname('s'));
    if (strstr($myos,'linux')) return 'linux';
    if (strstr($myos,'windows')) return 'windows';
    // extend for supported os's
    return 'unknown';
}


function utils_version_compare($required,$value) {
    $req = explode('.',$required);
    $got = explode('.',$value);
	$req_len = count($req);
	$req_count = 1;
    foreach ($req as $k => $v) {
        if (!isset($got[$k]) || (int)$got[$k] < (int)$v) return false;
		elseif ($got[$k] > $v) return true;
		if($req_count==$req_len){
			if (!isset($got[$k]) || (int)$got[$k] <= (int)$v) return false;
		}
		$req_count++;
    }
    return true;
}


function utils_caller_func() {
	$trace=debug_backtrace();
	
	$caller=$trace[2]; // relative to the calling function

	echo 'called by '.$caller['function'];
	if (isset($caller['class']))
   		echo 'in '.$caller['class'];

}

function utils_dump($obj) { 
	$trace=debug_backtrace();
	if (isset($trace[1])) $caller = $trace[1]; else $caller = $trace[0];
    $func='';
    if (isset($caller['line']))
   		$func = 'line: '. $caller['line'] . ' ';
    if (isset($caller['class']))
   		$func .= $caller['class'] . '->';
	$func .= $caller['function'].'()';
	
    
    if (is_string($obj)) $obj = htmlentities($obj);
	print "<pre style='text-align:left;border:1px solid #ddd;background:white;padding:5px;font-size:12px;'>";
    print "utils_dump <b>$func</b> in file: " . $_SERVER['PHP_SELF'] . "\n\n";
	print_r($obj);
	print "</pre>";
}

function utils_content_data_merge($content,$data=array(),$key_format='{%*%}') 
{
    // keyformat = {%_key_name_%} - default.

    list($key_start,$key_end) = explode('*',$key_format);

    // 1 - get keys from content
    if (is_array($data)) $keys = array_keys($data); else $keys = array();

    // 2 - merge data with content
  	$count = 0;
	while ($count < 100 && preg_match("/$key_start(.+?)$key_end/",$content)) { 
		$count++;
		foreach ($keys as $key) {
			$content = str_replace($key_start.$key.$key_end,$data[$key],$content);
		}
	}

    // 3 - remove missing keys
	$content = preg_replace("/$key_start(.+?)$key_end/",'',$content); // strip out unmatched vars

    return $content;
}
