<?php
class CF_Field_Sidebar extends Functions{

        protected static $class = __CLASS__;

	function __construct( $options ) {
		foreach( $options as $name => &$opt )
		    $this->{$name} = &$opt;
	}
	function cf_register_sidebar_field($id, $name, $output_callback, $save_callback, $options = array()) {
		$id = strtolower($id);
		if ( empty($output_callback) ) {
			unset($this->cf_registered_fields[$id]);
			return;
		}
		
		$id_base = $this->_get_field_id_base($id);
		if ( in_array($output_callback, $this->_cf_deprecated_fields_callbacks, true) && !is_callable($output_callback) ) {
			if ( isset($this->cf_registered_field_controls[$id]) )
				unset($this->cf_registered_field_controls[$id]);
			
			if ( isset($this->cf_registered_field_updates[$id_base]) )
				unset($this->cf_registered_field_updates[$id_base]);
		
			return;
		}
		
		$defaults = array('classname' => $output_callback);
		$options = wp_parse_args($options, $defaults);
		$field = array(
			'name' => $name,
			'id' => $id,
			'callback' => $output_callback,
			'save_callback' => $save_callback,
			'params' => array_slice(func_get_args(), 5)
		);
		$field = array_merge($field, $options);

		if ( is_callable($output_callback) && ( !isset($this->cf_registered_fields[$id]) || did_action( 'fields_init' ) ) ) {
			do_action( 'cf_register_sidebar_field', $field );
			$this->cf_registered_fields[$id] = $field;
		}
	}
}