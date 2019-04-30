<h1>{if $contact->inDatasource}Edit{else}Create{/if} Contact</h1>
<p>
    You can associate this department with a particular contact and an existing subunit on Class Climate.
    
</p>

{if $errorMap}
    <div class="info">
        <p>There are errors in the submission.</p>
        <dl>
        {foreach item='errorList' key='errorKey' from=$errorMap}
            <dt>{$errorKey}</dt>
            {foreach item='errorMessage' from=$errorList}
            <dd>{$errorMessage}</dd>
            {/foreach}
        {/foreach}
        </dl> 
    </div>
{/if}

<form method="post" action="">
    <div class="field">
        <label for="id" class="field-label">ID</label>
        <input type="text" id="id" class="text-field form-control" name="id" value="{$contact->id|escape}">
    </div>
    <div class="field">
        <label for="firstName" class="field-label">First Name</label>
        <input type="text" id="firstName" class="text-field form-control" name="firstName" value="{$contact->firstName|escape}">
    </div>
    <div class="field">
        <label for="lastName" class="field-label">Last Name</label>
        <input type="text" id="lastName" class="text-field form-control" name="lastName" value="{$contact->lastName|escape}">
    </div>
    <div class="field">
        <label for="emailAddress" class="field-label">Email</label>
        <input type="text" id="emailAddress" class="text-field form-control" name="emailAddress" value="{$contact->emailAddress|escape}">
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Departments</div>

        <div class="panel-body">
            <p>
                These are the departments that the contact is associated with.  
                If a course section needs to be have another department receive the
                Class Climate survey data, and this contact is associated with that
                department, this contact will receive that survey data.
            </p>
        </div>
        
        <ul class="list-group">
        {foreach item='department' from=$contact->departments}
            <li class="list-group-item">
                <a href="admin/departments/{$department->id}">{$department->name|escape}</a> <button type="submit" class="btn btn-danger btn-xs pull-right" name="command[remove-department][{$department->id}]"><i class="halflings-icon white remove"></i> Remove</button>
            </li>
        {foreachelse}
        <li>This contact is not associated with a department.</li>
        {/foreach}
        </ul>
    </div>
    <div class="field">
        <label for="add-department">Add Department</label>
        <select name="add-department" class="form-control">
            <option value="">No department specified</option>
            {foreach item='department' from=$departments}
            <option value="{$department->id}">{$department->name|escape}</option>
            {/foreach}
        </select>
    </div>
    <div class="controls">
        {generate_form_post_key}
        <input type="submit" class="command-button btn btn-primary" name="command[save]" value="{if $contact->inDatasource}Update{else}Create{/if}">
        {if $contact->inDatasource}
        <a href="admin/contacts/{$contact->id}/delete" class="btn btn-danger">Delete</a>
        {/if}
    </div>
</form>
