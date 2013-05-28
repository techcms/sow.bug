<?php

class MONGOPRESS_PERMA {
    public function current(){
        if (isset($GLOBALS['_MP']['SLUG'])) $slug = $GLOBALS['_MP']['SLUG'];        
        else {

            $tmp['DOCUMENT_ROOT'] = dirname(dirname(__FILE__)); // assumes rewrites.php is in mp-includes!

            $tmp['HOME'] = str_replace($_SERVER['DOCUMENT_ROOT'],'',$tmp['DOCUMENT_ROOT']);
            if (empty($tmp['HOME'])) $tmp['HOME'] = '/'; else $tmp['HOME'] .= '/';
            $slug = $_SERVER['REQUEST_URI'];
            $qs_pos = strpos($slug,'?');
            if ($qs_pos !== false) $slug = substr($slug,0,$qs_pos);
            $slug = substr($slug,strlen($tmp['HOME']));

        }

        if (substr($slug,-1) == '/') $slug = substr($slug,0,-1);
        
        return $slug;
    }

}
