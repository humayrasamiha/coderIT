<?php
class CF_Field_Dropdown_Users extends CF_Field{
	
	function CF_Field_Dropdown_Users() {
		
		$field_ops = array('classname' => 'field_dropdown', 'description' => __( 'Dropdown Users', 'custom-fields') );
		$this->CF_Field('dropdown', __('Dropdown Users', 'custom-fields'), 'input-dropdown', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		global $current_user, $user_ID;
		extract( $args );
		
		$entries = is_array($entries) ? $entries['name'] : $entries;
		
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
                        if(function_exists('get_users') )
                            $authors = get_users( array('role' => $instance['role']) );
                        else
                            $authors = get_editable_user_ids( $current_user->id, true );
                        
			if ( isset($entries) && !empty($entries) && !in_array($entries, $authors) )
				$authors[] = $entries;
			
			wp_dropdown_users( array('include' => $authors, 'name' => $this->get_field_name('name'), 'selected' => empty($entries) ? $user_ID : $entries) );

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
                $roles = get_option($wpdb->prefix . 'user_roles');
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
                <?php if(function_exists('get_users') ): ?>
                <p>
			<label for="<?php echo $this->get_field_id('role'); ?>"><?php _e('Role:', 'custom-fields'); ?></label>
                        <select class="widefat" id="<?php echo $this->get_field_id('role'); ?>" name="<?php echo $this->get_field_name('role'); ?>">
                            <option value="all" <?php selected('all', $instance['role']);?>><?php _e('All', 'custom-fields');?></option>
                            <?php foreach($roles as $key => $role):?>
                            <option value="<?php echo esc_attr($key);?>" <?php selected($key, $instance['role']);?>><?php echo esc_html($role['name']);?></option>
                            <?php endforeach;?>
                        </select>
		</p>
                <?php endif;?>
		<?php
	}
}
?>