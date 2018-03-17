<?php
/**
 * Class Full Editor Type
 *
 * @package default
 * @author Amaury Balmer
 */
class CF_Field_Editor extends CF_Field{
	
	function CF_Field_Editor(){
		$field_ops = array('classname' => 'field_editor', 'description' => __( 'Editor', 'custom-fields') );
		$this->CF_Field('editor', __('Editor', 'custom-fields'), '_input-editor', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$entries = is_array($entries) ? $entries['name'] : $entries;

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Editor', 'custom-fields') : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;

		the_editor( $entries, $this->get_field_name('name'), 'title', false, 10 );

		echo $after_widget;
	}
	
	function save( $values ){
		$values = $values['name'];
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = esc_attr( $instance['title'] );
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<?php
	}
}
?>