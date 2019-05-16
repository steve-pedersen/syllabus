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

    var $sidebar   = $(".anchor-links-sidebar > .sidebar-sticky > ul"), 
        $window    = $(window),
        offset     = $sidebar.offset(),
        topPadding = 30;
        transition = 200;
        transition = 0;

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


	// TODO: https://johnny.github.io/jquery-sortable/
    $('.sort-container').sortable({
        placeholder: "ui-state-highlight",
        handle: ".dragdrop-handle",
        opacity: 0.5,
        cursor: "move",
        update: function (event, ui) {
            $('.sort-order-value').each(function (index, value) {
                $(value).val(index+1);
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

	// let mainNavLinks = document.querySelectorAll(".anchor-links-sidebar ul li a");
	// let mainSections = document.querySelectorAll(".syllabus-editor .sort-container .sort-item");

	// let lastId;
	// let cur = [];

	// // This should probably be throttled.
	// // Especially because it triggers during smooth scrolling.
	// // https://lodash.com/docs/4.17.10#throttle
	// // You could do like...
	// // window.addEventListener("scroll", () => {
	// //    _.throttle(doThatStuff, 100);
	// // });
	// // Only not doing it here to keep this Pen dependency-free.

	// window.addEventListener("scroll", event => {
	//   let fromTop = window.scrollY;

	//   mainNavLinks.forEach(link => {
	//     let section = document.querySelector(link.hash);

	//     if (
	//       section.offsetTop <= fromTop &&
	//       section.offsetTop + section.offsetHeight > fromTop
	//     ) {
	//       link.classList.add("active");
	//     } else {
	//       link.classList.remove("active");
	//     }
	//   });
	// });

  });
})(jQuery);


