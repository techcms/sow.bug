<?php

class MONGOPRESS {
    public function dump($arr,$exit=true){
        echo '<pre>';
        if(is_array($arr)){
            print_r($arr);
        }elseif(is_object($arr)){
            echo __('This is an object:').'</br>';
            print_r($arr);
            echo '<br/>';
        }else{
            if(!empty($arr)){
                echo __('This $arr is a string = ').$arr.'<br />';
            } else
		        echo __('Empty $arr');
        }
	//echo '<br />Backtrace:<br />';
	//var_dump(debug_backtrace());
        echo '</pre>';
        if($exit){
            exit;
        }
    }
    private function between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
    }


    private function default_options(){
        /* IF GOT OPTIONS IN CACHE, RETURN THEM */
	if(isset($GLOBALS['_mp_cache']['mp_options'])){
            return $GLOBALS['_mp_cache']['mp_options'];
        }else{
            /* ELSE - GATHER CONFIG SETTINGS - numpty proofing the config ... */
		
            if(defined('MONGODB_NAME')){ $name = MONGODB_NAME; }else{ $name = 'mongopress'; }
            if(defined('MONGODB_HOST')){ $db_server = MONGODB_HOST; }else{ $db_server = 'localhost'; }
            if(defined('OBJECT_COLLECTION')){ $objs = OBJECT_COLLECTION; }else{ $objs = 'objs'; }
            if(defined('SLUG_COLLECTION')){ $slugs = SLUG_COLLECTION; }else{ $slugs = 'slugs'; }
            if(defined('SITE_NAME')){ $sitename = SITE_NAME; }else{ $sitename = ''; }
            if(defined('SITE_DESCRIPTION')){ $site_description = SITE_DESCRIPTION; }else{ $site_description = ''; }
            
            if (defined('BASE_URL_DIRECTORY')) $homedir = BASE_URL_DIRECTORY; // optional config
            elseif (isset($GLOBALS['_MP']['HOME'])) $homedir = $GLOBALS['_MP']['HOME'];
            else $homedir = '';
            
            if(defined('MONGOPRESS_THEME')){ $theme = MONGOPRESS_THEME; }else{ $theme = 'default'; }

            // COLLECTIONS.
            if (defined('COLLECTION_PREFIX')) $col_prefix = COLLECTION_PREFIX; else $col_prefix = '';
      

            if(defined('NONCE_PRIVATE_TTL')){ $nonce_private_ttl = NONCE_PRIVATE_TTL; } else {$nonce_private_ttl = 3600; } // 1 hour
            if(defined('NONCE_PUBLIC_TTL')){ $nonce_public_ttl = NONCE_PUBLIC_TTL; } else { $nonce_public_ttl = 86400; } // 1 day

            if(defined('SITE_SALT')){ $salt = SITE_SALT; }else{ $salt = 'mp_'.time(); }
            if(defined('COOKIE_SALT')){ $cookie_salt = COOKIE_SALT; }else{ $cookie_salt = 'mp_'.time(); }
            if(defined('COOKIE_TTL')){ $cookie_ttl = COOKIE_TTL; }else{ $cookie_ttl = 'session'; }
            if(defined('DB_USERNAME')){ $mongodb_db_username = DB_USERNAME; }else{ $mongodb_db_username = ''; }
            if(defined('DB_PASSWORD')){ $mongodb_db_password = DB_PASSWORD; }else{ $mongodb_db_password = ''; }
            if(defined('DB_PORT')){ $mongodb_db_port = DB_PORT; }else{ $mongodb_db_port = '27017'; }
            if(defined('QUERY_PERMA')){ $query_perma_key = QUERY_PERMA; }else{ $query_perma_key = 'mp'; }
            if(defined('SEARCH_PERMA')){ $search_perma_key = SEARCH_PERMA; }else{ $search_perma_key = 'search'; }

            if(defined('TIMEZONE')) { 
                $timezone = TIMEZONE; 
            } else {
			    $timezone = ini_get('date.timezone'); // from server settings.
			    if (!$timezone) $timezone = 'UTC'; 
	        }

            if(defined('SKIP_HT')){ $skip_htaccess = SKIP_HT; }else{ $skip_htaccess = false; }
            if(defined('ADMIN_SLUG') && !$skip_htaccess){ $admin_slug = ADMIN_SLUG; }else{ $admin_slug = 'mp-admin'; }
            if(defined('MEDIA_SLUG') && !$skip_htaccess){ $media_slug = MEDIA_SLUG; }else{ $media_slug = 'mp-media'; }
            if(defined('MP_DEBUG')){ $debug = MP_DEBUG; }else{ $debug = false; }
            if(defined('MONGODB_REPLICAS')){ $replicas = MONGODB_REPLICAS; }else{ $replicas = false; }
            if(defined('OBJS_PP')){ $objects_per_page = (int)OBJS_PP; }else{ $objects_per_page = 25; }
            if(defined('OPTIONS_COL')){ $opts = OPTIONS_COL; }else{ $opts = 'opts'; }

            /* BUILD OPTIONS ARRAY */
            $mp_options = array();
            $mp_options['replicas'] = $replicas;
            $mp_options['db_server'] = $db_server;
            $mp_options['debug'] = $debug;
            $mp_options['db_name'] = $name;

            $mp_options['collection_prefix'] = $col_prefix;

            /* The prefix allows multiple mongopress installations - the hardcoded section could be customised later 
                - but we'd need checks to ensure no conflicts - what happens when a user manually modifies 
                this in the config? - what level of numpty support do we really need? */

            $mp_options['obj_col'] = $col_prefix.'objs';
            $mp_options['slug_col'] = $col_prefix.'slugs';
            $mp_options['opts_col'] = $col_prefix.'opts';
            $mp_options['user_names'] = $col_prefix.'users';
            $mp_options['user_col'] = $col_prefix.'user';
            $mp_options['cookie_col'] = $col_prefix.'cookies';
            $mp_options['settings_col'] = $col_prefix.'settings';

            $mp_options['site_name'] = $sitename;
            $mp_options['site_description'] = $site_description;
            $mp_options['home_directory'] = $homedir;
            $mp_options['theme'] = $theme;
            //echo '$homedir = '.$homedir;
            if($homedir){
                $mp_options['root_path'] = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']).'/'.$homedir.'/';
                $mp_options['root_url'] = '/'.$homedir.'/';
            }else{
                $mp_options['root_path'] = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']).'/';
                $mp_options['root_url'] = '/';
            }
			// custom slugs
            $mp_options['admin_slug'] = $admin_slug;
            $mp_options['media_slug'] = $media_slug;
            $mp_options['admin_url'] = $mp_options['root_url'] . $admin_slug .'/';
            $mp_options['media_url'] = $mp_options['root_url'] . $media_slug .'/';
            $mp_options['theme_url'] = $mp_options['root_url'].'mp-content/themes/'.$theme;
            $mp_options['full_url'] = 'http://'.$_SERVER['HTTP_HOST'] . $mp_options['root_url'];
            $mp_options['fixed_salt'] = $salt;
            $mp_options['cookie_salt'] = $cookie_salt;
            $mp_options['cookie_ttl'] = $cookie_ttl;
            $mp_options['nonce_private_ttl'] =  $nonce_private_ttl;
            $mp_options['nonce_public_ttl'] =  $nonce_public_ttl;
            $mp_options['timezone'] = $timezone;
            $mp_options['db_username'] = $mongodb_db_username;
            $mp_options['db_password'] = $mongodb_db_password;
            $mp_options['db_port'] = $mongodb_db_port;
            $mp_options['query_perma_key'] = $query_perma_key;
            $mp_options['search_perma_key'] = $search_perma_key;
            $mp_options['skip_htaccess'] = $skip_htaccess;
            $mp_options['objects_per_page'] = $objects_per_page;
            /* GET MONGO BITS */
            try{
                if(!empty($name)){

                    // TODO - why is this in the trunk twice - rewrite needed.
                    if ($mp_options['replicas'] == true && $mp_options['db_username'] !==''){ 

                        //replica and database need authentication
            			$m = new Mongo("mongodb://{$mp_options['db_username']}:{$mp_options['db_password']}@{$mp_options['db_server']}:{$mp_options['db_port']}/{$mp_options['db_name']}", array('replicaSet' => true));

                    } elseif ($mp_options['replicas'] == true) { 
    
                        //replica set and no auth.
                        $m = new Mongo("mongodb://{$mp_options['db_server']}:{$mp_options['db_port']}/{$mp_options['db_name']}", array('replicaSet' => true));

                    } elseif ($mp_options['db_username'] !='') { //database need authentication

                        $m = new Mongo("mongodb://{$mp_options['db_username']}:{$mp_options['db_password']}@{$mp_options['db_server']}:{$mp_options['db_port']}/{$mp_options['db_name']}");

                    } else { 
            
                        //default without auth and replica
                        $m = new Mongo("mongodb://{$mp_options['db_server']}:{$mp_options['db_port']}/{$mp_options['db_name']}");

                    }

                    $db = $m->$name;
                    $opts = $db->$opts;
                    $saved_options = $this->arrayed($opts->find(array("key"=>'site_options')));
                    if(is_array($saved_options[0])){
                        if(!empty($saved_options[0]['options']['site_name'])){
                            $mp_options['site_name'] = $saved_options[0]['options']['site_name'];
                        }else{
                            $mp_options['site_name'] = $sitename;
                        }
                        if(!empty($saved_options[0]['options']['site_description'])){
                            $mp_options['site_description'] = $saved_options[0]['options']['site_description'];
                        }else{
                            $mp_options['site_description'] = $site_description;
                        }
                        if(!empty($saved_options[0]['options']['cookie_ttl'])){
                            $mp_options['cookie_ttl'] = $saved_options[0]['options']['cookie_ttl'];;
                        }else{
                            $mp_options['cookie_ttl'] = $cookie_ttl;
                        }
                    }
                }
            } catch (MongoConnectionException $e) {
                /* NOT SURE WHAT TO DO HERE */
                /* HAD TO COMMENT THIS OUT OTHERWISE I COULD NOT MAKE FRESH INSTALL */
                //$error = __('Error connecting to MongoDB server');
                //mongopress_pretty_page($error,__('MongoDB Error'),true);
                //exit;
            } catch (MongoException $e) {
                /* STRANGE THAT NO ONE ELSE ENCOUNTERED THIS PROBLEM */
                //$error = sprintf(__('Error: %s'),$e->getMessage());
                //mongopress_pretty_page($error,__('MongoDB Error'),true);
                //exit;
            } catch (MongoCursorException $e) {
                die(__('Error: probably username password in config').$e->getMessage()); 
            }
            /* STORE AND RETURN OPTIONS */
            $GLOBALS['_mp_cache']['mp_options'] = $mp_options;
            return $mp_options;
        }
    }
    private function default_query(){
        $mp_query = array();
        $mp_query['filter_by'] = 'obj';
        $mp_query['combine'] = false;
        $mp_query['where'] = false;
        $mp_query['order_by'] = 'oid';
        $mp_query['order'] = 'desc';
        $mp_query['limit'] = 1;
        return $mp_query;
    }
    private function defaults($options=false,$query=false){
        $mp_defaults = $this->default_options();
        $mp_query = $this->default_query();
        $settings = array_merge($mp_defaults,$mp_query);
        if(is_array($options)){
            $settings = array_merge($mp_defaults,$options);
            if(is_array($query)){
                $settings = array_merge($settings,$query);
            }
        }else{
            if(is_array($query)){
                $settings = array_merge($mp_defaults,$query);
            }else{
                $settings = $mp_defaults;
            }
        } return $settings;
    }
    private function user_name($name){
        $options = $this->default_options();
        $m = mongopress_load_m();
        $db = $m->$options['db_name'];
        $users = $db->$options['user_names'];
        $these_objects = $this->arrayed($users->find(array("un"=>$name)));
        $this_user = $these_objects[0]['un'];
        if($this_user==$name){
            if(is_array($these_objects[0]["_id"])){
                foreach($these_objects[0]["_id"] as $key => $id){
                    $this_id = $id;
                } $name = $name.'-'.$this_id;
                return $name;
            }else{
                return $name;
            }
        }else{
            return $name;
        }
    }
    private function connect($options,$query=false){
    	//$all missing value?
	    //$limit missing value?
        /* TODO: Confrim if this function is being used and how it is able to work
         * Becuase this is a complete mess!!! :-(
        */
        $settings = $this->defaults($options,$query);
        if($order=='asc'){ $order_value=1; }else{ $order_value=-1; }
        if($order_by){ $order=array($order_by=>$order_value); }else{ $order=array(); }
        if($where){ if((!is_array($where))||(empty($where))){ $where=false; }}
        if($where){ $combine = true; }
        /* OPTIONS EXTRACTED - NOW CONNECT TO MONGO */
        /* TODO: ADD TRY METHOD EVERYWHERE OR SIMPLY FUNCTIONALIZE THIS ASPECT */
        try{
            $m = mongopress_load_m();
            $db = $m->selectDB($settings['db_name']);
            // Select / Create a Collection (table)
            $objs = $db->$settings['obj_col'];
            /* ADD MAP REDUCE */
            $map = new MongoCode('function() { emit(this.user.$id, 1); }');
            $reduce_function = new MongoCode('function(k, vals) {
                var sum = 0;
                for (var i in vals) {
                    sum += vals[i];
                }
                return sum;
            }');
            $the_reduce = $db->command(array(
                'mapreduce' => 'events',
                'map' => $map,
                'reduce' => $reduce_function,
                'query' => array('type' => 'sale'))
            );
            $objs = $db->selectCollection2($the_reduce['result']);
            /* END OF MAP REDUCE */
            // Select ID Collection
            $ids = $db->$settings['id_col'];
            // Get & Allocate Objects
            $mp_db = array();
            if($all){
                $this_obj_set = $db->$all;
                $all_objects = $this->arrayed($this_obj_set->find());
                if(is_array($all_objects)){
                    foreach($all_objects as $obj) {
                        $mp_db[] = $obj;
                    }
                }
            }else{
                $ids = $ids->count(array("_id"=>'obj'));
                if($filter_by){
                    if($ids>0){
                        if($where){
                            $current_objects = $this->arrayed($objs->find($where)->sort($order)->limit($limit));
                        }else{
                            $current_objects = $this->arrayed($objs->find(array("type"=>$filter_by))->sort($order)->limit($limit));
                        }
                        if(is_array($current_objects)){
                            foreach($current_objects as $obj) {
                                $mp_db[] = $obj;
                            }
                        }
                    }
                }else{
                    if($combine){
                        if($where){
                            $current_objects = $this->arrayed($objs->find($where)->sort($order)->limit($limit));
                        }else{
                            $current_objects = $this->arrayed($objs->find()->sort($order)->limit($limit));
                        }
                        if(is_array($current_objects)){
                            foreach($current_objects as $obj) {
                                $mp_db[] = $obj;
                            }
                        }
                    }else{
                        if($ids>0){
                            if($where){
                                $current_objects = $this->arrayed($objs->find($where)->sort($order)->limit($limit));
                            }else{
                                $current_objects = $this->arrayed($objs->find()->sort($order)->limit($limit));
                            }
                            if(is_array($current_objects)){
                                foreach($current_objects as $obj) {
                                    $mp_db[] = $obj;
                                }
                            }
                        }
                    }
                }
            }
        // disconnect from server
        } catch (MongoConnectionException $e) {
            die(__('Error connecting to MongoDB server'));
        } catch (MongoException $e) {
            die(__('Error: ').$e->getMessage());
        } catch (MongoCursorException $e) {
            die(__('Error: probably username password in config').$e->getMessage()); 
        }
        if(!empty($mp_db)){
            return $mp_db;
        }else{
            if($validate){
                $progress['message'] = __('Empty Objs Array');
                $progress['success'] = false;
                return $progress;
            }else{
                return false;
            }
        }

    }
    /* TODO: CONFIRM IF THIS IS NEEDED AND WHERE TO USE IT ...? */
    /* THIS IS NOT YET BEING USED ANYWHERE */
    public function sanitize_query($val) {
        if (!is_array($val)) return $val;
        $indexes = array();
        foreach($val as $key => $value) {
            if (is_string($key)) {
                $key = str_replace(array('$', chr(0)), '', $key);
                $indexes[$key] = $value;
            }
        }
        foreach($indexes as $key => $value) {
            if (is_array($value)) $indexes[$key] = sanitize_query($value);
        }
        return $indexes;
    }

    public function slugs($slug){
        $options = $this->default_options();
		
		$slug = strtolower($slug);
		// TODO filter out punctuation? what about utf8 slugs?
		
        if(($slug=='')||($slug=='/')){ $slug='empty-slug-not-allowed'; }

		$aslug = $options['admin_slug']; $mslug = $options['media_slug'];
		$alen = strlen($aslug); $mlen = strlen($mslug);

		// match the substrs - against our dynamic urls...
        if(substr($slug,0,$alen) == $aslug){ $slug='reserved-'.$aslug; }
        if(substr($slug,0,$mlen) == $mslug){ $slug='reserved-'.$mslug; }
        if(substr($slug,0,3)== 'mp-'){ $slug='reserved-mp-slug'; }
        
		
		$slug = str_replace($options['query_perma_key'].'/',$options['query_perma_key'].'-reserved/',$slug);
        $slug = str_replace($options['search_perma_key'].'/',$options['search_perma_key'].'-reserved/',$slug);
        
		/* FINISHED CHECKING AND MANIPULATING */
        $m = mongopress_load_m();
        $db = $m->$options['db_name'];
        $slugs = $db->$options['slug_col'];
        $these_objects = $this->arrayed($slugs->find(array("slug"=>$slug)));
        $this_slug = $these_objects[0]['slug'];
        if($this_slug==$slug){
            if(isset($these_objects[0]["_id"])){
                foreach($these_objects[0]["_id"] as $key => $id){
                    $this_id = $id;
                } $slug = $slug.'-'.$this_id;
                return $slug;
            }else{
                return $slug;
            }
        }else{
            return $slug;
        }
    }

    public function get_mongoid_as_string($id){
        $mongo_id = '';
        if (isset($id)) {
            if(is_object($id)){
                foreach($id as $key => $value){
                    if($key=='$id'){
                        $mongo_id = $value;
                    }
                }
            }
            return (string)$mongo_id;
        } else {
            return (string)$id;
        }
    }
    
    public function arrayed($these_objs){
        if(is_object($these_objs)){
            $objects = array();
            foreach($these_objs as $this_obj) {
                $this_object = array();
                foreach($this_obj as $key => $value){
                    $this_object[$key] = $value;
                } $objects[] = $this_object;
            }
            if(is_array($objects)){
                if(!empty($objects)){
                    return $objects;
                }
            }
        }
    }
    public function options(){
        $mp_options = $this->defaults();
        return $mp_options;
    }
    public function mp_sensible_formatting_filter($the_content){
        $trimmed = trim($the_content);
        $stripped = strip_tags($trimmed,'<p><a><ul><li><h1><h2><h3><h4><br><span><code><pre><blockquote><img><strong><div><figure><figcaption><section><article><hr><header><footer><code><mark>');
        $stripped = str_replace('\\', '', $stripped);
        return $stripped;
    }
    public function get_slug($slug_id){
        $m = mongopress_load_m();
        $mp = mongopress_load_mp();
        $options = $mp->options();
        $db = $m->$options['db_name'];
        $slugs = $db->$options['slug_col'];
        $slug_mongo_id = new MongoId($slug_id);
        $slug_array = $slugs->findOne(array("_id"=>$slug_mongo_id));
        $slug = $slug_array['slug'];
        return $slug;
    }
    public function get_slug_from_obj_id($object_id){
        $m = mongopress_load_m();
        $mp = mongopress_load_mp();
        $options = $mp->options();
        $db = $m->$options['db_name'];
        $slugs = $db->$options['slug_col'];
        $objs = $db->$options['obj_col'];
        $this_mongoID = new MongoID($object_id);
        $this_object = $mp->arrayed($objs->find(array("_id"=>$this_mongoID)));
        $this_slug_id = $this_object[0]['slug_id'];
        $this_slug_mongo_id = new MongoID($this_slug_id);
        $this_slug_array = $mp->arrayed($slugs->find(array("_id"=>$this_slug_mongo_id)));
        $this_slug = $this_slug_array[0]['slug'];
        return $this_slug;
    }

    public function plugin_options($options=false,$plugin_settings=false){
	//utils_caller_func(); // debugging - we only want to do this when a plugin is actually there...
	//$this->dump(get_defined_vars(),false);
        $default_options = array(
            'action'    => 'get',
            'key'       => false
        );
        if(is_array($options)){
            $settings = array_merge($default_options,$options);
        }else{
            $settings = $default_options;
        }
        if($settings['key']){
            $m = mongopress_load_m();
            $mp = mongopress_load_mp();
            $options = $mp->options();
            $db = $m->$options['db_name'];
            $opts = $db->$options['opts_col'];
            if($settings['action']=='insert'){
                /* TODO: DEBUG HERE */
                // Ali: echo 'is inserting?';
                if(!empty($settings['plugin'])){
                    $check_for_option = $mp->arrayed($opts->find(array("key"=>$settings['plugin'])));
                    $current_options = $check_for_option[0]['options'][0];
                    $current_options[$settings['key']] = $settings['value'];
                }else{
                    $check_for_option = $mp->arrayed($opts->find(array("key"=>$settings['key'])));
                }
                $inserted = false;
                if(is_array($check_for_option[0])){
                    $this_mongo_id = new MongoID($mp->get_mongoid_as_string($check_for_option[0]["_id"]));
                    if(!empty($settings['plugin'])){
                        $updated = $opts->update(array("_id"=>$this_mongo_id),array('$set'=>array("options"=>array($current_options))));
                    }else{
                        $updated = $opts->update(array("_id"=>$this_mongo_id),array('$set'=>array("options"=>$settings['value'])));
                    }
                }else{
                    if(!empty($settings['plugin'])){
                        $current_options[][$settings['key']] = $settings['value'];
                        $inserted = $opts->insert(array("key"=>$settings['plugin'],"options"=>array($current_options)));
                    }else{
                        $inserted = $opts->insert(array("key"=>$settings['key'],"options"=>$settings['value']));
                    }
                }
                if($inserted){
                    $progress['success']=true;
                    $progress['message']=__('Successfully Added Option');
                }else{
                    if($updated){
                        $progress['success']=true;
                        $progress['message']=__('Successfully Updated Option');
                    }else{
                        $progress['success']=false;
                        $progress['message']=__('Unknown Error Updating Option');
                    }
                } return $progress;
            }elseif($settings['action']=='get'){
								try {
                    $these_options = $mp->arrayed($opts->find(array("key"=>$settings['key'])));
                } catch (MongoCursorException $e) {
                    // I do not understand why this error is caught here. It should be on connect!
                    // TODO - figure out why this is here.
										//Driver bug - https://jira.mongodb.org/browse/PHP-208
                    $error = (__('Error: probably username password in /mp-settings/security.php <br>').$e->getMessage(). ' code '. $e->getCode());
                    mongopress_pretty_page($error,'MongoPress - MongoDB Error',true);
                    die();
                }  

                if(is_array($these_options[0]['options'][0])){
                    foreach($these_options[0]['options'][0] as $key => $value){
                        $these_plugin_options[$key]=$value;
                    }
                    return $these_plugin_options;
                }
						}
        }
	// TODO - we drop out here every time an object is loaded? why?
	// print "HERE";
    }

	public function user_options($options){
        $default_options = array(
            'email'		=> false,
            'name'		=> false,
			'id'		=> false,
			'avatar'	=> false
        );
        if(is_array($options)){
            $settings = array_merge($default_options,$options);
        }else{
            $settings = $default_options;
        }
        if(($settings['email'])||($settings['name'])||($settings['avatar'])){
            $m = mongopress_load_m();
            $mp = mongopress_load_mp();
            $options = $mp->options();
            $db = $m->$options['db_name'];
            $user = $db->$options['user_col'];
			$timestamp = time();
			$mongo_id = new MongoId($settings['id']);
			if($settings['avatar']){
				$updated = $user->update(array("_id" => $mongo_id), array('$set' => array("avatar"=>$settings['avatar'],"updated"=>$timestamp)));
			}else{
				$updated = $user->update(array("_id" => $mongo_id), array('$set' => array("email"=>$settings['email'],"name"=>$settings['name'],"updated"=>$timestamp)));
			}
			if($updated){
				$progress['success']=true;
				$progress['message']=__('Successfully Updated User Options');
			}else{
				$progress['success']=false;
				$progress['message']=__('Unable to Update User Options');
			}
			return $progress;
        }
    }


    public function push($options=false,$obj,$mongo_id=false,$delete=false){
        $errors = false;
        $default_options = $this->default_options();
        if(is_array($options)){
            $settings = array_merge($default_options,$options);
        }else{
            $settings = $default_options;
        }
        /* OPTIONS EXTRACTED - NOW MAKE CHECKS */
        if(empty($obj)){ $errors = 'Need Obj to Push'; }
        if(isset($settings['validate'])){
            if($settings['validate']){ if($errors){ return $errors; }}
        } if(!$errors){
            /* OPTIONS EXTRACTED - NOW CONNECT TO MONGO */
            $m = mongopress_load_m();
            // Select / Create a Database
            $db = $m->$settings['db_name'];
            // Select / Create a Collection (table)
            $objs = $db->$settings['obj_col'];
            // Access Slugs
            $slugs = $db->$settings['slug_col'];
            // Access Usernmaes
            $users = $db->$settings['user_names'];
            $user= $db->$settings['user_col'];
            // Admin Only TODO: FUNCTIONALIZE / HIDE
            $adminDB = $m->admin;
            // First time inserting to objs
            $first_time_user = false;
            $first_time_obj = false;
            $timestamp = time();
            /* TODO: MAKE BETTER CONFIGURABLE FIXED SALT */
            $random_salt = $timestamp;
            if(isset($obj['password'])){ $this_pw = $obj['password']; }else{ $this_pw = false; }
            $hashed_pw = hash('sha256',$settings['fixed_salt'].$this_pw.$random_salt);
            $this_object = sanitize_title_with_dashes($obj['type']);
            if(isset($obj['title'])){ $this_title = sanitize_title($obj['title']); }
            if(isset($obj['slug'])){ $santized_slug = sanitize_title_with_dashes($obj['slug']); }
            if(isset($obj['custom'])){ $custom_content = $obj['custom']; }
            $include_custom_content = false;
            if(isset($custom_content)){
                if(is_array($custom_content)){
                    $include_custom_content = true;
                    foreach($custom_content as $key => $value){
                        if(empty($key)){
                            $include_custom_content = false;
                        }
                    }
                } if($include_custom_content){
                    $custom_content = $obj['custom'];
                }
            }
            /* END OF CHEAP ENCRYPTION */
            if($this_object=='user'){
                $this_username = $this->user_name($obj['username']);
                if($user->find()->count()<1){ $first_time_user=true; }
                if($first_time_user){
                    $key = array("un"=>$this_username);
                    $data = array("un"=>$this_username,"email"=>$obj['email'],"name"=>$obj['name'],"created"=>$timestamp,"updated"=>$timestamp);
                    $results = $db->command( array(
                        'findAndModify' => $settings['user_col'],
                        'query' => $key,
                        'update' => $data,
                        'new' => true,
                        'upsert' => true,
                        'fields' => array( '_id' => 1 )
                    ) );
                    $user_id = $this->get_mongoid_as_string($results['value']['_id']);
                    $users->insert(array("uid"=>$user_id,"password"=>$hashed_pw));
                }else{
                    $this_username = $this->user_name($obj['username']);
                    $insert_or_update_users = 'update';
                    if($obj['_id']<1){ $insert_or_update_users = 'insert'; }else{ $insert_or_update_users = 'update'; }
                    if($insert_or_update_users=='insert'){
                        $key = array("un"=>$this_username);
                        $data = array("un"=>$this_username, "email"=>$obj['email'],"name"=>$obj['name'],"created"=>$timestamp,"updated"=>$timestamp);
                        $results = $db->command( array(
                            'findAndModify' => $settings['user_col'],
                            'query' => $key,
                            'update' => $data,
                            'new' => true,
                            'upsert' => true,
                            'fields' => array( '_id' => 1 )
                        ) );
                        $user_id = $this->get_mongoid_as_string($results['value']['_id']);
                        $users->insert(array("uid"=>$user_id,"password"=>$hashed_pw));
                    }else{
                        $mongo_id = new MongoId($obj['_id']);
                        $user->update(array("_id" => $mongo_id), array('$set' => array("email"=>$obj['email'],"name"=>$obj['name'],"updated"=>$timestamp)));
                    }
                }
            }else{
                /* OBJECTS */
                $lng = 0; $lat = 0;
                /* ASSIGN LAT / LNG IF NEEDED */
                if((!empty($obj['lng']))&&(!empty($obj['lat']))){
                    $lng = (float)$obj['lng'];
                    $lat = (float)$obj['lat'];
                }if((empty($lng))||(empty($lat))){
                    if((!empty($obj['custom']['lng']))&&(!empty($obj['custom']['lat']))){
                        $lng = (float)$obj['custom']['lng'];
                        $lat = (float)$obj['custom']['lat'];
                    }else{
                        $lng = 0;
                        $lat = 0;
                    }
                }
                //echo 'lng = '.$lng.' and lat = '.$lat; exit;
                if($objs->find()->count()<1){ $first_time_obj=true; }
                if($first_time_obj){
                    $slugs->insert(array("slug"=>$santized_slug));
                    $key = array("slug"=>$santized_slug);
                    $data = array("slug"=>$santized_slug, "created"=>$timestamp, "updated"=>$timestamp);
                    $results = $db->command( array(
                        'findAndModify' => $settings['slug_col'],
                        'query' => $key,
                        'update' => $data,
                        'new' => true,
                        'upsert' => true,
                        'fields' => array( '_id' => 1 )
                    ) );
                    $slug_id = $this->get_mongoid_as_string($results['value']['_id']);
                    if(isset($custom_content)){
                        if(is_array($custom_content)){
                            $include_custom_content = true;
                            foreach($custom_content as $key => $value){
                                if(empty($key)){
                                    $include_custom_content = false;
                                }
                            }
                        }
                    } if($include_custom_content){
                        $objs->insert(array("slug_id"=>$slug_id,"type"=>$this_object,"title"=>$this_title,"content"=>$obj['content'],"created"=>$timestamp,"updated"=>$timestamp,"points"=>array("lng"=>$lng, "lat"=>$lat),"custom"=>$custom_content));
                    }else{
                        $objs->insert(array("slug_id"=>$slug_id,"type"=>$this_object,"title"=>$this_title,"content"=>$obj['content'],"created"=>$timestamp,"updated"=>$timestamp, "points"=>array("lng"=>$lng, "lat"=>$lat)));
                    }
					$dirty_url = $settings['root_url'].$santized_slug;
					$object_url = '<a href="'.$dirty_url.'" '.mp_get_attr_filter('core.php','a',$dirty_url,'','').'>'.__('View Object').'</a>';
					$progress['message'] = sprintf(__('Successfully Added First Object - %s'), $object_url);
                    $progress['success'] = true;
                    $progress['state'] = 'insert';
                }else{
                    $insert_or_update = 'update';
                    if((isset($obj['mongo_id']))&&(!empty($obj['mongo_id']))){
                        $insert_or_update = 'update';
                    }else{
                        $insert_or_update = 'insert';
                    } if($insert_or_update=='insert'){
                        /* INSERT */
                        $this_slug = $this->slugs($santized_slug);
                        $key = array("slug"=>$this_slug);
                        $data = array("slug"=>$this_slug, "created"=>$timestamp,"updated"=>$timestamp);
                        $results = $db->command( array(
                            'findAndModify' => $settings['slug_col'],
                            'query' => $key,
                            'update' => $data,
                            'new' => true,
                            'upsert' => true,
                            'fields' => array( '_id' => 1 )
                        ) );
                        $slug_id = $this->get_mongoid_as_string($results['value']['_id']);
                        if(isset($custom_content)){
                            if(is_array($custom_content)){
                                $include_custom_content = true;
                                foreach($custom_content as $key => $value){
                                    if(empty($key)){
                                        $include_custom_content = false;
                                    }
                                }
                            }
                        } if($include_custom_content){
                            $objs->insert(array("slug_id"=>$slug_id,"type"=>$this_object,"title"=>$this_title,"content"=>$obj['content'],"created"=>$timestamp,"updated"=>$timestamp,"points"=>array("lng"=>$lng, "lat"=>$lat),"custom"=>$custom_content));
                        }else{
                            $objs->insert(array("slug_id"=>$slug_id,"type"=>$this_object,"title"=>$this_title,"content"=>$obj['content'],"created"=>$timestamp,"updated"=>$timestamp, "points"=>array("lng"=>$lng, "lat"=>$lat)));
                        }
                        $dirty_url = $settings['root_url'].$this_slug;
						$object_url = '<a href="'.$dirty_url.'" '.mp_get_attr_filter('core.php','a',$dirty_url,'','').'>'.__('View Object').'</a>';
                        $progress['message'] = sprintf(__('New Object Added - %s'), $object_url);
                        $progress['success'] = true;
                        $progress['state'] = 'insert';
                    }else{
                        /* UPDATE */
                        $saved_slug_count = $slugs->find(array("slug"=>$santized_slug))->count();
                        //echo '$saved_slug_count = '.$saved_slug_count; exit;
                        if($saved_slug_count<1){
                            $key = array("slug"=>$santized_slug);
                            $data = array("slug"=>$santized_slug, "created"=>$timestamp,"updated"=>$timestamp);
                            $results = $db->command( array(
                                'findAndModify' => $settings['slug_col'],
                                'query' => $key,
                                'update' => $data,
                                'new' => true,
                                'upsert' => true,
                                'fields' => array( '_id' => 1 )
                            ) );
                            $slug_id = $this->get_mongoid_as_string($results['value']['_id']);
                            $this_slug = $santized_slug;
                        }else{
                            $object = $this->find_one($mongo_id);
                            $current_slug = sanitize_title_with_dashes($this->get_slug($object['slug_id']));
                            if($current_slug!=sanitize_title_with_dashes($obj['slug'])){
                                $this_slug = $this->slugs(sanitize_title_with_dashes($obj['slug']));
                                $key = array("slug"=>$this_slug);
                                $data = array("slug"=>$this_slug, "created"=>$timestamp,"updated"=>$timestamp);
                                $results = $db->command( array(
                                    'findAndModify' => $settings['slug_col'],
                                    'query' => $key,
                                    'update' => $data,
                                    'new' => true,
                                    'upsert' => true,
                                    'fields' => array( '_id' => 1 )
                                ) );
                                $slug_id = $this->get_mongoid_as_string($results['value']['_id']);
                            }else{
                                $this_slug = sanitize_title_with_dashes($obj['slug']);
                                $key = array("slug"=>$this_slug);
                                $data = array("slug"=>$this_slug, "created"=>$timestamp,"updated"=>$timestamp);
                                $results = $db->command( array(
                                    'findAndModify' => $settings['slug_col'],
                                    'query' => $key,
                                    'update' => $data,
                                    'new' => true,
                                    'upsert' => true,
                                    'fields' => array( '_id' => 1 )
                                ) );
                                $slug_id = $this->get_mongoid_as_string($results['value']['_id']);
                            }
                        }
                        $mongo_id = new MongoId($mongo_id);
                       
                        $include_custom_content = false;
                        if(isset($custom_content)){
                            if(is_array($custom_content)){
                                $include_custom_content = true;
                                foreach($custom_content as $key => $value){
                                    if(empty($key)){
                                        $include_custom_content = false;
                                    }
                                }
                            }
                        }
                        
                        if($include_custom_content){
                            $objs->update(array("_id"=>$mongo_id), array('$set' => array("slug_id" => $slug_id, "type"=>$this_object,"slug"=>$this_slug,"title"=>$this_title,"content"=>$obj['content'],"updated"=>$timestamp,"points"=>array("lng"=>$lng, "lat"=>$lat),"custom"=>$custom_content)));
                        }else{
                            $objs->update(array("_id" => $mongo_id), array('$set' => array("slug_id" => $slug_id, "type"=>$this_object,"slug"=>$this_slug,"title"=>$this_title,"content"=>$obj['content'],"updated"=>$timestamp,"points"=>array("lng"=>$lng, "lat"=>$lat))));
                        }
                        $progress['message'] = __('Object Updated');
                        $progress['success'] = true;
                        $progress['state'] = 'update';
                    }
                }
            }
            if(isset($settings['validate'])){
                if($settings['validate']){ return $progress; }else{ return false; }
            }
        }
    }
    public function get($query,$options=false){
        $default_options = $this->default_options();
        $default_query = $this->default_query();
        if(is_array($options)){
            $default_options = array_merge($default_options,$options);
        }else{
            $default_options = $default_options;
        }
        if(is_array($query)){
            $this_query = array_merge($default_query,$query);
        }else{
            $this_query = $default_query;
        }
        $mongo = $this->connect($default_options,$this_query);
        return $mongo;
    }
    public function find_one($id,$options=false,$apply_shortcodes=false, $apply_filters=true){
        $default_options = $this->default_options();
        if(is_array($options)){
            $default_options = array_merge($default_options,$options);
        }else{
            $default_options = $default_options;
        };
        /* OPTIONS EXTRACTED - NOW MAKE CHECKS */
        $m = mongopress_load_m();
        $mongo_id = new MongoId($id);
        $db = $m->$default_options['db_name'];
        $objs = $db->$default_options['obj_col'];
        $mongo_obj = $objs->findOne(array("_id"=>$mongo_id));
        if(is_array($mongo_obj)){
            if($apply_shortcodes){
                $mongo_obj['content'] = apply_filters('mp_shortcodes', $mongo_obj['content']);
            }else{
                $mongo_obj['content'] = $mongo_obj['content'];
            }
			if ($apply_filters) {
                $mongo_obj['content'] = apply_filters('mp_get_content', $mongo_obj['content'], $id);
			}
            $object = $mongo_obj;
            $object['success']=true;
        }else{
            $object['success']=false;
        }
        return $object;
    }
    public function remove($options=false,$mongo_id=false){
        $default_options = $this->default_options();
        if(is_array($options)){
            $settings = array_merge($default_options,$options);
        }else{
            $settings = $default_options;
        };
        $m = mongopress_load_m();
        // Select / Create a Database
        $db = $m->$settings['db_name'];
        // Select / Create a Collection (table)
        $objs = $db->$settings['obj_col'];
        $criteria = array(
            '_id' => new MongoId($mongo_id),
        );
        $progress = $objs->remove($criteria,array('safe'=>true));
    }
    public function flush_slugs(){
        $default_options = $this->options();
        $m = mongopress_load_m();
        $db = $m->$default_options['db_name'];
        $slugs = $db->$default_options['slug_col'];
        $objs = $db->$default_options['obj_col'];
        $all_slugs = $this->arrayed($slugs->find());
				$progress_array = false;
        if(is_array($all_slugs)){
            foreach($all_slugs as $slug_array){
                $this_mongo_id = $this->get_mongoid_as_string($slug_array['_id']);
                $this_where_clause = array("slug_id"=>$this_mongo_id);
                $this_object_count = $objs->find($this_where_clause)->count();
		 if (isset($this_mongo_id) && $this_object_count<1) {
                    if(gettype($this_mongo_id == 'string')){
                        $criteria['_id'] = new MongoID($this_mongo_id);
                    }
                    $progress_array[$this_mongo_id] = $slugs->remove($criteria,array('safe'=>true));
        	}

            } 
	    if(is_array($progress_array) && !empty($progress_array)){
                return $progress_array;
            }
        }
    }
    public function import_html(){
        $default_options = $this->options();
        $content_folder = $GLOBALS['_MP']['THEME_ROOT'].'/'.$default_options['theme'].'/content/';
        if($folders = opendir($content_folder)){
            $content = array();
            while (false !== ($folder = readdir($folders))) {
                if(!strstr($folder, '.')){
                    $content['types'][$folder] = true;
                }
            }
            closedir($folders);
        }
        foreach($content['types'] as $type => $use){
            if($files = opendir($content_folder.'/'.$type.'/')){
                while (false !== ($file = readdir($files))) {
                    if(strstr($file, '.html')){
                        ob_start();
                        require_once $GLOBALS['_MP']['THEME_ROOT'].'/'.$default_options['theme'].'/content/'.$type.'/'.$file;
                        $inner_content = ob_get_clean();
                        $file = substr_replace($file,'',-5);
                        $file_array = explode('-_-',$file);
                        if(is_array($file_array)){
                            if(!empty($file_array[1])){
                                $file_name = $file_array[1];
                                if(isset($file_array[2])){
                                    $duplicate = $file_array[2];
                                }else{
                                    $duplicate = false;
                                }
                                $priority = $file_array[0];
                            }else{
                                $file_name = $file_array[0];
                                $priority = '';
                                $duplicate = '';
                            }
                        }else{
                            $file_name = $file;
                            $priority = '';
                        }
						//$this->dump($inner_content,false);
                        if(!empty($inner_content)){
                            $content_array['title'] = $this->between($inner_content, '<title>', '</title>');
                            /* TODO: THIS NEEDS SERIOUS IMPROVEMENT */
							if((strstr($inner_content, '<meta id="meta-start"'))&&(strstr($inner_content, 'id="meta-end">'))){
								$key1 = false; $value1 = false; $key2 = false; $value2 = false;
								$key1 = $this->between($inner_content, '<meta id="meta-start" name="key1" content="', '">');
								$value1 = $this->between($inner_content, '<meta name="value1" content="', '">');
								$key2 = $this->between($inner_content, '<meta name="key2" content="', '">');
								$value2 = $this->between($inner_content, '<meta name="value2" content="', '"');
								$meta_content = $this->between($inner_content, '<meta id="meta-start"', 'id="meta-end">');
								if((!empty($key1))&&(!empty($value1))){
									$content_array['custom'][$key1] = $value1;
									if((!empty($key2))&&(!empty($value2))){
										$content_array['custom'][$key2] = $value2;
									}
									$inner_content = str_replace('<meta id="meta-start"'.$meta_content.'id="meta-end">','',$inner_content);
								}
								//$this->dump($content_array,false);
								/* END OF TODO: */
							}else{
								$key1 = false; $value1 = false; $key2 = false; $value2 = false;
								$content_array['custom'] = false;
							}
                            $to_be_removed = '<title>'.$content_array['title'].'</title>';
                            $stripped_content = str_replace($to_be_removed,'',$inner_content);
                            $content_array['content'] = $stripped_content;
                        }
                        if($priority){
                            $content['objects'][$type][$priority]['slug'] = $file_name;
                            $content['objects'][$type][$priority]['type'] = $type;
                            $content['objects'][$type][$priority]['title'] = trim($content_array['title']);
                            $content['objects'][$type][$priority]['content'] = trim($content_array['content']);
                            if(isset($content_array['custom'])){
                                $content['objects'][$type][$priority]['custom'] = $content_array['custom'];
                            }
                            if($duplicate){
                                foreach($content['objects'][$type][$priority] as $object){
                                    //$mp->dump($object);
                                    $content['objects'][$type][$priority+1] = $object;
                                }
                            }
                        }else{
                            $object_contents = array();
                            $object_contents['slug'] = $file_name;
                            $object_contents['type'] = $type;
                            $object_contents['title'] = trim($content_array['title']);
                            $object_contents['content'] = trim($content_array['content']);
                            if(isset($content_array['custom'])){
                                $object_contents['custom'] = $content_array['custom'];
                            }
                            $content['objects'][$type][$file_name] = $object_contents;
                            if($duplicate){
                                for($i = 0; $i < $duplicate; $i++) {
                                    foreach($object_contents as $object_key => $object_value){
                                        //$x = floor($i/300);
                                        //if ($i % 100 == 0) { sleep(62); }
                                        $new_file_name = $file_name.'_'.$i;
                                        //if($object_key=='slug'){ $object_value = $x.'_'.$object_value.'_'.$i; }
                                        if($object_key=='slug'){ $object_value = $object_value.'_'.$i; }
                                        $temporarily_duplicated_object[$object_key] = $object_value;
                                        //$content['objects'][$type][$x.'_'.$new_file_name] = $temporarily_duplicated_object;
                                        $content['objects'][$type][$x.'_'.$new_file_name] = $temporarily_duplicated_object;
                                    }
																		
                                }
                            }
                        }
                    }
                }
                closedir($files);
            }
        }
        if (is_array($content['objects']) && !empty($content['objects'])){
		foreach($content['objects'] as $object_type => $objects){
                    ksort($objects);
                    foreach($objects as $object){
                        $this->push($default_options,$object,false);
                    }
                }
                //create index on database here to speed process later
                $progress['message']=__('Sucessfully Imported'); $progress['success']=true;
        } else {
                $progress['message']=__('Objects Not Imported'); $progress['success']=false;
        }
        return $progress;
    }
    public function is_logged_in(){
        $options = $this->default_options();
        $cookies = $GLOBALS['_MP']['COOKIE'];
        
		if(isset($cookies['mp_logged_in'])){ 
            if(isset($cookies['mp_user_id'])){
				return true;
            }
		}

        return false;
    }
	public function get_user_info($user_id){
		$m = mongopress_load_m();
		$options = $this->options();
		if($user_id){
			$db = $m->$options['db_name'];
			$user_mongo_id = new MongoID($user_id);
			$current_user = $this->arrayed($db->user->find(array("_id"=>$user_mongo_id)));
			return $current_user[0];
		}else{
			return false;
		}

    }
    public function get_current_user(){
        $logged_in = $this->is_logged_in();
        if($logged_in){
            $m = mongopress_load_m();
            $options = $this->options();
            $user_id = $GLOBALS['_MP']['COOKIE']['mp_user_id'];
            if($user_id){
                $db = $m->$options['db_name'];
                $user_mongo_id = new MongoID($user_id);
                $current_user = $this->arrayed($db->user->find(array("_id"=>$user_mongo_id)));
                return $current_user[0];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
   public function get_current_user_id(){
        $logged_in = $this->is_logged_in();
        if($logged_in){
            $m = mongopress_load_m();
            $options = $this->options();
            $user_id = $GLOBALS['_MP']['COOKIE']['mp_user_id'];
            if($user_id){
                $db = $m->$options['db_name'];
                $user_mongo_id = new MongoID($user_id);
                $current_user = $this->arrayed($db->user->find(array("_id"=>$user_mongo_id)));
                return $current_user[0];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function mp_site_options(){
        $m = mongopress_load_m();
        $options = $this->options();
        $db = $m->$options['db_name'];
        $opts = $db->$options['opts_col'];
        $saved_options = $this->arrayed($opts->find(array("key"=>'site_options')));
        if(is_array($saved_options[0]['options'])){
            $site_options = array();
            $site_name = $saved_options[0]['options']['site_name'];
            $site_description = $saved_options[0]['options']['site_description'];
            $cookie_ttl = $saved_options[0]['options']['cookie_ttl'];
            $site_options['site_name']=$site_name;
            $site_options['site_description']=$site_description;
            $site_options['cookie_ttl']=$cookie_ttl;
            return $site_options;
        }
    }
	public function mp_user_options($id){
        $m = mongopress_load_m();
        $options = $this->options();
        $db = $m->$options['db_name'];
        $user = $db->$options['user_col'];
		$mongo_id = new MongoId($id);
		$user_info = $this->arrayed($user->find(array("_id"=>$mongo_id)));
		/* SAVE USER INFO
		$mongo_id = new MongoId($obj['_id']);
		$user->update(array("_id" => $mongo_id), array('$set' => array("email"=>$obj['email'],"name"=>$obj['name'],"updated"=>$timestamp)));
		 *
		 */
        return $user_info;
    }

	public function setup_collection($collection) {
		$options = $this->default_options();
		$m = mongopress_load_m();

        $db = $m->$options['db_name'];
		
		$col = $db->$collection;
		$count = $col->count();

		if ($count == 0) {
			$data = array('-1');
			$col->insert($data);
		}
	}

    public function set_cookies($data){
		if (!is_array($data)) $data[0] = $data;
        $options = $this->default_options();

        $m = mongopress_load_m();

        $db = $m->$options['db_name'];

        $this->setup_collection($options['cookie_col']);

        $cookies = $db->$options['cookie_col'];


		$results = $db->command( array(
			'findAndModify' => $options['cookie_col'],
			'query' => false,
			'update' => $data,
			'new' => true,
			'upsert' => true,
			'fields' => array( '_id' => 1 )
		) );

		$cookie_id = $this->get_mongoid_as_string($results['value']['_id']);


		return $cookie_id;
    }

	public function get_cookies($key=false){
        $options = $this->default_options();

		if ($key == false && isset($_COOKIE['mp_' . $options['cookie_salt']])) {
				 $key = str_replace('$','',$_COOKIE['mp_' . $options['cookie_salt']]);
		} elseif ($key==false) return array();
		

        $m = mongopress_load_m();

        $db = $m->$options['db_name'];
        $cookies = $db->$options['cookie_col'];
        
		$mongo_id = new MongoId($key);
        $cookie = $cookies->findOne(array("_id"=>$mongo_id));

		return $cookie;
    }

	public function rm_cookies($key=false){
        $options = $this->default_options();

		if ($key == false && isset($_COOKIE['mp_' . $options['cookie_salt']])) {
				 $key = str_replace('$','',$_COOKIE['mp_' . $options['cookie_salt']]);
		} elseif ($key==false) return array();
		
        $m = mongopress_load_m();

        $db = $m->$options['db_name'];
        $cookies = $db->$options['cookie_col'];
        
		$mongo_id = new MongoId($key);
        $cookie = $cookies->remove(array("_id"=>$mongo_id));

		return $cookie;
    }

	public function types(){
		$m = mongopress_load_m();
		$default_options = $this->options();
		$db = $m->$default_options['db_name'];
		$objects = $db->command(array("distinct"=>$default_options['obj_col'],"key"=>"type"));
		$object_types = $objects['values'];
		return $object_types;
	}
}
