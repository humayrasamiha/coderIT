<?php
/*
Plugin Name: Custom Fields for WordPress
Version: 3.0.2
Plugin URI: http://redmine.beapi.fr/projects/show/custom-fields
Description: This plugin add custom fields for some things on WordPress, blog, term taxonomy and custom object types. Meta for Taxonomies plugin is required to use custom fields with taxonomies.
Author: Julien Guilmont
Author URI: http://blog.djudorange.fr

----

Copyright 2011 Julien Guilmont (julien.guilmont@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

// Constant base
define ( 'SCF_VERSION', '2.2.2' );
define ( 'SCF_OPTION',  'custom-fields' );
define ( 'SCF_FOLDER',  'custom-fields' );
define ( 'FLAG_CACHE',  'Fields' );

// Build constant for url/dir
define ( 'SCF_URL', plugins_url('', __FILE__) );
define ( 'SCF_DIR', dirname(__FILE__) );

// Library
require( SCF_DIR . '/inc/functions.php' );

// Call admin class
require( SCF_DIR . '/inc/abs.functions.php' );
require( SCF_DIR . '/inc/class.admin.php' );
require( SCF_DIR . '/inc/class.page.php' );
require( SCF_DIR . '/inc/class.ajax.php' );
require( SCF_DIR . '/inc/class.sidebar.php' );
require( SCF_DIR . '/inc/class.admin.posttype.php' );
require( SCF_DIR . '/inc/class.field.base.php');
require( SCF_DIR . '/inc/class.field.factory.php');
require( SCF_DIR . '/inc/class.field.manager.php');
require( SCF_DIR . '/inc/class.field.sidebar.php');
require( SCF_DIR . '/inc/class.field.control.php');
require( SCF_DIR . '/inc/class.admin.object.php');
require( SCF_DIR . '/inc/class.admin.object.taxo.php');
require( SCF_DIR . '/inc/class.admin.taxo.php');
require( SCF_DIR . '/inc/class.page.taxo.php' );

// Call built'in composants
require( SCF_DIR . '/composants/checkbox.php' );
require( SCF_DIR . '/composants/radio.php' );
require( SCF_DIR . '/composants/editor-light.php' );
require( SCF_DIR . '/composants/select-multiple.php' );
require( SCF_DIR . '/composants/select.php' );
require( SCF_DIR . '/composants/textarea.php' );
require( SCF_DIR . '/composants/input-text.php' );
require( SCF_DIR . '/composants/medias/simple-media.php' );
require( SCF_DIR . '/composants/date-picker/date-picker.php' );
require( SCF_DIR . '/composants/dropdown-users/dropdown-users.php' );
require( SCF_DIR . '/composants/dropdown-pages/dropdown-pages.php' );
require( SCF_DIR . '/composants/separator.php' );
require( SCF_DIR . '/composants/relation-posttype/relation-posttype.php' );

add_action( 'plugins_loaded', 'initCustomFields' );
function initCustomFields() {

	global $custom_fields;

	// Load translations
	load_plugin_textdomain ( 'custom-fields', false, SCF_FOLDER . '/languages' );
	
	// Init
	$custom_fields['admin-base'] = new CF_Admin();
}
?>