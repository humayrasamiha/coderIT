<?php
/**
 * Create HTML list of relations items.
 *
 * @uses Walker_Relations_Menu
 */
class Walker_Field_Relations_Checklist extends Walker_Relations_Menu  {
	/**
	 * @see Walker::start_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item item data object.
	 * @param int $depth Depth of item. Used for padding.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth, $args) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		// Clean possible label
		$item->post_title = trim($item->post_title);
		$item->post_name  = trim($item->post_name);

		$output .= $indent . '<li>';
		$output .= '<label class="menu-item-title">';
			$output .= '<input type="checkbox" '.checked( true, in_array($item->ID, (array) $args->current_items), false).' class="menu-item-checkbox" name="' . $args->name . '[]" value="'. esc_attr( $item->ID ) .'" /> ';

			if ( !empty($item->post_title) ) {
				$output .= esc_html( $item->post_title );
			} elseif ( !empty($item->post_name) ) {
				$output .= esc_html( $item->post_name );
			} else {
				$output .= esc_html( sprintf(__('Item %d', 'relations-post-types'), $item->ID) );
			}
		$output .= '</label>';
	}
}
/**
 * Create HTML dropdown list of pages.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_Field_RelationsDropdown extends Walker_Relations_Menu {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page in reference to parent pages. Used for padding.
	 * @param array $args Uses 'selected' argument for selected page to set selected HTML attribute for option element.
	 */
	function start_el(&$output, $item, $depth, $args) {

                // Clean possible label
		$item->post_title = trim($item->post_title);
		$item->post_name  = trim($item->post_name);

		$pad = str_repeat('&nbsp;', $depth * 3);

		$output .= "\t<option class=\"level-$depth\" value=\"$item->ID\"";
		if ( in_array($item->ID, (array) $args->current_items) )
			$output .= ' selected="selected"';
		$output .= '>';
		$title = esc_html($item->post_title);
		$output .= "$pad$title";
		$output .= "</option>\n";
	}
}
?>
