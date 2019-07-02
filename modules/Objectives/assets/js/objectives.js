(function ($) {
  $(function () {

  	$('#objectivesSection #addSectionItemBtn').on('click', function (e) {
  		e.stopPropagation();
  		e.preventDefault();

  		var $itemToClone = $(this).parents('#objectivesSection').find('.sort-item').last();
  		var i = parseInt($itemToClone.attr('id').substring(11)) + 1;
      var $clone = $itemToClone.clone();
  		
      var sortOrder = parseInt($itemToClone.find(`input[name='section[real][new-${i-1}][sortOrder]']`).val());
      $clone.attr('id', 'newSortItem'+i);
      $clone.find('.sort-order-value').attr('name',`section[real][new-${i}][sortOrder]`).val(sortOrder+1);
      $clone.find('.form-group').first().find('label').text(`Objective #${i+1} Title`);
      $clone.find('.form-group').first().find('input').attr('name',`section[real][new-${i}][title]`).val('').text('');
      $clone.find('.form-group').last().find('label').text(`Objective #${i+1} Title`);
      
      var $textarea = $clone.find('.form-group').last().find('textarea');
      $textarea.attr('name',`section[real][new-${i}][description]`).val('').text('').attr('id', 'ckeditor-'+i);
      $textarea.next('.cke').remove();
      $textarea.ckeditor({customConfig: '../ckeditor_custom/ckeditor_config_basic.js'});
      
      $itemToClone.after($clone);
  	});


    $('#objectivesSection', '[id^="toggleEditViewTab"] a').on('click', function (e) {
      e.preventDefault();
      $(this).tab('show');
    });

    $('#objectivesSection [name^="command[deletesectionitem]"]').on('click', function (e) {
      e.preventDefault();
      var container = $('#objectivesSection').find('#objectiveContainer' + $(this).attr('id'));
      container.css({"background-color": "#f8d7da"}).fadeTo(250, 0.1).slideUp(250, function () {
        container.detach();
      });
    });

  });
})(jQuery);