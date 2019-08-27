(function ($) {
  $(function () {

  	// auto-scroll down to section being edited
	if ($('#editUri').length) {
		var uri = $('#editUri').val();
		var target = $(`div${uri}`);
		// document.location.href=`${document.location.href}${uri}`;
		// $('html,body').animate({scrollTop: target.offset().top}, 100);	
		window.scroll( {top: target.offset().top, behavior: 'smooth'} );
	}

    if ($('#syllabusEditor').length) {
        if ($('#sidebar').hasClass('active')) {
            // $('#sidebar').removeClass('active');
        } else {
            $('#sidebar').addClass('active');
        }
    }

	$('[id^=clickToCopy]').on("focus", function(e) {
		e.target.select();
	  $(e.target).one('mouseup', function(e) {
	    e.preventDefault();
	  });
	});
	
	$('[id^=copyBtn]').on('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		const em = $(this).parents().siblings('[id^=clickToCopy]');
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(em.val()).select();
        document.execCommand("copy");
        $temp.remove();
        $(this).parents(('[id^=copiedAlert]').animate({opacity:1}, 10).animate({opacity:0}, 1000);
	});


	 $('[data-toggle="popover"]').popover();

	// enable tooltips
	$('[data-toggle="tooltip"]').tooltip();

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

	$('#anchorLinksCollapse .nav-link').on('click', function (e) {
		if ($(window).width() < 992) {
			e.preventDefault();
			var id = $(this).attr('href');
			id = id.substring(id.indexOf('#'));
			var target = $(id);
			var scrollTo = target.offset().top - 360;
			$('html,body').animate({
	          scrollTop: scrollTo
	        }, 500);
		}
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
		let resourceId = cardBody.find('#campusResourceId'+id).val();
		$('#addTitle').html(title.html());
		$('#addImage').attr('src', img.attr('src')).attr('alt', img.attr('alt'));
		$('#resourceToSyllabiBtn').attr('name','command[resourceToSyllabi]['+resourceId+']');
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

    if ($('#toggleSummaryModal').length) {
    	$('#toggleSummaryModal').click();
    }
	$('#resourceAddSummaryModal').on('hidden.bs.modal', function (e) {
		window.location.replace(window.location.href);
	});

    var $sidebar   = $(".anchor-links-sidebar > .sidebar-sticky > ul");
    var maxW = 767;
    if (!$sidebar.length) {
    	$sidebar = $(".anchor-links-sidebar-left > .sidebar-sticky > ul");
    	maxW = 991;
    }
    var $window    = $(window),
        minimize   = false,
		scrollPos = 0,
    	windowWidth = $(window).width();

    $window.resize(function() {
        windowWidth = $(window).width();

	    if (windowWidth < 992) {
	    	$('#stickyNavbar').addClass('sticky');
			var scrollPos = 0;
			$window.scroll(function () {
			    var currentScrollPos = $(this).scrollTop();
			    stickyMobileLinks(scrollPos, currentScrollPos);
			    scrollPos = currentScrollPos;
			});   	
	    }
    });

    if (windowWidth < 992) {
		$('#stickyNavbar').addClass('sticky');
		var scrollPos = 0;
		$window.scroll(function () {
		    var currentScrollPos = $(this).scrollTop();
		    stickyMobileLinks(scrollPos, currentScrollPos);
		    scrollPos = currentScrollPos;
		});  	
    }

	var stickyMobileLinks = function (scrollPos, currentScrollPos) {
		var $stickyNavbar = $('#stickyNavbar');
	    if ((scrollPos > 375) && (currentScrollPos > scrollPos)) {
	    	if (!$stickyNavbar.hasClass('minimize')) {
	    		$stickyNavbar.addClass('minimize');
	    	}
	    } else {
	    	if ($stickyNavbar.hasClass('minimize')) {
	    		$stickyNavbar.removeClass('minimize');
	    	}
	    } 		
	}

    $('#anchorLinksCollapse a').on('click', function (e) {
    	$('#stickyNavbar .navbar-toggler').click();
    	if (!$('#stickyNavbar').hasClass('minimize')) {
    		$('#stickyNavbar').addClass('minimize');
    	}
    });


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

	// // accessibility fix for smooth scrolling links
	// // Select all links with hashes
	// $('a[href*="#"]')
	//   // Remove links that don't actually link to anything
	//   .not('[href="#"]')
	//   .not('[href="#0"]')
	//   .click(function(event) {
	//     // On-page links
	//     if (
	//       location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
	//       && 
	//       location.hostname == this.hostname
	//     ) {
	//       // Figure out element to scroll to
	//       var target = $(this.hash);
	//       target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
	//       // Does a scroll target exist?
	//       if (target.length) {
	//         // Only prevent default if animation is actually gonna happen
	//         event.preventDefault();
	//         $('html, body').animate({
	//           scrollTop: target.offset().top
	//         }, 200, function() {
	//           // Callback after animation
	//           // Must change focus!
	//           var $target = $(target);
	//           $target.focus();
	//           if ($target.is(":focus")) { // Checking if the target was focused
	//             return false;
	//           } else {
	//             $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
	//             $target.focus(); // Set focus again
	//           };
	//         });
	//       }
	//     }
	//   }); 


  });
})(jQuery);


