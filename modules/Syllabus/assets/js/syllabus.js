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
				// $(this).submit();
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
				// $(this).submit();
			}
		}
	});

	// TODO: https://johnny.github.io/jquery-sortable/
    $('.sort-container').sortable({
        placeholder: "ui-state-highlight",
        handle: ".dragdrop-handle",
        opacity: 0.5,
        cursor: "move",
        update: function (event, ui) {
            $('.sort-order-value').each(function (index, value) {
                $(value).val(index + 1);
            });
            $('.sort-order-value-overall').each(function (index, value) {
                $(value).val(index + 1);
            });
        }
    });
    $('.sort-container').disableSelection();

    var getSemesterDisplay = function (text) {
    	switch (text) {
			case '1':
				return 'Winter'; break;
			case '3':
				return 'Spring'; break;
			case '5':
				return 'Summer'; break;
			case '7':
				return 'Fall'; break;
			default:
				return text;
    	}
    };

	var autofillCourseForm = function (data) {
		var results = [];
		var courseForm = $('#sectionForm');

		for (var id in data) {
			var value, text = data[id];

			if (id === 'id') {
				id = 'external_key';
				var extKey = $('<input type="hidden" name="section[real][external_key]">');
				courseForm.prepend(extKey);
			} else if (id === 'semester') {
				text, value = getSemesterDisplay(text);
			}

			var formEm = courseForm.find(`[name="section[real][${id}]"]`);
			if (formEm.hasClass('wysiwyg')) {
				CKEDITOR.instances[id].setData('<p>' + text + '</p>');
			} else {
				formEm.val(value).text(text).attr('value', value);	
			}
		}
	};

    var apiUrl = $('base').attr('href') + 'syllabus/courses';
    $('#courseSelectLookup').on('change', function (e) {
		$.ajax(apiUrl + '?courses=' + $(this).val(), {
			type: 'get',
			dataType: 'json',
			success: function (o) {
				switch (o.status) {
					case 'success':
						autofillCourseForm(o.data)
						break;
					case 'error':
						console.log(o.message);
						break;
					default:
						console.log('unknown error');
						break;
				}
			}
		});
    });

    



  });
})(jQuery);

// const courseSelect = function (e) {

// 	console.log('welp made it here');
// 	console.log(e);

// }




