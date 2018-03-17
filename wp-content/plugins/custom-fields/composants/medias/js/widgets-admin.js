/**
 * Registers a button to be used with the media uploader
 * @param   String      selector    the button (jQuery selector)
 * @param   Function    callback    the callback (will override the window.send_to_editor function)
 * @param   String      parameters  the media uploader paramters (GET format)
 */
function cf_register_editor_button(selector , callback , parameters)
{
    jQuery(selector).live('click' , function()
    {
        var parent_widget = jQuery(this).parents('.form-field');
        parent_widget.addClass('cf-current-widget');
        window.default_send_to_editor = window.send_to_editor;
        window.send_to_editor = callback;
        tb_show('' , parameters);
        return false;
    });
}

/**
 * Parses a html string that contains a <img> tag, and extracts its properties (src, alt, caption..)
 * The function is used to catch the media uploader output and inject the picture properties in a custom widget
 * @return  Object  an associative array with the extracted data
 */
function cf_get_image_data_from_html(html)
{
    var image = jQuery(html);
    if (image.filter('a').length > 0)
        image = image.find('img');
    var image_data = new Object();
    image_data ['id'] = image.attr('class').match(/wp-image-([0-9]+)/i) [1];
    image_data ['src'] = image.attr('src');
    image_data ['caption'] = image.attr('title');
    image_data ['alt'] = image.attr('alt');
    return image_data;
}