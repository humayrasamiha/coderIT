jQuery(document).ready(function() {
	initSortableTable();
});

function initSortableTable() {
	jQuery('#table-custom-fields').tableDnD({
		dragHandle: "dragHandle"
	});

	jQuery("#table-custom-fields tr").hover(function() {
		jQuery(this.cells[0]).addClass('showDragHandle');
	},
	function() {
		jQuery(this.cells[0]).removeClass('showDragHandle');
	});
}