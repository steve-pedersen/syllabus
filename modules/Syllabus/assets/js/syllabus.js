(function ($) {
  $(function () {

    if ($('#syllabusEditor').length) {
      closeSidebar(true);
      $('#sidebar').mouseenter( openSidebar ).mouseleave( closeSidebar );
    }

	$('.section-collapsible').on('hide.bs.collapse', function () {
		var icon = $(this).parent().find('.section-collapse-link').find('small > i');
		icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
	});
	$('.section-collapsible').on('show.bs.collapse', function () {
		var icon = $(this).parent().find('.section-collapse-link').find('small > i');
		icon.addClass('fa-chevron-down').removeClass('fa-chevron-right');
	});


	$('#editorMainControlsTop input').on('click', function (e) {
		if ($( '#sectionForm' ).length) {
			// save section
			if ($(this).attr("value") === "Save Syllabus") {
				e.preventDefault();
				e.stopPropagation();
				$( '#sectionForm' ).find('[name="command[savesection]"]').click();
			}
		}
	});

	$('#editorMainControlsBottom input').on('click', function (e) {
		if ($( '#sectionForm' ).length) {
			// save section
			if ($(this).attr("value") === "Save Syllabus") {
				e.preventDefault();
				e.stopPropagation();
				$( '#sectionForm' ).find('[name="command[savesection]"]').click();
			}
		}
	});

  });
})(jQuery);