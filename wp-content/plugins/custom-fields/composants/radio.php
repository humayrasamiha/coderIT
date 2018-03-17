<?php
class CF_Field_Radio extends CF_Field{
	
	function CF_Field_Radio() {
		$field_ops = array('classname' => 'field_radio', 'description' => __( 'Radio buttons are used when you want to let the visitor select one - and just one - option from a set of alternatives.', 'custom-fields') );
		$this->CF_Field('radio', __('Radio', 'custom-fields'), '_input-radio', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$entries = is_array($entries) ? $entries['name'] : $entries;
		
		$values = array();
		$v = explode('#', $instance['settings']);
		//$ti = array_shift($v);
		if( empty($v))
			return false;
		foreach($v as $val) {
			$a = explode('|', $val);
			if( count($a) != 2)
				continue;

                        if( is_numeric( strpos($a[1], '~') ) ){
                            $a[1] = str_replace('~', '', $a[1]);
                            $default_checked = $a[1];
                        }
			$values[$a[0]] = $a[1];
		}
		if(empty($values))
			return false;

                if( empty($entries) && isset($default_checked) )
                    $entries[] = $default_checked;

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
		
		echo '<div class="radio-field">';
			foreach( (array) $values as $key => $val ) {
				echo '<label><input type="radio" name="'.$this->get_field_name('name').'[]" id="'.$this->get_field_id('name').'" value="'.esc_attr($val).'" '.checked(true, in_array($val, (array)$entries), false).'/> '.$key.'</label>' . "\n";
			}
		echo '</div>';
		
		if( isset($instance['description']) && $instance['description'] != '' )
			echo '<p class="howto">' . $instance['description'] . '</p>';
			
		echo $after_widget;
	}
	
	function save( $values ) {
		$values = isset($values['name']) ? $values['name'] : '' ;
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['settings'] 		= strip_tags($new_instance['settings']);
		$instance['description'] 	= strip_tags($new_instance['description']);

		return $instance;
	}

	function form( $instance ) {
		// Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'settings' => '', 'description' => '' ) );
		
		$title = esc_attr( $instance['title'] );
		$settings = esc_attr( $instance['settings'] );
		$description = esc_html($instance['description']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('settings'); ?>"><?php _e('Settings:', 'custom-fields'); ?></label> 
			<textarea class="widefat" id="<?php echo $this->get_field_id('settings'); ?>" name="<?php echo $this->get_field_name('settings'); ?>" ><?php echo $settings; ?></textarea>
			<br/><small>Parameters like : label1|id1#label2|id2 . Use ~ after id for default item</small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'custom-fields'); ?></label>
			<textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea>
		</p>
		<?php
	}
	
}
?>