(function ($) {
  $(function () {
    var autoCompleteUrl = $('base').attr('href') + 'syllabus/autocomplete';

    var transformAccounts = function (data) {
      var results = [];
      // console.log(data);
      for (var id in data) {
        var info = data[id];
        results.push({
          value: id,
          username: info.username,
          label: info.firstName + ' ' + info.lastName + ' (' + info.username + ')' 
        });
      }

      return results;
    };

    $('.syllabus-account-autocomplete').autocomplete({
      delay: 200,
      minLength: 3,
      appendTo: ".search-container",
      source: function (request, response) {
        var term = request.term;
        // console.log(autoCompleteUrl + '?s=' + term);
        if (term.length > 2) {
          $.ajax(`${autoCompleteUrl}?s=${term}`, {
            type: 'get',
            dataType: 'json',
            success: function (o) {
              switch (o.status) {
                case 'success':
                  // console.log('success!', o.data);
                  response(transformAccounts(o.data));
                  break;
                case 'error':
                  //console.log(o.message);
                  break;
                default:
                  //console.log('unknown error');
                  break;
              }
            }
          });
        }
      },
      select: function (event, ui) {
        event.stopPropagation();
        event.preventDefault();
        var item = ui.item;
        var $self = $(this);
        var shadowId = this.id + '-shadow';
        var $shadow = $('#' + shadowId);

        if ($shadow.length === 0) {
          $shadow = $('<input type="hidden" name="adhocUsers[]">');
          $shadow.attr('id', shadowId);
          $('#addEditorContainer').prepend($shadow);
        }

        $shadow.attr('value', item.value); // search by username
        this.value = item.label;
      }
    });

  });
}(jQuery));
