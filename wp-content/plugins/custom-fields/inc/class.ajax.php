<?php
class CF_Ajax_Field extends Functions{
	
	private $pt;
	protected static $class = __CLASS__;
        
	function __construct( $options, $objects ) {
        foreach( $options as $name => &$opt )
            $this->{$name} = &$opt;
        foreach( $objects as $name => &$obj )
        	$this->{$name} = &$obj;
		
		add_action( 'wp_ajax_save-field-' . $this->post_type, 		array(&$this, 'cfSaveField') );
		add_action( 'wp_ajax_fields-order-' . $this->post_type, 	array(&$this, 'cfFieldsOrder') );
		add_action( 'wp_ajax_add-sidebar-' . $this->post_type, 		array(&$this, 'cfAddSidebar') );
		add_action( 'wp_ajax_del-sidebar-' . $this->post_type, 		array(&$this, 'cfDelSidebar') );
                add_action( 'wp_ajax_rename-sidebar-' . $this->post_type, 		array(&$this, 'cfRenameSidebar') );
		add_action( 'admin_enqueue_scripts', 				array(&$this, 'addJs') );
	}
	
	function addJs($hook_suffix) {
            if( is_numeric(strpos($hook_suffix, 'page_custom_fields')) ){
		wp_enqueue_script( 'admin-fields', SCF_URL . '/inc/js/fields.js', array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ), '0.1', false);
		wp_enqueue_style( 'fields', SCF_URL . '/inc/css/fields.css');
		wp_enqueue_style( 'fields-colors', SCF_URL . '/inc/css/colors.css');
            }
	}
	
	function cfSaveField() {
		check_ajax_referer( 'save-sidebar-fields', 'savefields' );
		if ( !current_user_can('edit_theme_options') || !isset($_POST['id_base']) )
			die('-1');
		
		unset( $_POST['savefields'], $_POST['action'] );
		
		do_action('load-fields.php');
		do_action('fields.php');
		do_action('sidebar_admin_setup');
		
		$id_base = $_POST['id_base'];
		$field_id = $_POST['field-id'];
		$sidebar_id = $_POST['sidebar'];
		$multi_number = !empty($_POST['multi_number']) ? (int) $_POST['multi_number'] : 0;
		$settings = isset($_POST['field-' . $id_base]) && is_array($_POST['field-' . $id_base]) ? $_POST['field-' . $id_base] : false;
		$error = '<p>' . __('An error has occured. Please reload the page and try again.') . '</p>';
		
		$sidebars = $this->cf_get_sidebars_fields();
		$sidebar = isset($sidebars[$sidebar_id]) ? $sidebars[$sidebar_id] : array();
		// delete
		if ( isset($_POST['delete_field']) && $_POST['delete_field'] ) {
			if ( !isset($this->cf_registered_fields[$field_id]) )
				die($error);
			
			$sidebar = array_diff( $sidebar, array($field_id) );
			$_POST = array('sidebar' => $sidebar_id, 'field-' . $id_base => array(), 'the-field-id' => $field_id, 'delete_field' => '1');
		} elseif ( $settings && preg_match( '/__i__|%i%/', key($settings) ) ) {
			if ( !$multi_number )
				die($error);
			$_POST['field-' . $id_base] = array( $multi_number => array_shift($settings) );
			$field_id = $id_base . '-' . $multi_number;
			$sidebar[] = $field_id;
		}
		
		$_POST['field-id'] = $sidebar;
		foreach ( (array) $this->cf_registered_field_updates as $name => $control ) {
			$control['params'][0]['post_type'] = $this->post_type;
			$control['params'][0]['number'] = $multi_number;
			if ( $name == $id_base ) {
				if ( !is_callable( $control['callback'] ) )
					continue;
				ob_start();
					call_user_func_array( $control['callback'], $control['params'] );
				ob_end_clean();
				break;
			}
		}
		if ( isset($_POST['delete_field']) && $_POST['delete_field'] ) {
			$sidebars[$sidebar_id] = $sidebar;
			$this->cf_set_sidebars_fields($sidebars);
			echo "deleted:$field_id";
			die();
		}

        if ( !empty($_POST['add_new']) )
			die();

		if ( $form = $this->cf_registered_field_controls[$field_id] ) {
			$form['params'][0]['post_type'] = $this->post_type;
			call_user_func_array( $form['callback'], $form['params'] );
		}
		die();
	}
	
	function cfFieldsOrder() {
		check_ajax_referer( 'save-sidebar-fields', 'savefields' );
		if ( !current_user_can('edit_theme_options') )
			die('-1');
		unset( $_POST['savefields'], $_POST['action'] );
		if ( is_array($_POST['sidebars']) ) {
			$sidebars = array();
			foreach ( $_POST['sidebars'] as $key => $val ) {
				$sb = array();
				if ( !empty($val) ) {
					$val = explode(',', $val);
					foreach ( $val as $k => $v ) {
						if ( strpos($v, 'field-') === false )
							continue;
						
						$sb[$k] = substr($v, strpos($v, '_') + 1);
					}
				}
				$sidebars[$key] = $sb;
			}
			$this->cf_set_sidebars_fields($sidebars);
			die('1');
		}
		
		die('-1');
	}
	
	function cfAddSidebar() {
		check_ajax_referer( 'save-sidebar-fields', 'savefields' );
		if ( !current_user_can('edit_theme_options') )
			die('-1');
		unset( $_POST['savefields'], $_POST['action'] );
		
		$name = $_POST['sidebar'];
		$name_attr = sanitize_title($name);
        $i = 0;
        $name_s = null;
        while( !isset($name_s) ){
            if( $i == 0 ){
                if( isset($this->sidebars[$name_attr] ))
                        $i++;
                else
                    $name_s = $name_attr;
            }else{
                if( isset($this->sidebars[$name_attr . '-' . $i]))
                        $i++;
                else
                    $name_s = $name_attr . '-' . $i;
            }
        }
        $name_attr = $name_s;
		$this->sidebars[$name_attr] = array(
			'name' => $name,
			'id' => $name_attr . '-' . $this->post_type,
			'before_widget' => '',
			'after_widget' => '<br/>',
			'before_title' => '<label class="title-field">',
			'after_title' => ' :</label>',
		);
		$this->update_var('sidebars');
		die();
	}
	
	function cfDelSidebar() {
		check_ajax_referer( 'save-sidebar-fields', 'savefields' );
		if ( !current_user_can('edit_theme_options') )
			die('-1');
		unset( $_POST['savefields'], $_POST['action'] );
		$sidebars = array();
		foreach( $this->sidebars as $key => $sidebar) {
			if($sidebar['id'] != $_POST['sidebar']) {
				$sidebars[$key] = $sidebar;
			}
		}
		$this->sidebars = $sidebars;
		$this->update_var('sidebars');

		if ( isset( $this->cf_registered_sidebars[$_POST['sidebar']] ) ) {
			unset( $this->cf_registered_sidebars[$_POST['sidebar']] );
		}
		die();
	}

    function cfRenameSidebar(){
        check_ajax_referer( 'save-sidebar-fields', 'savefields' );
        if ( !current_user_can('edit_theme_options') )
			die('-1');
        unset( $_POST['savefields'], $_POST['action'] );
        $sidebar_id = null;
        foreach( $this->sidebars as $key => $sidebar) {
			if($sidebar['id'] == $_POST['sidebar']) {
				$sidebar_id = $key;
			}
		}
            
        if(!isset($sidebar_id))
            die('-1');

        $new_name = $_POST['rename'];
        $this->sidebars[$sidebar_id]['name'] = $new_name;
		$this->update_var('sidebars');
        die();
    }
}
?>