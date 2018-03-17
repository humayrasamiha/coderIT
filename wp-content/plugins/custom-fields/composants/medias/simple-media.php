<?php
class CF_Field_Media extends CF_Field{

	function CF_Field_Media() {
		$field_ops = array('classname' => 'field_simplemedia', 'description' => __('Simple Media', 'custom-fields') );
		$this->CF_Field('simplemedia', __('Simple Media', 'custom-fields'), 'simplemedia', true, $field_ops);
                add_action( 'cf-fields-scriptstyles-field_simplemedia', array(&$this, 'add_js'), 10, 1 );
                add_action('admin_head' , array($this , 'registerMediaUploaderScripts'));
	}

        function add_js(){
            // Needed scripts
            $needed_scripts = array('jquery' , 'media-upload' , 'thickbox');
            // Widgets scripts
            wp_enqueue_script('cf-widgets-admin' , SCF_URL . '/composants/medias/js/widgets-admin.js' , $needed_scripts);
            wp_enqueue_script('cf-image-widget-admin' , SCF_URL . '/composants/medias/js/image-widget-admin.js' , $needed_scripts);

        }

	function field( $args, $instance ) {
		global $post;
		extract( $args );

		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);

		echo $before_widget;
			echo $before_title . $title . $after_title;
			//$entries = is_array($entries) ? $entries['id'] : $entries;
			$media_id 	= intval	($entries['id']);
                        $link = !empty($entries['link']) ? esc_attr($entries['link']) : '';
                        $displayed_picture = wp_get_attachment_image_src($media_id , 'medium');
                        $source = $displayed_picture[0];
			?>
<div style="border: 1px solid #DFDFDF">
                        <!-- The upload button -->
                        <p class="upload-image" style="line-height: 24px; <?php echo($media_id) ? 'display:none' : ''?>">
                            <input class="cf-image-select button-secondary" id="<?php echo $this->get_field_id('picture'); ?>" type="button" value="<?php echo 'Choose a picture'?>" />
                        </p>
                        <!-- Current picture info -->
                        <div class="current-image" <?php echo(!$media_id) ? 'style="display:none"' : ''?>>
                            <!-- The picture -->
                            <p style="line-height: 24px">
                                <a class="full-view" href="<?php echo $source?>" target="_blank" title="<?php echo 'View fullsize picture'?>">
                                    <img class="picture" src="<?php echo $source?>" style="border: 1px dotted #afafaf" />
                                </a>
                                <input type="hidden" class="id" name="<?php echo $this->get_field_name('id')?>" value="<?php echo $media_id?>" />
                                <input type="hidden" class="source" name="<?php echo $this->get_field_name('source')?>" value="<?php echo $source?>" />
                                <input type="hidden" class="alt" name="<?php echo $this->get_field_name('alt')?>" value="<?php echo $alt?>" />
                            </p>
                            <!-- The link -->
                            <p style="line-height: 24px">
                                <label for="<?php echo $this->get_field_id('link'); ?>"><?php echo 'Link:'?></label>
                                <input class="widefat link" id="<?php echo $this->get_field_id('link')?>" name="<?php echo $this->get_field_name('link')?>" type="text" value="<?php echo $link?>" />
                            </p>
                            <!-- Delete button -->
                            <p style="text-align: center; padding: 10px 0 10px 0;">
                                <a class="button-secondary cf-image-delete" href="#"><?php echo 'Delete this picture'?></a>
                            </p>
                        </div>
			<?php
			if( isset($instance['description']) && $instance['description'] != '' )
				echo '<p>' . $instance['description'] . '</p>';
                        ?>
</div>
                        <?php
		echo $after_widget;

		return true;
	}

        public function registerMediaUploaderScripts()
        {
            global $pagenow;
            if ($pagenow != 'media-upload.php' || !isset($_GET ['cf_upload_type']))
                return;
            // Gets the right label depending on the caller widget
            switch ($_GET ['cf_upload_type'])
            {
                case 'slideshow': $button_label = __('Insert in slideshow', 'custom-fields'); break;
                case 'image': $button_label = __('Select picture', 'custom-fields'); break;
		case 'tripleimage': $button_label = __('Select number of pictures' , 'custom-fields'); break;
                case 'podcast_image': $button_label = __('Select picture' , 'custom-fields'); break;
                case 'podcast_sound': $button_label = __('Select sound' , 'custom-fields'); break;
                case 'flash': $button_label = __('Select Flash file' , 'custom-fields'); break;
                case 'contenu': $button_label = __('Select number of container' , 'custom-fields'); break;
                default: $button_label = __('Insert into Post'); break;
            }
            // Overrides the label when displaying the media uploader panels
            // and adds the needed field
            ?>
                <script type="text/javascript">
                    jQuery(document).ready(function()
                    {
                        jQuery('#media-items').bind('DOMSubtreeModified' , function()
                        {
                            jQuery('td.savesend input[type="submit"]').val("<?php echo $button_label?>");
                        });
                        var mediaInput = '<input type="hidden" name="cf_upload_type" value="<?php echo htmlentities($_GET ['cf_upload_type'])?>" />';
                        jQuery('#filter').prepend(mediaInput);
                    });
                </script>
            <?php
        }

	function save( $values ) {
				$values ['id'] = isset($values ['id']) ? intval($values ['id']) : '' ;
                $values ['source'] = isset($value ['source']) ? $values ['source'] : '' ;
                $values ['alt'] = isset($values ['alt']) ? $values ['alt'] : '' ;
                $values ['link'] = isset($values ['link']) ? $values ['link'] : '' ;
		return $values;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['picture_target'] 		= strip_tags($new_instance['picture_target']);
		$instance['description'] 	= strip_tags($new_instance['description']);

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'picture_target' => 'modal', '$description' => '' ) );

		$title = esc_attr( $instance['title'] );
		$picture_target = esc_attr( $instance['picture_target'] );
		$description = esc_html($instance['description']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('picture_target'); ?>"><?php _e('Show all medias in WP', 'custom-fields'); ?></label>
                        <label for="">
                            <input <?php checked($picture_target , 'modal')?>style="margin-right: 4px" type="radio" name="<?php echo $this->get_field_name('picture_target')?>" id="<?php echo $this->get_field_id('picture_target')?>" value="modal" />
                            <?php echo 'Modal window'?>
                        </label>
                        <label for="">
                            <input <?php checked($picture_target , 'blank')?>style="margin-right: 4px" type="radio" name="<?php echo $this->get_field_name('picture_target')?>" id="<?php echo $this->get_field_id('picture_target')?>" value="blank" />
                            <?php echo 'New window'?>
                        </label>
                        <label for="">
                            <input <?php checked($picture_target , 'self')?>style="margin-right: 4px" type="radio" name="<?php echo $this->get_field_name('picture_target')?>" id="<?php echo $this->get_field_id('picture_target')?>" value="self" />
                            <?php echo 'Same window'?>
                        </label>
		</p>
		<p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'custom-fields'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
		<?php
	}

}
?>