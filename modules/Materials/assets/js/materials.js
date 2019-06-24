(function ($) {
  $(function () {

  	$('#materialsSection #addMaterialsSectionItemBtn').on('click', function (e) {
  		e.stopPropagation();
  		e.preventDefault();

  		var $itemToClone = $(this).parents('#materialsSection').find('.sort-item').last();
  		var i = parseInt($itemToClone.attr('id').substring(11)) + 1;

      var $clone = $itemToClone.clone();
  		
      var sortOrder = parseInt($itemToClone.find(`input[name='section[real][new-${i-1}][sortOrder]']`).val());
      $clone.attr('id', 'newSortItem'+i);
      $clone.find('.sort-order-value').attr('name',`section[real][new-${i}][sortOrder]`).val(sortOrder+1);
      $clone.find('.form-group.title').find('label').text(`Material #${i+1} Title`);
      $clone.find('.form-group.title').find('input').attr('name',`section[real][new-${i}][title]`).val('').text('');
      // $clone.find('.form-group').last().find('label').text(`Material #${i+1} Title`);
      $clone.find('.form-group.url').find('label').text(`Material #${i+1} URL`);
      $clone.find('.form-group.url').find('input').attr('name',`section[real][new-${i}][url]`).val('').text('');
      var $yesReq = $clone.find('.form-group.required').find('.form-check').first();
      var $noReq = $clone.find('.form-group.required').find('.form-check').last();
      $yesReq.find('input').attr('name',`section[real][new-${i}][required]`).attr('checked',false).attr('id', `material${i+1}Yes`);
      $yesReq.find('label').attr('for', `material${i+1}Yes`);
      $noReq.find('input').attr('name',`section[real][new-${i}][required]`).attr('checked',true).attr('id', `material${i+1}No`);
      $noReq.find('label').attr('for', `material${i+1}No`);
      
      $itemToClone.after($clone);
  	});


    $('#materialsSection', '[id^="toggleEditViewTab"] a').on('click', function (e) {
      e.preventDefault();
      $(this).tab('show');
    });


  });
})(jQuery);