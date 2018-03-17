<?php
/**
 * Textarea field type
 *
 * @package default
 * @author Julien Guilmont
 */
class CF_Field_Textarea extends CF_Field{
	function CF_Field_Textarea() {
		$field_ops = array('classname' => 'field_textarea', 'description' => __( 'Block Text without editor', 'custom-fields') );
		$this->CF_Field('textarea', __('Textarea', 'custom-fields'), 'input-textarea', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);
		$entries = is_array($entries) ? $entries['name'] : $entries;
			echo $before_widget;
			if ( $title)
				echo $before_title . $title . $after_title;
		?>
		<textarea name="<?php echo $this->get_field_name('name'); ?>" id="<?php echo $this->get_field_id('pages'); ?>" rows="5" cols="50"><?php echo esc_html($entries)?></textarea>
		<?php if( isset($instance['description']) )?>
			<p class="howto"><?php echo $instance['description']; ?></p>
		<?php
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
		$title = esc_attr( $instance['title'] );
		$description = esc_html($instance['description']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'custom-fields'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
		<?php
	}
}
?>