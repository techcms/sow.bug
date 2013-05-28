<?php

$GLOBALS['_debug']=true;
/* collection of all the install and check related functions for mongopress */

function install_get_server() 
{
    $webserver = strtolower($_SERVER["SERVER_SOFTWARE"]);
    if (strstr($webserver, "apache")) return 'apache';
    if (strstr($webserver, "nginx")) return 'nginx';
    // continue list of supported webservers
    return 'other';
}

function install_check($file,$dir='')
{
    $test_file = install_locate($file,$dir);

    if (file_exists($test_file)) return true;
    
    return false;
}

function install_locate($file,$dir='') 
{
    if ($dir) $dir = '/'.$dir;
    return $GLOBALS['_MP']['DOCUMENT_ROOT'].$dir.'/'.$file;
}

function install_get_content($file) 
{
    $default = $GLOBALS['_MP']['DOCUMENT_ROOT'].'/mp-includes/install/'.$file;
    if (file_exists($default)) {
        $c = file_get_contents($default);
        return $c;
    } 
	throw new installException('Default install content not found','Missing the following installation file: ' . $default . ' You may need to download MongoPress again');
}

function install_create($file,$dir='',$data=array())
{
    Global $_MP;
    $test_file = install_locate($file,$dir);

    if (file_exists($test_file)) return false;

    $content = install_get_content($file);
    $content = utils_content_data_merge($content,$data);
    
    $dest_dir = $_MP['DOCUMENT_ROOT'].'/'.$dir;

    if (is_writeable($dest_dir)) {
            file_put_contents($test_file,$content); 
            return true;
    } else {
        throw new installException('Directory Not Writeable','The following directory is not writeable: '.$dest_dir,'chmod 777 '.$dest_dir,'On windows - check permissions as administrator user - ' . $dest_dir);
    }
    
    return false;
}


function install_check_versions() {
    $versions = mongopress_get_versions();
    $errors = false;

    if (utils_version_compare($versions['mp_mins']['php'],$versions['current']['php'])) $errors['php'] = 'passed'; else $errors['php'] = 'failed';

    if( $versions['current']['mongodb'] == 'unknown') $errors['mongodb'] = 'tba'; 
    elseif (utils_version_compare($versions['mp_mins']['mongodb'],$versions['current']['mongodb'])) $errors['mongodb'] = 'passed'; else $errors['mongodb'] = 'failed';

    if (utils_version_compare($versions['mp_mins']['phpd'],$versions['current']['phpd'])) $errors['phpd'] = 'passed'; else $errors['phpd'] = 'failed';
    
    $errors['extensions'] = array();

    foreach ($versions['mp_extensions'] as $extension) {
        if (!extension_loaded($extension)) $errors['extensions'][$extension] = 'failed'; else $errors['extensions'][$extension] = 'passed';

    }

    return $errors;
}


function install_check_versions_passed($verbose = false) {

    $test = install_check_versions();
    $passed = true; $html = false;

    foreach ($test as $k => $v) {
        if (is_array($v)) {
            foreach ($v as $k2 => $v2) {
                if ($v2 == 'failed') {
                    if ($verbose) $html = "Failed: $k2 ";
                    $passed = false;
                }
            }
        } else {
             if ($v == 'failed') {
                    if ($verbose) $html = "Failed: $k ";
                    $passed = false;
            }
        }
    }
    
    if ($verbose && $html) return $html;
    return $passed;
}


?>
