(function ($) {
  $(function () {

  	$('.real-section-editor #addSectionItemBtn').on('click', function (e) {
  		e.stopPropagation();
  		e.preventDefault();

  		var $itemToClone = $(this).parents('#realSectionSortContainer').find('.sort-item').last();
  		var i = parseInt($itemToClone.attr('id').substring(11)) + 1;
      var $clone = $itemToClone.clone();
  		
      $clone.attr('id', 'newSortItem'+i);
      $clone.find('.sort-order-value').attr('name',`section[real][new-${i}][sortOrder]`).val(i);
      $clone.find('.form-group').first().find('label').text(`Objective #${i+1} Title`);
      $clone.find('.form-group').first().find('input').attr('name',`section[real][new-${i}][title]`).val('').text('');
      $clone.find('.form-group').last().find('label').text(`Objective #${i+1} Title`);
      
      var $textarea = $clone.find('.form-group').last().find('textarea');
      $textarea.attr('name',`section[real][new-${i}][description]`).val('').text('').attr('id', 'ckeditor-'+i);
      $textarea.next('.cke').remove();
      $textarea.ckeditor({customConfig: '../ckeditor_custom/ckeditor_config_basic.js'});
      
      $itemToClone.after($clone);
  	});


    $('#realSectionSortContainer', '[id^="toggleEditViewTab"] a').on('click', function (e) {
      e.preventDefault();
      $(this).tab('show');
    });


  });
})(jQuery);