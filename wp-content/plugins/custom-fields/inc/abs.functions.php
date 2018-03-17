<?php
abstract class Functions {
	
    private $options;
	
    function _get_field_id_base($id) {
        return preg_replace( '/-[0-9]+$/', '', $id );
    }

    function cf_list_field_controls_dynamic_sidebar( $params ) {
		static $i = 0;
		$i++;

		$field_id = $params[0]['field_id'];
		$id = isset($params[0]['_temp_id']) ? $params[0]['_temp_id'] : $field_id;
		$hidden = isset($params[0]['_hide']) ? ' style="display:none;"' : '';

		$params[0]['before_field'] = "<div id='field-${i}_$id' class='field'$hidden>";
		$params[0]['after_field'] = "</div>";
		//$params[0]['before_field'] = "%BEG_OF_TITLE%"; // deprecated
		//$params[0]['after_field'] = "%END_OF_TITLE%"; // deprecated
		if ( is_callable( $this->cf_registered_fields[$field_id]['callback'] ) ) {
			$this->cf_registered_fields[$field_id]['_callback'] = $this->cf_registered_fields[$field_id]['callback'];
			$this->cf_registered_fields[$field_id]['callback'] = 'cf_field_control';
		}

		return $params;
	}
	
	function update_var( $field = null ) {
		$options = array();
		$flag = true;
		if($field == null) {
			$options['cf_registered_sidebars'] 		= $this->cf_registered_sidebars;
			$options['sidebars_fields'] 			= $this->sidebars_fields;
			$options['cf_registered_fields']		= $this->cf_registered_fields;
		}else{
			if( empty($this->options) )
				$this->options = wp_cache_get('cf_options-'.$this->post_type, FLAG_CACHE);

            $options = $this->options;
			if( !isset($options[$field]) || 
			(isset($options[$field]) && $options[$field] !== $this->{$field} ) ) {
				$options[$field] = $this->{$field};
			} else {
				$flag = false;
			}	
		}
		
		if($flag == true) {
			update_option( 'cf_options-'.$this->post_type, $options );
			wp_cache_replace('cf_options-'.$this->post_type, $options, FLAG_CACHE, 3600);
		}
	}
	
	function get_var( $field = null ) {
		/*$options = wp_cache_get('cf_options-'.$this->post_type, FLAG_CACHE);
		if( $field == null ) {
			$this->cf_registered_sidebars 	= (array)$options['cf_registered_sidebars'];
			$this->sidebars_fields			= (array)$options['sidebars_fields'];
			$this->cf_registered_fields 	= (array)$options['cf_registered_fields'];
			$this->option_fields 			= (array)$options['option_fields'];
		}else{
			if( isset($options[$field]) )
				$this->$field = (array)$options[$field];
			else
				$this->$field = array();
		}*/
	}
	
	function cf_set_sidebars_fields( $sidebars_fields ) {
		if ( !isset( $sidebars_fields['array_version'] ) )
			$sidebars_fields['array_version'] = 3;
		$this->sidebars_fields = $sidebars_fields;
		$this->update_var('sidebars_fields');
	}
	
	function cf_get_sidebars_fields($deprecated = true) {
		if ( $deprecated !== true )
			_deprecated_argument( __FUNCTION__, '2.8.1' );
		//global $this->_cf_sidebars_fields;
		// If loading from front page, consult $this->_cf_sidebars_fields rather than options
		// to see if cf_convert_field_settings() has made manipulations in memory.
		if ( !is_admin() ) {
			if ( empty($this->_cf_sidebars_fields) )
				$this->_cf_sidebars_fields = $this->sidebars_fields;
	
			$sidebars_fields = $this->_cf_sidebars_fields;
		} else {
			$sidebars_fields = $this->sidebars_fields;
			$_sidebars_fields = array();
			if ( isset($sidebars_fields['cf_inactive_fields']) || empty($sidebars_fields) )
				$sidebars_fields['array_version'] = 3;
			elseif ( !isset($sidebars_fields['array_version']) )
				$sidebars_fields['array_version'] = 1;
			switch ( $sidebars_fields['array_version'] ) {
				case 1 :
					foreach ( (array) $sidebars_fields as $index => $sidebar )
					if ( is_array($sidebar) )
					foreach ( (array) $sidebar as $i => $name ) {
						$id = strtolower($name);
						if ( isset($this->cf_registered_fields[$id]) ) {
							$_sidebars_fields[$index][$i] = $id;
							continue;
						}
						$id = sanitize_title($name);
						if ( isset($this->cf_registered_fields[$id]) ) {
							$_sidebars_fields[$index][$i] = $id;
							continue;
						}
	
						$found = false;
	
						foreach ( $this->cf_registered_fields as $field ) {
							if ( strtolower($field['name']) == strtolower($name) ) {
								$_sidebars_fields[$index][$i] = $field['id'];
								$found = true;
								break;
							} elseif ( sanitize_title($field['name']) == sanitize_title($name) ) {
								$_sidebars_fields[$index][$i] = $field['id'];
								$found = true;
								break;
							}
						}
	
						if ( $found )
							continue;
	
						unset($_sidebars_fields[$index][$i]);
					}
					$_sidebars_fields['array_version'] = 2;
					$sidebars_fields = $_sidebars_fields;
					unset($_sidebars_fields);
	
				case 2 :
					$sidebars = array_keys($this->cf_registered_fields );
					if ( !empty( $sidebars ) ) {
						// Move the known-good ones first
						foreach ( (array) $sidebars as $id ) {
							if ( array_key_exists( $id, $sidebars_fields ) ) {
								$_sidebars_fields[$id] = $sidebars_fields[$id];
								unset($sidebars_fields[$id], $sidebars[$id]);
							}
						}
	
						// move the rest to wp_inactive_fields
						if ( !isset($_sidebars_fields['cf_inactive_fields']) )
							$_sidebars_fields['cf_inactive_fields'] = array();
	
						if ( !empty($sidebars_fields) ) {
							foreach ( $sidebars_fields as $val ) {
								if ( is_array($val) )
									$_sidebars_fields['cf_inactive_fields'] = array_merge( (array) $_sidebars_fields['cf_inactive_fields'], $val );
							}
						}
	
						$sidebars_fields = $_sidebars_fields;
						unset($_sidebars_fields);
					}
			}
		}
	
		if ( is_array( $sidebars_fields ) && isset($sidebars_fields['array_version']) )
			unset($sidebars_fields['array_version']);
		
		$this->sidebars_fields = apply_filters('sidebars_fields', $sidebars_fields);

		return $sidebars_fields;
	}
}
?>
