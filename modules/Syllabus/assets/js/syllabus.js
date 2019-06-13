(function ($) {
  $(function () {


    if ($('#syllabusEditor').length) {
      closeSidebar(true);
      // $('#sidebar').on('click', toggleSidebar);
    }

	$('.section-collapsible').on('hide.bs.collapse', function () {
		var icon = $(this).parent().find('.section-collapse-link').find('small > i');
		icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
	});
	$('.section-collapsible').on('show.bs.collapse', function () {
		var icon = $(this).parent().find('.section-collapse-link').find('small > i');
		icon.addClass('fa-chevron-down').removeClass('fa-chevron-right');
	});

	$('.anchor-links-sidebar .nav-link').on('click', function (e) {
		$('.anchor-links-sidebar').find('.nav-item > .active').removeClass('active');
		$(this).addClass('active');
	});


	$('#resourcePreviewModal').on('show.bs.modal', function(e) {
		let cardBody = $(e.relatedTarget).parents('.card-body');
		let id = cardBody.attr('id');
		let title = cardBody.find('#title'+id);
		let img = cardBody.find('#image'+id);
		let url = cardBody.find('#url'+id);
		let text = cardBody.find('#text'+id);
		$('#resourceTitle').html(title.html());
		$('#resourceImage').attr('src', img.attr('src')).attr('alt', img.attr('alt'));
		$('#resourceDescription').text(text.text());
		$('#resourceUrl').attr('href', url.text()).text(url.text());
	});
	// $('#resourcePreviewModal').on('hide.bs.modal', function(e) {
	// 	$('#resourceTitle').html('');
	// 	$('#resourceImage').attr('src', '').attr('alt', '');
	// 	$('#resourceDescription').text('');
	// 	$('#resourceUrl').attr('href', '').text('');
	// });
	$('#resourceAddModal').on('show.bs.modal', function(e) {
		let cardBody = $(e.relatedTarget).parents('.card-body');
		let id = cardBody.attr('id');
		let title = cardBody.find('#title'+id);
		let img = cardBody.find('#image'+id);
		$('#addTitle').html(title.html());
		$('#addImage').attr('src', img.attr('src')).attr('alt', img.attr('alt'));
	});


    var $sidebar   = $(".anchor-links-sidebar > .sidebar-sticky > ul"), 
        $window    = $(window),
        offset     = $sidebar.offset(),
        topPadding = 30;
        transition = 200;
        transition = 0;

    if ($sidebar.length) {
	    $window.scroll(function() {
	        if ($window.scrollTop() > offset.top) {
	            $sidebar.stop().animate({
	                marginTop: $window.scrollTop() - offset.top + topPadding
	            }, transition);
	        } else {
	            $sidebar.stop().animate({
	                marginTop: 0
	            }, transition);
	        }
	    });    	
    }


    if ($('#mySyllabi').length) {
    	var $modeEm = $('#mySyllabi').find('input[name="mode"]');
    	// var mode = $modeEm.val();
    	var selector = '#myTab a#' + $modeEm.val() +'-tab';
    	$(selector).tab('show');
    }
    

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
		var courseForm = $('#courseForm');

		for (var id in data) {
			var value = data[id];
			var text = data[id];

			if (id === 'semester') {
				text = getSemesterDisplay(text);
				value = text;
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

	$("#makeReadOnly").on("click", function(){
		if($(this).is(":not(:checked)")) {
			$('#readOnlyHelpBlock').show();
		}
		else {
			$('#readOnlyHelpBlock').hide();
		}
	});


  });
})(jQuery);


