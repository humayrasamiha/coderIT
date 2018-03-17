jQuery(document).ready(function()
{

    // Registers the "Add image button"
    cf_register_editor_button('.cf-image-select' , cf_send_image_to_editor , 'media-upload.php?cf_upload_type=image&amp;tab=library&amp;post_mime_type=image&amp;type=image&amp;TB_iframe=true');

    /**
     * Custom "send_to_editor" function - adds the uploaded picture to the image widget
     * The function filters the html output to retrieve the attachment data, and adds it to the widget
     * @param   String  html    the default output
     */
    function cf_send_image_to_editor(html)
    {
        // Gets the current widget
        var current_widget = jQuery('.cf-current-widget');
        current_widget.removeClass('cf-current-widget');
        // Gets the attachment data
        var image_data = cf_get_image_data_from_html(html);
        // Fills the widget
        jQuery('.id' , current_widget).val(image_data ['id']);
        jQuery('.source' , current_widget).val(image_data ['src']);
        jQuery('.alt' , current_widget).val(image_data ['alt']);
        jQuery('.picture' , current_widget).attr('src' , image_data ['src']);
        jQuery('.full-view' , current_widget).attr('href' , image_data ['src']);
        jQuery('.link' , current_widget).val(image_data ['src']);
        // Updates the UI
        jQuery('.upload-image' , current_widget).hide();
        jQuery('.current-image' , current_widget).show();
        // Removes the media uploader and switches back to the default editor functions
        // to keep the compatibility with the default WP upload functions
        tb_remove();
        window.send_to_editor = window.default_send_to_editor;
    };

    /**
     * Registers the "Delete" button
     * The button clears the inputs and display the upload area
     */
    jQuery('.cf-image-delete').live('click' , function()
    {
        var current_widget = jQuery(this).parent().parent().parent();
        // Clears the data
        jQuery('.id' , current_widget).val('');
        jQuery('.alt' , current_widget).val('');
        jQuery('.picture' , current_widget).attr('src' , '');
        jQuery('.full-view' , current_widget).attr('href' , '');
        jQuery('.link' , current_widget).val('');
        jQuery('input[type="radio"]' , current_widget).removeAttr('checked');
        // Updates the UI
        jQuery('.current-image' , current_widget).hide();
        jQuery('.upload-image' , current_widget).show();
        return false;
    });

});