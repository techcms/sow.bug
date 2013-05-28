<?php

/* INCLUDE REQUIRED FILES */
require_once(dirname(dirname(__FILE__)).'/ajax-loader.php');

/* GET VARS FROM QUERY */
$selected_collection = sanitize_text_field($_GET['collection']);
$allow_edits = (int)($_GET['allow_edits']);
if(empty($selected_collection)){$selected_collection='objs';}
$offset = sanitize_text_field((int)$_GET['iDisplayStart']);
if(empty($offset)){ $offset=0; }
$limit = sanitize_text_field((int)$_GET['iDisplayLength']);
$sort_direction = sanitize_text_field($_GET['sSortDir_0']);
if($sort_direction=='asc'){ $order_value=-1; }else{ $order_value=1; }
$order_by_id = sanitize_text_field((int)$_GET['iSortCol_0']);
$nonce = sanitize_text_field($_GET['nonce']);
$use_mongo_meantime = sanitize_text_field($_GET['use_mongo_meantime']);
if($use_mongo_meantime!='true'){ $use_mongo_meantime=false; }else{ $use_mongo_meantime=true; }
if($order_by_id==1){
    $order_by = 'type';
}elseif($order_by_id==3){
    $order_by = 'title';
}elseif($order_by_id==4){
    $order_by = 'created';
}elseif($order_by_id==5){
    $order_by = 'updated';
}else{
	$order_by = 'updated';
}

mp_json_nonce_check($nonce,'objects-form');

/* CONNECT TO MONGO */
$mp = mongopress_load_mp();
$default_options = $mp->options();
$m = mongopress_load_m();
$db = $m->$default_options['db_name'];
$users = $db->$default_options['user_names'];
$objs = $db->$default_options['obj_col'];
$slugs = $db->$default_options['slug_col'];
if($selected_collection=='objs'){
    $total_objects = $objs->count();
    if((!empty($order_by))&&(!empty($order_value))){
        $all_objects = $objs->find()->sort(array($order_by => $order_value))->limit($limit)->skip($offset);
    }else{
        $all_objects = $objs->find()->limit($limit)->skip($offset);
    }
    $these_objects = $all_objects->count();
}
$output = array(
    "sEcho" => (int)($_GET['sEcho']),
    "iTotalRecords" => $total_objects,
    "iTotalDisplayRecords" => $these_objects,
    "aaData" => array()
);
foreach($all_objects as $obj) {
    foreach($obj['_id'] as $key => $this_mongo_id){}
    $this_type = $obj['type'];
    $this_slug_id = $obj['slug_id'];
    $this_slug_mongo_id = new MongoId($this_slug_id);
    $this_slug_array = $slugs->findOne(array("_id"=>$this_slug_mongo_id));
    $this_slug = $this_slug_array['slug'];
    $this_title = $obj['title'];
    if($use_mongo_meantime){
		$this_created = mingo_meantime($obj['created'], false, 'd / M / Y');
		$this_updated = mingo_meantime($obj['updated'], false, 'd / M / Y');
    }else{
        $this_created = $obj['created'];
        $this_updated = $obj['updated'];
    }
    if($default_options['skip_htaccess']){
        $this_object_url = $default_options['root_url'].'?obj='.$this_mongo_id;
    }else{
        /* TODO: BUILD THIS OUT AS A FUNCTION */
        $this_object_url = $default_options['root_url'].$mp->get_slug_from_obj_id($this_mongo_id);
    }
    $checkbox = '<input type="checkbox" name="delete" value="delete" class="delete-me" data-mongo-id="'.$this_mongo_id.'" data-form="object" />';
	$this_action_set = '';
	//$this_action_set = '<a href="'.$this_object_url.'" class="view-object" '.mp_get_attr_filter('table.php','a',$this_object_url,'','view-object','data-mongo-id="'.$this_mongo_id.'" data-form="object" target="_blank"').'>'.__('VIEW').'</a>';
    if($allow_edits){
        $this_action_set.= '<a href="#" class="edit-object" title="'.__('edit this object').'" '.mp_get_attr_filter('table.php','a','#','','edit-object','data-mongo-id="'.$this_mongo_id.'" data-form="object"').'>'.__('EDIT').'</a>';
    }
    $this_action_set.= '<a href="#" class="delete-object" title="'.__('delete this object').'" '.mp_get_attr_filter('table.php','a','#','','delete-object','data-mongo-id="'.$this_mongo_id.'"  data-form="object"').'>'.__('DELETE').'</a>';
    /* LAST MINUTE CHANGES */
	$this_slug = '<a href="'.$this_object_url.'" title="'.__('click to view object').'">'.$this_slug.'</a>';
	$this_slug = apply_filters('mp_object_slug_in_table', $this_slug, $this_mongo_id);
	if($allow_edits){
		$this_title = '<a href="#" class="edit-object" title="'.__('click to edit object').'" '.mp_get_attr_filter('table.php','a','#','','edit-object','data-mongo-id="'.$this_mongo_id.'" data-form="object"').'>'.$this_title.'</a>';
	}else{
		$this_title = $this_title;
	}
	$this_title = apply_filters('mp_object_title_in_table', $this_title, $this_mongo_id);
	$output['aaData'][] = array($checkbox, $this_type, $this_slug, $this_title, $this_created, $this_updated, $this_action_set);
}
mp_json_send($output);
