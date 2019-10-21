(function ($) {
  $(function () {

    $('#reviewSubmissionModal').on('show.bs.modal', function(e) {
        var submissionId = $(e.relatedTarget).attr('data-submission');
        var status = $(e.relatedTarget).siblings('#status').val();
        var feedback = $(e.relatedTarget).siblings('#feedback').val();
        var dueDate = $(e.relatedTarget).siblings('#dueDate').val();
        var submittedDate = $(e.relatedTarget).siblings('#submittedDate').val();
        var approvedDate = $(e.relatedTarget).siblings('#approvedDate').val();
        var courseSummary = $(e.relatedTarget).siblings('#courseSummary').val();
        var syllabusId = $(e.relatedTarget).siblings('#syllabusId').val();
        var syllabusTitle = $(e.relatedTarget).siblings('#syllabusTitle').val();
        var fileSrc = $(e.relatedTarget).siblings('#fileSrc').val();
        var fileName = $(e.relatedTarget).siblings('#fileName').val();

        $('#editSubmissionForm').attr('action', $('#editSubmissionForm').attr('action') + submissionId);
        $('#submissionTitle').text('Evaluating submission for ' + courseSummary);
        $('#subCourseSection').text(courseSummary);
        $('#subDueDate').text(dueDate);
        $('#subSubmittedDate').text((submittedDate ? submittedDate : 'N/A'));
        $('#subApprovedDate').text((approvedDate ? approvedDate : 'N/A'));
        
        if (syllabusId) {
            $('#syllabusViewLink').attr('href', 'syllabus/' + syllabusId + '/view').text(syllabusTitle);
            if (!fileSrc) {
                $('#subFileDownload').text('N/A');
            } else {
                var message = 'This user has submitted both a file and an online syllabus. '+
                    'They have been notified that the online version is what will be reviewed, instead of the file. ' +
                    'The file can be <a href="'+fileSrc+'">downloaded here</a> anyway.';
                $('#subFileDownload').html(message);
            }
        } else {
            $('#subSyllabusView').text('N/A');
            if (fileSrc) {
                $('#fileDownloadLink').attr('href', fileSrc).text(fileName);
            }
        }

        CKEDITOR.instances.subFeedback.setData(feedback);

        if (status == 'Approved') {
            $('#subStatus').text(status).addClass('text-success font-w900');
            $('#approveButton').hide();
            $('#denyButton').val('Deny Even Though Already Approved?').attr('name', `command[deny][${submissionId}]`);
        } else {
            $('#subStatus').text(status).removeClass('text-success font-w900');
            $('#approveButton').show().attr('name', `command[approve][${submissionId}]`);    
            $('#denyButton').val('Deny').attr('name', `command[deny][${submissionId}]`);
        }

    });

  });
})(jQuery);
