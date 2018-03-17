<?php
class CF_Field_RelationPostType extends CF_Field{

        function CF_Field_RelationPostType() {
                $field_ops = array('classname' => 'field_relation-posttype', 'description' => __( 'Relation Post type', 'custom-fields') );
                $this->CF_Field('relation-posttype', __('Relation Post Type', 'custom-fields'), '_input-relatioposttype', true, $field_ops);
                require_once( SCF_DIR . '/composants/relation-posttype/walker.php' );
        }

        function field( $args, $instance ) {
                global $post;
                extract( $args );
                $entries = is_array($entries) ? $entries['name'] : $entries;

                $title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);

                // Get current items for checked datas.
                $current_items = rpt_get_object_relation( $args['post_id'], $instance['posttype'] );
                $current_items = array_map( 'intval', $current_items );
                $_args = array(
                    'nopaging' 			=> true,
                    'order' 			=> 'ASC',
                    'orderby' 			=> 'title',
                    'post_type' 		=> $instance['posttype'],
                    'post_status' 		=> 'publish',
                    /*'suppress_filters'          => true,*/
                    'update_post_term_cache'    => false,
                    'update_post_meta_cache'    => false,
                    'current_id' 		=> $args['post_id'],
                    'current_items'		=> $current_items,
                );

                // For the same post type, exclude current !
                $_args['post__not_in'] = array($args['post_id']);

                $post_type = get_post_type_object($instance['posttype']);
                // Default ?
                if ( isset( $post_type->_default_query ) )
                        $_args = array_merge($_args, (array) $post_type->_default_query );

                $get_posts = new WP_Query;
                $posts = $get_posts->query( $_args );
                if ( ! $get_posts->post_count ) {
                        echo '<p>' . __( 'No items.', 'relation-post-types' ) . '</p>';
                        return;
                }
                
                switch( $instance['mode'] ){
                    case 'multiple_checkboxes':
                        $walker = new Walker_Field_Relations_Checklist;
                        $start_lv = '<ul id="'. $instance['posttype'] . 'checklist" class="list:'. $instance['posttype']. ' categorychecklist form-no-clear">';
                        $end_lv = '</ul>';
                        break;

                    case 'simple':
                        $walker = new Walker_Field_RelationsDropdown;
                        $start_lv = '<select name="'.$this->get_field_name('name').'" style="width: 47%;">';
                        $end_lv = '</select>';
                        break;

                    case 'multiple_select':
                        $walker = new Walker_Field_RelationsDropdown;
                        $start_lv = '<select name="'.$this->get_field_name('name').'[]" style="width: 47%;height: auto;" size="10" multiple>';
                        $end_lv = '</select>';
                        break;

                    default:
                        return false;
                        break;
                }
                echo $before_widget;
                if ( $title)
                        echo $before_title . $title . $after_title;
                ?>
            <div id="posttype-<?php echo $instance['posttype']; ?>" class="posttypediv">
                <div id="<?php echo $instance['posttype']; ?>-all" class="tabs-panel-view-all tabs-panel-active">
                        <?php
                            echo isset($start_lv) ? $start_lv : '';
                            $_args['walker'] = $walker;
                            $_args['name'] = $this->get_field_name('name');
                            $checkbox_items = walk_nav_menu_tree( $posts, $instance['depth'], (object) $_args );
                            echo $checkbox_items;
                            echo isset($end_lv) ? $end_lv : '';
                        ?>
                </div><!-- /.tabs-panel -->
            </div>
            <?php
                if( isset($instance['description']) && $instance['description'] != '' )
                        echo '<p>' . $instance['description'] . '</p>';
                echo $after_widget;

                

                



/*

                $post_type_name = $post_type['args']->name;

            // Get current items for checked datas.
            $current_items = rpt_get_object_relation( $object->ID );
            $current_items = array_map( 'intval', $current_items );

            // Build args for walker
            $args = array(
                    'nopaging' 			=> true,
                    'order' 			=> 'ASC',
                    'orderby' 			=> 'title',
                    'post_type' 		=> $post_type_name,
                    'post_status' 		=> 'publish',
                    'suppress_filters' 	=> true,
                    'update_post_term_cache' => false,
                    'update_post_meta_cache' => false,
                    'current_id' 		=> $object->ID,
                    'current_items'		=> $current_items
            );

            // For the same post type, exclude current !
            if ( $object->post_type == $post_type_name )
                    $args['post__not_in'] = array($object->ID);

            // Default ?
            if ( isset( $post_type['args']->_default_query ) )
                    $args = array_merge($args, (array) $post_type['args']->_default_query );

            $get_posts = new WP_Query;
            $posts = $get_posts->query( $args );
            if ( ! $get_posts->post_count ) {
                    echo '<p>' . __( 'No items.', 'relation-post-types' ) . '</p>';
                    return;
            }

            $walker = new Walker_Relations_Checklist;
            ?>
            <div id="posttype-<?php echo $post_type_name; ?>" class="posttypediv">
                    <div id="<?php echo $post_type_name; ?>-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
                            <ul id="<?php echo $post_type_name; ?>checklist" class="list:<?php echo $post_type_name; ?> categorychecklist form-no-clear">
                                    <?php
                                    $args['walker'] = $walker;
                                    $checkbox_items = walk_nav_menu_tree( $posts, 0, (object) $args );
                                    echo $checkbox_items;
                                    ?>
                            </ul>
                    </div><!-- /.tabs-panel -->
            </div><!-- /.posttypediv -->

            <input type="hidden" name="post-relation-post-types" value="1" />
            <?php
*/
        }

        function save( $values, $args ) {
            if( isset($values['name']) )
                $values = $values['name'];
            if( empty($values) )
                rpt_delete_object_relation( $args['post_id'], array($this->post_type) );
            // Secure datas
            if ( is_array($values) )
                    $values = array_map( 'intval', $values );
            else
                    $values = (int) $values;

            rpt_set_object_relation( $args['post_id'], $values, $this->post_type, false );

            return 1;
        }

        function update( $new_instance, $old_instance ) {
                $instance = $old_instance;
                $instance['title'] = strip_tags($new_instance['title']);
                $instance['posttype'] = strip_tags($new_instance['posttype']);
                $instance['mode'] = strip_tags($new_instance['mode']);
                $instance['description'] = strip_tags($new_instance['description']);
                $instance['depth'] = strip_tags($new_instance['depth']);
                return $instance;
        }

        function form( $instance ) {
                //Defaults
                $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'posttype' => '', 'mode' => 'multiple', 'description' => '', 'depth' => '0' ) );

                $title = esc_attr( $instance['title'] );
                $posttype = esc_attr( $instance['posttype'] );
                $mode = esc_attr($instance['mode']);
                $description = esc_attr($instance['description']);
                $params = array( 'show_ui' => true ); // To get all post_type depends post
                $post_types = get_post_types($params, 'objects');
                $depth = $instance['depth'];
                ?>
                <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-fields'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

                <p>
                    <label for="<?php echo $this->get_field_id('posttype'); ?>"><?php _e('Post Type:', 'custom-fields'); ?></label>
                    <select class="widefat" id="<?php echo $this->get_field_id('posttype'); ?>" name="<?php echo $this->get_field_name('posttype'); ?>">
                        <?php foreach($post_types as $pt):?>
                        <option value="<?php echo esc_attr($pt->name);?>" <?php selected($pt->name, $posttype);?>><?php echo esc_html($pt->name)?></option>
                        <?php endforeach;?>
                    </select>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('depth'); ?>"><?php _e('Depth:', 'custom-fields'); ?> <input type="text" name="<?php echo $this->get_field_name('depth'); ?>" value="<?php echo $depth;?>"></label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id('mode'); ?>"><?php _e('Mode:', 'custom-fields'); ?></label>
                    <label><input type="radio" name="<?php echo $this->get_field_name('mode'); ?>" value="simple" <?php checked('simple', $mode);?>/> Simple</label>
                    <label><input type="radio" name="<?php echo $this->get_field_name('mode'); ?>" value="multiple_checkboxes" <?php checked('multiple_checkboxes', $mode);?>/> Multiple checkboxes</label>
                    <label><input type="radio" name="<?php echo $this->get_field_name('mode'); ?>" value="multiple_select" <?php checked('multiple_select', $mode);?>/> Multiple select</label>
                </p>
                <p><label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'custom-fields'); ?></label> <textarea name="<?php echo $this->get_field_name('description'); ?>" id="<?php echo $this->get_field_id('description'); ?>" cols="20" rows="4" class="widefat"><?php echo $description; ?></textarea></p>
                <?php
        }

}
?>