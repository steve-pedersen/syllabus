(function ($) {
  $(function () {

  	$('#instructorsSection #addInstructorsSectionItemBtn').on('click', function (e) {
  		e.stopPropagation();
  		e.preventDefault();
      
  		var $itemToClone = $(this).parents('#instructorsSection').find('.sort-item').last();
  		var i = parseInt($itemToClone.attr('id').substring(11)) + 1;
      var isHidden = $('[id^="newSortItem"]').attr('hidden');

      if ($('[id^="toggleEditViewTab"]').length && typeof isHidden !== typeof undefined && isHidden !== false) {
        // unhide instructor container
        $('[id^="newSortItem"]').attr('hidden', false);
        $itemToClone.show();

      } else {
        // clone instructor container
        var $clone = $itemToClone.clone();
        var sortOrder = parseInt($itemToClone.find(`input[name='section[real][new-${i-1}][sortOrder]']`).val());
        $clone.attr('id', 'newSortItem'+i);
        $clone.find('.sort-order-value').attr('name',`section[real][new-${i}][sortOrder]`).val(sortOrder+1);
        $clone.find('.name').find('input').attr('name',`section[real][new-${i}][name]`).val('').text('');
        $clone.find('.email').find('input').attr('email',`section[real][new-${i}][email]`).val('').text('');
        $clone.find('.title').find('input').attr('title',`section[real][new-${i}][title]`).val('').text('');
        $clone.find('.credentials').find('input').attr('credentials',`section[real][new-${i}][credentials]`).val('').text('');
        $clone.find('.office').find('input').attr('office',`section[real][new-${i}][office]`).val('').text('');
        $clone.find('.website').find('input').attr('website',`section[real][new-${i}][website]`).val('').text('');
        $clone.find('.phone').find('input').attr('phone',`section[real][new-${i}][phone]`).val('').text('');

        var rowSize = null;
        var $textarea = $clone.find('.office-hours').find('textarea');
        if ($textarea.attr('rows'))
        {
          var rows = parseInt($textarea.attr('rows'));
          rowSize = (rows * 2) + 'em';
        }
        var config = {
          customConfig: '../ckeditor_custom/ckeditor_config_syllabus_standard.js',
          height:rowSize,
          autoGrow_minHeight: rowSize
        };
        $textarea.attr('name',`section[real][new-${i}][officeHours]`).val('').text('').attr('id', `ckeditor-${i}-office-hours`);
        $textarea.next('.cke').remove();
        $textarea.ckeditor(config);

        $textarea = $clone.find('.about').find('textarea');
        if ($textarea.attr('rows'))
        {
          var rows = parseInt($textarea.attr('rows'));
          rowSize = (rows * 2) + 'em';
        }
        $textarea.attr('name',`section[real][new-${i}][about]`).val('').text('').attr('id', `ckeditor-${i}-about`);
        $textarea.next('.cke').remove();
        $textarea.ckeditor(config);

        $itemToClone.after($clone);
      }

    });


    $('#instructorsSection', '[id^="toggleEditViewTab"] a').on('click', function (e) {
      e.preventDefault();
      $(this).tab('show');
    });

    $('#instructorsSection [name^="command[deletesectionitem]"]').on('click', function (e) {
      e.preventDefault();
      var container = $('#instructorsSection').find('#instructorContainer' + $(this).attr('id'));
      container.css({"background-color": "#f8d7da"}).fadeTo(250, 0.1).slideUp(250, function () {
        container.detach();
      });
    });

  });
})(jQuery);