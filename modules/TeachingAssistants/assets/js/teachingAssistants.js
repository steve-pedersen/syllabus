(function ($) {
  $(function () {

  	$('#teachingAssistantsSection #addSectionItemBtn').on('click', function (e) {
  		e.stopPropagation();
  		e.preventDefault();

  		var $itemToClone = $(this).parents('#teachingAssistantsSection').find('.sort-item').last();
  		var i = parseInt($itemToClone.attr('id').substring(11)) + 1;
      var $clone = $itemToClone.clone();
  		
      var sortOrder = parseInt($itemToClone.find(`input[name='section[real][new-${i-1}][sortOrder]']`).val());
      $clone.attr('id', 'newSortItem'+i);
      $clone.find('.sort-order-value').attr('name',`section[real][new-${i}][sortOrder]`).val(sortOrder+1);
      $clone.find('.form-group.name').find('label').text(`Name`);
      $clone.find('.form-group.name').find('input').attr('name',`section[real][new-${i}][name]`).val('').text('');
      $clone.find('.form-group.email').find('label').text(`Email`);
      $clone.find('.form-group.email').find('input').attr('name',`section[real][new-${i}][email]`).val('').text('');
      
      var rowSize = null;
      var $textarea = $clone.find('.form-group.additional-information').last().find('textarea');
      if ($textarea.attr('rows'))
      {
        var rows = parseInt($textarea.attr('rows'));
        rowSize = (rows * 2) + 'em';
      }
      var config = {
        customConfig: '../ckeditor_custom/ckeditor_config_basic.js',
        height:rowSize,
        autoGrow_minHeight: rowSize
      };
      $textarea.attr('name',`section[real][new-${i}][additionalInformation]`).val('').text('').attr('id', 'ckeditor-'+i);
      $textarea.next('.cke').remove();
      $textarea.ckeditor(config);
      
      $itemToClone.after($clone);
  	});


    $('#teachingAssistantsSection', '[id^="toggleEditViewTab"] a').on('click', function (e) {
      e.preventDefault();
      $(this).tab('show');
    });

    $('#teachingAssistantsSection [name^="command[deletesectionitem]"]').on('click', function (e) {
      e.preventDefault();
      var container = $('#teachingAssistantsSection').find('#teachingAssistantContainer' + $(this).attr('id'));
      container.css({"background-color": "#f8d7da"}).fadeTo(250, 0.1).slideUp(250, function () {
        container.detach();
      });
    });

  });
})(jQuery);