// send html to the post editor
function send_to_editor(h) {
	var datas = h.split('|');

	// Set attachment ID on hidden input
	jQuery('input[name=' + datas[0] + ']').val(datas[1]);

	// Use Ajax for load preview
	jQuery('#preview-' + datas[0]).load(mediaUploadL10n.site_url + '?action=st_preview_media', {
		'preview_id_media': datas[1],
		'field_name': datas[0]
	});

	// Close thickbox !
	tb_remove();
}

// thickbox settings
var tb_position;
(function($) {
	tb_position = function() {
		var tbWindow = $('#TB_window'),
			width = $(window).width(),
			H = $(window).height(),
			W = (720 < width) ? 720 : width;

		if (tbWindow.size()) {
			tbWindow.width(W - 50).height(H - 45);
			$('#TB_iframeContent').width(W - 50).height(H - 75);
			tbWindow.css({
				'margin-left': '-' + parseInt(((W - 50) / 2), 10) + 'px'
			});
			if (typeof document.body.style.maxWidth != 'undefined') tbWindow.css({
				'top': '20px',
				'margin-top': '0'
			});
		};

		return $('a.thickbox').each(function() {
			var href = $(this).attr('href');
			if (!href) return;
			href = href.replace(/&width=[0-9]+/g, '');
			href = href.replace(/&height=[0-9]+/g, '');
			$(this).attr('href', href + '&width=' + (W - 80) + '&height=' + (H - 85));
		});
	};

	$(window).resize(function() {
		tb_position();
	});

})(jQuery);

jQuery(document).ready(function($) {
	$('a.thickbox').click(function() {
		if (typeof tinyMCE != 'undefined' && tinyMCE.activeEditor) {
			tinyMCE.get('content').focus();
			tinyMCE.activeEditor.windowManager.bookmark = tinyMCE.activeEditor.selection.getBookmark('simple');
		}
	});
});