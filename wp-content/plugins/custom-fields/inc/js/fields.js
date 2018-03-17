var wpfields;
(function($) {
	wpfields = {
		init: function() {
			var rem, sidebars = $('div.fields-sortables'), the_id;

                        $('a.edit-sidebar-link').click(function(){
                            $(this).parent().parent().find('.sidebar-name .rename').slideToggle("fast");
                            return false;
                        });
			$('#fields-left .add_sidebar input[type=submit]').click(function() {
				wpfields.addSidebar();
				return false;
			});
                        $('#fields-left .rename input[type=submit]').click(function() {
				wpfields.renameSidebar($(this));
				return false;
			});
            
			$('#fields-left').children('.fields-holder-wrap').children('.sidebar-name').children('.sidebar-name-arrow').click(function() {
				var c = $(this).parent().siblings('.fields-sortables'),
					p = $(this).parent().parent();
				if (!p.hasClass('closed')) {
					c.sortable('disable');
					p.addClass('closed');
				} else {
					p.removeClass('closed');
					c.sortable('enable').sortable('refresh');
				}
			});

			$('#fields-left a.remove-sidebar-link').click(function() {
				wpfields.delSidebar($(this));
				return false;
			});
            
			$('#fields-left').children('.fields-holder-wrap').children('.sidebar-name').children('.sidebar-name-arrow').click(function() {
				$(this).parent().siblings('.field-holder').parent().toggleClass('closed');
			});

			sidebars.not('#wp_inactive_fields').each(function() {
				var h = 50,
					H = $(this).children('.field').length;
				h = h + parseInt(H * 48, 10);
				$(this).css('minHeight', h + 'px');
			});

			$('a.field-action').live('click', function() {
				var css = {},
					field = $(this).closest('div.field'),
					inside = field.children('.field-inside'),
					w = parseInt(field.find('input.field-width').val(), 10);

				if (inside.is(':hidden')) {
					if (w > 250 && inside.closest('div.fields-sortables').length) {
						css['width'] = w + 30 + 'px';
						if (inside.closest('div.field-liquid-right').length) css['marginLeft'] = 235 - w + 'px';
						field.css(css);
					}
					wpfields.fixLabels(field);
					inside.slideDown('fast');
				} else {
					inside.slideUp('fast', function() {
						field.css({
							'width': '',
							'marginLeft': ''
						});
					});
				}
				return false;
			});

			$('input.field-control-save').live('click', function() {
				wpfields.save($(this).closest('div.field'), 0, 1, 0);
				return false;
			});

			$('a.field-control-remove').live('click', function() {
				wpfields.save($(this).closest('div.field'), 1, 1, 0);
				return false;
			});

			$('a.field-control-close').live('click', function() {
				wpfields.close($(this).closest('div.field'));
				return false;
			});

			sidebars.children('.field').each(function() {
				wpfields.appendTitle(this);
				if ($('p.field-error', this).length) $('a.field-action', this).click();
			});

			$('#field-list').children('.field').draggable({
				connectToSortable: 'div.fields-sortables',
				handle: '> .field-top > .field-title',
				distance: 2,
				helper: 'clone',
				zIndex: 5,
				containment: 'document',
				start: function(e, ui) {
					wpfields.fixWebkit(1);
					ui.helper.find('div.field-description').hide();
					the_id = this.id;
				},
				stop: function(e, ui) {
					if (rem) $(rem).hide();
					rem = '';
					wpfields.fixWebkit();
				}
			});
			
			sidebars.sortable({
                placeholder: 'field-placeholder',
				items: '> .field',
				handle: '> .field-top > .field-title',
				cursor: 'move',
				distance: 2,
				containment: 'document',
				start: function(e, ui) {
					wpfields.fixWebkit(1);
					ui.item.children('.field-inside').hide();
					ui.item.css({
						'marginLeft': '',
						'width': ''
					});
				},
				stop: function(e, ui) {
					if (ui.item.hasClass('ui-draggable')) ui.item.draggable('destroy');

					if (ui.item.hasClass('deleting')) {
						wpfields.save(ui.item, 1, 0, 1); // delete field
						ui.item.remove();
						return;
					}

					var add = ui.item.find('input.add_new').val(),
						n = ui.item.find('input.multi_number').val(),
						id = the_id,
						sb = $(this).attr('id');
						
					ui.item.css({
						'marginLeft': '',
						'width': ''
					});
					wpfields.fixWebkit();
					the_id = '';
					
					if (add) {
						if ('multi' == add) {
							ui.item.html(ui.item.html().replace(/<[^<>]+>/g, function(m) {
								return m.replace(/__i__|%i%/g, n);
							}));
							ui.item.attr('id', id.replace(/__i__|%i%/g, n));
							n++;
							$('div#' + id).find('input.multi_number').val(n);
						} else if ('single' == add) {
							ui.item.attr('id', 'new-' + id);
							rem = 'div#' + id;
						}
						wpfields.save(ui.item, 0, 0, 1);
						ui.item.find('input.add_new').val('');
						ui.item.find('a.field-action').click();
						return;
					}
					wpfields.saveOrder(sb);
                                        

				},
				receive: function(e, ui) {
					if (!$(this).is(':visible')) $(this).sortable('cancel');
				}
			}).sortable('option', 'connectWith', 'div.fields-sortables').parent().filter('.closed').children('.fields-sortables').sortable('disable');

			$('#available-fields').droppable({
				tolerance: 'pointer',
				accept: function(o) {
					return $(o).parent().attr('id') != 'field-list';
				},
				drop: function(e, ui) {
					ui.draggable.addClass('deleting');
					$('#removing-field').hide().children('span').html('');
				},
				over: function(e, ui) {
					ui.draggable.addClass('deleting');
					$('div.field-placeholder').hide();

					if (ui.draggable.hasClass('ui-sortable-helper')) $('#removing-field').show().children('span').html(ui.draggable.find('div.field-title').children('h4').html());
				},
				out: function(e, ui) {
					ui.draggable.removeClass('deleting');
					$('div.field-placeholder').show();
					$('#removing-field').hide().children('span').html('');
				}
			});
		},

		saveOrder: function(sb) {
			if (sb) $('#' + sb).closest('div.fields-holder-wrap').find('img.ajax-feedback').css('visibility', 'visible');

			var a = {
				action: 'fields-order-' + $('.post_type').val(),
				savefields: $('#_wpnonce_fields').val(),
				sidebars: [],
				post_type: $('.post_type').val()
			};

			$('div.fields-sortables').each(function() {
				a['sidebars[' + $(this).attr('id') + ']'] = $(this).sortable('toArray').join(',');
			});

			$.post(ajaxurl, a, function() {
				$('img.ajax-feedback').css('visibility', 'hidden');
			});

			this.resize();
		},

		save: function(field, del, animate, order) {
			var sb = field.closest('div.fields-sortables').attr('id'),
				data = field.find('form').serialize(),
				a;
			field = $(field);
			$('.ajax-feedback', field).css('visibility', 'visible');

			a = {
				action: 'save-field-' + $('.post_type').val(),
				savefields: $('#_wpnonce_fields').val(),
				sidebar: sb,
				post_type: $('.post_type').val()
			};

			if (del) a['delete_field'] = 1;

			data += '&' + $.param(a);

			$.post(ajaxurl, data, function(r) {
				var id;

				if (del) {
					if (!$('input.field_number', field).val()) {
						id = $('input.field-id', field).val();
						$('#available-fields').find('input.field-id').each(function() {
							if ($(this).val() == id) $(this).closest('div.field').show();
						});
					}

					if (animate) {
						order = 0;
						field.slideUp('fast', function() {
							$(this).remove();
							wpfields.saveOrder();
						});
					} else {
						field.remove();
						wpfields.resize();
					}
				} else {
					$('.ajax-feedback').css('visibility', 'hidden');
					if (r && r.length > 2) {
						$('div.field-content', field).html(r);
						wpfields.appendTitle(field);
						wpfields.fixLabels(field);
					}
				}
				if (order) wpfields.saveOrder();
			});
		},

		addSidebar: function() {
			var a = {
				action: 'add-sidebar-' + $('.post_type').val(),
				savefields: $('#_wpnonce_fields').val(),
				sidebar: $('#fields-left .add_sidebar input[type=text]').val(),
				post_type: $('.post_type').val()
			};

			$.post(ajaxurl, a, function() {
				location.reload();
			});

			return false;
		},

		delSidebar: function(sb) {
			var a = {
				action: 'del-sidebar-' + $('.post_type').val(),
				savefields: $('#_wpnonce_fields').val(),
				sidebar: sb.parent().children('input[type=hidden]').val(),
				post_type: $('.post_type').val()
			};

			$.post(ajaxurl, a, function() {
				location.reload();
			});
		},

                renameSidebar: function(sb) {
                        var a = {
                                action: 'rename-sidebar-' + $('.post_type').val(),
				savefields: $('#_wpnonce_fields').val(),
				sidebar: sb.parent().children('input[type=hidden]').val(),
                                rename: sb.parent().children('input[type=text]').val(),
				post_type: $('.post_type').val()
                        };

                        $.post(ajaxurl, a, function() {
				location.reload();
			});
                },
		appendTitle: function(field) {
			var title = $('input[id*="-title"]', field);
			if (title = title.val()) {
				title = title.replace(/<[^<>]+>/g, '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
				$(field).children('.field-top').children('.field-title').children().children('.in-field-title').html(': ' + title);
			}
		},

		resize: function() {
			$('div.fields-sortables').not('#wp_inactive_fields').each(function() {
				var h = 50,
					H = $(this).children('.field').length;
				h = h + parseInt(H * 48, 10);
				$(this).css('minHeight', h + 'px');
			});
		},

		fixWebkit: function(n) {
			n = n ? 'none' : '';
			$('body').css({
				WebkitUserSelect: n,
				KhtmlUserSelect: n
			});
		},

		fixLabels: function(field) {
			field.children('.field-inside').find('label').each(function() {
				var f = $(this).attr('for');
				if (f && f == $('input', this).attr('id')) $(this).removeAttr('for');
			});
		},

		close: function(field) {
			field.children('.field-inside').slideUp('fast', function() {
				field.css({
					'width': '',
					'marginLeft': ''
				});
			});
		}
	};

	$(document).ready(function($) {
		wpfields.init();
	});

})(jQuery);