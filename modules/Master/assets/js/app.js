#require js/3.3.1/jquery-3.3.1.min.js
#require js/jquery-ui.min.js
#require js/bootstrap/4.2.1/dist/bootstrap.bundle.min.js
#require js/bootstrap/4.2.1/dist/bootstrap.min.js
#require js/ckeditor.js
#require js/jquery.are-you-sure.js
#require js/jquery.are-you-sure.ios-support.js

// #require js/sidebar.js
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


(function ($) {
  $(function () {
    $(document.body).on('click', '.disabled :input', function (e) {
      e.stopPropagation();
      e.preventDefault();
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
        if (768 < currentWidth && currentWidth < 991) {
            if (!$('#sidebar').hasClass('active')) {
                $('#sidebar').addClass('active');
            }
        } else if (currentWidth > 991) {
            if ($('#sidebar').hasClass('active')) {
                $('#sidebar').removeClass('active');
            }          
        }
    });



    /*                   *\
    |                     |
    | --==| SIDEBAR |==-- |
    |                     |
    \*                   */

    // $('#sidebarToggle').on('click', toggleSidebar);
    // $('#sidebar').on('click', function (e) {
    //   var target = $(e.target);
    //   if (target.is('div') || target.is('nav')) {
    //     e.stopPropagation();
    //     e.preventDefault();
    //     toggleSidebar();
    //   }
    // });


    // var startSize = $(window).width();
    // if (startSize < 977) {
    //   closeSidebar(true);
    //   $('#sidebarToggle').hide();
    //   // brandLogo.css({"margin-left": "-0.2em"}, 0);
    // } else {
    //   // brandLogo.css({"margin-left": "0"}, 0);
    // }

    // $( window ).resize(function() {
    //   let currentWidth = $(window).width();
    //   if (970 < currentWidth || currentWidth < 980) {
    //     if (currentWidth < 977) {
    //       closeSidebar();
    //       // brandLogo.css({"margin-left": "-0.2em"}, 0);
    //       $('#sidebarToggle').hide();
    //     }
    //     else if (!minimized || !$('#syllabusEditor').length) {
    //       openSidebar();
    //       // brandLogo.css({"margin-left": "0"}, 0);
    //     }
    //   }
    // });

    

  });


})(jQuery);

// console.log(CKEDITOR);

