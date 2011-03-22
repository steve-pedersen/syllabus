// form group class

jQuery.fn.sort = function() {
	
    // for jQuery, we must return the result
    return this.each(function() {
    
        buildMoveLinks('[id$=_sort_parent]');
        bindMoveLinks();
    
        /**
         * Build the move up / down links (recalled on move complete to modify classes of links as necessary)
         */
        function buildMoveLinks(q) {
            $(q).each(function() {
                var controls_class = $(this).attr('id').replace('_parent', '_controls');
                $('.' + controls_class + ' a.move-link').removeClass('disabled').data('is-first', false).data('is-last', false);
                $('.' + controls_class + ' a.move-link.move-up').slice(0,1).addClass('disabled').data('is-first', true);
                $('.' + controls_class + ' a.move-link.move-down').slice(-1).addClass('disabled').data('is-last', true);
                updateOrder('.' + controls_class);
            });
        }
        
        /**
         * Bind the move links
         */
        function bindMoveLinks() {
            $('.move-link').die().live('click', function(e) {
                e.preventDefault();
                if(!$(this).hasClass('disabled')) {
                    var parent_id = $(this).attr('rel');
                    var item_class = parent_id.replace('_parent', '_item');
                    var controls_class = parent_id.replace('_parent', '_controls');
                    var item = $(this).parents('.' + item_class);
                    var direction = ($(this).hasClass('move-up')) ? 'up' : 'down';
                    item.fadeTo(500, .1, function() {
                        var target = (direction == 'up')
                            ? $(this).insertBefore(item.prev())
                            : $(this).insertAfter(item.next());
                        $(this).fadeTo(500, 1, function() {
                            buildMoveLinks('#' + parent_id);
                            order_string = updateOrder('.' + controls_class);
                            save(order_string);
                        });
                    });
                } else {
                    if($(this).data('is-first') == true) {
                        alert('This item cannot be moved up further');
                    }
                    if($(this).data('is-last') == true) {
                        alert('This item cannot be moved down further');
                    }
                }
            });
        }
        
        /**
         * Update the order fields
         */
        function updateOrder(q) {
            var inc = 1;
			var order_string = '';
            // var order_string = '&';
            $(q).each(function() {
                input = $(this).find('[class$=_order_field] input');
                input.attr('value', inc);
                order_string = order_string + input.serialize() + '&';
                inc++;
            });
            return order_string;
        }
		
        /**
         * Save
         */
        function save(order_string) {
            var syllabus_id = $('input[name=syllabus_id]').attr('value');
			var form_action = $('input[name=syllabus_id]').parent('form').attr('action');
			var form_data = $('input[name=syllabus_id]').parent('form').serialize();
            $.ajax({
                url: basehref + form_action,
                data: 'command[saveOrder]=Save+Syllabus&' + form_data + '&syllabus_id=' + syllabus_id,
                type: 'POST',
                dataType: 'json'
            });
        }
    
        
    });
    
};

