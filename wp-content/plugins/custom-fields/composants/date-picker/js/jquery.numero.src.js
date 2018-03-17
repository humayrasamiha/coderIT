function send_to_editor(b){
	jQuery("div#image_uploaded").html(b);
	jQuery("input#image_uploaded_url").val( jQuery("#image_uploaded > img").attr("src") );
 	tb_remove();
}