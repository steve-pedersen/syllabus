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
      $clone.find('.form-group.name').find('label').text(`Name`);
      $clone.find('.form-group.name').find('input').attr('name',`section[real][new-${i}][name]`).val('').text('');
      $clone.find('.form-group.value').find('label').text(`Value`);
      $clone.find('.form-group.value').find('input').attr('name',`section[real][new-${i}][value]`).val('').text('');
      
      var $textarea = $clone.find('.form-group.description').last().find('textarea');
      $textarea.attr('name',`section[real][new-${i}][description]`).val('').text('').attr('id', 'ckeditor-'+i);
      $textarea.next('.cke').remove();
      $textarea.ckeditor({customConfig: '../ckeditor_custom/ckeditor_config_basic.js'});
      
      $itemToClone.after($clone);
  	});


    $('#activitiesSection', '[id^="toggleEditViewTab"] a').on('click', function (e) {
      e.preventDefault();
      $(this).tab('show');
    });


  });
})(jQuery);