<?php
class CF_Admin_Object_Taxo extends Functions {
	private $_editTags;
	/*
	 * Constructor
	 **/
	function __construct( $options, $objects ) {
            foreach( $options as $name => &$opt )
              $this->{$name} = &$opt;
            foreach( $objects as $name => &$obj )
                $this->{$name} = &$obj;

		$this->_editTags = false;
		//$this->pt = &$obj_pt;
		
		// Register Javascript need for custom fields
		add_action( 'admin_enqueue_scripts', array(&$this, 'initStyleScript'), 10 );
		//add_action( 'admin_print_footer_scripts', array(&$this, 'customTinyMCE'), 9999 );
		
		// Add blocks on write page
		add_action( $this->taxo->name . '_edit_form_fields', array(&$this, 'loadCustomFields'), 10, 2 );
		
		add_action( "edited_" . $this->taxo->name, array(&$this, 'saveCustomFields'), 10 , 2);
	}
	
	/**
	 * Load JS and CSS need for admin features.
	 *
	 */
	function initStyleScript( $hook_sufix ) {
		global $taxonomy;
		if ( $hook_sufix == 'edit-tags.php' ) {

                    if( $taxonomy != $this->taxo->name )
                            return false;

                    foreach( $this->sidebars_fields as $name => $sidebar ){
                        if($name == 'cf_inactive_fields' || empty($sidebar) || !is_array($sidebar))
                            continue;

                        foreach( $sidebar as $widget ){
                            $idbase = $this->cf_registered_fields[$widget]['classname'];
                            // Allow composant to add JS/CSS
                            do_action( 'cf-fields-scriptstyles-'.$idbase );
                        }
                    }



			$this->_editTags = true;
			// Add CSS for boxes
			wp_enqueue_style ( 'simple-custom-types-object', SCF_URL.'/inc/css/admin.css', array(), SCF_VERSION);
			
			wp_enqueue_script("tiny_mce", includes_url('js/tinymce').'/tiny_mce.js');
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Save datas
	 *
	 * @param $post_ID
	 * @param $post
	 * @return boolean
	 */
 	function saveCustomFields( $term_id, $tt_id )  {
            if( !isset($this->cf_registered_sidebars) || empty($this->cf_registered_sidebars) )
                    return false;
		foreach( $this->cf_registered_sidebars as $index => $_s) {
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
				continue;
			
			$did_one = false;
			$params = array();
			foreach ( (array) $sidebars_fields[$index] as $id ) {
				if ( !isset($this->cf_registered_fields[$id]) )
					continue;
				
				$number = current($this->cf_registered_fields[$id]['params']);
				$id_base = str_ireplace('_', '-', $this->cf_registered_fields[$id]['classname']);

				$entries = array();
				if ( isset($_FILES[$id_base]['name'][$number['number']]) ) {
					$entries = $this->formatFilesArray($_FILES, $id_base, $number['number']);
				}
				if( isset($_POST[$id_base][$number['number']]) ) {
					$entries = array_merge( $entries, $_POST[$id_base][$number['number']] );
				}
					
				$params = array_merge( array( array_merge( $this->cf_registered_sidebars[$index], array('field_id' => $id, 'field_name' => $this->cf_registered_fields[$id]['name'], 'entries' => $entries) ) ), (array) $this->cf_registered_fields[$id]['params'] );
				
				$params[0]['term_id'] = $term_id;
				$params[0]['tt_id'] = $tt_id;
				
				if ( is_callable( $this->cf_registered_fields[$id]['save_callback'] ) ) {
					call_user_func_array( $this->cf_registered_fields[$id]['save_callback'], $params);
					$did_one = true;
				}
			}
		
		}
		return $did_one;
	
	}
	
	/**
	 * Check if post type is load ?
	 *
	 * @param string $post_type
	 * @return boolean
	 */
	function initCustomFields( $post_type = '' ) {
		if ( isset($post_type) && !empty($post_type) && $post_type == $this->post_type) {
			return $this->loadCustomFields( $post_type );
		}
		return false;
	}
	
	/**
	 * Group custom fields for build boxes.
	 *
	 * @param $post_type
	 * @return boolean
	 */
	function loadCustomFields( $tag ) {
		//$index = 'top-sidebar-' . $post_type;
            if( !isset($this->cf_registered_sidebars) || empty($this->cf_registered_sidebars) )
                    return false;
		foreach( $this->cf_registered_sidebars as $index => $_s) {
			if( $index == 'cf_inactive_fields' )
				continue;
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
				continue;
			
			$sidebar = $this->cf_registered_sidebars[$index];
			
			$did_one = false;
			$params = array();
			$i = 0;
			foreach ( (array) $sidebars_fields[$index] as $id ) {
				
				if ( !isset($this->cf_registered_fields[$id]) ) continue;
				
				//$params = array_merge(
				//	array( array_merge( $sidebar, array('field_id' => $id, 'field_name' => $this->pt->cf_registered_fields[$id]['name']) ) ),
				//	(array) $this->pt->cf_registered_fields[$id]['params']
				//);
				
				$i = 1;
			
			}
			if( $i == 0 )
				continue;
			
			$this->genericRenderBoxes($tag, array( $index ));
			
		}
		return $did_one;
	}
	
	/**
	 * Generic boxes who allow to build xHTML for each box
	 *
	 * @param $post
	 * @param $box
	 * @return boolean
	 */
	function genericRenderBoxes( $tag = null, $box = null ) {
		
		$index = current($box);
		$sidebars_fields = $this->cf_get_sidebars_fields();
		$sidebar = $this->cf_registered_sidebars[$index];
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
			$params[0]['tt_id'] = $tag->term_taxonomy_id;
			$params[0]['term_id'] = $tag->term_id;
                        $params[0]['taxonomy'] = $tag->taxonomy;
			//$params = apply_filters( 'dynamic_sidebar_params', $params );
			
			$callback = $this->cf_registered_fields[$id]['callback'];
			do_action( 'dynamic_sidebar', $this->cf_registered_fields[$id] );
			if ( is_callable( $callback ) ) {
				call_user_func_array( $callback, $params);
				$did_one = true;
			}
			
		}
		
		return true;
	}
	
}
?>