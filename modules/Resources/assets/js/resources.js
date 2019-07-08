(function ($) {
  $(function () {

  	$('#resourcesSection #addResourcesSectionItemBtn').on('click', function (e) {
  		e.stopPropagation();
  		e.preventDefault();

  		var $itemToClone = $(this).parents('#resourcesSection').find('.sort-item').last();
  		var i = parseInt($itemToClone.attr('id').substring(11)) + 1;

      var $clone = $itemToClone.clone();
  		
      var sortOrder = parseInt($itemToClone.find(`input[name='section[real][new-${i-1}][sortOrder]']`).val());
      $clone.attr('id', 'newSortItem'+i);
      $clone.find('.sort-order-value').attr('name',`section[real][new-${i}][sortOrder]`).val(sortOrder+1);
      $clone.find('.form-group.title').find('input').attr('name',`section[real][new-${i}][title]`).val('').text('');
      $clone.find('.form-group.url').find('input').attr('name',`section[real][new-${i}][url]`).val('').text('');
      $clone.find('.form-group.abbreviation').find('input').attr('name',`section[real][new-${i}][abbreviation]`).val('').text('');
      
      var rowSize = '4em'; 
      var $textarea = $clone.find('.form-group.description').find('textarea');
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
      $textarea.attr('name',`section[real][new-${i}][description]`).val('').text('').attr('id', 'ckeditor-'+i);
      $textarea.next('.cke').remove();
      $textarea.ckeditor(config);
      
      $itemToClone.after($clone);
  	});


    $('#resourcesSection', '[id^="toggleEditViewTab"] a').on('click', function (e) {
      e.preventDefault();
      $(this).tab('show');
    });

    $('#resourcesSection [name^="command[deletesectionitem]"]').on('click', function (e) {
      e.preventDefault();
      var container = $('#resourcesSection').find('#resourceContainer' + $(this).attr('id'));
      container.css({"background-color": "#f8d7da"}).fadeTo(250, 0.1).slideUp(250, function () {
        container.detach();
      });
    });

  });
})(jQuery);