<h1>{$user->firstName|escape} {$user->lastName|escape} ({$user->id|escape})</h1>
<form method="post" action="">
    <dl class="metadata">
        <dt>User ID</dt>
        <dd>{$user->id|escape}</dd>

        <dt>First name</dt>
        <dd>{$user->firstName|escape|default:"&mdash;"}</dd>

        <dt>Last name</dt>
        <dd>{$user->lastName|escape|default:"&mdash;"}</dd>

        <dt>E-mail address</dt>
        <dd>{$user->emailAddress|escape|default:"&mdash;"}</dd>

        <dt>Employing Departments</dt>
        {foreach item='department' from=$user->departments}
        <dd>
            {$department->name|escape}
        </dd>
        {/foreach}
        <dt>Academic Departments</dt>
        {foreach item='department' from=$user->organizations}
        <dd>
            {$department->name|escape}
        </dd>
        {/foreach}
        <dd>
            <div class="input-group">
                <select name="department" id="department" class="form-control">
                    <option value="">Add Department</option>
                    {foreach item="group" from=$academicGroups}
                    <optgroup label="{$group->name|escape}">
                        {foreach item='org' from=$group->organizations}
                        <option value="{$org->id}">{$org->name|escape}</option>
                        {/foreach}
                    </optgroup>
                    {/foreach}
                    </select>
                <span class="input-group-btn">
                    <input type="submit" class="command-button btn btn-default" name="command[add-department]" value="Add department">
                </span>
            </div>
        </dd>

        <dt>Enrollments</dt>
        <dd>
            <ul class="bullet">
            {foreach item="course" from=$user->enrollments}
                <li>
                    {$user->enrollments->getProperty($course, 'role')|ucfirst}
                    {l href="admin/sections/`$course->id`" text=$course->shortName}
                    <span class="minor">({l href="admin/sections/`$course->id`" text=$course->id})</span>
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
        <a href="admin/audit?userId={$user->id|escape}&amp;crumb={$user->lastName|escape},+{$user->firstName|escape}&amp;crumb_href=admin/users/{$user->id|escape}" class="command-button btn btn-default alt">View audit logs</a>
    </div>
</form>
