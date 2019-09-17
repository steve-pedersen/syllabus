
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
	    	$form.removeClass('is-success is-error');
	    	if (result.status == 200) {
	    		$form.addClass('is-success');
	    		$('#profileImage').attr('src',result.imageSrc);
	   //  	} else if (result.status == 422) {
	   //  		$form.addClass('is-error');
				errorMsg.textContent = "";
	    	} else {
	    		$form.addClass('is-error');
	    		errorMsg.textContent = result.message;
	    	}
	    }
	});

	$('.box__restart').on('click', function(e) {
		e.preventDefault();
		$form.removeClass('is-success is-error');
	});


	var theForm = document.getElementById('dragDropUploadForm');
	var errorMsg = theForm.querySelector(".box__error span");
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

	var ajaxFlag = document.createElement("input");
	ajaxFlag.setAttribute("type", "hidden");
	ajaxFlag.setAttribute("name", "ajax");
	ajaxFlag.setAttribute("value", 1);
	theForm.appendChild(ajaxFlag);
});