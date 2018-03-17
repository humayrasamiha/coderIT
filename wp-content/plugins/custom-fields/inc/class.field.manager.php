<?php
class CF_Field_Manager extends Functions{
	private $pt;
	protected static $class = __CLASS__;
        
	function __construct(){
	}
	
	function setter( $options, $objects) {
	   foreach( $options as $name => &$opt )
	       $this->{$name} = &$opt;
	   foreach( $objects as $name => &$obj )
	   	$this->{$name} = &$obj;
	 }
	
	function register_field($field_class) {
		$this->cf_field_factory->register($field_class);
	}
	
	function unregister_field($field_class) {
		$this->cf_field_factory->unregister($field_class);
	}

	function cf_list_fields() {
		$sort = $this->cf_registered_fields;
		usort( $sort, create_function( '$a, $b', 'return strnatcasecmp( $a["name"], $b["name"] );' ) );
		$done = array();
	
		foreach ( $sort as $field ) {
			if ( in_array( $field['callback'], $done, true ) ) // We already showed this multi-field
				continue;

			$sidebar = $this->cf_is_active_field( $field['callback'], $field['id'], false, false );
			$done[] = $field['callback'];
	
			if ( ! isset( $field['params'][0] ) )
				$field['params'][0] = array();
	
			$args = array( 'field_id' => $field['id'], 'field_name' => $field['name'], '_display' => 'template' );
	
			if ( isset($this->cf_registered_field_controls[$field['id']]['id_base']) && isset($field['params'][0]['number']) ) {
				$id_base = $this->cf_registered_field_controls[$field['id']]['id_base'];
				$args['_temp_id'] = "$id_base-__i__";
				$args['_multi_num'] = $this->next_field_id_number($id_base);
				$args['_add'] = 'multi';
			} else {
				$args['_add'] = 'single';
				if ( $sidebar )
					$args['_hide'] = '1';
			}
			$args = $this->cf_field_control->cf_list_field_controls_dynamic_sidebar( array( 0 => $args, 1 => $field['params'][0] ) );
			$args[0]['post_type'] = $this->post_type;
			call_user_func_array( array(&$this->cf_field_control, 'cf_field_control'), $args );
		}
	}
	
	
	function cf_is_active_field($callback = false, $field_id = false, $id_base = false, $skip_inactive = true) {
		$sidebars_fields = $this->cf_get_sidebars_fields();
		if ( is_array($sidebars_fields) ) {
			foreach ( $sidebars_fields as $sidebar => $fields ) {
				if ( $skip_inactive && 'cf_inactive_fields' == $sidebar )
					continue;
				if ( is_array($fields) ) {
					foreach ( $fields as $field ) {
						if ( ( $callback && isset($this->cf_registered_fields[$field]['callback']) && $this->cf_registered_fields[$field]['callback'] === $callback ) || ( $id_base && $this->_get_field_id_base($field) == $id_base ) ) {
							if ( !$field_id || $field_id == $this->cf_registered_fields[$field]['id'] )
								return $sidebar;
						}
					}
				}
			}
		}
		return false;
	}
	
	function _register_field_update_callback($id_base, $update_callback, $options = array()) {
		if ( isset($this->cf_registered_field_updates[$id_base]) ) {
			if ( empty($update_callback) )
				unset($this->cf_registered_field_updates[$id_base]);
			return;
		}
	
		$field = array(
			'callback' => $update_callback,
			'params' => array_slice(func_get_args(), 3)
		);
	
		$field = array_merge($field, $options);
		$this->cf_registered_field_updates[$id_base] = $field;
	}
	
	function _register_field_form_callback($id, $name, $form_callback, $options = array()) {
	
		$id = strtolower($id);
	
		if ( empty($form_callback) ) {
			unset($this->cf_registered_field_controls[$id]);
			return;
		}
	
		if ( isset($this->cf_registered_field_controls[$id]) && !did_action( 'fields_init' ) )
			return;
	
		$defaults = array('width' => 250, 'height' => 200 );
		$options = wp_parse_args($options, $defaults);
		$options['width'] = (int) $options['width'];
		$options['height'] = (int) $options['height'];
	
		$field = array(
			'name' => $name,
			'id' => $id,
			'callback' => $form_callback,
			'params' => array_slice(func_get_args(), 4)
		);
		$field = array_merge($field, $options);
	
		$this->cf_registered_field_controls[$id] = $field;
	}

	function cf_convert_field_settings($base_name, $option_name, $settings) {
		// This test may need expanding.
		$single = $changed = false;

		if ( empty($settings) ) {
			$single = true;
		} else {
			foreach ( array_keys($settings) as $number ) {
				if ( 'number' == $number )
					continue;
				if ( !is_numeric($number) ) {
					$single = true;
					break;
				}
			}
		}
		if ( $single ) {
			$settings = array( 2 => $settings );

			// If loading from the front page, update sidebar in memory but don't save to options
			$this->get_var('sidebars_fields');
			if ( is_admin() ) {
				$sidebars_fields = $this->sidebars_fields;
			} else {
				if ( empty($this->_cf_sidebars_fields) )
					$this->_cf_sidebars_fields = $this->sidebars_fields;
				$sidebars_fields = &$this->_cf_sidebars_fields;
			}
			foreach ( (array) $sidebars_fields as $index => $sidebar ) {
				if ( is_array($sidebar) ) {
					foreach ( $sidebar as $i => $name ) {
						if ( $base_name == $name ) {
							$sidebars_fields[$index][$i] = "$name-2";
							$changed = true;
							break 2;
						}
					}
				}
			}
			if ( is_admin() && $changed ) {
				$this->sidebars_fields = $sidebars_fields;
				$this->update_var('sidebars_fields');
			}
		}
		
		$settings['_multifield'] = 1;
		if ( is_admin() ) {
			$this->option_fields[$option_name] = $settings;
			$this->update_var('option_fields');
		}
			//update_option( $this->option_fields[$this->option_name], $settings );
		return $settings;
	}
	
	function next_field_id_number($id_base) {
		$number = 1;
	
		foreach ( $this->cf_registered_fields as $field_id => $field ) {
			$matches = array();
			if ( preg_match( '/' . $id_base . '-([0-9]+)$/', $field_id, $matches ) )
				$number = max($number, $matches[1]);
		}
		$number++;
	
		return $number;
	}
}
?>