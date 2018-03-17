<?php
class CF_Field_Checkbox extends CF_Field{
	
	function CF_Field_Checkbox() {
		$field_ops = array('classname' => 'field_checkbox', 'description' => __( 'Check boxes are used when you want to let the visitor select one or more options from a set of alternatives.', 'custom-fields') );
		$this->CF_Field('checkbox', __('Checkbox', 'custom-fields'), '_input-checkbox', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$values = array();
                $tabflag = false;
		$v = explode('#', $instance['settings']);
                if( count($v) == 1 )
                    $tabflag = true;
		//$ti = array_shift($v);
		if( empty($v))
			return false;
		foreach($v as $val) {
			$a = explode('|', $val);
			if( count($a) != 2)
				continue;
			$values[$a[0]] = $a[1];
		}
		if(empty($values))
			return false;
			
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
		
		echo '<div class="checkbox-field">';
			foreach( (array) $values as $key => $val ) {
                                if( $tabflag )
                                    $checked = $val == $entries;
                                else
                                    $checked = in_array($val, (array)$entries);
				echo '<label><input type="checkbox" name="'.$this->get_field_name('name'). ($tabflag ? '' : '[]') . '" id="'.$this->get_field_id('name').'" value="'.esc_attr($val).'" '.checked(true, $checked, false).'/> '.$key.'</label>' . "\n";
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
			<br/><small>Parameters like : label1|id1#label2|id2</small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'custom-fields'); ?></label>
			<textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea>
		</p>
		<?php
	}
	
}
?>