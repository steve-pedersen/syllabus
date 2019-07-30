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
			// console.log(id, scrollTo);
			// window.scroll( {top: scrollTo, behavior: 'smooth'} );
			// $('body').css('height', '100%');
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
        offset     = $sidebar.offset(),
        topPadding = 20,
    	transition = 0,
        minimize   = false,
        maxY = $('#footer').offset().top;


    var windowWidth = $(window).width();
    $window.resize(function() {
        windowWidth = $(window).width();
	    if ($sidebar.length && (windowWidth > maxW)) {
		    $window.scroll(function() {
		    	maxY = $('#footer').offset().top;
		        if ($window.scrollTop() > offset.top && (windowWidth > maxW) && $window.scrollTop() < maxY) {
		        	// console.log('1');
		            $sidebar.stop().animate({
		                marginTop: $window.scrollTop() - offset.top + topPadding
		            }, transition);
		        } else {
		        	// console.log('2');
		            $sidebar.stop().animate({
		                marginTop: 0
		            }, 10);
		        }
		    });    	
	    } else if (windowWidth < 992) {
			var $stickyNavbar = $('#stickyNavbar');
  
		    $window.scroll(function() {
		    	maxY = $('#footer').offset().top;
		    	if ($window.scrollTop() > offset.top) {
		    		// console.log('3');
		    		if (!$stickyNavbar.hasClass('sticky')) {
		    			$stickyNavbar.addClass('sticky');
		    		}
		    	} else {
		    		// console.log('4');
		    		if ($stickyNavbar.hasClass('sticky')) {
		    			$stickyNavbar.removeClass('sticky');
		    		}
		        }
		    });    	
	    }
    });

    if ($sidebar.length && (windowWidth > maxW)) {
	    $window.scroll(function() {
	    	maxY = $('#footer').offset().top;
	        if ($window.scrollTop() > offset.top && (windowWidth > maxW) && $window.scrollTop() < maxY) {
	        	// console.log('5');
	            $sidebar.stop().animate({
	                marginTop: $window.scrollTop() - offset.top + topPadding
	            }, transition);
	        } else {
	        	// console.log('6');
	            $sidebar.stop().animate({
	                marginTop: 0
	            }, 0);
	        }
	    });    	
    } else if (windowWidth < 992) {
		var $stickyNavbar = $('#stickyNavbar');
    	// var offset     = $stickyNavbar.offset();

	    $window.scroll(function() {
	    	maxY = $('#footer').offset().top;
	    	if ($window.scrollTop() > offset.top) {
	    		// console.log('7');
	    		if (!$stickyNavbar.hasClass('sticky')) {
	    			$stickyNavbar.addClass('sticky');
	    		}		
	    	} else {
	    		// console.log('8');
	    		if ($stickyNavbar.hasClass('sticky')) {
	    			$stickyNavbar.removeClass('sticky');
	    		}
	        }
	    });    	
    }

    $('#anchorLinksCollapse a').on('click', function (e) {
    	$('#stickyNavbar .navbar-toggler').click();
    	if (!$('#stickyNavbar').hasClass('minimize')) {
    		$('#stickyNavbar').addClass('minimize');
    	}
    });


	var iScrollPos = 0;
	$window.scroll(function () {
	    var iCurScrollPos = $(this).scrollTop();
	    if ((iScrollPos > 375) && (iCurScrollPos > iScrollPos)) {
	    	if (!$('#stickyNavbar').hasClass('minimize')) {
	    		$('#stickyNavbar').addClass('minimize');
	    	}
	    } else {
	    	if ($('#stickyNavbar').hasClass('minimize')) {
	    		$('#stickyNavbar').removeClass('minimize');
	    	}
	    }
	    iScrollPos = iCurScrollPos;
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


