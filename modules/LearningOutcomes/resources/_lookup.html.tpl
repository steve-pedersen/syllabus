{if $userCourses}
<div class="form-group alert alert-secondary p-3 mt-1">
    <label class="col-form-label form-control-label">Choose from my courses <small>Selecting one of your courses will automatically populate the Student Learning Outcomes fields below.</small></label>
    <select class="form-control" name="courses" id="courseSelectLookup">
        <option value="off">Choose course...</option>
        {foreach $userCourses as $course}
            <option value="{$course->id}" {if $realSection->external_key == $course->id}selected{/if}>
                {if $course->sectionNumber}[{$course->getShortName()}] {/if}{$course->title} - {$course->term}
            </option>
        {/foreach}
    </select>
	<span style="display:none;" id="outcomesLookupError">
		<span class="text-danger">No Student Learning Outcomes were found for this course.</span><br>
        <span>You may update the official SLOs for this course by <a href="#">following this link</a>. Once approved they will be available for import into your syllabus.</span>
	</span>  
	<span style="display:none;" id="outcomesLookupSuccess" class="text-success">
		The Student Learning Outcomes have been filled in below.
	</span>  
</div>   
{/if}