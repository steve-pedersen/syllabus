
$(function() {
	'use strict';

	var $form = $('#dragDropUploadForm');
	var $input = $form.find('input[type="file"]');

	$form.addClass("has-advanced-upload");

	// Initialize the jQuery File Upload widget:
	$form.fileupload({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: $form.attr('action'),
	    done: function (e, data) {
	    	var result = JSON.parse(data.result);
	    	if (result.status == 200) {

	    		$('#profileImage').attr('src',result.imageSrc);
	    	}
	    }
	});


	var theForm = document.getElementById('dragDropUploadForm');
	["dragover", "dragenter"].forEach(function(event) {
		theForm.addEventListener(event, function() {
			theForm.classList.add("is-dragover");
		});
	});
	["dragleave", "dragend", "drop"].forEach(function(event) {
		theForm.addEventListener(event, function() {
			theForm.classList.remove("is-dragover");
		});
	});
});