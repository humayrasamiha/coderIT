<?php
class CF_Field_Control extends Functions{

        protected static $class = __CLASS__;
        
	function __construct( $options, $objects ) {
                foreach( $options as $name => &$opt )
                    $this->{$name} = &$opt;
                foreach( $objects as $name => &$obj )
                	$this->{$name} = &$obj;
	}
	
	function cf_field_control( $sidebar_args ) {
		$field_id = $sidebar_args['field_id'];
		$sidebar_id = isset($sidebar_args['id']) ? $sidebar_args['id'] : false;
		$key = $sidebar_id ? array_search( $field_id, $this->sidebars_fields[$sidebar_id] ) : '-1'; // position of field in sidebar
		$control = isset($this->cf_registered_field_controls[$field_id]) ? $this->cf_registered_field_controls[$field_id] : array();
		$field = $this->cf_registered_fields[$field_id];
	
		$id_format = $field['id'];
		$field_number = isset($control['params'][0]['number']) ? $control['params'][0]['number'] : '';
		$id_base = isset($control['id_base']) ? $control['id_base'] : $field_id;
		$multi_number = isset($sidebar_args['_multi_num']) ? $sidebar_args['_multi_num'] : '';
		$add_new = isset($sidebar_args['_add']) ? $sidebar_args['_add'] : '';
	
		$query_arg = array( 'editfield' => $field['id'] );
		if ( $add_new ) {
			$query_arg['addnew'] = 1;
			if ( $multi_number ) {
				$query_arg['num'] = $multi_number;
				$query_arg['base'] = $id_base;
			}
		} else {
			$query_arg['sidebar'] = $sidebar_id;
			$query_arg['key'] = $key;
		}
	
		// We aren't showing a field control, we're outputing a template for a mult-field control
		if ( isset($sidebar_args['_display']) && 'template' == $sidebar_args['_display'] && $field_number ) {
			// number == -1 implies a template where id numbers are replaced by a generic '__i__'
			$control['params'][0]['number'] = -1;
			// with id_base field id's are constructed like {$id_base}-{$id_number}
			if ( isset($control['id_base']) )
				$id_format = $control['id_base'] . '-__i__';
		}
		$control['params'][0]['post_type'] = $this->post_type;
		$this->cf_registered_fields[$field_id]['callback'] = $this->cf_registered_fields[$field_id]['_callback'];
		unset($this->cf_registered_fields[$field_id]['_callback']);
	
		$field_title = esc_html( strip_tags( $sidebar_args['field_name'] ) );
		$has_form = 'noform';
	
		echo $sidebar_args['before_field'];?>
		<div class="field-top">
		<div class="field-title-action">
			<a class="field-action hide-if-no-js" href="#available-fields"></a>
			<a class="field-control-edit hide-if-js" href="<?php echo esc_url( add_query_arg( $query_arg ) ); ?>"><span class="edit"><?php _e('Edit'); ?></span><span class="add"><?php _e('Add'); ?></span></a>
		</div>
		<div class="field-title"><h4><?php echo $field_title ?><span class="in-field-title"></span></h4></div>
		</div>
	
		<div class="field-inside">
		<form action="" method="post">
		<div class="field-content">
	<?php
		if ( isset($control['callback']) )
			$has_form = call_user_func_array( $control['callback'], $control['params'] );
		else
			echo "\t\t<p>" . __('There are no options for this field.') . "</p>\n"; ?>
		</div>
		<input type="hidden" name="field-id" class="field-id" value="<?php echo esc_attr($id_format); ?>" />
		<input type="hidden" name="id_base" class="id_base" value="<?php echo esc_attr($id_base); ?>" />
		<input type="hidden" name="field-width" class="field-width" value="<?php if (isset( $control['width'] )) echo esc_attr($control['width']); ?>" />
		<input type="hidden" name="field-height" class="field-height" value="<?php if (isset( $control['height'] )) echo esc_attr($control['height']); ?>" />
		<input type="hidden" name="field_number" class="field_number" value="<?php echo esc_attr($field_number); ?>" />
		<input type="hidden" name="multi_number" class="multi_number" value="<?php echo esc_attr($multi_number); ?>" />
		<input type="hidden" name="add_new" class="add_new" value="<?php echo esc_attr($add_new); ?>" />
	
		<div class="field-control-actions">
			<div class="alignleft">
			<a class="field-control-remove" href="#remove"><?php _e('Delete'); ?></a> |
			<a class="field-control-close" href="#close"><?php _e('Close'); ?></a>
			</div>
			<div class="alignright<?php if ( 'noform' === $has_form ) echo ' field-control-noform'; ?>">
			<img src="<?php echo esc_url( SCF_URL . '/inc/img/cfspin_light.gif' ); ?>" class="ajax-feedback " title="" alt="" />
			<input type="submit" name="savefield" class="button-primary field-control-save" value="<?php esc_attr_e('Save'); ?>" />
			</div>
			<br class="clear" />
		</div>
		</form>
		</div>
	
		<div class="field-description">
	<?php echo ( $field_description = $this->cf_field_description($field_id) ) ? "$field_description\n" : "$field_title\n"; ?>
		</div>
	<?php
		echo $sidebar_args['after_field'];
		return $sidebar_args;
	}
	
	function cf_field_description( $id ) {
		if ( !is_scalar($id) )
			return;
		if ( isset($this->cf_registered_fields[$id]['description']) )
			return esc_html( $this->cf_registered_fields[$id]['description'] );
	}
	
	function cf_list_field_controls( $sidebar ) {
		add_filter( 'dynamic_sidebar_params', array(&$this, 'cf_list_field_controls_dynamic_sidebar') );
	
		echo "<div id='$sidebar' class='fields-sortables'>\n";
		$description = $this->cf_sidebar_description( $sidebar );
	
		if ( !empty( $description ) ) {
			echo "<div class='sidebar-description'>\n";
			echo "\t<p class='description'>$description</p>";
			echo "</div>\n";
		}
		$this->cf_sidebar->dynamic_sidebar( $sidebar );
		echo "</div>\n";
	}
	
	function cf_register_field_control($id, $name, $control_callback, $options = array()) {
	
		$id = strtolower($id);
		$id_base = $this->_get_field_id_base($id);
		if ( empty($control_callback) ) {
			unset($this->cf_registered_field_controls[$id]);
			unset($this->cf_registered_field_updates[$id_base]);
			return;
		}
	
		if ( in_array($control_callback, $this->_cf_deprecated_fields_callbacks, true) && !is_callable($control_callback) ) {
			if ( isset($this->cf_registered_fields[$id]) )
				unset($this->cf_registered_fields[$id]);
	
			return;
		}
	
		if ( isset($this->cf_registered_field_controls[$id]) && !did_action( 'fields_init' ) )
			return;
	
		$defaults = array('width' => 250, 'height' => 200 ); // height is never used
		$options = wp_parse_args($options, $defaults);
		$options['width'] = (int) $options['width'];
		$options['height'] = (int) $options['height'];
	
		$field = array(
			'name' => $name,
			'id' => $id,
			'callback' => $control_callback,
			'params' => array_slice(func_get_args(), 4)
		);
		$field = array_merge($field, $options);
		$this->cf_registered_field_controls[$id] = $field;
		//$this->update_temp('cf_registered_field_controls');
		
		if ( isset($this->cf_registered_field_updates[$id_base]) )
			return;
	
		if ( isset($field['params'][0]['number']) )
			$field['params'][0]['number'] = -1;
	
		unset($field['width'], $field['height'], $field['name'], $field['id']);
		$this->cf_registered_field_updates[$id_base] = $field;
		//$this->update_temp('cf_registered_field_updates');
	}
	
	function cf_unregister_field_control($id) {
		return $this->cf_register_field_control($id, '', '');
	}
	
	function cf_sidebar_description( $id ) {
		if ( !is_scalar($id) )
			return;
	
		global $cf_registered_sidebars;
	
		if ( isset($cf_registered_sidebars[$id]['description']) )
			return esc_html( $cf_registered_sidebars[$id]['description'] );
	}
}