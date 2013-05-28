<?php

require_once(dirname(__FILE__).'/includes.php');

$mp_media_object_id = sanitize_text_field($_GET['id']);
$nonce = sanitize_text_field($_GET['nonce']);
$start_count = (int)sanitize_text_field($_GET['start']);

if(!mp_verify_nonce($nonce,'media_view')){
    $progress['success'] = false;
    $progress['message'] = __('Unidentified Object in The Imperial Vortex!!!');
    echo json_encode($progress);
    return false;
}

$mp_media_object_mongo_id = new MongoId($mp_media_object_id);
$m = mongopress_load_m();
$mp = mongopress_load_mp();
$options = $mp->options();
$db = $m->$options['db_name'];
$grid = $db->getGridFS();
$download = $grid->findOne(
    array("_id" => $mp_media_object_mongo_id)
);
$file_object = $download->file;
if($start_count>0){
    if($file_object['downloads']<$start_count){
        $grid->update(array("_id" => $mp_media_object_mongo_id), array('$inc' => array("downloads" => $start_count)));
    }else{
        $grid->update(array("_id" => $mp_media_object_mongo_id), array('$inc' => array("downloads" => 1)));
    }
}else{
    $grid->update(array("_id" => $mp_media_object_mongo_id), array('$inc' => array("downloads" => 1)));
}
header('Content-type: application/zip');
header('Content-Disposition: attachment; filename="'.$download->file['actual'].'"');
echo $download->getBytes();
?>
