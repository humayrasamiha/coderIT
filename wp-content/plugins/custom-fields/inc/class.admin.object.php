<?php
class CF_Admin_Object extends Functions {
	protected $post_type;
	/*
	 * Constructor
	 **/
	function __construct( $options, $objects ) {
	  foreach( $options as $name => &$opt )
	      $this->{$name} = &$opt;
	  foreach( $objects as $name => &$obj )
	  	$this->{$name} = &$obj;
		
		// Register Javascript need for custom fields
		add_action( 'admin_enqueue_scripts', array(&$this, 'initStyleScript'), 10 );
		
		// Save custom datas
		add_action( 'wp_insert_post', array(&$this, 'saveCustomFields'), 10, 3 );
		
		// Add blocks on write page
		add_action( 'add_meta_boxes', array(&$this, 'initCustomFields'), 10, 1 );

                //Add notice
                //add_action( 'admin_notices', array(&$this, 'errorsCustomFields'));
	}

        /*
         * Show errors
         */
        function errorsCustomFields() {
                $error = wp_cache_get('cf_field');
                if ( empty( $error ) ) return false;

                $errors = unserialize( $error );

            if (count($errors) >= 1) {
                        echo '<div class="error below-h2" id="wp-post-validator-errors" style="width: 80%;"><p>' . __('This post could not be published:', 'post-validator') . '<br /><br />';
                        foreach ($errors as $error) {
                                echo ''. $error. '<br />';
                        }
                        echo '</p></div>';
                        wp_cache_delete('cf_field');
                        //unset($_SESSION['cf_field']);
                }
        }

	/**
	 * Load JS and CSS need for admin features.
	 *
	 * @param string $hook_suffix
	 * @return void
	 * @author Julien Guilmont
	 */
	function initStyleScript( $hook_sufix ) {
		global $post_type;
		
		if ( $hook_sufix == 'post-new.php' || $hook_sufix == 'post.php' ) {
                    if( !isset($post_type) )
                        $post_type == 'post';

                    if( $post_type != $this->post_type )
                            return false;
                    foreach( $this->sidebars_fields as $name => $sidebar ){
                        if($name == 'cf_inactive_fields' || empty($sidebar) || !is_array($sidebar))
                            continue;

                        foreach( $sidebar as $widget ){
                            $idbase = $this->cf_registered_fields[$widget]['classname'];
                            // Allow composant to add JS/CSS
                            do_action( 'cf-fields-scriptstyles-'.$idbase, $post_type );
                        }

                    }
			// Add CSS for boxes
			wp_enqueue_style ( 'simple-custom-types-object', SCF_URL.'/inc/css/admin.css', array(), SCF_VERSION);

		}
	}
	
	/**
	 * Save datas during post saving
	 *
	 * @param string $post_ID
	 * @param string $post
	 * @return void
	 * @author Julien Guilmont
	 */
 	function saveCustomFields( $post_id, $post )  {
                $did_one = false;
 		if ( $post->post_type != $this->post_type || ( isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'inline-save') ) {
 			return false;
 		}
		if( isset($this->cf_registered_sidebars) ){
                    $sidebar_errors = array();
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
                            $errors = array();
                            foreach ( (array) $sidebars_fields[$index] as $id ) {
                                    if ( !isset($this->cf_registered_fields[$id]) )
                                            continue;

                                    $number = current($this->cf_registered_fields[$id]['params']);
                                    $id_base = str_ireplace('_', '-', $this->cf_registered_fields[$id]['classname']);

                                    $field_name = isset($this->cf_registered_fields[$id]['name']) ? $this->cf_registered_fields[$id]['name'] : '';
                                    $field_params = isset($this->cf_registered_fields[$id]['params']) ? $this->cf_registered_fields[$id]['params'] : array();

                                    $entries = array();
                                    if ( isset($_FILES[$id_base]['name'][$number['number']]) ) {
                                            $entries = $this->formatFilesArray($_FILES, $id_base, $number['number']);
                                    }
                                    if( isset($_POST[$id_base][$number['number']]) ) {
                                            $entries = array_merge( $entries, $_POST[$id_base][$number['number']] );
                                    }

                                    $params = array_merge( array( array_merge( $this->cf_registered_sidebars[$index], array('field_id' => $id, 'field_name' => $field_name, 'entries' => $entries) ) ), (array) $field_params );
                                    $params[0]['post_id'] = $post->ID;

                                    if ( is_callable( $this->cf_registered_fields[$id]['save_callback'] ) ) {
                                            $error = call_user_func_array( $this->cf_registered_fields[$id]['save_callback'], $params );
                                            if( !empty($error) ){
                                                $errors = array_merge($errors, $error);
                                            }
                                            $did_one = true;
                                    }
                            }
                            $sidebar_errors = array_merge($sidebar_errors, $errors);
                    }
                    if ( count($sidebar_errors) >= 1 ) {
                            // failed the check
                            // set post_status to draft
                            remove_action( 'save_post', array(&$this, 'saveCustomFields'), 10, 2 );
                            wp_update_post( array('ID' => $post->ID, 'post_status' => 'draft') );
                            wp_cache_set('cf_field', serialize($sidebar_errors));
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
	function loadCustomFields( $post_type = '' ) {
            $did_one = false;
            if( isset($this->cf_registered_sidebars) ){
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
						
						$params = array_merge(
							array( array_merge( $sidebar, array('field_id' => $id, 'field_name' => $this->cf_registered_fields[$id]['name']) ) ),
							(array) $this->cf_registered_fields[$id]['params']
						);
						
						$i = 1;
					
					}
					if( $i == 0 )
						continue;
						
					$p = current($params);
					if( has_filter("postbox_classes_" . $post_type . "_" . $p['id']) )
						add_filter( "postbox_classes_" . $post_type . "_" . $p['id'], array(&$this, 'addClass' ) );
						
					add_meta_box($p['id'], $p['name'], array(&$this, 'genericRenderBoxes'), $post_type, 'normal', 'default', array( $index, $p['id'], $post_type ) );
				}
            }
		return $did_one;
	}
	
	function printClass( $id ){
		?>
		<script type="text/javascript">
		jQuery(document).ready( function(){
			jQuery('#<?php echo $id;?>').addClass('metaboxfield');
		});
		</script>
		<?php
	}
	
	function addClass( $classes ){
		return array_merge( $classes, array('metaboxfield'));
	}
	
	/**
	 * Generic boxes who allow to build xHTML for each box
	 *
	 * @param $post
	 * @param $box
	 * @return boolean
	 */
	function genericRenderBoxes( $post = null, $box = null ) {
		list($index, $box_id, $post_type) = $box['args'];
		if( !has_filter("postbox_classes_" . $post_type . "_" . $box_id) )
			$this->printClass( $box_id );	
		$sidebars_fields = $this->cf_get_sidebars_fields();
		$sidebar = $this->cf_registered_sidebars[$index];
		$count = count($sidebars_fields[$index]);
		$i=1;
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
			$params[0]['post_id'] = $post->ID;
			
			$callback = $this->cf_registered_fields[$id]['callback'];
			do_action( 'dynamic_sidebar', $this->cf_registered_fields[$id] );
			
			$class = $count == $i ? 'lastchild' : '';		
			
			echo '<div class="container-sct '.$class.'">';
				if ( is_callable( $callback ) ) {
					call_user_func_array( $callback, $params);
				}
			echo '</div>';
			$i++;
		}
		
		echo '<div class="clear"></div>' . "\n";
		return true;
	}
	
}
?>