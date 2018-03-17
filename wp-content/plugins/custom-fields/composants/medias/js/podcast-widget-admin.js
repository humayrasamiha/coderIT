jQuery(document).ready(function()
{

    // Registers the "Select image" button
    teq_widgets_register_editor_button('.teq-medias-podcast-select-picture' , teq_medias_send_podcast_image_to_editor , 'media-upload.php?teq_medias_upload_type=podcast_image&amp;tab=library&amp;post_mime_type=image&amp;type=image&amp;TB_iframe=true');

    // Registers the "Select sound file" button
    teq_widgets_register_editor_button('.teq-medias-podcast-select-sound' , teq_medias_send_podcast_sound_to_editor , 'media-upload.php?teq_medias_upload_type=podcast_sound&amp;tab=library&amp;post_mime_type=image&amp;type=image&amp;TB_iframe=true');

    /**
     * Custom "send_to_editor" function - adds the uploaded picture to the podcast widget
     * The function filters the html output to retrieve the attachment ID, and adds it to the list
     * @param   String  html    the default output
     */
    function teq_medias_send_podcast_image_to_editor(html)
    {
        // Gets the current widget
        var current_widget = jQuery('.teq-medias-current-widget');
        current_widget.removeClass('teq-medias-current-widget');
        // Gets the attachment data
        var image_data = teq_get_image_data_from_html(html);
        // Fills the widget
        jQuery('.picture-id' , current_widget).val(image_data ['id']);
        jQuery('.picture-source' , current_widget).val(image_data ['src']);
        jQuery('.picture' , current_widget).attr('src' , image_data ['src']);
        // Updates the UI
        jQuery('.upload-image' , current_widget).hide();
        jQuery('.current-image' , current_widget).show();
        // Removes the media uploader and switches back to the default editor functions
        // to keep the compatibility with the default WP upload functions
        tb_remove();
        window.send_to_editor = window.default_send_to_editor;
    };

    /*
     * "Delete picture" button
     */
    jQuery('.teq-medias-podcast-delete-picture').live('click' , function()
    {
        var current_widget = jQuery(this).parent().parent().parent();
        // Clears the data
        jQuery('.picture-id' , current_widget).val('');
        jQuery('.picture-source' , current_widget).attr('src' , '');
        // Updates the UI
        jQuery('.current-image' , current_widget).hide();
        jQuery('.upload-image' , current_widget).show();
        return false;
    });

    /**
     * Displays the right form depending on the podcast type (url / file)
     */
    jQuery('.teq-medias-podcast-type').live('change' , function()
    {
        jQuery('.podcast-type' , jQuery(this).parent().parent()).hide();
        jQuery('.podcast-type-' + jQuery(this).val() , jQuery(this).parent().parent()).show();
    });

    /**
     * Custom "send_to_editor" function - adds the uploaded sound to the podcast widget
     * The function filters the html output to retrieve the attachment ID, and adds it to the list
     * @param   String  html    the default output
     */
    function teq_medias_send_podcast_sound_to_editor(html)
    {
        
    };

});