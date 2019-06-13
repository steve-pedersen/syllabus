{if $success}
<p class="alert alert-success">{$success}</p>
{/if}

<h1>{$course->shortName|escape}</h1>

<form method="post" action="">
    <dl class="metadata">
        <dt>Instance of</dt>
        <dd>
            {if $course->archiveCourse}
                {l href="admin/courses/`$course->archiveCourse->catalog_number`" text="Catalog #`$course->archiveCourse->catalog_number`"}
                <span class="minor">
                    ({if $course->archiveCourse->course_status == 'A'}Authorized{else}Unauthorized{/if}; 
                        Semesters
                        {$course->archiveCourse->begin_period|escape} &ndash;
                        {$course->archiveCourse->end_period|escape})
                </span>
            {else}
                <em>None found</em>
            {/if}
        </dd>

        <dt>Semester</dt>
        <dd>{$course->semester->description|escape}</dd>

        <dt><label for="academic_organization_id">Department</label></dt>
        <dd>
            <select name="academic_organization_id" id="academic_organization_id">
                <option value="">No department specified</option>
                {foreach item="group" from=$academicGroups}
                <optgroup label="{$group->name|escape}">
                    {foreach item='org' from=$group->organizations}
                    <option value="{$org->id}"{if $course->organization->id == $org->id} selected="selected"{/if}>{$org->name|escape}</option>
                    {/foreach}
                </optgroup>
                {/foreach}
            </select>
        </dd>

        <dt>Course ID</dt>
        <dd>{$course->id|escape}</dd>

        <dt>Short Name</dt>
        <dd>{$course->shortName|escape}</dd>

        <dt>Title</dt>
        <dd>{$course->title|escape}</dd>

        <dt>Description</dt>
        <dd>{$course->description|escape|default:"<em>None</em>"}</dd>

        <dt>Prerequisites</dt>
        <dd>{$course->prerequisites|escape|default:"<em>None</em>"}</dd>
        
        <dt>Created date</dt>
        <dd>{$course->createdDate->format('c')}</dd>

        <dt>Last modified date</dt>
        <dd>{$course->modifiedDate->format('c')}</dd>

        <dt>Enrollments</dt>
        <dd>
            <ul class="bullet">
            {foreach item="user" from=$course->enrollments}
                <li>
                    {$course->enrollments->getProperty($user, 'role')|ucfirst}
                    <a href="admin/users/{$user->id}">{$user->firstName|escape} {$user->lastName|escape}</a>
                    <span class="minor">({l href="admin/users/`$user->id`" text=$user->id})</span>
                </li>
            {foreachelse}
                <li>No current enrollments</li>
            {/foreach}
            </ul>
        </dd>
    </dl>

    <div class="controls">
        {generate_form_post_key}
        <input type="submit" class="command-button btn btn-primary" name="command[save]" value="{if $department->inDatasource}Update{else}Create{/if}">
    </div>
</form>
<section>
<h3>Other Actions</h3>
<p>
    <a href="admin/audit?courseId={$course->id|escape}&amp;crumb={$course->shortName|escape}&amp;crumb_href=admin/sections/{$course->id|escape}" class="command-button btn btn-default">View audit logs</a>
    {if courseSentToClassClimate == 0}
    <a href="classclimate/upload/{$course->id|escape}" class="btn btn-warning">Upload to Class Climate</a>
    {/if}
</p>
</section>