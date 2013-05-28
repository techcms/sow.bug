<?php
	class DT_cats_Widget extends WP_Widget {
		function DT_cats_Widget() {
			parent::WP_Widget( 'dt_categories', $name = THEME_TITLE. __( ' Categories', 'dt') );
		}

		function form($instance) {
			// outputs the options form on admin
			if ( $instance ) {
				$title = esc_attr( $instance[ 'title' ] );
				$cols = intval( $instance[ 'cols' ] );
			}else{
				$title = '';
				$cols = 1;
			}
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title') ?>"><?php echo __( 'Title:', 'dt' ) ?></label>
				<input 	style="width: 100px;" id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>" type="text" value="<?php echo $title ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('columns') ?>"><?php echo __( 'Columns:', 'dt' ) ?></label>
				<select style="width: 100px;" id="<?php echo $this->get_field_id('columns') ?>" name="<?php echo $this->get_field_name('cols') ?>">
				   <option value="1"<?php echo (1 == $cols? ' selected="selected"': '') ?>>1</option>
				   <option value="2"<?php echo (2 == $cols? ' selected="selected"': '') ?>>2</option>
				</select>
			</p>
			<?php
		}

		function update($new_instance, $old_instance) {
			// processes widget options to be saved
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['cols'] = intval($new_instance['cols']);
			return $instance;
		}

		function widget($args, $instance) {
			// outputs the content of the widget
			extract( $args );
			
			$title = apply_filters( 'widget_title', $instance['title'] );
			$cols = isset($instance['cols'])?$instance['cols']:1;
			ob_start();
			wp_list_categories('orderby=name');
			$ret=ob_get_clean();
			preg_match_all('/(<a[^>]+>[^<]+<\/a>)/', $ret, $m);
			$cats_count = count( $m[1] );
			$i = 0;
			
			echo $before_widget;
			if( $title )
				echo $before_title . $title . $after_title;
			echo '<ul class="categories'. (2 == $cols?' col_2':''). '">';
			
			foreach( $m[1] as $cat ){
				echo '<li'. ($i++ >= ($cats_count - $cols)? ' class="last"' : ''). '>'. $cat. '</li>'."\n";
			}
			
			echo '</ul>';
			echo $after_widget;
		}
	}
	
	function dt_cats_register() {
		register_widget('DT_cats_Widget');
	}
	
	add_action( 'widgets_init', 'dt_cats_register' );
/*	
	
	
	
	
	
	
	
	
	
	function widget_sakura_Cats($args) {

		extract($args);

		// These are our own options
		$options = get_option('widget_sakura_Cats');
		$curtag = $options['curtag'];  // Your Twitter account name
		$title = $options['title'];  // Title in sidebar for widget
		$show = $options['show'];  // # of Updates to show
		$upd = $options['upd'];  // # of Updates to show
		$cols = $options['cols'];  // # of Updates to show
		if (!$cols) $cols = "1";

        // Output
		echo $before_widget ;

		// start
		
		echo $before_title . ($title ? $title : "Categories") . $after_title;
              
      ?>
  
  <ul class="categories<?php if ($cols == '2') echo ' col_2'; ?>">
   <?php
      ob_start();
      wp_list_categories('orderby=name');
      $ret=ob_get_clean();
      preg_match_all('/(<a[^>]+>[^<]+<\/a>)/', $ret, $m);
      $c=1;
	  $l = count($m[1]) - 1;
      foreach ($m[1] as $s)
      {
         echo '<li'.($c++ >= $l? ' class="last"' : '').'>'.$s.'</li>'."\n";
		 
      }
   ?>
  </ul>
      
      <?php

		// echo widget closing tag
		echo $after_widget;
	}

	// Settings form
	function widget_sakura_Cats_control() {

		// Get options
		$options = get_option('widget_sakura_Cats');
		// options exist? if not set defaults
		if ( !is_array($options) )
			$options = array('curtag'=>'', 'title'=>'Categories', 'show'=>'5', 'upd' => '60', 'cols' => '1');

        // form posted?
		if ( isset($_POST['sakura_Twitter2-submit']) && $_POST['sakura_Twitter2-submit'] ) {

			// Remember to sanitize and format use input appropriately.
			$options['curtag'] = strip_tags(stripslashes($_POST['sakura_Twitter-curtag']));
			$options['title'] = strip_tags(stripslashes($_POST['sakura_Twitter2-title']));
			$options['cols'] = strip_tags(stripslashes($_POST['sakura_Twitter322-title']));
			$options['show'] = strip_tags(stripslashes($_POST['sakura_Twitter-show']));
			$options['upd'] = strip_tags(stripslashes($_POST['sakura_Twitter-upd']));
			update_option('widget_sakura_Cats', $options);
		}

		// Get options for form fields to show
		$curtag = htmlspecialchars($options['curtag'], ENT_QUOTES);
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$show = htmlspecialchars($options['show'], ENT_QUOTES);
		$upd = htmlspecialchars($options['upd'], ENT_QUOTES);
		$cols = htmlspecialchars($options['cols'], ENT_QUOTES);

      if (!$upd) $upd=60;

		// The form fields
		   echo '<p>
				   <label for="Twitter-title">' . __('Title:') . '<br />
				   <input style="width: 100px;" id="Twitter-title" name="sakura_Twitter2-title" type="text" value="'.$title.'" />
				   </label></p>';
		echo '<p>
				<label for="Twitter-title222">' . __('Columns:') . '<br />
				<select style="width: 100px;" id="Twitter-title222" name="sakura_Twitter322-title">
				   <option value="1"'.($cols == "1" ? ' selected="selected"': '').'>1</option>
				   <option value="2"'.($cols == "2" ? ' selected="selected"': '').'>2</option>
				</select>
				</label></p>';
		echo '<input type="hidden" id="sakura_Twitter-submit" name="sakura_Twitter2-submit" value="1" />';
	}	
	
   wp_register_sidebar_widget(9001, (THEME_TITLE.' Categories'), 'widget_sakura_Cats');
   wp_register_widget_control(9001, (THEME_TITLE.' Categories'), 'widget_sakura_Cats_control');
   
?>*/
