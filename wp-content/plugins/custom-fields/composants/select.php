<?php
 /**
  * Class for select list type
  *
  * @package default
  * @author Julien Guilmont
  */
 class CF_Field_Select extends CF_Field{
 	
 	function CF_Field_Select() {
 		$field_ops = array('classname' => 'field_select', 'description' => __( 'The Select tag creates a control through which users are able to select from a list of options.', 'custom-fields') );
 		$this->CF_Field('select', __('Select', 'custom-fields'), '_input-select', true, $field_ops);
 	}
 	
 	function field( $args, $instance ) {
 		extract( $args );
 		
 		$entries = is_array($entries) ? $entries['name'] : $entries;
 		$values = array();
 		$v = explode('#', $instance['settings']);
 		
 		//$ti = array_shift($v);
 		
 		foreach($v as $val) {
 			$a = explode('|', $val);
 			$values[$a[0]] = $a[1];
 		}
 		$title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);
 		
 		echo $before_widget;
 		if ( $title)
 			echo $before_title . $title . $after_title;
 		echo '<div class="select-field">';
 		echo '<select name="'.$this->get_field_name('name').'" id="'.$this->get_field_id('name').'" style="width: 47%;">';
 			foreach( (array) $values as $key => $val ) {
 				echo '<option value="'.esc_attr($val).'" '.selected($val, $entries, false).'>'.esc_html(ucfirst($key)).'</option>' . "\n";
 			}
 		echo '</select>' . "\n";
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
 
 		$instance['title'] = strip_tags($new_instance['title']);
 		$instance['settings'] = strip_tags($new_instance['settings']);
 		$instance['description'] = strip_tags($new_instance['description']);
 		return $instance;
 	}
 
 	function form( $instance ) {
 		//Defaults
 		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'settings' => '' ) );
 		$title = esc_attr( $instance['title'] );
 		$settings = esc_attr( $instance['settings'] );
 		$description = esc_html($instance['description']);
 		?>
 		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
 		
 		<p><label for="<?php echo $this->get_field_id('settings'); ?>"><?php _e('Settings:', 'custom-fields'); ?></label> 
 		<textarea class="widefat" id="<?php echo $this->get_field_id('settings'); ?>" name="<?php echo $this->get_field_name('settings'); ?>" ><?php echo $settings; ?></textarea>
 		<br/><small><?php _e('Parameters like : label1|id1#label2|id2', 'custom-fields'); ?></small></p>
 		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'custom-fields'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
 		<?php
 	}
 }
 ?>