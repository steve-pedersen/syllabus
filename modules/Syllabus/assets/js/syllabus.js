(function ($) {
  $(function () {

  	// auto-scroll down to section being edited
	if ($('#editUri').length) {
		var uri = $('#editUri').val();
		var target = $(`div${uri}`);
		$('html,body').animate({scrollTop: target.offset().top}, 200);		
	}

    if ($('#syllabusEditor').length) {
      // closeSidebar(true);
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


	$('.collapse-all').on('click', function (e) {
		e.preventDefault();
		if ($(this).hasClass('collapsed')) {
			$(this).removeClass('collapsed').addClass('expanded');
		} else {
			$(this).removeClass('expanded').addClass('collapsed');
		}
		$('.section-collapse-link').click();
		// $('.multi-collapse').toggle({
		// 	duration: 300
		// });
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
	$('#resourceAddModal').on('show.bs.modal', function(e) {
		let cardBody = $(e.relatedTarget).parents('.card-body');
		let id = cardBody.attr('id');
		let title = cardBody.find('#title'+id);
		let img = cardBody.find('#image'+id);
		$('#addTitle').html(title.html());
		$('#addImage').attr('src', img.attr('src')).attr('alt', img.attr('alt'));
	});
	$('#resourceAddModal').on('hide.bs.modal', function(e) {
		$(this).find('[id^=overlayCheck]:checked').each(function(i, em) {
			$(em).click();
		});
	});
    $('[id^=overlayCheck]').on('change', function (e) {
		var id = $(this).attr('data-index');
		if (this.checked == true) {
			$('#checkIcon'+id).show();
		} else {
			$('#checkIcon'+id).hide();
		}
    });
    var templateId = $('#templateId input[name="template"]');
    if (templateId.length)
    {
    	if (templateId.val() == 1)
    	{
    		var id = templateId.attr('data-index');
    		if ($('[id^=overlayCheck]').checked == true) {
    			$('#checkIcon'+id).show();	
    		}
    	}
    }


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


