// QuickGuide Tabs jQuery plugin
// Author: Pony Smith

// create the object
jQuery.fn.tabs = function() {
      
	// return the object for jquery chaining
	return this.each(function() {
        
        // alert if the tab group does not have an id ... needed for cookies
        if(!$(this).attr('id')) {
            alert('Tab group does not have an id');
        } else {
            var tab_group_id = $(this).attr('id');
        }
        
        var tab_selected = false;
        var cookie_options = { path:'/' };
        
		$(this).children('li').each(function() {
            $(this).addClass('inline-block');
            $(this).addClass('tab');
            // if the current tab is selected, set a flag
			if($(this).hasClass('tab-selected')) {
                tab_selected = true;
                $.cookie(tab_group_id, $(this).attr('id'), cookie_options);
            }
		});
        
        // if no tab is selected, check the cookies
        if(!tab_selected) {
            if($.cookie(tab_group_id) && $('#' + tab_group_id).length > 0) {
                if($('#' + $.cookie(tab_group_id)).length) {
                    $('#' + $.cookie(tab_group_id)).addClass('tab-selected');
                    tab_selected = true;
                }
            }
        }
        
        // if there is still no tab selected, set the first tab by default
        if(!tab_selected) {
            default_selected_href = $('#' + tab_group_id).find('li:first-child').addClass('tab-selected');
            tab_selected = true;
        }
		
        // hide any headers
        $('.tab-header').addClass('hideJS');
		
		// hide all tab content except the current one
		$(this).find('li a').each(function() {
			var href = $(this).attr('href');
			var target_id = href.substr(href.indexOf('#') + 1);
            
			if($(this).parent().hasClass('tab-selected')) {
                old_tab_id = target_id;
            } else {
                $('#' + target_id).hide();
            }
            
			// build the behavior for any link with the same target as the tab
			$('a[href='+href+']').bind('click', function(e) {
				// change the selected tab
				$('li a[href='+href+']').parents('ul').find('li.tab-selected').removeClass('tab-selected');
				$('li a[href='+href+']').parent('li').addClass('tab-selected');
				// hide the current tab content
				$('#' + old_tab_id).hide();
				// find the new tab content and show it
				var show_id = $(this).attr('href').substr(href.indexOf('#') + 1);
				$('#' + show_id).show();
				old_tab_id = show_id;
                
                // set the cookie for the tab associated with the currently shown tab
                var tab_id = $('.tab a[href$="' + href + '"]').parent().attr('id');
                $.cookie(tab_group_id, tab_id);
				
				e.preventDefault();
			});
		});        
        
    });
};
