<?php

function get_fieldmeta( $post_id = null, $name = null, $alone = true) {
	if($post_id == null || $name == null)
		return false;
		
	$metas = get_all_fieldsmeta( $post_id );
	
	if(empty($metas))
		return false;
	
	$name = strtolower($name);
	if( array_key_exists($name, $metas) )
		$return = $metas[$name];
	else	
		return false;
	
	if( !isset($metas[$name]) )
		return false;
		
	if($alone == true)
		return current( (array)$metas[$name] );
	else
		return $metas[$name];
}

function get_all_fieldsmeta( $post_id = null ) {
	global $custom_fields;
	
	if($post_id == null)
		return false;
		
	$post = get_post($post_id);
	
	$ar = get_post_custom($post_id);
	if(is_wp_error($ar))
		return false;
	
	$metas = array();	
	$cf_pt = &$custom_fields['admin-base']->post_type_nav[$post->post_type];
	foreach($ar as $key => $value) {
		$key = explode('__', $key);
		if(count($key)<=1)
			continue;
		
		$value = current($value);
		$value = maybe_unserialize($value);
		
		if ( isset($cf_pt->option_fields[$key[0]][$key[1]]['title']) )
			$metas[ strtolower($cf_pt->option_fields[$key[0]][$key[1]]['title']) ] = $value;
	}
	return $metas;
}

function get_fieldmeta_taxo( $term_id = null, $taxonomy = null, $name = null, $alone = true ) {
	if($term_id == null || $name == null || $taxonomy == null)
		return false;
	
	$metas = get_all_fieldtaxo( $term_id, $taxonomy );
	
	if(empty($metas))
		return false;
	
	$name = strtolower($name);
	if( array_key_exists($name, $metas) )
		$return = $metas[$name];
	else	
		return false;
	
	if( !isset($metas[$name]) )
		return false;
		
	if($alone == true)
		return current( (array)$metas[$name] );
	else
		return $metas[$name];
}

function get_all_fieldtaxo( $term_id = null, $taxonomy = null ) {
	global $custom_fields;
	
	if($term_id == null || $taxonomy == null)
		return false;
		
	$term = get_term($term_id, $taxonomy);
	
	if(is_wp_error($term))
		return false;
	
	$ar = get_term_custom( $taxonomy, $term_id );
	if(is_wp_error($ar) || $ar == false)
		return false;
	
	$metas = array();	
	$cf_pt = &$custom_fields['admin-base']->taxo_nav[$taxonomy];
	foreach($ar as $key => $value) {
		$key = explode('__', $key);
		if(count($key)<=1)
			continue;
		
		$value = current($value);
		$value = maybe_unserialize($value);
		
		if ( isset($cf_pt->option_fields[$key[0]][$key[1]]['title']) )
			$metas[ strtolower($cf_pt->option_fields[$key[0]][$key[1]]['title']) ] = $value;
	}
	return $metas;
}

add_action('in_field_form', 'addSlug', 10, 3);

function addSlug( $field, $return, $instance) {
        if( ( isset($field->slug) || !empty($field->slug) ) && isset($instance['slug']) )
            $slug = esc_attr( $instance['slug'] );
        else
            $slug = '';
	?>
	<p><label for="<?php echo $field->get_field_id('slug'); ?>"><?php _e('Slug:'); ?></label> <input class="widefat" id="<?php echo $field->get_field_id('slug'); ?>" name="<?php echo $field->get_field_name('slug'); ?>" type="text" value="<?php echo $slug; ?>" style="font-style: italic;"/>
	<br/>
	<?php if( isset($field->slug) && !empty($field->slug) ): ?>
		<small><?php _e('Meta slug:') ?> <i><?php echo esc_html($field->slug);?></i></small>
	<?php else:?>
		<small><?php _e('Default meta slug:') ?> <i><?php echo $field->option_name . '__' . $field->number;?></i></small>
	<?php endif; ?>
		</p>
	<?php
}

//add_action('in_field_form', 'requiredForm', 9, 3);

function requiredForm( $field, $return, $instance ){
        if( !isset($field->require) )
            $require = false;
        else
            $require = $field->require;
    ?>
                <p><label for="<?php echo $field->get_field_id('require'); ?>"><?php _e('Require:'); ?> <input class="widefat" id="<?php echo $field->get_field_id('require'); ?>" name="<?php echo $field->get_field_name('require'); ?>" type="checkbox" value="1" <?php checked($require);?> style="width:auto;"/></label></p>
    <?php

}