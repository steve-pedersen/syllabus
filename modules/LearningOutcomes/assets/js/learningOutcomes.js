(function ($) {
  $(function () {

  	$('#learningOutcomesSection #addSectionItemBtn').on('click', function (e) {
  		e.stopPropagation();
  		e.preventDefault();

  		var $itemToClone = $(this).parents('#learningOutcomesSection').find('.sort-item').last();
  		var i = parseInt($itemToClone.attr('id').substring(11)) + 1;
      var $clone = $itemToClone.clone();
  		// console.log(i);
      var sortOrder = parseInt($itemToClone.find(`input[name='section[real][new-${i-1}][sortOrder]']`).val());
      $clone.attr('id', 'newSortItem'+i);
      $clone.find('.sort-order-value').attr('name',`section[real][new-${i}][sortOrder]`).val(sortOrder+1);
      $clone.find('.row-label').text(`Row #${i+1}`);
     
      var rowSize = '4em'; 
      var $textarea = $clone.find('.learning-outcome-row .column1').find('textarea');
      if ($textarea.attr('rows'))
      {
        var rows = parseInt($clone.find('.learning-outcome-row .column2').find('textarea').attr('rows'));
        rowSize = (rows * 2) + 'em';
      }
      var config = {
        customConfig: '../ckeditor_custom/ckeditor_config_syllabus_standard.js',
        height:rowSize,
        autoGrow_minHeight: rowSize
      };

      $textarea.attr('name',`section[real][new-${i}][column1]`).val('').text('').attr('id', `ckeditor-${i}-1`);
      // $textarea.next('.cke').remove();
      // $textarea.ckeditor(config);
      
      $textarea = $clone.find('.learning-outcome-row .column2').find('textarea');
      $textarea.attr('name',`section[real][new-${i}][column2]`).val('').text('').attr('id', `ckeditor-${i}-2`);
      $textarea.next('.cke').remove();
      $textarea.ckeditor(config);

      $textarea = $clone.find('.learning-outcome-row .column3').find('textarea');
      $textarea.attr('name',`section[real][new-${i}][column3]`).val('').text('').attr('id', `ckeditor-${i}-3`);
      $textarea.next('.cke').remove();
      $textarea.ckeditor(config);

      $itemToClone.after($clone);
  	});


    $('#learningOutcomesSection', '[id^="toggleEditViewTab"] a').on('click', function (e) {
      e.preventDefault();
      $(this).tab('show');
    });

    $('#learningOutcomesSection [name^="command[deletesectionitem]"]').on('click', function (e) {
      e.preventDefault();
      $('#outcomesList').find('#li-' + $(this).attr('id')).detach();
      var container = $('#learningOutcomesSection').find('#learningOutcomeContainer' + $(this).attr('id'));
      container.css({"background-color": "#f8d7da"}).fadeTo(250, 0.1).slideUp(250, function () {
        container.detach();
      });
    });


    var displayFormat = 'list';

    var showTable = function () {
      displayFormat = 'table';
      $('#outcomesList').hide();
      $('#outcomesTable').show();
      $('#addSectionItemBtn').show();
    }

    var showList = function () {
      displayFormat = 'list';
      $('#outcomesList').show();
      $('#outcomesTable').hide();
      $('#addSectionItemBtn').hide();
    }

    var toggleAccordion = function () {
      showTable();
      var col2 = $('#columns2');
      var col3 = $('#columns3');
      
      if (col2.is(':checked')) {
        $('#learningOutcomesSection .collapse').each(function () {
          $(this).removeClass('show');
        })
      } else if (col3.is(':checked')) {
        $('#learningOutcomesSection .collapse').each(function () {
          $(this).addClass('show');
        })
      }
    };


    var autofillLearningOutcomes = function (data) {
      $('#outcomesLookupError').hide();
      $('#outcomesLookupSuccess').show();
      var sloForm = $('#learningOutcomesForm');
      var addRowBtn = sloForm.find('#addSectionItemBtn');
      var currentRows = $('.learning-outcome-row');
      var outcomesList = $('#outcomesList ul');
      var outcomesListItems = outcomesList.find('li');
      var sectionId = data['external_key'];
      delete data['external_key'];
      sloForm.find('#courseExternalKey').val(sectionId);

      for (let row in data) {
        if (!currentRows[row]) {
          addRowBtn.click();
        }
        let id = 'ckeditor-' + row + '-1';
        // CKEDITOR.instances[id].setData('<p>' + data[row] + '</p>');
        $('#'+id).val(data[row]);

        if (!outcomesListItems[row]) {
          let input = `<input type="hidden" name="section[real][new-${row}][column1]" value="${data[row]}">`;
          let li = $(`<li class="learning-outcome-li" id="li-${row}"></li>`).text(data[row]).append(input);
          // let li = $(`<li class="learning-outcome-li" id="li-${row}"></li>`).text(data[row]);
          outcomesList.append(li);
        } else {
          $(outcomesListItems[row]).attr('id', `li-${row}`).text(data[row]);
          // $(outcomesListItems[row]).find(`[name="section[real][${row}][column1]"]`).val(data[row]);
        }
      }
    }

    var clearLearningOutcomes = function (courseSelected = true) {
      if (courseSelected) {
        $('#outcomesLookupError').show();
        $('#outcomesLookupSuccess').hide();
      } else {
        $('#outcomesLookupError').hide();
        $('#outcomesLookupSuccess').hide();        
      }

      $('#learningOutcomesForm #courseExternalKey').val('');
      var outcomesTableCells = $('#learningOutcomesForm .learning-outcome-row');
      var outcomesListItems = $('#learningOutcomesForm #outcomesList li');

      $.each(outcomesListItems, function (i, li) {
        li.remove();
      });

      if (outcomesTableCells.length > 1) {
        for (let row in outcomesTableCells) {
          let id = 'ckeditor-' + row + '-1';
          $('#'+id).val('').text('');
          // if (CKEDITOR.instances[id]) {
          //   CKEDITOR.instances[id].setData('<p></p>');  
          // }
        }     
      } else {
        $(outcomesTableCells[0]).find('.column1 textarea').text('').val('');
        // CKEDITOR.instances['ckeditor-0-1'].setData('<p></p>');
      }
    }

    var apiFetch = function (lookup) {
      if ($(lookup).val() !== 'off') {
        var apiUrl = $('base').attr('href') + 'syllabus/outcomes?section='+ $(lookup).val();

        $.ajax(apiUrl, {
          type: 'get',
          dataType: 'json',
          success: function (o) {
            switch (o.status) {
              case 'success':
                autofillLearningOutcomes(o.data);
                break;
              case 'error':
                clearLearningOutcomes();
                break;
              default:
                console.log('unknown error');
                break;
            }
          }
        });       
      } else {
        clearLearningOutcomes(false);
      }
    }

    if ($('#columns1').is(':checked')) {
      showList();
    } else if ($('#columns2').is(':checked')) {
      showTable();
      $('#learningOutcomesSection .collapse').each(function () {
        $(this).removeClass('show');
      })
    } else if ($('#columns3').is(':checked')) {
      showTable();
      $('#learningOutcomesSection .collapse').each(function () {
        $(this).addClass('show');
      })
    }

    $('#columns1').on('click', function (e) {
      showList();
    });
    $('#columnAccordion').on('show.bs.collapse', function (e) {
      e.preventDefault();
      e.stopPropagation();
      toggleAccordion();
    });
    $('#columnAccordion').on('hide.bs.collapse', function (e) {
      e.preventDefault();
      e.stopPropagation();
      toggleAccordion();
    });

    if ($('#courseSelectLookup').val() !== 'off') {
      if ($('#courseInfoDefault').val() === 'true') {
        apiFetch($('#courseSelectLookup'));
      }
    }

    $('#courseSelectLookup').on('change', function (e) {
      apiFetch(this);
    });

    $('#refreshSLOs').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      apiFetch($('#courseSelectLookup'));
      $('#updatedMessage').show(10).hide(3000);
    });

  });
})(jQuery);