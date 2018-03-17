jQuery(document).ready(function()
{

    // Registers the "Add image button"
    cf_widgets_register_editor_button('.cf-slideshow-add-picture' , cf_send_slideshow_to_editor , 'media-upload.php?cf_upload_type=slideshow&amp;tab=library&amp;post_mime_type=image&amp;type=image&amp;TB_iframe=true');

    /**
     * Custom "send_to_editor" function - adds the uploaded picture to the widget slideshow
     * The function filters the html output to retrieve the attachment ID, and adds it to the list
     * @param   String  html    the default output
     */
    function cf_send_slideshow_to_editor(html)
    {
        // Gets the current widget
        var current_widget = jQuery('.cf-current-widget');
        current_widget.removeClass('cf-current-widget');
        // Gets the attachment data
        var image_data = cf_get_image_data_from_html(html);
        // Creates the new slideshow node and fills it with the data
        var cloned_row = jQuery('.cf-slideshow-list .demo-item' , current_widget).clone();
        cloned_row.removeClass('demo-item');
        jQuery('img' , cloned_row).attr('src' , image_data ['src']);
        jQuery('.caption' , cloned_row).html(image_data ['caption']);
        jQuery('input' , cloned_row).val(image_data ['id']);
        jQuery('.cf-slideshow-list' , current_widget).append(cloned_row);
        jQuery('.slideshow-empty' , current_widget).css('display' , 'none');
        cloned_row.show();
        // Removes the media uploader and switches back to the default editor functions
        // to keep the compatibility with the default WP upload functions
        tb_remove();
        window.send_to_editor = window.default_send_to_editor;
    };

    /*
     * Removes a picture from the slideshow list
     */
    jQuery('.cf-slideshow-list a.remove-item').live('click' , function()
    {
        var current_list = jQuery(this).parent().parent().parent();
        jQuery(this).parent().parent().remove();
        if (jQuery('tr' , current_list).length < 3) // the clonable default <tr> and the "empty" message
            jQuery('.slideshow-empty' , current_list).css('display' , 'block');
        return false;
    });

});