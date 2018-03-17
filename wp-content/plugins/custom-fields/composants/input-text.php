<?php
class CF_Field_Input extends CF_Field{
	
	function CF_Field_Input() {
		$field_ops = array('classname' => 'field_inputtext', 'description' => __( 'Text fields are one line areas that allow the user to input text.', 'custom-fields') );
		$this->CF_Field('inputtext', __('Input Text', 'custom-fields'), 'input-inputtext', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );

                if( is_array($entries) && isset($entries['name']) && !empty($entries['name']) ){
                    $entries = $entries['name'];
                }elseif( !is_array($entries) ){
                    $entries = $entries;
                }else{
                    $entries = '';
                }

		
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
		
		echo '<input type="text" name="'.$this->get_field_name('name').'" id="'.$this->get_field_id('name').'" value="'.esc_attr($entries).'"/> ';
		if( isset($instance['description']) && $instance['description'] != '' )
			echo '<p>' . $instance['description'] . '</p>';
		
		echo $after_widget;
	}
	
	function save( $values ) {
		$values = isset($values['name']) ? $values['name'] : '' ;
		return $values;
	}


	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['description'] = strip_tags($new_instance['description']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'description' => '' ) );
		$description = esc_html($instance['description']);
		$title = esc_attr( $instance['title'] );
		?>
                <div class="col1">
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
		</div><?php
	}
	
}
?>