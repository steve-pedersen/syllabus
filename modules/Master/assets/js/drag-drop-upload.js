
$(function() {
	'use strict';

	var $form = $('#dragDropUploadForm');
	var $input = $form.find('input[type="file"]');
	var uploading = false;

	$form.addClass("has-advanced-upload");

	// Initialize the jQuery File Upload widget:
	$form.fileupload({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: $form.attr('action'),
	    done: function (e, data) {
	    	var result = JSON.parse(data.result);
	    	$form.removeClass('is-success is-error');
	    	uploading = false;
	    	if (result.status == 200) {
	    		console.log('success');
	    		$form.addClass('is-success');
	    		if ($('#profileImage').length) {
					$('#profileImage').attr('src',result.imageSrc);
	    		}
	    		if ($('#submissionFile').length) {
					$('#submissionFile').attr('href', result.fileSrc);
					$('#submissionFile').text(result.fileName);
	    		}
	    		if ($('#uploadedSyllabusFile').length) {
	    			$('#uploadedSyllabusFile').attr('href', result.fileSrc);
	    			$('#uploadedSyllabusFile').text(result.fileName);
	    			$('#deleteUpload > a').attr('href', 
	    				`syllabus/${result.sid}/delete?return=syllabus/${result.cid}/ilearn`
    				);
	    			$('#deleteUpload').show();
	    			$('#publishAndReturn').show();
	    			$('#uploadedFile').attr('value', result.fid);
	    		}
	    		$('.box__uploading').hide();
	   //  	} else if (result.status == 422) {
	   //  		$form.addClass('is-error');
				errorMsg.textContent = "";
	    	} else {
	    		console.log('no success');
	    		$form.addClass('is-error');
	    		errorMsg.textContent = result.message;
	    	}
	    },
	    progress: function (e) {
	    	var activeUploads = $form.fileupload('active');
	    	uploading = true;
	    	$('.box__uploading').show();
	    	setTimeout(checkUpload, 350);
	    }
	});

	var $progressBar = $('#uploadProgress .progress-bar');
	var checkUpload = function () {
		if (uploading && $progressBar.length) {
			var current = parseInt($progressBar.attr('aria-valuenow')) + 1;
			current = current < 100 ? current : 100;
			$progressBar.attr('aria-valuenow', current);
			$progressBar.css('width', current + '%');
			setTimeout(checkUpload2, 350);
		}
	}
	var checkUpload2 = function () {
		if (uploading && $progressBar.length) {
			var current = parseInt($progressBar.attr('aria-valuenow')) + 1;
			current = current < 100 ? current : 100;
			$progressBar.attr('aria-valuenow', current);
			$progressBar.css('width', current + '%');
			setTimeout(checkUpload, 350);
		}
	}

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