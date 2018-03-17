<?php
class CF_Field_Separator extends CF_Field{
	
	function CF_Field_Separator() {
		$field_ops = array('classname' => 'field_separator', 'description' => __( 'Separator horizontal') );
		$this->CF_Field('separator', __('Separator'), 'field_separator', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );

		echo $before_widget;
		echo '<hr class="separator" />';
		echo $after_widget;
	}
	
	function save( $values ) {
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		return $instance;
	}

	function form( $instance ) {
	}
	
}
?>