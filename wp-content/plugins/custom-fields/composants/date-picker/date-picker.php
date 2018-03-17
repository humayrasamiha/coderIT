<?php
class CF_Field_DatePicker extends CF_Field{
	
	function CF_Field_DatePicker(){
		add_action( 'cf-fields-scriptstyles-field_datepicker', array(&$this, 'add_js'), 10, 4 );
		
		$field_ops = array('classname' => 'field_datepicker', 'description' => __( 'Date Picker', 'custom-fields') );
		$this->CF_Field('datepicker', __('Date Picker', 'custom-fields'), 'input-datepicker', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);
		$entries = is_array($entries) && isset($entries['name']) ? $entries['name'] : $entries;

                if ( !empty($entries) )
			$entries = date_i18n( 'Y-m-d', $entries );

		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
                
		echo '<input id="'.$this->get_field_id('name').'" name="'.$this->get_field_name('name').'" type="text" value="'.esc_attr($entries).'" size="40" />' . "\n";
	
		echo '<script type="text/javascript">' . "\n";
			echo 'jQuery(document).ready(function(){' . "\n";
				echo 'jQuery.datepicker.setDefaults( jQuery.datepicker.regional["fr"] );' . "\n";
				echo 'jQuery("#'.$this->get_field_id('name').'").datepicker({ dateFormat: "yy-mm-dd", firstDay: 1 });' . "\n";
			echo '});' . "\n";
		echo '</script>' . "\n";

		echo $after_widget;

	}
	
	function save( $values ){
		//$values = $values['name'];
                $values = $values['name'];
		if ( !empty($values) )
                    $values = mysql2date( 'U', $values );
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
	
	function add_js( $pagenow = '', $current_taxo = array(), $flag_editor = false, $flag_media = false ) {
		// Datepicker in Custom fields ?

		wp_enqueue_script('jquery-ui-datepicker',    SCF_URL . '/composants/date-picker/js/jquery-ui-1.7.2.custom.min.js', array('jquery'), '1.7.2' );
		wp_enqueue_script('jquery-ui-datepicker-fr', SCF_URL . '/composants/date-picker/js/ui.datepicker-fr.js', array('jquery-ui-datepicker'), '1.7.2' );
		
		wp_enqueue_style ('jquery-ui-datepicker', 	 SCF_URL . '/composants/date-picker/css/smoothness/jquery-ui-1.7.2.custom.css', array(), '1.7.2', 'all');
	}
	
}
?>