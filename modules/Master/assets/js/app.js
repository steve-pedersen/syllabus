
#require js/3.3.1/jquery-3.3.1.min.js
#require js/jquery-migrate-3.0.0.min.js
#require js/jquery-ui.min.js
#require js/bootstrap/4.2.1/dist/bootstrap.bundle.min.js
// #require js/bootstrap/4.2.1/dist/bootstrap.min.js
#require js/ckeditor.js
#require js/jquery.are-you-sure.js
#require js/jquery.are-you-sure.ios-support.js
#require js/autocomplete.js
#require js/bootstrap4-toggle.js
#require js/jquery.fileupload.js

#require js/drag-drop-upload.js
#require js/syllabus.js
#require js/courses.js
#require js/objectives.js
#require js/policies.js
#require js/materials.js
#require js/instructors.js
#require js/teachingAssistants.js
#require js/activities.js
#require js/grades.js
#require js/resources.js
#require js/schedules.js
#require js/learningOutcomes.js
#require js/organizations.js



(function ($) {
  $(function () {
    $(document.body).on('click', '.disabled :input', function (e) {
      e.stopPropagation();
      e.preventDefault();
    });

    $('[data-src]').each(function () {
        var url = $(this).attr('data-src');
        $.ajax(url, {
            type: 'get',
            dataType: 'json',
            success: function (o) {
                switch (o.status) {
                    case 'success':
                        $(`[id^='syllabus-${o.syllabusId}']`).attr('src', o.imageSrc);
                        break;
                    case 'error':
                    default:
                        //console.log('unknown error');
                        break;
                }
            }
        });
    });

    if ($('#printContainer').length) {
        $('.print-button').click();
    }

    var pad = function (num, size) {
        var s = num+"";
        while (s.length < size) s = "0" + s;
        return s;
    }

    // TODO: https://johnny.github.io/jquery-sortable/
    var ckeConfigs = [];
    $('.sort-container').sortable({
        placeholder: "ui-state-highlight",
        handle: ".dragdrop-handle",
        opacity: 0.5,
        cursor: "move",
        items: ".sort-item",
        start:function (event,ui) {
            // save each ckeditor config, create ckeditor html's clone
            // destroy ckeditor, hide the textarea and insert the clone to create an illusion that cke is still there
            $('textarea', ui.item).each(function(i){
                if (!$(this).attr('id')) {
                    $(this).attr('id', `ckeditor-${i}-${$(this.attr('name'))}`);   
                }
                var tagId = $(this).attr('id');
                var ckeClone = $(this).next('.wysiwyg').clone().addClass('cloned');
                ckeConfigs[tagId] = CKEDITOR.instances[tagId].config;
                CKEDITOR.instances[tagId].destroy();
                $(this).hide().after(ckeClone);
            });
        },
        stop: function(event, ui) {
            // for each textarea init ckeditor anew and remove the clone
            $('textarea', ui.item).each(function(){
                var tagId = $(this).attr('id');
                CKEDITOR.replace(tagId, ckeConfigs[tagId]);
                $(this).next('.cloned').remove();
            });
        },
        update: function (event, ui) {
            // update sort order
            $('.sort-order-value').each(function (index, value) {
                var sortOrder = pad(index+1, 3);
                $(value).val(sortOrder);
            });
            
            // auto-save the syllabus after sortable finishes
            $("<input />").attr("type", "hidden")
                .attr("name", "sortOrderUpdate")
                .attr("value", "true")
                .appendTo("#viewSections");
            $('#globalSave').click();
        }
    });
    // $('.sort-container').disableSelection();

    var saveSortOrder = function () {
        $("#viewSections").submit(function(e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data) {
                    // add a flash notice or something

                }
            });

        });
    };

    // DatePicker 
    $.datepicker.setDefaults(
      $.extend( $.datepicker.regional[ '' ] )
    );
    $('.datepicker').each(function () {
      var $self = $(this);
      $self.datepicker({
        formatDate: 'yy-mm-dd'
      });
    });
    $('.timepicker').each(function () {
      var $self = $(this);
      $self.timepicker({
        timeFormat: 'h:mm p',
        interval: 15,
        minTime: '8',
        maxTime: '6:00pm',
        dynamic: false,
        dropdown: true,
        scrollbar: true
      });
    });


    // var message = 'You have unsaved changes. Are you sure you want to leave the page?';
    // $('#viewSections a#viewFromEditor').on('click', function(e) {
    //   if ($('#viewSections').hasClass('dirty')) {
    //     if (!window.confirm(message)) { 
    //       e.preventDefault();
    //     }
    //   }
    // });
    // $('#viewSections').areYouSure( {
    //   'message': message
    // } );
    // $('#viewSections #addSectionItemBtn').on('click', function (e) {
    //   $('#viewSections').trigger('rescan.areYouSure');
    //   $('#viewSections').trigger('checkform.areYouSure');
    // });
    // $('#viewSections').on('submit')

    // TODO: ADD SOMETHING LIKE THIS IN. WONT CATCH FORMAT ONLY CHANGE (NO KEY EVENT)
    // if ( CKEDITOR.instances.FOREACHINSTANCESasINSTANCE.checkDirty() ) alert("Content changed");
    // https://github.com/codedance/jquery.AreYouSure/pull/130/files
    // $('#viewSections .wysiwyg').ckeditorGet().on('key', function(e) {
    //     var keyCode = e.data.keyCode; // if you need to track the key
    //     isModified = true;
    // });

    $('#mainSidebarCollapse').on('click', function (e) {
        if ($('#sidebar').hasClass('active')) {
            $('#sidebar').removeClass('active');
        } else {
            $('#sidebar').addClass('active');
        }
    });

    var startSize = $(window).width();
    if (768 < startSize && startSize < 991) {
        if ($('#sidebar').hasClass('active')) {
            $('#sidebar').removeClass('active');
        } else {
            $('#sidebar').addClass('active');
        }
    }

    $( window ).resize(function() {
        let currentWidth = $(window).width();
        if (!$('#syllabusEditor').length) {
            if (768 < currentWidth && currentWidth < 991) {
                if (!$('#sidebar').hasClass('active')) {
                    $('#sidebar').addClass('active');
                }
            } else if (currentWidth > 991) {
                if ($('#sidebar').hasClass('active')) {
                    $('#sidebar').removeClass('active');
                }          
            }            
        }
    });
 

  });


})(jQuery);
