<?php
global $current_screen;

/** WordPress Administration fields API */
//require_once(ABSPATH . 'wp-admin/includes/fields.php');
//require( SCF_DIR . '/inc/functions.widget.php');
if ( ! current_user_can('edit_theme_options') )
	wp_die( __( 'Cheatin&#8217; uh?' ));

wp_admin_css( 'widgets', true );

$fields_access = get_user_setting( 'widgets_access' );
if ( isset($_GET['fields-access']) ) {
	$fields_access = 'on' == $_GET['fields-access'] ? 'on' : 'off';
	set_user_setting( 'widgets_access', $fields_access );
}

if ( 'on' == $fields_access )
	add_filter( 'admin_body_class', create_function('', '{return " widgets_access ";}') );
else
	wp_enqueue_script('admin-widgets');

do_action( 'sidebar_admin_setup' );

$title = __( 'Page Fields', 'custom_fields' );
$parent_file = 'themes.php';

$help = '
	<p>' . __('fields are independent sections of content that can be placed into any fieldized area provided by your theme (commonly called sidebars). To populate your sidebars/field areas with individual fields, drag and drop the title bars into the desired area. By default, only the first field area is expanded. To populate additional field areas, click on their title bars to expand them.') . '</p>
	<p>' . __('Available fields section contains all the fields you can choose from. Once you drag a field into a sidebar, it will open to allow you to configure its settings. When you are happy with the field settings, click the Save button and the field will go live on your site. If you click Delete, it will remove the field.') . '</p>
	<p>' . __('If you want to remove the field but save its setting for possible future use, just drag it into the Inactive fields area. You can add them back anytime from there. This is especially helpful when you switch to a theme with less or different field areas.') . '</p>
	<p>' . __('fields may be used multiple times. You can give each field a title, to display on your site, but it&#8217;s not required.') . '</p>
	<p>' . __('Enabling Accessibility Mode, via Screen Options, allows you to use Add and Edit buttons instead of using drag and drop.') . '</p>
';
$help .= '<p><strong>' . __('For more information:') . '</strong></p>';
$help .= '<p>' . __('<a href="http://codex.wordpress.org/Appearance_fields_SubPanel">fields Documentation</a>') . '</p>';
$help .= '<p>' . __('<a href="http://wordpress.org/support/">Support Forums</a>') . '</p>';
add_contextual_help($current_screen, $help);
// register the inactive_fields area as sidebar
$this->cf_sidebar->cf_register_sidebar(array(
	'name' => __('Inactive fields', 'custom_fields'),
	'id' => 'cf_inactive_fields',
	'description' => '',
	'before_field' => '',
	'after_field' => '',
	'before_title' => '',
	'after_title' => '',
));

// These are the fields grouped by sidebar
$this->sidebars_fields = $this->cf_get_sidebars_fields();
if ( empty( $this->sidebars_fields ) )
	$this->sidebars_fields = $this->cf_get_field_defaults();
// look for "lost" fields, this has to run at least on each theme change
$this->retrieve_fields();

// We're saving a field without js
if ( isset($_POST['savefield']) || isset($_POST['removefield']) ) {
	$field_id = $_POST['field-id'];
	check_admin_referer("save-delete-field-$field_id");

	$number = isset($_POST['multi_number']) ? (int) $_POST['multi_number'] : '';
	if ( $number ) {
		foreach ( $_POST as $key => $val ) {
			if ( is_array($val) && preg_match('/__i__|%i%/', key($val)) ) {
				$_POST[$key] = array( $number => array_shift($val) );
				break;
			}
		}
	}

	$sidebar_id = $_POST['sidebar'];
	$position = isset($_POST[$sidebar_id . '_position']) ? (int) $_POST[$sidebar_id . '_position'] - 1 : 0;

	$id_base = $_POST['id_base'];
	$sidebar = isset($GLOBALS['sidebars_fields'][$sidebar_id]) ? $GLOBALS['sidebars_fields'][$sidebar_id] : array();

	// delete
	if ( isset($_POST['removefield']) && $_POST['removefield'] ) {

		if ( !in_array($field_id, $sidebar, true) ) {
			wp_redirect('widgets.php?error=0');
			exit;
		}

		$sidebar = array_diff( $sidebar, array($field_id) );
		$_POST = array('sidebar' => $sidebar_id, 'field-' . $id_base => array(), 'the-field-id' => $field_id, 'delete_field' => '1');
	}

	$_POST['field-id'] = $sidebar;

	foreach ( (array) $cf_registered_field_updates as $name => $control ) {
		if ( $name != $id_base || !is_callable($control['callback']) )
			continue;

		ob_start();
			call_user_func_array( $control['callback'], $control['params'] );
		ob_end_clean();

		break;
	}

	$GLOBALS['sidebars_fields'][$sidebar_id] = $sidebar;

	// remove old position
	if ( !isset($_POST['delete_field']) ) {
		foreach ( $GLOBALS['sidebars_fields'] as $key => $sb ) {
			if ( is_array($sb) )
				$GLOBALS['sidebars_fields'][$key] = array_diff( $sb, array($field_id) );
		}
		array_splice( $GLOBALS['sidebars_fields'][$sidebar_id], $position, 0, $field_id );
	}

	$this->cf_set_sidebars_fields($GLOBALS['sidebars_fields']);
	wp_redirect('fields.php?message=0');
	exit;
}
// Output the field form without js
if ( isset($_GET['editfield']) && $_GET['editfield'] ) {
	$field_id = $_GET['editfield'];

	if ( isset($_GET['addnew']) ) {
		// Default to the first sidebar
		$sidebar = array_shift( $keys = array_keys($this->cf_registered_sidebars) );

		if ( isset($_GET['base']) && isset($_GET['num']) ) { // multi-field
			// Copy minimal info from an existing instance of this field to a new instance
			foreach ( $this->cf_registered_field_controls as $control ) {
				if ( $_GET['base'] === $control['id_base'] ) {
					$control_callback = $control['callback'];
					$multi_number = (int) $_GET['num'];
					$control['params'][0]['number'] = -1;
					$field_id = $control['id'] = $control['id_base'] . '-' . $multi_number;
					$this->cf_registered_field_controls[$control['id']] = $control;
					break;
				}
			}
		}
	}

	if ( isset($this->cf_registered_field_controls[$field_id]) && !isset($control) ) {
		$control = $this->cf_registered_field_controls[$field_id];
		$control_callback = $control['callback'];
	} elseif ( !isset($this->cf_registered_field_controls[$field_id]) && isset($cf_registered_fields[$field_id]) ) {
		$name = esc_html( strip_tags($cf_registered_fields[$field_id]['name']) );
	}
	$control['params'][0]['post_type'] = $this->post_type;
	if ( !isset($name) )
		$name = esc_html( strip_tags($control['name']) );

	if ( !isset($sidebar) )
		$sidebar = isset($_GET['sidebar']) ? $_GET['sidebar'] : 'cf_inactive_fields';

	if ( !isset($multi_number) )
		$multi_number = isset($control['params'][0]['number']) ? $control['params'][0]['number'] : '';

	$id_base = isset($control['id_base']) ? $control['id_base'] : $control['id'];

	// show the field form
	$width = ' style="width:' . max($control['width'], 350) . 'px"';
	$key = isset($_GET['key']) ? (int) $_GET['key'] : 0;

	require_once( './admin-header.php' ); ?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( $title ); ?></h2>
	<div class="editfield"<?php echo $width; ?>>
	<h3><?php printf( __( 'field %s', 'custom_fields' ), $name ); ?></h3>
	<form action="fields.php" method="post">
	<div class="field-inside">
           
<?php
	if ( is_callable( $control_callback ) )
		call_user_func_array( $control_callback, $control['params'] );
	else
		echo '<p>' . __('There are no options for this field.', 'custom_fields') . "</p>\n"; ?>
	</div>

	<p class="describe"><?php _e('Select both the sidebar for this field and the position of the field in that sidebar.', 'custom_fields'); ?></p>
	<div class="field-position">
	<table class="widefat"><thead><tr><th><?php _e('Sidebar'); ?></th><th><?php _e('Position'); ?></th></tr></thead><tbody>
<?php
	foreach ( $this->cf_registered_sidebars as $sbname => $sbvalue ) {
		echo "\t\t<tr><td><label><input type='radio' name='sidebar' value='" . esc_attr($sbname) . "'" . checked( $sbname, $sidebar, false ) . " /> $sbvalue[name]</label></td><td>";
		if ( 'cf_inactive_fields' == $sbname ) {
			echo '&nbsp;';
		} else {
			if ( !isset($this->sidebars_fields[$sbname]) || !is_array($this->sidebars_fields[$sbname]) ) {
				$j = 1;
				$this->sidebars_fields[$sbname] = array();
			} else {
				$j = count($this->sidebars_fields[$sbname]);
				if ( isset($_GET['addnew']) || !in_array($field_id, $this->sidebars_fields[$sbname], true) )
					$j++;
			}
			$selected = '';
			echo "\t\t<select name='{$sbname}_position'>\n";
			echo "\t\t<option value=''>" . __('&mdash; Select &mdash;') . "</option>\n";
			for ( $i = 1; $i <= $j; $i++ ) {
				if ( in_array($field_id, $this->sidebars_fields[$sbname], true) )
					$selected = selected( $i, $key + 1, false );
				echo "\t\t<option value='$i'$selected> $i </option>\n";
			}
			echo "\t\t</select>\n";
		}
		echo "</td></tr>\n";
	} ?>
	</tbody></table>
	</div>

	<div class="field-control-actions">
<?php	if ( isset($_GET['addnew']) ) { ?>
	<a href="fields.php" class="button alignleft"><?php _e('Cancel'); ?></a>
<?php	} else { ?>
	<input type="submit" name="removefield" class="button alignleft" value="<?php esc_attr_e('Delete'); ?>" />
<?php	} ?>
	<input type="submit" name="savefield" class="button-primary alignright" value="<?php esc_attr_e('Save field', 'custom_fields'); ?>" />
	<input type="hidden" name="field-id" class="field-id" value="<?php echo esc_attr($field_id); ?>" />
	<input type="hidden" name="id_base" class="id_base" value="<?php echo esc_attr($id_base); ?>" />
	<input type="hidden" name="multi_number" class="multi_number" value="<?php echo esc_attr($multi_number); ?>" />
<?php	wp_nonce_field("save-delete-field-$field_id"); ?>
	<br class="clear" />
	</div>
	</form>
	</div>
	</div>
<?php
	require_once( './admin-footer.php' );
	exit;
}

$messages = array(
	__('Changes saved.')
);

$errors = array(
	__('Error while saving.'),
	__('Error in displaying the field settings form.', 'custom_fields')
);

require_once( './admin-header.php' ); ?>

<div id="page-fields" class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<?php if ( isset($_GET['message']) && isset($messages[$_GET['message']]) ) { ?>
<div id="message" class="updated"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php } ?>
<?php if ( isset($_GET['error']) && isset($errors[$_GET['error']]) ) { ?>
<div id="message" class="error"><p><?php echo $errors[$_GET['error']]; ?></p></div>
<?php } ?>

<?php do_action( 'fields_admin_page' ); ?>
<div class="field-liquid-left">
<div id="fields-left">

<div class="add_sidebar fields-holder-wrap closed"><div>
<h3><span><input type="text" value="<?php _e('More sidebar'); ?>" name="add_sidebar_name"/><input type="submit" value="OK" name="add_sidebar_submit"/></span></h3>
</div>
</div>
<?php
$i = 0;
foreach ( $this->cf_registered_sidebars as $sidebar => $registered_sidebar ) {
	if ( 'cf_inactive_fields' == $sidebar )
		continue;
	$closed = $i ? ' closed' : ''; ?>
	<div class="fields-holder-wrap<?php echo $closed; ?>">
	<div class="sidebar-name">
	<div class="sidebar-name-arrow"><br /></div>
	<h3><?php echo esc_html( $registered_sidebar['name'] ); ?>
	<span>
		<img src="<?php echo esc_url( admin_url( 'images/wpspin_dark.gif' ) ); ?>" class="ajax-feedback" title="" alt="" />
	</span></h3>
        <div style="text-align: center;display:none;margin-bottom:5px" class="rename">
            <input type="hidden" name="attr_sidebar" value="<?php echo $registered_sidebar['id']?>"/>
            <input type="text" style="width: 70%;" name="rename_sidebar" value="<?php echo esc_html( $registered_sidebar['name'] ); ?>"/>
            <input type="submit" class="submit-rename" value="OK"/>
        </div></div>
        <div class="sidebar-options">
            <input type="hidden" name="attr_sidebar" value="<?php echo $registered_sidebar['id']?>"/>
            <input type="hidden" name="del_sidebar" value="-" class="del_sidebar"/>
            <a href="" class="edit-sidebar-link"><?php _e('Edit', 'custom_fields'); ?></a>
            <a href="" class="remove-sidebar-link"><?php _e('Delete', 'custom_fields'); ?></a>
        </div>
	<?php $this->cf_field_control->cf_list_field_controls( $sidebar ); // Show the control forms for each of the fields in this sidebar ?>
            <div class="clear"></div>
        </div>
<?php
	$i++;
} ?>
</div>
</div>

<div class="field-liquid-right">
<div id="fields-right">
	<div id="available-fields" class="fields-holder-wrap">
		<div class="sidebar-name">
		<div class="sidebar-name-arrow"><br /></div>
		<h3><?php _e('Available fields', 'custom_fields'); ?> <span id="removing-field"><?php _e('Deactivate'); ?> <span></span></span></h3></div>
		<div class="field-holder">
		<p class="description"><?php _e('Drag fields from here to a sidebar on the right to activate them. Drag fields back here to deactivate them and delete their settings.', 'custom_fields'); ?></p>
		<div id="field-list">
		<?php $this->cf_field_manager->cf_list_fields(); ?>
		</div>
		<br class='clear' />
		</div>
		<br class="clear" />
	</div>

	<div class="fields-holder-wrap">
		<div class="sidebar-name">
		<div class="sidebar-name-arrow"><br /></div>
		<h3><?php _e('Inactive fields', 'custom_fields'); ?>
		<span><img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-feedback" title="" alt="" /></span></h3></div>
		<div class="field-holder inactive">
		<p class="description"><?php _e('Drag fields here to remove them from the sidebar but keep their settings.', 'custom_fields'); ?></p>
		<?php $this->cf_field_control->cf_list_field_controls('cf_inactive_fields'); ?>
		<br class="clear" />
		</div>
	</div>
</div>
</div>
<form action="" method="post">
<input type="hidden" name="post_type" class="post_type" value="<?php echo esc_attr($this->post_type);?>" />
<?php wp_nonce_field( 'save-sidebar-fields', '_wpnonce_fields', false ); ?>
</form>

<br class="clear" />
</div>