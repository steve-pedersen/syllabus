#require js/3.3.1/jquery-3.3.1.min.js
#require js/jquery-ui.min.js

#require js/bootstrap/4.1.1/dist/popper.min.js
#require js/bootstrap/4.1.1/dist/tooltip.min.js
#require js/bootstrap/4.1.1/dist/bootstrap.min.js

// #require js/quill.js
// #require vendor/ckeditor/ckeditor/ckeditor.js
#require js/ckeditor.js

#require js/materialize.js


(function ($) {
    $(function () {
      $(document.body).on('click', '.disabled :input', function (e) {
        e.stopPropagation();
        e.preventDefault();
      });

      $('.datepicker').each(function () {
        var $self = $(this);
        $self.datepicker({
        });
      });



    });
})(jQuery);

// console.log(CKEDITOR);



