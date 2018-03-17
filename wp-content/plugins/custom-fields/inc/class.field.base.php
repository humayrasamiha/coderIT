<?php
/**
 * This class must be extended for each field and WP_field::field(), WP_field::update()
 * and WP_field::form() need to be over-ridden.
 *
 * @package WordPress
 * @subpackage fields
 * @since 2.8
 */
class CF_Field extends Functions {
	var $id_base;			// Root id for all fields of this type.
	var $name;				// Name for this field type.
	var $field_options;	// Option array passed to wp_register_sidebar_field()
	var $control_options;	// Option array passed to wp_register_field_control()
	var $data_name;
	var $alone_value = true;
	var $option_name;
        var $require = null;
	var $slug = null;
	
	var $number = false;	// Unique ID number of the current instance.
	var $id = false;		// Unique ID string of the current instance (id_base-number)
	var $updated = false;	// Set true when we update the data after a POST submit - makes sure we don't do it twice.
	var $post_type;
	private $pt;

	// Member functions that you must over-ride.

	/** Echo the field content.
	 *
	 * Subclasses should over-ride this function to generate their field code.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_field, and after_field.
	 * @param array $instance The settings for the particular instance of the field
	 */
	function field($args, $instance) {
		die('function cf_Field::field() must be over-ridden in a sub-class.');
	}
	
	
	/** Echo the field content.
	 *
	 * Subclasses should over-ride this function to generate their field code.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_field, and after_field.
	 * @param array $instance The settings for the particular instance of the field
	 */
	function save($args, $instance) {
		die('function cf_Field::save() must be over-ridden in a sub-class.');
	}

        function required($args, $instance) {
                $error = array();
                if( !isset($instance['require']) )
                    return false;
                if( $instance['require'] && empty( $args['entries'] ) ){
                    $error[$args['field_id']] =  sprintf(__('Field %s is required', 'custom-fields'), $instance['title']);
                }
                return $error;
                die('function cf_Field::required() must be over-ridden in a sub-class.');
	}

	/** Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** Echo the settings update form
	 *
	 * @param array $instance Current settings
	 */
	function form($instance) {
		echo '<p class="no-options-field">' . __('There are no options for this field.') . '</p>';
		return 'noform';
	}

	// Functions you'll need to call.

	/**
	 * PHP4 constructor
	 */
	function cf_Field( $id_base = false, $name, $data_name, $alone_value, $field_options = array(), $control_options = array() ) {
		$this->__construct( $id_base, $name, $data_name, $alone_value, $field_options, $control_options );
	}

	/**
	 * PHP5 constructor
	 *
	 * @param string $id_base Optional Base ID for the field, lower case,
	 * if left empty a portion of the field's class name will be used. Has to be unique.
	 * @param string $name Name for the field displayed on the configuration page.
	 * @param array $field_options Optional Passed to wp_register_sidebar_field()
	 *	 - description: shown on the configuration page
	 *	 - classname
	 * @param array $control_options Optional Passed to wp_register_field_control()
	 *	 - width: required if more than 250px
	 *	 - height: currently not used but may be needed in the future
	 */
	function __construct( $id_base = false, $name, $data_name, $alone_value = true, $field_options = array(), $control_options = array() ) {
		$this->id_base = empty($id_base) ? preg_replace( '/(wp_)?field_/', '', strtolower(get_class($this)) ) : strtolower($id_base);
		$this->name = $name;
		$this->data_name = $name;
		$this->option_name = 'field_' . $this->id_base;
		$this->alone_value = $alone_value;
		$this->field_options = wp_parse_args( $field_options, array('classname' => $this->option_name) );
		$this->control_options = wp_parse_args( $control_options, array('id_base' => $this->id_base, 'width' => '750') );
	}

	/**
	 * Constructs name attributes for use in form() fields
	 *
	 * This function should be used in form() methods to create name attributes for fields to be saved by update()
	 *
	 * @param string $field_name Field name
	 * @return string Name attribute for $field_name
	 */
	function get_field_name($field_name) {
		return 'field-' . $this->id_base . '[' . $this->number . '][' . $field_name . ']';
	}

	/**
	 * Constructs id attributes for use in form() fields
	 *
	 * This function should be used in form() methods to create id attributes for fields to be saved by update()
	 *
	 * @param string $field_name Field name
	 * @return string ID attribute for $field_name
	 */
	function get_field_id($field_name) {
			return 'field-' . $this->id_base . '-' . $this->number . '-' . $field_name;
	}

	// Private Functions. Don't worry about these.
	function _register( &$obj ) {
		$this->cf_registered_fields = &$obj->cf_registered_fields ;
                $this->cf_field_manager = &$obj->cf_field_manager ;
                $this->option_fields = &$obj->option_fields ;
                $this->cf_field_sidebar = &$obj->cf_field_sidebar ;
		$this->post_type = $obj->post_type;
		//$this->post_type = $obj->post_type;
		$settings = $this->get_settings();
		$empty = true;
                
		if ( is_array($settings) ) {
			foreach ( array_keys($settings) as $number ) {
				if ( is_numeric($number) ) {
					$this->_set($number);
					$this->_register_one($number);
					$empty = false;
				}
			}
		}

		if ( $empty ) {
			// If there are none, we register the field's existance with a
			// generic template
			$this->_set(1);
			$this->_register_one();
		}
	}

	function _set($number) {
		$this->number = $number;
		$this->id = $this->id_base . '-' . $number;
	}

	function _get_save_callback() {
		return array(&$this, 'save_callback');
	}

	function _get_display_callback() {
		return array(&$this, 'display_callback');
	}

	function _get_update_callback() {
		return array(&$this, 'update_callback');
	}

	function _get_form_callback() {
		return array(&$this, 'form_callback');
	}

	/**
	 * Generate the actual field content. Just finds the instance and calls field().
	 * Do NOT over-ride this function.
	 *
	 * @param array $args 
	 * @param integer $field_args 
	 * @return void
	 * @author Julien Guilmont
	 */
	function save_callback( $args, $field_args = 1 ) {
		if ( is_numeric($field_args) )
			$field_args = array( 'number' => $field_args );
		
		$field_args = wp_parse_args( $field_args, array( 'number' => -1 ) );
                if( $field_args['number'] > 1 )
                    $this->_set( $field_args['number'] );
                $error = null;
		$instance = $this->get_settings();
		if ( array_key_exists( $this->number, $instance ) ) {
                        if( isset($instance[$this->number]['slug']) && !empty($instance[$this->number]['slug']))
                                $this->slug = $instance[$this->number]['slug'];
                        else
                                $this->slug = $this->option_name . '__' . $this->number;

			$entries = $this->save($args['entries'], $args);

			$args['entries'] = $entries;
			$this->updateEntries($args, $this->number);
                        $error = $this->required($args, $instance[$this->number]);
		}
                return $error;
	}

	/** Generate the actual field content.
	 *	Just finds the instance and calls field().
	 *	Do NOT over-ride this function. */
	function display_callback( $args, $field_args = 1) {
		$this->getEntries($args, $field_args['number']);
		if ( is_numeric($field_args) )
			$field_args = array( 'number' => $field_args );

		$field_args = wp_parse_args( $field_args, array( 'number' => -1 ) );
		$this->_set( $field_args['number'] );
		$instance = $this->get_settings();

		if ( array_key_exists( $this->number, $instance ) ) {
			$instance = $instance[$this->number];
                        $this->require = isset($instance['require']) ? $instance['require'] : false;

                        if( isset($instance['slug']) && !empty($instance['slug']))
                                $this->slug = $instance['slug'];
                        else
                                $this->slug = $this->option_name . '__' . $this->number;
			// filters the field's settings, return false to stop displaying the field
			$instance = apply_filters('field_display_callback', $instance, $this, $args);
			if ( false !== $instance )
				$this->field($args, $instance);
		}
	}

	/** Deal with changed settings.
	 *	Do NOT over-ride this function. */
	function update_callback( $field_args = 1 ) {
		if ( is_numeric($field_args) )
			$field_args = array( 'number' => $field_args );

		$field_args = wp_parse_args( $field_args, array( 'number' => -1 ) );
		
		$all_instances = $this->get_settings();
		
		if ( isset($_POST['delete_field']) && $_POST['delete_field'] ) {
			// Delete the settings for this instance of the field
			if ( isset($_POST['the-field-id']) )
				$del_id = $_POST['the-field-id'];
			else
				return;

			if ( isset($this->cf_registered_fields[$del_id]['params'][0]['number']) ) {
				$number = $this->cf_registered_fields[$del_id]['params'][0]['number'];

				if ( $this->id_base . '-' . $number == $del_id )
					unset($all_instances[$number]);
			}
		} else {
			
			if ( isset($_POST['field-' . $this->id_base]) && is_array($_POST['field-' . $this->id_base]) ) {
				$settings = $_POST['field-' . $this->id_base];
			} elseif ( isset($_POST['id_base']) && $_POST['id_base'] == $this->id_base ) {
				$num = $_POST['multi_number'] ? (int) $_POST['multi_number'] : (int) $_POST['field_number'];
				$settings = array( $num => array() );
			} else {
				return;
			}
			foreach ( $settings as $number => $new_instance ) {
				$new_instance = stripslashes_deep($new_instance);
				$this->_set($number);

				$old_instance = isset($all_instances[$number]) ? $all_instances[$number] : array();

				$instance = $this->update($new_instance, $old_instance);
                                $instance['require'] = isset($new_instance['require']) ? true : false;
                                $this->require = $instance['require'];
				$instance['slug'] = strip_tags($new_instance['slug']);
				if( isset($instance['slug']) && $instance['slug'] != '')
					$this->slug = $instance['slug'];
				else
					$this->slug = $this->option_name . '__' . $number;
				
				if ( empty($old_instance['slug']) )
					$old_instance['slug'] = $this->option_name . '__' . $number;
				
				if( $new_instance['slug'] != $old_instance['slug'] )
					$this->regenerateSlug( $new_instance['slug'], $old_instance['slug'], $this );
				
				$this->_register_one( $number );
				
				
				// filters the field's settings before saving, return false to cancel saving (keep the old settings if updating)
				$instance = apply_filters('field_update_callback', $instance, $new_instance, $old_instance, $this);

				if ( false !== $instance )
					$all_instances[$number] = $instance;
				break; // run only once
			}
		}
		$this->save_settings($all_instances);
	}

	/** Generate the control form.
	 *	Do NOT over-ride this function. */
	function form_callback( $field_args = 1 ) {
		
		if ( is_numeric($field_args) )
			$field_args = array( 'number' => $field_args );

		$field_args = wp_parse_args( $field_args, array( 'number' => -1 ) );

		$all_instances = $this->get_settings();

		if ( -1 == $field_args['number'] ) {
			// We echo out a form where 'number' can be set later
			$this->_set('__i__');
			$instance = array();
		} else {
			$this->_set($field_args['number']);
			$instance = $all_instances[ $field_args['number'] ];
		}
		$this->require = isset($instance['require']) ? $instance['require'] : false;

                if( isset($instance['slug']) && !empty($instance['slug']))
                        $this->slug = $instance['slug'];
                else
                        $this->slug = $this->option_name . '__' . $field_args['number'];
		// filters the field admin form before displaying, return false to stop displaying it
		$instance = apply_filters('field_form_callback', $instance, $this);

		$return = null;
		if ( false !== $instance ) {
                        echo '<div class="col1">';
			$return = $this->form($instance);
			echo '</div>';
			echo '<div class="col2">';
			// add extra fields in the field form - be sure to set $return to null if you add any
			// if the field has no form the text echoed from the default form method can be hidden using css
			do_action_ref_array( 'in_field_form', array(&$this, &$return, $instance) );
                        echo '</div><div class="clear"></div>';
		}
		return $return;
	}

	/** Helper function: Registers a single instance. */
	function _register_one($number = -1) {
		$this->cf_field_sidebar->cf_register_sidebar_field(	$this->id, $this->name,	$this->_get_display_callback(),	$this->_get_save_callback(), $this->field_options, array( 'number' => $number ) );
		$this->cf_field_manager->_register_field_update_callback( $this->id_base, $this->_get_update_callback(), $this->control_options, array( 'number' => -1 ) );
		$this->cf_field_manager->_register_field_form_callback(	$this->id, $this->name,	$this->_get_form_callback(), $this->control_options, array( 'number' => $number ) );
	}

	function save_settings($settings) {
		$settings['_multifield'] = 1;
		$this->option_fields[$this->option_name] = $settings;
		$this->update_var('option_fields');
	}

	function get_settings() {
		//$this->pt->get_var('option_fields');
		if( isset($this->option_name) && isset($this->option_fields[$this->option_name]) )
			$settings = (array) $this->option_fields[$this->option_name];
		else
			$settings = array();
		if ( !array_key_exists('_multifield', $settings) ) {
			// old format, conver if single field
			$settings = $this->cf_field_manager->cf_convert_field_settings($this->id_base, $this->option_name, $settings);
		}
		unset($settings['_multifield'], $settings['__i__']);
		return $settings;
	}
	
	function getEntries(&$args, $number) {
		if( !isset($this->option_fields[$this->option_name][$number]['slug']) || empty($this->option_fields[$this->option_name][$number]['slug']) ) {
			$this->slug = $this->option_name . '__' . $number;
		}else{
			$this->slug = $this->option_fields[$this->option_name][$number]['slug'];
		}
		if( ( !isset($args['post_id']) || $args['post_id'] == null ) && $args['tt_id'] == null)
			return false;
		if( isset($args['post_id']) && $args['post_id'] != null )
			$args['entries'] = get_post_meta($args['post_id'], $this->slug, true);
		elseif($args['tt_id'] != null)
			$args['entries'] = get_term_taxonomy_meta( $args['tt_id'], $this->slug, true );

	}
	
	function updateEntries($args, $number) {
		if( !isset($this->option_fields[$this->option_name][$number]['slug']) || empty($this->option_fields[$this->option_name][$number]['slug']) ) {
			$this->slug = $this->option_name . '__' . $number;
		}else{
			$this->slug = $this->option_fields[$this->option_name][$number]['slug'];
		}
		if( isset($args['post_id']) && $args['post_id'] != null ) {
			$test = get_post_meta($args['post_id'], $this->slug, true);
			if ( $test == false && empty($args['entries']) ) {
				return false;
			} elseif( $test !== false && empty($args['entries']) ) {
				delete_post_meta($args['post_id'], $this->slug );
				return false;
			} else {
				$entries['entries'] = update_post_meta($args['post_id'], $this->slug, $args['entries']);
			}
		} elseif($args['tt_id'] != null) {
			$test = get_term_taxonomy_meta($args['tt_id'], $this->slug, true);
			if ( $test == false && empty($args['entries']) ) {
				return false;
			} elseif( $test !== false && empty($args['entries']) ) {
				delete_term_taxonomy_meta($args['tt_id'], $this->slug );
				return false;
			} else {
				$entries['entries'] = update_term_taxonomy_meta($args['tt_id'], $this->slug, $args['entries']);
			}
		} else {
			return false;
		}
		return true;
	}
	
	function regenerateSlug( $new_slug, $old_slug, $object ) {
		global $wpdb;
		
		$new_slug = strip_tags($new_slug);
		if( isset($object->pt->taxo) ) { //if taxonomy
			$metas = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->termmeta WHERE meta_key = %s", $old_slug) );
			if( empty($metas) )
				return false;
			foreach( $metas as &$meta ) {
				$meta->meta_key = $new_slug;
				$id = $meta->meta_id;
				$meta = (array)$meta;
				$wpdb->update( $wpdb->termmeta, $meta, array( 'meta_id' => $id ) );
			}
			
		}else{ //If posttype
			$meta_ids = $wpdb->get_col( $wpdb->prepare("SELECT meta_id FROM $wpdb->postmeta WHERE meta_key = %s", $old_slug) );
			if( empty($meta_ids) )
				return false;
				
			foreach( $meta_ids as $meta_id ) {
				$wpdb->update( $wpdb->postmeta, array('meta_key' => $new_slug), array( 'meta_id' => $meta_id ) );
			}
		}
	}
	
}

?>