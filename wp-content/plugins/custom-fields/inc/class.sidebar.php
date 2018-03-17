<?php
class CF_Sidebar_Field extends Functions{
	private $pt;
	protected static $class = __CLASS__;
        
	function __construct(){
	}
	
	function setter( $options, $objects ) {
	   foreach( $options as $name => &$opt )
	       $this->{$name} = &$opt;
	   foreach( $objects as $name => &$obj )
	   	$this->{$name} = &$obj;
	 }

	function cf_register_sidebar($args = array()) {
		$i = count($this->cf_registered_sidebars) + 1;
		$defaults = array(
			'name' => sprintf(__('Sidebar %d'), $i ),
			'id' => "sidebar-$i",
			'description' => '',
			'before_field' => '<li id="%1$s" class="field %2$s">',
			'after_field' => "</li>\n",
			'before_title' => '<h2 class="fieldtitle">',
			'after_title' => "</h2>\n",
		);
	
		$sidebar = wp_parse_args( $args, $defaults );
	
		$this->cf_registered_sidebars[$sidebar['id']] = $sidebar;
		add_theme_support('fields');
	
		do_action( 'cf_register_sidebar', $sidebar );
	
		return $sidebar['id'];
	}
	
	function cf_unregister_sidebar( $name ) {
		if ( isset( $this->cf_registered_sidebars[$name] ) ) {
			unset( $this->cf_registered_sidebars[$name] );
		}
	}

	function dynamic_sidebar($index = 1) {
		if ( is_int($index) ) {
			$index = "sidebar-$index";
		} else {
			$index = sanitize_title($index);
			foreach ( (array) $this->cf_registered_sidebars as $key => $value ) {
				if ( sanitize_title($value['name']) == $index ) {
					$index = $key;
					break;
				}
			}
		}
		
		$sidebars_fields = $this->cf_get_sidebars_fields();
		if ( empty($this->cf_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_fields) || !is_array($sidebars_fields[$index]) || empty($sidebars_fields[$index]) )
			return false;
		$sidebar = $this->cf_registered_sidebars[$index];

		$did_one = false;
		
		foreach ( (array) $sidebars_fields[$index] as $id ) {
			if ( !isset($this->cf_registered_fields[$id]) ) continue;

			$params = array_merge(
				array( array_merge( $sidebar, array('field_id' => $id, 'field_name' => $this->cf_registered_fields[$id]['name']) ) ),
				(array) $this->cf_registered_fields[$id]['params']
			);
			// Substitute HTML id and class attributes into before_widget
			$classname_ = '';
			foreach ( (array) $this->cf_registered_fields[$id]['classname'] as $cn ) {
				if ( is_string($cn) )
					$classname_ .= '_' . $cn;
				elseif ( is_object($cn) )
					$classname_ .= '_' . get_class($cn);
			}

			$classname_ = ltrim($classname_, '_');
			$params[0]['before_field'] = sprintf($params[0]['before_field'], $id, $classname_);

			$params = apply_filters( 'dynamic_sidebar_params', $params );
			$params[0]['post_type'] = $this->post_type;
			$callback = $this->cf_registered_fields[$id]['callback'];
			do_action( 'dynamic_sidebar', $this->cf_registered_fields[$id] );
			if ( is_callable( array(&$this->cf_field_control, $callback) ) ) {
				call_user_func_array( array(&$this->cf_field_control, $callback), $params);
				$did_one = true;
			}
		}
		return $did_one;
	}
}