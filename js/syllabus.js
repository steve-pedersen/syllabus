/**
 * When the document is ready, run the JS functions
 */

$(document).ready(function() {

	// get the basehref for use in scripts
	basehref = $('base').attr('href');

    // run init functions .. these are grouped so they can be rerun with a context after dynamic changes
    init('body');
    
});


/**
 * Init function. This groups together all the functions run at startup.  The functions are grouped so that they can easily be recalled
 * with a context after dynamic changes to the page
 * @param selector string The jquery selector string for the context
 */
function init(context) {
	prepare(context);    
    buildTabs(context);
	emailLinks(context);
	popupLinks(context);
	expandCollapse(context);
	expandCollapseAll(context);
	buildColorbox(context);
	buildCKEditor(context);
	buildSortable(context);
    checkAll(context);
    cleanup(context);
    fadeNotification();
}


/**
 * Function to be run as needed after AJAX updates
 */
function refresh(context) {
	buildSortable(context);
}


/**
 * Fade out the page notification and move off screen
 */
function fadeNotification() {
    setTimeout(function() {
        $('#notification_container.show').fadeTo(3000, .01, function() {
            $(this).removeClass('show').addClass('hide');
        });
    }, 7000);
}


/**
 * Prepare the document for use with JS
 */
function prepare(context) {
    $(context + ' .enableJS').removeClass('enableJS');
}


/**
 * remove / hide elements as necessary
 */
function cleanup(context) {
    $(context + ' .removeJS').remove();
    $(context + ' .hideJS').css({ 'position':'absolute' , 'left':'-9999px' });    
}


/**
 * Create email links from the obfuscated strings
 */
function emailLinks(context) {
    $(context + ' span.emailLink').each(function() {
        var email = $(this).text();
        email = email.replace(/\s\[at\]\s/g,'@');
        email = email.replace(/\s\[dot\]\s/g,'.');
        $(this).text(email);		
        $(this).wrap('<a href="mailto:'+email+'"></a>');
    });
}


/**
 * Build links for popup windows
 */
function popupLinks(context) {
    $(context + ' a.popup').each(function() {
        if(!$(this).hasClass('noicon')) {
            this.style.backgroundImage = "url(images/popup.png)";
            this.style.backgroundPosition = "top right";
            this.style.backgroundRepeat = "no-repeat";
            this.style.paddingRight = "14px";
            this.style.marginRight = "3px";
        }
        
        $(this).find('img').attr('alt','Link opens in a new window');
        var title = $(this).attr('title');
        $(this).attr('title',title + ' [Opens in New Window]');
        $(this).bind('click', function(e) {
            var href = $(this).attr('href');
            window.open(href,'','');			
            e.preventDefault();
        });
    });
}


/**
 * Expand / Collapse boxes
 */
function expandCollapse(context) {
    $(context + ' a.expand-source').each(function() {
        $(this).data('target_id', $(this).attr('rel')); 
        if(!$(this).hasClass('prevent-auto-collapse')) {
            $(this).data('state','collapsed');
            $('#' + $(this).data('target_id')).css('display','none');
            $(this).empty().html('&#x25B6;');
        } else {
            $(this).data('state', 'expanded');
            $(this).empty().html('&#x25BC;');
        }
        $(this).bind('click', function(e) {
            var target = $('#' + $(this).data('target_id'));
            if($(this).data('state') == 'collapsed') {
                target.slideDown(500);
                $(this).data('state','expanded');
                $(this).empty().html('&#x25BC;');
            } else {
                target.slideUp(500);
                $(this).data('state','collapsed');
                $(this).empty().html('&#x25B6;');
            }
            e.preventDefault();
        });
    });
}


/**
 * Expand / Collapse all
 */
function expandCollapseAll(context) {
    $(context + ' a.expand-collapse-all').each(function() {
        $(this).data('state', 'collapsed');
        $(this).bind('click', function(e) {
            e.preventDefault();
            if($(this).data('state') == 'expanded') {
                $(this).data('state','collapsed');
                $(this).find('span.icon').removeClass('collapse').addClass('expand').next('.text').html('Expand All');
            } else {
                $(this).data('state','expanded');
                $(this).find('span.icon').removeClass('expand').addClass('collapse').next('.text').html('Collapse All');
            }
            set_targets_to_state = $(this).data('state');
            
            var target_class = $(this).attr('rel');
            $('a.' + target_class).each(function() {
                if(set_targets_to_state == 'expanded' && $(this).data('state') == 'collapsed') {
                    $(this).trigger('click');
                }
                if(set_targets_to_state == 'collapsed' && $(this).data('state') == 'expanded') {
                    $(this).trigger('click');
                }
            });            
        });
    });
}

/**
 * Check all checkboxes
 */
function checkAll(context) {
    $(context + ' .check-all').bind('change', function(e) {
        var id = $(this).attr('id');
        if($(this).attr('checked') == false) {
            $('input[type=checkbox].' + id).attr('checked', false);
        } else {
            $('input[type=checkbox].' + id).attr('checked', true);
        }
    });
}

/**
 * Build CK Editor instances
 */
function buildCKEditor(context) {
    $(context + ' .make_ckeditor').each(function() {
        // customize the link dialog box
        CKEDITOR.on('dialogDefinition', function(ev) {
            var dialogName = ev.data.name;
            var dialogDefinition = ev.data.definition;
            if(dialogName == 'link') {
                dialogDefinition.removeContents('target');
                dialogDefinition.removeContents('advanced');
            }
        });
        var ckeditorName = $(this).attr('name');
        CKEDITOR.replace(ckeditorName, { customConfig: basehref + 'js/ckeditor/syllabus_config.js' });
    });	
}


/**
 * Wrapper function to call all necessary methods for sorting
 */
function buildSortable(context) {
    if($('.move-link').length) {
        $(context).sort();
    }
}    


/**
 * Build tab interfaces
 */
function buildTabs(context) {
    $(context + ' .tabs').tabs();
}


/**
 * Build colorbox
 */
function buildColorbox(context) {
    $(context + ' .colorbox').each(function() {
		var old_href = $(this).attr('href');
		var href = (old_href.indexOf('?', 0) != -1)
			? old_href + '&view=ajax'
			: old_href + '?view=ajax';
        $(this).attr('href', href);
        $(this).colorbox({
            maxWidth: '85%',
            maxHeight: '85%',
            opacity: 0.65,
            overlayClose: false,
            close: '',
            onComplete: function() {
                buildCKEditor('#colorbox');
                
                $('#cboxContent form').append('<div id="cboxSaveRow"></div>');
                $('#cboxSaveRow').prepend( $('#cboxContent .cancel_link') );
                $('#cboxSaveRow').prepend( $('#cboxContent input[type=submit]') );
                $('#cboxContent form .save_row').remove();
				
                $('.cancel_link').bind('click', function(e) {
                    $.colorbox.close();
                    e.preventDefault();
                });
                
                $('input[type=submit]').bind('click', function(e) {
                    ajaxFormSubmit($(this));
                    e.preventDefault();
                });
            }
        });
    });
    
    $(document).bind('cbox_cleanup', function() {
        $('#colorbox #cboxSaveRow').remove();
        $('#colorbox .make_ckeditor').each(function() {
            var instance = $(this).attr('id');
            var editor = CKEDITOR.instances[instance];
            if(editor) editor.destroy();
        });
    });
}


/**
 * Submit a form via ajax and catch the result
 */
function ajaxFormSubmit(button_object) {
    for(instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
    }
    var command = button_object.attr('name') + '=' + button_object.attr('value');
    var form_data = button_object.parents('form').serialize();
	var form_action = button_object.parents('form').attr('action');
    $.ajax({
        url: form_action,
        data: command + '&' + form_data + '&ajax_submit=1',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
			if(response.close_colorbox) {
				$.colorbox.close();
			}
			setTimeout(function() {
				ajaxPageUpdate(response);
			}, 300);
        }
    });
}


/**
 * Update the page as necessary based off the Ajax response
 * @param response {object} The Ajax response object
 */
function ajaxPageUpdate(response) {
    var update_target = $('#' + response.update_id);
    switch(response.update_method) {
        case 'replace':
            update_target.replaceWith(response.update_html);
            init('#' + response.update_id);
        break;
        
        case 'replace_contents':
            update_target.empty().html(response.update_html);
            init('#' + response.update_id);
        break;
        
        case 'remove':
            update_target.fadeOut(1000, function() {
                init_selector = $(this).parents('[id$=_sort_parent]').attr('id');
                $(this).remove();
                refresh('#' + init_selector);
            });
        break;
		
		case 'prepend':
            update_target.find('div.message.error').parents('tr').remove();
            var prepended = $(response.update_html).prependTo(update_target);
            init('#' + prepended.attr('id'));
		break;
        
        case 'append':
            update_target.find('div.message.error').parents('tr').remove();
            var appended = $(response.update_html).appendTo(update_target);
            init('#' + appended.attr('id'));
        break;
        
        case 'error':
            update_target.empty().html(response.messages);    
        break;
        
        default: break;
    }
	
	$('#page_messages').fadeTo(300, .1, function() {
		$(this).html(response.message);
		$(this).fadeTo(300, 1);
	});
    
}