#require js/3.3.1/jquery-3.3.1.min.js
#require js/jquery-ui.min.js
#require js/bootstrap/4.2.1/dist/popper.min.js
#require js/bootstrap/4.2.1/dist/tooltip.min.js
#require js/bootstrap/4.2.1/dist/bootstrap.min.js
#require js/ckeditor.js
// #require js/materialize.js

#require js/jquery.timepicker.min.js

#require js/sidebar.js
#require js/syllabus.js


(function ($) {
  $(function () {
    $(document.body).on('click', '.disabled :input', function (e) {
      e.stopPropagation();
      e.preventDefault();
    });

    // $('.datepicker').each(function () {
    //   var $self = $(this);
    //   $self.datepicker({
    //   });
    // });

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


    /*                   *\
    |                     |
    | --==| SIDEBAR |==-- |
    |                     |
    \*                   */

    $('#sidebarToggle').on('click', toggleSidebar);
    $('#sidebar').on('click', function (e) {
      var target = $(e.target);
      if (target.is('div') || target.is('nav')) {
        e.stopPropagation();
        e.preventDefault();
        toggleSidebar();
      }
    });

    // if sidebar minimized and user hovers over icon, open it up. terrible ux.
    // $('#sidebar a.nav-link > h6 i').mouseenter( openSidebarIfClosed );

    var startSize = $(window).width();
    if (startSize < 977) {
      closeSidebar(true);
      $('#sidebarToggle').hide();
      brandLogo.css({"margin-left": "-0.2em"}, 0);
    } else {
      brandLogo.css({"margin-left": "0"}, 0);
    }

    $( window ).resize(function() {
      let currentWidth = $(window).width();
      if (970 < currentWidth || currentWidth < 980) {
        if (currentWidth < 977) {
          closeSidebar();
          brandLogo.css({"margin-left": "-0.2em"}, 0);
          $('#sidebarToggle').hide();
        }
        else if (!minimized || !$('#syllabusEditor').length) {
          openSidebar();
          brandLogo.css({"margin-left": "0"}, 0);
        }
      }
    });



  });


})(jQuery);

// console.log(CKEDITOR);

