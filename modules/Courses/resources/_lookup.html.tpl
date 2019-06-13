{if $userCourses}
<div class="form-group alert alert-secondary p-3 mt-1">
    <label class="col-form-label form-control-label">Choose from my courses <small>Selecting one of your courses will automatically populate the course fields below.</small></label>
    <select class="form-control" name="courses" id="courseSelectLookup">
        <option value="off">Choose course...</option>
        {foreach $userCourses as $course}
            <option value="{$course->id}" {if $realSection->external_key == $course->id}selected{/if}>
                {if $course->sectionNumber}[{$course->getShortName()}] {/if}{$course->title} - {$course->term}
            </option>
        {/foreach}
    </select>
</div>   
{/if}