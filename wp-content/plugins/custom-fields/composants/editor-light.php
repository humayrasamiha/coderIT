<?php
/**
 * Light editor type
 *
 * @package default
 * @author Julien Guilmont
 */
class CF_Field_EditorLight extends CF_Field{
	
	function CF_Field_EditorLight() {
		$field_ops = array('classname' => 'field_editorlight', 'description' => __( 'The Light Editor', 'custom-fields') );
		$this->CF_Field('editorlight', __('Editor Light', 'custom-fields'), '_input-editorlight', true, $field_ops);
	}
	
	function field( $args, $instance ) {
		extract( $args );
		
		$entries = is_array($entries) ? $entries['name'] : $entries;
		add_action( 'admin_print_footer_scripts', array(&$this, 'customTinyMCE'), 9999 );
		$title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;

		?>
		<div class="editor-light-field" style="background:#FFFFFF">
			<textarea class="mceEditor" name="<?php echo $this->get_field_name('name'); ?>" id="<?php echo $this->get_field_id('name'); ?>" rows="5" cols="50" style="width: 97%;"><?php echo esc_html($entries)?></textarea>
		</div>
		<?php if( isset($instance['description']) )?>
			<p class="howto"><?php echo $instance['description']; ?></p>
		<?php
		echo $after_widget;
	}
	
	function save( $values ) {
		$values = isset($values['name']) ? $values['name'] : '' ;
		return $values;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['description'] = strip_tags($new_instance['description']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'description' => '' ) );
		$title = esc_attr( $instance['title'] );
		$description = esc_html($instance['description']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'custom-fields'); ?></label>
			<textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea>
		</p>
		<?php
	}
	
	/**
	 * Display TinyMCE JS for init light editor.
	 * 
	 */
	function customTinyMCE() {
		global $flag_tiny_mce, $concatenate_scripts, $compress_scripts, $tinymce_version, $editor_styles;
		
		if ( isset($flag_tiny_mce) && $flag_tiny_mce == true)
			return false;

		if ( ! user_can_richedit() )
			return;

		$baseurl = includes_url('js/tinymce');

		$mce_locale = ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) ); // only ISO 639-1
		
		/*
		The following filter allows localization scripts to change the languages displayed in the spellchecker's drop-down menu.
		By default it uses Google's spellchecker API, but can be configured to use PSpell/ASpell if installed on the server.
		The + sign marks the default language. More information:
		http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/spellchecker
		*/
		$mce_spellchecker_languages = apply_filters('mce_spellchecker_languages', '+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv');
		$plugins = array( 'inlinepopups', 'spellchecker', 'tabfocus', 'paste', 'media', 'wordpress', 'wpfullscreen', 'wpeditimage', 'wpgallery', 'wplink', 'wpdialogs' );

		/*
		The following filter takes an associative array of external plugins for TinyMCE in the form 'plugin_name' => 'url'.
		It adds the plugin's name to TinyMCE's plugins init and the call to PluginManager to load the plugin.
		The url should be absolute and should include the js file name to be loaded. Example:
		array( 'myplugin' => 'http://my-site.com/wp-content/plugins/myfolder/mce_plugin.js' )
		If the plugin uses a button, it should be added with one of the "$mce_buttons" filters.
		*/
		$mce_external_plugins = apply_filters('mce_external_plugins', array());

		$ext_plugins = '';
		if ( ! empty($mce_external_plugins) ) {

			/*
			The following filter loads external language files for TinyMCE plugins.
			It takes an associative array 'plugin_name' => 'path', where path is the
			include path to the file. The language file should follow the same format as
			/tinymce/langs/wp-langs.php and should define a variable $strings that
			holds all translated strings.
			When this filter is not used, the function will try to load {mce_locale}.js.
			If that is not found, en.js will be tried next.
			*/
			$mce_external_languages = apply_filters('mce_external_languages', array());

			$loaded_langs = array();
			$strings = '';

			if ( ! empty($mce_external_languages) ) {
				foreach ( $mce_external_languages as $name => $path ) {
					if ( @is_file($path) && @is_readable($path) ) {
						include_once($path);
						$ext_plugins .= $strings . "\n";
						$loaded_langs[] = $name;
					}
				}
			}

			foreach ( $mce_external_plugins as $name => $url ) {

				if ( is_ssl() ) $url = str_replace('http://', 'https://', $url);

				$plugins[] = '-' . $name;

				$plugurl = dirname($url);
				$strings = $str1 = $str2 = '';
				if ( ! in_array($name, $loaded_langs) ) {
					$path = str_replace( WP_PLUGIN_URL, '', $plugurl );
					$path = WP_PLUGIN_DIR . $path . '/langs/';

					if ( function_exists('realpath') )
						$path = trailingslashit( realpath($path) );

					if ( @is_file($path . $mce_locale . '.js') )
						$strings .= @file_get_contents($path . $mce_locale . '.js') . "\n";

					if ( @is_file($path . $mce_locale . '_dlg.js') )
						$strings .= @file_get_contents($path . $mce_locale . '_dlg.js') . "\n";

					if ( 'en' != $mce_locale && empty($strings) ) {
						if ( @is_file($path . 'en.js') ) {
							$str1 = @file_get_contents($path . 'en.js');
							$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str1, 1 ) . "\n";
						}

						if ( @is_file($path . 'en_dlg.js') ) {
							$str2 = @file_get_contents($path . 'en_dlg.js');
							$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str2, 1 ) . "\n";
						}
					}

					if ( ! empty($strings) )
						$ext_plugins .= "\n" . $strings . "\n";
				}

				$ext_plugins .= 'tinyMCEPreInit.load_ext("' . $plugurl . '", "' . $mce_locale . '");' . "\n";
				$ext_plugins .= 'tinymce.PluginManager.load("' . $name . '", "' . $url . '");' . "\n";
			}
		}
		
		$plugins = implode($plugins, ',');
		
		$mce_buttons = apply_filters('mce_buttons', array('bold', 'italic', 'strikethrough', '|', 'bullist', 'numlist', 'blockquote', '|', 'justifyleft', 'justifycenter', 'justifyright', '|', 'link', 'unlink', 'wp_more', '|', 'spellchecker', 'wp_adv', '|', 'add_media', 'add_image', 'add_video', 'add_audio' ));
		$mce_buttons = implode($mce_buttons, ',');

		$mce_buttons_2 = array('formatselect', 'underline', 'justifyfull', 'forecolor', '|', 'pastetext', 'pasteword', 'removeformat', '|', 'media', 'charmap', '|', 'outdent', 'indent', '|', 'undo', 'redo', 'wp_help', 'code' );
		if ( is_multisite() )
			unset( $mce_buttons_2[ array_search( 'media', $mce_buttons_2 ) ] );
		$mce_buttons_2 = apply_filters('mce_buttons_2', $mce_buttons_2);
		$mce_buttons_2 = implode($mce_buttons_2, ',');
		
		$mce_buttons_3 = apply_filters('mce_buttons_3', array());
		$mce_buttons_3 = implode($mce_buttons_3, ',');

		$mce_buttons_4 = apply_filters('mce_buttons_4', array());
		$mce_buttons_4 = implode($mce_buttons_4, ',');
		
		$no_captions = (bool) apply_filters( 'disable_captions', '' );

		// TinyMCE init settings
		$initArray = array (
			'mode' => 'specific_textareas',
			'editor_selector' => 'mceEditor',
			'width' => '100%',
			'theme' => 'advanced',
			'skin' => 'wp_theme',
			'theme_advanced_buttons1' => $mce_buttons,
			'theme_advanced_buttons2' => $mce_buttons_2,
			'theme_advanced_buttons3' => $mce_buttons_3,
			'theme_advanced_buttons4' => $mce_buttons_4,
			'language' => $mce_locale,
			'spellchecker_languages' => $mce_spellchecker_languages,
			'theme_advanced_toolbar_location' => 'top',
			'theme_advanced_toolbar_align' => 'left',
			'theme_advanced_statusbar_location' => 'bottom',
			'theme_advanced_resizing' => true,
			'theme_advanced_resize_horizontal' => false,
			'dialog_type' => 'modal',
			'relative_urls' => false,
			'remove_script_host' => false,
			'convert_urls' => false,
			'apply_source_formatting' => false,
			'remove_linebreaks' => true,
			'gecko_spellcheck' => true,
			'entities' => '38,amp,60,lt,62,gt',
			'accessibility_focus' => true,
			'tabfocus_elements' => 'major-publishing-actions',
			'media_strict' => false,
			'paste_remove_styles' => true,
			'paste_remove_spans' => true,
			'paste_strip_class_attributes' => 'all',
			'wpeditimage_disable_captions' => $no_captions,
			'plugins' => $plugins
		);

		if ( ! empty( $editor_styles ) && is_array( $editor_styles ) ) {
			$mce_css = array();
			$style_uri = get_stylesheet_directory_uri();
			if ( TEMPLATEPATH == STYLESHEETPATH ) {
				foreach ( $editor_styles as $file )
					$mce_css[] = "$style_uri/$file";
			} else {
				$style_dir    = get_stylesheet_directory();
				$template_uri = get_template_directory_uri();
				$template_dir = get_template_directory();
				foreach ( $editor_styles as $file ) {
					if ( file_exists( "$style_dir/$file" ) )
						$mce_css[] = "$style_uri/$file";
					if ( file_exists( "$template_dir/$file" ) )
						$mce_css[] = "$template_uri/$file";
				}
			}
			$mce_css = implode( ',', $mce_css );
		} else {
			$mce_css = '';
		}

		$mce_css = trim( apply_filters( 'mce_css', $mce_css ), ' ,' );

		if ( ! empty($mce_css) )
			$initArray['content_css'] = $mce_css;

		if ( isset($settings) && is_array($settings) )
			$initArray = array_merge($initArray, $settings);

		// For people who really REALLY know what they're doing with TinyMCE
		// You can modify initArray to add, remove, change elements of the config before tinyMCE.init
		// Setting "valid_elements", "invalid_elements" and "extended_valid_elements" can be done through "tiny_mce_before_init".
		// Best is to use the default cleanup by not specifying valid_elements, as TinyMCE contains full set of XHTML 1.0.
		if ( isset($teeny) && $teeny ) {
			$initArray = apply_filters('teeny_mce_before_init', $initArray);
		} else {
			$initArray = apply_filters('tiny_mce_before_init', $initArray);
		}

                $initArray = apply_filters('custom_tiny_mce_before_init', $initArray, $this->id_base, $this->id, $this->post_type);

		if ( empty($initArray['theme_advanced_buttons3']) && !empty($initArray['theme_advanced_buttons4']) ) {
			$initArray['theme_advanced_buttons3'] = $initArray['theme_advanced_buttons4'];
			$initArray['theme_advanced_buttons4'] = '';
		}

		if ( ! isset($concatenate_scripts) )
			script_concat_settings();

		$language = $initArray['language'];

		$compressed = false;

		/**
		 * Deprecated
		 *
		 * The tiny_mce_version filter is not needed since external plugins are loaded directly by TinyMCE.
		 * These plugins can be refreshed by appending query string to the URL passed to mce_external_plugins filter.
		 * If the plugin has a popup dialog, a query string can be added to the button action that opens it (in the plugin's code).
		 */
		$version = apply_filters('tiny_mce_version', '');
		$version = 'ver=' . $tinymce_version . $version;

		if ( 'en' != $language )
			include_once(ABSPATH . WPINC . '/js/tinymce/langs/wp-langs.php');

		$mce_options = '';
                foreach ( $initArray as $k => $v ) {
                        if ( is_bool($v) ) {
                                $val = $v ? 'true' : 'false';
                                $mce_options .= $k . ':' . $val . ', ';
                                continue;
                        } elseif ( !empty($v) && is_string($v) && ( '{' == $v{0} || '[' == $v{0} ) ) {
                                $mce_options .= $k . ':' . $v . ', ';
                                continue;
                        }

                        $mce_options .= $k . ':"' . $v . '", ';
                }

                $mce_options = rtrim( trim($mce_options), '\n\r,' );
		?>
		<script type="text/javascript">
		/* <![CDATA[ */
		tinyMCEPreInit = {
			base : "<?php echo $baseurl; ?>",
			suffix : "",
			query : "<?php echo $version; ?>",
			mceInit : {<?php echo $mce_options; ?>},
			load_ext : function(url,lang) {var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
		};
		/* ]]> */
		</script>
		
		<?php
		/*
			if ( $compressed )
				echo "<script type='text/javascript' src='$baseurl/wp-tinymce.php?c=1&amp;$version'></script>\n";
			else
				echo "<script type='text/javascript' src='$baseurl/tiny_mce.js?$version'></script>\n";

			if ( 'en' != $language && isset($lang) )
				echo "<script type='text/javascript'>\n$lang\n</script>\n";
			else
				echo "<script type='text/javascript' src='$baseurl/langs/wp-langs-en.js?$version'></script>\n";
		*/
		?>
		
		<script type="text/javascript">
		/* <![CDATA[ */
		<?php if ( $ext_plugins ) echo "$ext_plugins\n"; ?>
		<?php if ( $compressed ) { ?>
		tinyMCEPreInit.go();
		<?php } else { ?>
		(function() {var t=tinyMCEPreInit,sl=tinymce.ScriptLoader,ln=t.mceInit.language,th=t.mceInit.theme,pl=t.mceInit.plugins;sl.markDone(t.base+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'_dlg.js');tinymce.each(pl.split(','),function(n) {if(n&&n.charAt(0)!='-') {sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'.js');sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'_dlg.js');}});})();
		<?php } ?>
		tinyMCE.init(tinyMCEPreInit.mceInit);
		/* ]]> */
		</script>
		<?php	
		$flag_tiny_mce = true;
	}
}
?>