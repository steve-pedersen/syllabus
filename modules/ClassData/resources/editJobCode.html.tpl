<h1>{if $jobCode->inDatasource}Edit{else}Create{/if} Job Code</h1>
<p>
    You can set whether this job code qualifies as tenure track.
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
        <input type="text" id="id" class="text-field form-control" name="id" value="{$jobCode->id|escape}">
    </div>
    <div class="field">
        <label for="description" class="field-label">Description</label>
        <input type="text" id="description" class="text-field form-control" name="description" value="{$jobCode->description|escape}">
    </div>
    <div class="field">
        <label><input type="checkbox" name-"tenureTrack" value="t"{if $jobCode->tenureTrack} checked="checked"{/if}> Tenure track</label>
    </div>
    <div class="controls">
        {generate_form_post_key}
        <input type="submit" class="command-button btn btn-primary" name="command[save]" value="{if $jobCode->inDatasource}Update{else}Create{/if}">
    </div>
</form>
