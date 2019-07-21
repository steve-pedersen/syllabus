(function ($) {
  $(function () {

  	$('#activitiesSection #addSectionItemBtn').on('click', function (e) {
  		e.stopPropagation();
  		e.preventDefault();

  		var $itemToClone = $(this).parents('#activitiesSection').find('.sort-item').last();
  		var i = parseInt($itemToClone.attr('id').substring(11)) + 1;
      var $clone = $itemToClone.clone();
  		
      var sortOrder = parseInt($itemToClone.find(`input[name='section[real][new-${i-1}][sortOrder]']`).val());
      $clone.attr('id', 'newSortItem'+i);
      $clone.find('.sort-order-value').attr('name',`section[real][new-${i}][sortOrder]`).val(sortOrder+1);
      $clone.find('.form-group.name').find('input').attr('name',`section[real][new-${i}][name]`).val('').text('');
      $clone.find('.form-group.value').find('input').attr('name',`section[real][new-${i}][value]`).val('').text('');
      
      var rowSize = '4em'; 
      var $textarea = $clone.find('.form-group.description').last().find('textarea');
      if ($textarea.attr('rows'))
      {
        var rows = parseInt($textarea.attr('rows'));
        rowSize = (rows * 2) + 'em';
      }
      var config = {
        customConfig: '../ckeditor_custom/ckeditor_config_full.js',
        height:rowSize,
        autoGrow_minHeight: rowSize
      };
      $textarea.attr('name',`section[real][new-${i}][description]`).val('').text('').attr('id', 'ckeditor-'+i);
      $textarea.next('.cke').remove();
      $textarea.ckeditor(config);
      
      $itemToClone.after($clone);
  	});


    $('#activitiesSection', '[id^="toggleEditViewTab"] a').on('click', function (e) {
      e.preventDefault();
      $(this).tab('show');
    });

    $('#activitiesSection [name^="command[deletesectionitem]"]').on('click', function (e) {
      e.preventDefault();
      var container = $('#activitiesSection').find('#activityContainer' + $(this).attr('id'));
      container.css({"background-color": "#f8d7da"}).fadeTo(250, 0.1).slideUp(250, function () {
        container.detach();
      });
    });

  });
})(jQuery);