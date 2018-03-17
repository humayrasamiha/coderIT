<?php
class SimpleCustomTypes_Admin_Taxonomy extends Functions{
	
	public $post_type;
        public $taxo;
	public $id_menu;
	
	public $sidebars_fields;
	public $cf_registered_sidebars;
	public $cf_registered_fields;
	public $cf_registered_field_controls;
	
	public $cf_field_factory;
	public $cf_ajax;
	public $cf_page;
	public $cf_sidebar;
	public $cf_field_manager;
	public $cf_field_sidebar;
	public $option_fields;
	public $_cf_sidebars_fields;
	public $sidebars;
	
	public $_cf_deprecated_fields_callbacks = array(
		'widget_input',
		'wp_widget_pages_control',
		'wp_widget_calendar',
		'wp_widget_calendar_control',
		'wp_widget_archives',
		'wp_widget_archives_control',
		'wp_widget_links',
		'wp_widget_meta',
		'wp_widget_meta_control',
		'wp_widget_search',
		'wp_widget_recent_entries',
		'wp_widget_recent_entries_control',
		'wp_widget_tag_cloud',
		'wp_widget_tag_cloud_control',
		'wp_widget_categories',
		'wp_widget_categories_control',
		'wp_widget_text',
		'wp_widget_text_control',
		'wp_widget_rss',
		'wp_widget_rss_control',
		'wp_widget_recent_comments',
		'wp_widget_recent_comments_control'
	);
	
	function SimpleCustomTypes_Admin_Taxonomy( $taxo, $options ) {
		$this->__construct( $taxo, $options );
	}
	
	function __construct( $taxo, $options ) {
		$this->post_type 					= $taxo['name'];
		$this->taxo						= $taxo['taxo'];
		$this->id_menu 						= 'cf_taxonomies';
                $this->cf_load_options( $options );
		
		$this->cf_load_object();

                add_action( 'cf_init-'.$this->post_type, array(&$this, 'cf_fields_init') );
                add_action( 'wp_loaded', array(&$this, 'cf_fields_load') , 1);

	}


	function cf_load_object(){

		$this->cf_field_factory =& new CF_Field_Factory(array('post_type' => &$this->post_type));
		$this->cf_ajax 			=& new CF_Ajax_Field(
			array(
				'post_type' 					=> &$this->post_type,
				'cf_registered_fields' 			=> &$this->cf_registered_fields,
				'cf_registered_field_updates' 	=> &$this->cf_registered_field_updates,
				'cf_registered_field_controls' 	=> &$this->cf_registered_field_controls,
				'sidebars' 						=> &$this->sidebars,
				'sidebars_fields' 				=> &$this->sidebars_fields,
				'cf_registered_sidebars' 		=> &$this->cf_registered_sidebars,
				'_cf_sidebars_fields'			=> &$this->_cf_sidebars_fields,
			),
			array());

                $this->cf_page 			=& new CF_Page_Field_Taxo();

		$this->cf_sidebar 		=& new CF_Sidebar_Field();
		$this->cf_field_manager	=& new CF_Field_Manager();
		$this->cf_field_sidebar	=& new CF_Field_Sidebar(
			array(
				'_cf_sidebars_fields' 				=> &$this->_cf_sidebars_fields ,
				'sidebars_fields' 					=> &$this->sidebars_fields,
				'cf_registered_fields' 				=> &$this->cf_registered_fields,
				'_cf_deprecated_fields_callbacks' 	=> &$this->_cf_deprecated_fields_callbacks,
				'cf_registered_field_controls' 		=> &$this->cf_registered_field_controls,
				'cf_registered_field_updates' 		=> &$this->cf_registered_field_updates,
				'post_type' 						=> &$this->post_type,
			));

		$this->cf_field_control	=& new CF_Field_Control(
                        array(
                            'cf_registered_fields' 				=> &$this->cf_registered_fields,
                            'cf_registered_field_controls' 		=> &$this->cf_registered_field_controls,
                            'sidebars_fields' 					=> &$this->sidebars_fields,
                            'post_type' 						=> &$this->post_type,
                            'cf_registered_field_updates' 		=> &$this->cf_registered_field_updates,
                            '_cf_deprecated_fields_callbacks' 	=> &$this->_cf_deprecated_fields_callbacks
                        ),
                        array(
                            'cf_sidebar' 						=> &$this->cf_sidebar
                        ));

		$this->cf_sidebar->setter(
			array(
				'cf_registered_sidebars' 	=> &$this->cf_registered_sidebars,
				'cf_registered_fields'		=> &$this->cf_registered_fields,
				'post_type' 				=> &$this->post_type,
				'sidebars_fields' 			=> &$this->sidebars_fields,
				'_cf_sidebars_fields' 		=> &$this->_cf_sidebars_fields,
				),
			array(
				'cf_field_control' 			=> &$this->cf_field_control,
				));

		$this->cf_field_manager->setter(
			array(
				'cf_registered_fields' 			=> &$this->cf_registered_fields,
				'cf_registered_field_controls' 	=> &$this->cf_registered_field_controls,
				'post_type' 					=> &$this->post_type,
				'cf_registered_field_updates' 	=> &$this->cf_registered_field_updates,
				'sidebars_fields' 				=> &$this->sidebars_fields,
				'_cf_sidebars_fields' 			=> &$this->_cf_sidebars_fields,
				'option_fields' 				=> &$this->option_fields,
				),
			array(
				'cf_field_factory' => &$this->cf_field_factory,
				'cf_field_control' => &$this->cf_field_control,

			));

                $this->cf_page->setter(
			array(
				'id_menu' 						=> &$this->id_menu,
				'post_type' 					=> &$this->post_type,
				'cf_registered_sidebars' 		=> &$this->cf_registered_sidebars,
				'sidebars_fields' 				=> &$this->sidebars_fields,
				'cf_registered_fields' 			=> &$this->cf_registered_fields,
				'cf_registered_field_controls' 	=> &$this->cf_registered_field_controls,
                                'taxo'                          => &$this->taxo
			),
			array(
				'cf_sidebar' 					=> &$this->cf_sidebar,
				'cf_field_manager' 				=> &$this->cf_field_manager,
				'cf_field_control' 				=> &$this->cf_field_control,
				));

		$this->cf_admin_object	=& new CF_Admin_Object_Taxo(
			array(
				'post_type' 				=> &$this->post_type,
				'cf_registered_sidebars' 	=> &$this->cf_registered_sidebars,
				'cf_registered_fields' 		=> &$this->cf_registered_fields,
				'sidebars_fields' 			=> &$this->sidebars_fields,
				'_cf_sidebars_fields' 		=> &$this->_cf_sidebars_fields,
                                'taxo'                          => &$this->taxo
			),
			array());


	}

        function cf_load_options( $options ){

		$options = wp_cache_get('cf_options-'.$this->post_type, FLAG_CACHE);

		if( !empty($options) ) {
			$this->sidebars_fields 				= isset($options['sidebars_fields']) ? (array) $options['sidebars_fields'] : array('cf_inactive_fields' => array(), 'array_version' => 3);
			$this->option_fields				= isset($options['option_fields']) ? (array) $options['option_fields'] : array();
			$this->sidebars						= isset($options['sidebars']) ? (array) $options['sidebars'] : array();
			$this->update_var('sidebars_fields');

		} else {
			$this->sidebars_fields = array('cf_inactive_fields' => array(), 'array_version' => 3);
			$this->update_var('sidebars_fields');
		}
	}

	function cf_fields_load() {
		do_action( 'cf_init-'.$this->post_type );
	}
	
	function cf_fields_init() {
		if ( !is_blog_installed() )
			return;
		
		$this->cf_field_manager->register_field('CF_Field_Input');
		$this->cf_field_manager->register_field('CF_Field_Textarea');
		$this->cf_field_manager->register_field('CF_Field_EditorLight');
		$this->cf_field_manager->register_field('CF_Field_Select');
		$this->cf_field_manager->register_field('CF_Field_SelectMultiple');
		$this->cf_field_manager->register_field('CF_Field_Checkbox');
		$this->cf_field_manager->register_field('CF_Field_DatePicker');
		$this->cf_field_manager->register_field('CF_Field_Dropdown_Users');
		$this->cf_field_manager->register_field('CF_Field_Media');
		$this->cf_field_manager->register_field('CF_Field_Separator');
                
		do_action_ref_array( 'fields_init-' . $this->post_type, array(&$this) );

		$this->get_var('sidebars');

		if( isset($this->sidebars) && is_array($this->sidebars) ) {
			foreach( $this->sidebars as $sidebar ) {
				$sidebar['before_widget'] = '<tr class="form-field">';
				$sidebar['after_widget'] = '</td></tr>';
				$sidebar['before_title'] = '<th valign="top" scope="row"><label>';
				$sidebar['after_title'] = '</label></th><td>';
				$this->cf_sidebar->cf_register_sidebar( $sidebar );
			}
		}
                
	}
}