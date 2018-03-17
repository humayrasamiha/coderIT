<?php
class CF_Field_Dropdown_Pages extends CF_Field{
	
	function CF_Field_Dropdown_Pages() {
		
		$field_ops = array('classname' => 'field-dropdown_page', 'description' => __( 'Dropdown Pages', 'custom-fields') );
		$this->CF_Field('dropdown-pages', __('Dropdown Pages', 'custom-fields'), 'input-dropdown-pages', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		global $current_user, $user_ID, $post;
		extract( $args );
		
		$entries = is_array($entries) ? $entries['name'] : $entries;
		
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
                        if( $post->post_type == 'page' )
                                $exclude = $post->ID;
			wp_dropdown_pages( array('name' => $this->get_field_name('name'), 'selected' => empty($entries) ? 0 : $entries) );

		echo $after_widget;
	}
	
	function save( $values ) {
		$values = $values['name'];
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form( $instance ) {
                global $wpdb;
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<?php
	}
}
?>