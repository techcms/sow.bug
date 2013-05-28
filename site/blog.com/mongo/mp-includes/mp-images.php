<?php
require_once(dirname(__FILE__).'/includes.php');

$mp_media_object_id = sanitize_text_field($_GET['id']);
$nonce = sanitize_text_field($_GET['nonce']);

if(!mp_verify_nonce($nonce,'media_view','public')){
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
$image = $grid->findOne(
    array("_id" => $mp_media_object_mongo_id)
);
header('Content-type: '.$image->file['type']);
echo $image->getBytes();
$grid->update(array("_id" => $mp_media_object_mongo_id), array('$inc' => array("views" => 1)));
?>
