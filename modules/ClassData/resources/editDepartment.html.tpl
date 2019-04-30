{if $success}
<p class="alert alert-success">{$success}</p>
{/if}

<h1>{if $department->inDatasource}Edit{else}Create{/if} Department</h1>
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

    {if !$department->inDataSource}
    <div class="field">
        <label for="id" class="field-label">ID</label>
        <input type="text" id="id" class="text-field form-control" name="id" value="{$department->id|escape}">
    </div>
    {/if}

    <div class="field">
        <label for="name" class="field-label">Name</label>
        <input type="text" id="name" class="text-field form-control" name="name" value="{$department->name|escape}">
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Contacts</div>

        <div class="panel-body">
            <p>
                These are the contacts that the department is associated with.  
                If a course section needs to be have this department receive the
                Class Climate survey data, these contacts will receive that survey data.
            </p>
        </div>

        <ul class="list-group">
        {foreach item='contact' from=$department->contacts}
            <li class="list-group-item">
                <a href="admin/contacts/{$contact->id}">{$contact->lastName|escape}, {$contact->firstName|escape} ({$contact->id|escape})</a> <button type="submit" name="command[remove-contact][{$contact->id}]" class="pull-right btn btn-danger btn-xs"><i class="halflings-icon white remove"></i>Remove</button>
            </li>
        {foreachelse}
            <li>This department has no contacts.</li>
        {/foreach}
        </ul>
    </div>

    <div class="field">
        <label for="contact" class="field-label">Add Contact</label>
        <select name="contact_id" id="contact" class="form-control">
            <option value="">No Contact Specified</option>
            {foreach item='contact' from=$contacts}
            <option value="{$contact->id}">{$contact->displayName}</option>
            {/foreach}
        </select>
    </div>

    <div class="field">
        <label for="subunit" class="field-label">Subunit</label>
        <div class="input-group">
            <select name="subunitId" id="subunit"class="form-control">
                <option value="">No Subunit</option>
                {foreach item='subunit' from=$subunits}
                <option value="{$subunit->m_nId}"{if $department->subunitId == $subunit->m_nId} selected="selected"{/if}>{$subunit->m_sName}</option>
                {/foreach}
            </select>
            {if $department->inDataSource}
            <a href="admin/departments/{$department->id}/newsubunit" class="command-button btn input-group-addon">Create New Subunit</a>
            {/if}
        </div>
    </div>

    <div class="controls">
        {generate_form_post_key}
        <input type="submit" class="command-button btn btn-primary" name="command[save]" value="{if $department->inDatasource}Update{else}Create{/if}">
    </div>
</form>
