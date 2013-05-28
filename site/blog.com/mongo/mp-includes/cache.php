<?php

function mongopress_load_m(){
    /*
    For authentication and security, mongodb need to execute/run with --auth parameter
    Reference: http://www.mongodb.org/display/DOCS/Security+and+Authentication
    */
    try{
        $mp = mongopress_load_mp();
        $options = $mp->options();
        if(isset($GLOBALS['_mp_cache']['mongo_m'])) return $GLOBALS['_mp_cache']['mongo_m'];

        if ($options['replicas'] == true && $options['db_username'] !==''){ 

            //replica and database need authentication
			$m = new Mongo("mongodb://{$options['db_username']}:{$options['db_password']}@{$options['db_server']}:{$options['db_port']}/{$options['db_name']}", array('replicaSet' => true));

        } elseif ($options['replicas'] == true) { 

            //replica set and no auth.
            $m = new Mongo("mongodb://{$options['db_server']}:{$options['db_port']}/{$options['db_name']}", array('replicaSet' => true));

        } elseif ($options['db_username'] !='') { //database need authentication

            $m = new Mongo("mongodb://{$options['db_username']}:{$options['db_password']}@{$options['db_server']}:{$options['db_port']}/{$options['db_name']}");

        } else { 
            
            //default without auth and replica
            $m = new Mongo("mongodb://{$options['db_server']}:{$options['db_port']}/{$options['db_name']}");

        }
				//var_dump($options);
				//echo '<br><br>';
				//var_dump($m);exit;

        $db = $m->$options['db_name'];

				//TODO: this should only run on install only
        if(!$options['skip_htaccess']){
            $slugs = new MongoCollection($db,$options['slug_col']);
            $slugs->ensureIndex(array("slug"=>1));
        }
        /* ADDED INDEXES FOR SLUG_IDs in OBJECT COLLECTION */
        $objs = new MongoCollection($db,$options['obj_col']);
        $objs->ensureIndex(array("slug_id"=>1));
        $objs->ensureIndex(array("points"=>'2d'));
        $GLOBALS['_mp_cache']['mongo_m'] = $m;
        return $m;
    } catch (MongoConnectionException $e) {
        $error = 'Error connecting to MongoDB Server';
        mongopress_pretty_page($error,'MongoPress - MongoDB Error',true);
        exit;
    } catch (MongoException $e) {
        $error = 'Error: ' . $e->getMessage();
        mongopress_pretty_page($error,'MongoPress - MongoDB Error',true);
        exit;
    } catch (MongoCursorException $e) {
        die(__('Error: probably username password in config').$e->getMessage()); 
    }
 

}
function mongopress_load_perma(){
    if(isset($GLOBALS['_mp_cache']['mongo_perma'])) return $GLOBALS['_mp_cache']['mongo_perma'];
    $perma = new MONGOPRESS_PERMA();
    $GLOBALS['_mp_cache']['mongo_perma'] = $perma;
    return $perma;
}


function mongopress_get_versions(){
    
    if(isset($GLOBALS['_mp_cache']['mongo_versions'])) return $GLOBALS['_mp_cache']['mongo_versions'];
    
    if (isset($GLOBALS['do_not_run']) && $GLOBALS['do_not_run'] == true) {
        $mongodb_version = 'unknown';
    } else {
        $m = mongopress_load_m();
        $adminDB = $m->admin; //require admin priviledge
        $mongodb_info = $adminDB->command(array('buildinfo'=>true));
        $mongodb_version = (float)$mongodb_info['version'];
    } 
        
    $php_version = floatval(PHP_VERSION);
    $php_driver_version = MONGO::VERSION;
    $versions['current']['php']=$php_version;
    $versions['current']['mongodb']=$mongodb_version;
    $versions['current']['phpd']=$php_driver_version;
    
    
    require_once $GLOBALS['_MP']['DOCUMENT_ROOT'] .'/mp-includes/install/requirements.php';
    $versions['labels'] = $mp_labels;
    $versions['mp_mins'] = $mp_mins;
    $versions['mp_extensions'] = $mp_extensions;
    $versions['mongopress'] = $mongopress_version;
    
    $GLOBALS['_mp_cache']['mongo_versions'] = $versions;
    return $versions;
}


function mongopress_check_versions(){
    $versions = mongopress_get_versions();
    $php_version = $versions['current']['php'];
    $mongodb_version = $versions['current']['mongodb'];
    $php_driver_version = $versions['current']['phpd'];
    $php_min=$versions['mp_mins']['php'];
    $mongodb_min=$versions['mp_mins']['mongodb'];
    $phpd_min=$versions['mp_mins']['phpd'];
    $errors = false;
    if($php_version<$php_min){
    /* TODO: ADD LINKS TO HELP GET NEW DRIVERS */
        echo '<h3 class="article-title header">'.sprintf(__("PHP %s+ required"), $php_min).'</h3>';
        $errors = true;
    }
    if(!utils_version_compare($phpd_min,$php_driver_version)){
        echo '<h3 class="article-title header">'.sprintf(__("PHP Mongo Drivers %s+ required"), $phpd_min).'</h3>';
        $errors = true;
    }
    if($mongodb_version<$mongodb_min){
        echo '<h3 class="article-title header">'.sprintf(__("MongoDB %s+ required"), $mongodb_min).'</h3>';
        $errors = true;
    }
    //check hash extension for sha2
    //check hash extension for s56 encryption
    if (!extension_loaded('hash')) {
        echo '<h3 class="article-title header">'.__("PHP Hash extension required").'</h3>';
        $errors = true;
    }
    //check json extension
    if (!extension_loaded('json')) {
        echo '<h3 class="article-title header">'.__("PHP JSON extension required").'</h3>';
        $errors = true;
    }
    return $errors;
}

function mongopress_get_base64_src($id=false){
    if($id=='default-logo'){
        $base64 = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQ0AAABHCAMAAAAqeOKEAAAAA3NCSVQICAjb4U/gAAAC/VBMVEX///9/5eV+3t6D3NyF1taZmZkAZmYAWVkZGRkAAACHzs6JysqLxsaZmZkAZmYAWVkAUlIQSEgISkoASkoPQkIJQ0MEQ0MzMzMgICAZGRkQEBAICAgAAACHzs6LxsaVpaWZmZkZQUEPQkIJQ0MqOTkhOjobPDwzMzMsNTUpKSkgICAZGRkQEBCNvb2PtraZmZlCQkI6OjoqOTkhOjosNTUzMzMpKSmNvb2PtraVpaWZmZlCQkI6OjozMzNCMDA7MDBDLCwpKSmPtraVpaWZmZlSUlJKSkpCQkI6OjpCMDAzMzM7MDBRKChDLCxJKSmPtraRs7OTrKyVpaWZmZlSUlJKSkpCQkJRKChZJiZjISFTJSVaIiKRs7OTrKyVpaWZmZlaWlpSUlJKSkpCQkJZJiZjISFTJSVaIiKTrKyVpaWZmZlmZmZaWlpSUlJZJiZjISFrICBvHh5aIiJlGxuTrKyVpaWZmZlmZmZaWlpSUlJKSkpvHh5rICBjISFaIiJzGRllGxtrGRmVpaWZmZlmZmZaWlpSUlJvHh57GRlzGRlrGRllGxuVpaWZmZmcjIxzc3NmZmZaWlpSUlKCGBh7GRlzGRmLEBCDDw+VpaWZmZmcjIx7e3tzc3NmZmZaWlqCGBiVEBCLEBB8ERGDDw+ZmZmcjIyfhYWEhISle3t7e3tzc3NmZmaVEBCdDQ2LEBCFDAzm5ubg5eXj4uLe3t7V39/W1tbP1tbG1dXB0tLMzMy9zs64ysrFxcXAxMS0xcW9vb2ywMCuvb21tbWotLStra2lra2lpaWgpqaVpaWlnp6ZmZmllJScjIyMjIyfhYWhgoKEhISle3ulc3N7e3ugcnKoa2uqZ2dzc3OqZGSkZGStW1umWVmwVFSxUlKoUlK0SUmrSkq2QkKtQ0O5OTm4OjqwOzu8MTG2MDC+Kiq3KCjDISG7ISHGGRm9GBjGERHLBwfIBwfVAADMAACuCAizBwedDQ3FAAClCAi9AACbCAi1AACOCQmUBwetAAClAAAG4B/XAAAA/3RSTlMAERERERERERERIiIiIiIiIiIiIiIiIiIiIiIiIjMzMzMzMzMzMzMzMzMzMzNEREREREREREREVVVVVVVVVVVVVVVmZmZmZmZmZmZmZmZmd3d3d3d3d3d3d3d3d4iIiIiIiIiIiIiIiJmZmZmZmZmZmZmZmaqqqqqqqqqqqqqqqqqqu7u7u7u7u7u7u8zMzMzMzMzMzMzMzN3d3d3d3d3d3d3d3e7u7u7u7u7u7u7u7v////////////////////////////////////////////////////////////////////////////////////////////////////////9iuDbzAAAACXBIWXMAAA50AAAOdAFrJLPWAAAAFnRFWHRDcmVhdGlvbiBUaW1lADA2LzE0LzExoRNtagAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNXG14zYAABB/SURBVHic7ZwPeFTVlcAvbTPZyKbFaltBpAEFtgpNNzKxDURYNmuUBaRFiMuuG4kF2SQUykIAEVCSKAmmJEJ2GZsQwQGaEOKEEGlQiMThyVwzMkmEKIrInypoiPASZiaTTL4959z35l/eoPt9TEb5PN9H5t5zz71zz++d+++9NzAWKok2RISs7W+dRBSkh7sL3xyJNmSFuwvfHIk2Zoe7C98ciTEWfDdpqBLLDdHh7kNIJSImKT1bSNbMhGv7+ptGY0z/9CosEp2UZeScWyQziAQpXpAeG9T6n481Bi/81kssoLCYD9bV1Ow1mUx7a2pq6+oBScFM7anht81Nv+nnHvafxGZz3lBXgxw8AulaAGJM1eDx2Psnftf/vewfiU7nvL7Wl4SHyN46MzckBFZYcPKj/wxHR/tDEoy8oUYDhcpD4ln+4bH09JllYepryCWVS3XBWAge9dwQ47UfsOzMpzfqrisiO2hgeLSmOs49o+X76863GX4Qzi6HTiIKeH0QFnX13nSNxJNEhYEF59p3DAxvp0MlACPIKDHt42aTDw6zwDHQ8Kn87thwdztEkh0cxjsfH/QtAhyw2xq244Lc9Otw9zpEkh50mNS8femT/SY/jWSMHrbjc/n4w+HudYgkgTcEgVF95KLcvM9fV8u37Gy7cvI/wt3rEEmEUdJkATDe+kyW36kO0B5qar9yemm4ex0qydKcNGB3Xv3W32T5ktm/0HSg+csr59aFu9OhkhiNcQJHk4MN5iNnZVn+7IBfqen1D+QrF4wDwt3rUEk2r+nDAk4l3Hj0E4Ahnzpc63uE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";
    }
    $src = apply_filters('mp_get_base64',$base64);
    return $src;
}

function mongopress_load_base64s(){
    if(isset($GLOBALS['_mp_cache']['mongo_images'])) return $GLOBALS['_mp_cache']['mongo_images'];
    $images['default-logo']=  mongopress_get_base64_src('default-logo');
    $GLOBALS['_mp_cache']['mongo_images'] = $images;
    return $images;
}

function mongopress_get_db_info(){
	if(isset($GLOBALS['_mp_cache']['mp_db'])) return $GLOBALS['_mp_cache']['mp_db'];
	$mp = mongopress_load_mp();
	$default_options = $mp->options();
	$m = mongopress_load_m();
	$db = $m->$default_options['db_name'];
	$users = $db->$default_options['user_names'];
	$objs = $db->$default_options['obj_col'];
	$slugs = $db->$default_options['slug_col'];
	$total_objects = $objs->count();
	$total_users = $users->count();
	$total_media = $db->fs->files->count();
	$object_types = $db->command(array("distinct"=>$default_options['obj_col'],"key"=>"type"));
	$total_types = count($object_types['values']);
    $db_info['total_objs'] = $total_objects;
	$db_info['total_users'] = $total_users;
	$db_info['total_media'] = $total_media;
	$db_info['total_types'] = $total_types;
    $GLOBALS['_mp_cache']['mp_db'] = $db_info;
    return $db_info;
}
