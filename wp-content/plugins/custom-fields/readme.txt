=== Custom fields ===
Contributors: djudorange
Donate link: http://www.djudorange.fr/donate/
Tags: custom, fields, custom fields, term meta, meta, post meta, object meta, editor
Requires at least: 3.0.2
Tested up to: 3.3.1
Stable tag: 3.0.2

This plugin add custom fields for some things on WordPress, term taxonomy and custom object types

== Description ==

This plugin add custom fields for some things on WordPress, term taxonomy and custom object types
The usage is a Widgets like.

== Installation ==

1. Download, unzip and upload to your WordPress plugins directory
2. Activate the plugin within you WordPress Administration Backend
3. Go to Post > Fields and add sidebar and drag and drop a "field" like widget for add field. The menu Taxo field is to manage field in taxonomies (in edit page).

To use in your theme, you can use next functions:

get_fieldmeta( $post_id = null, $name = null, $alone = true) : for post_type
get_fieldmeta_taxo( $term_id = null, $taxonomy = null, $name = null, $alone = true ) : for taxonomy

== Screenshots ==

1. The field manager page which you can add field, like widget (drag and drop)
2. The edit post page with fields created by custom fields

== Changelog ==
* Version 3.0.2
	* Fix JS compatible with new Sortable JQuery UI
* Version 3.0.1 :
	* New Style of Admin page fields and in Post edit page
	* Fix multiple Bug (already Stable)
	* Add function to rename and remove Sidebars
	* Fix all built'in Fields (remove editor and add functionnalities in editor light)
	* Add Simple media to get image with upload box
	* Core Restructuration (performance increase)
* Version 2.2.2 :
	* Add composant Editor with upload media
	* Fix bug with constant
* Version 2.2.1 :
	* Add composant separator
* Version 2.2.0 :
	* Add composant "Ajax Uploader", only classic mode actually.
	* Update some mecanism for allow composant with _FILES and _POST
* Version 2.1.0 :
	* Fix bug with renamming slug of custom fields
	* Fix bug with auto-save and loss datas
	* Fix bug with visual editor and taxo
	* Change datepicker for standalone library (no bug with IE and past dates)
	* Fix bug that not allow to delete a custom field...
* Version 2.0.9 : 
	* Add function to reload slug
	* Fix bug with slug
* Version 2.0.8 : 
	* Add custom slug of metas in field of "fields"
* Version 2.0.7 : 
	* Change to save entry in string (no serialize) for all fields
	* Load JS/CSS just if you are in edit post type or taxonomy
* Version 2.0.6 : 
	* Split composants on singular files.
* Version 2.0.5 :
	* Fix bug with the loop of TinyMCE Declaration.
	* Start to internationalize missing code
* Version 2.0.0 :
	* First version stable