<h1>Faculty information</h1>
<p>
    You can upload updates to faculty information from HR.
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

<form method="post" action="" enctype="multipart/form-data">
    <section>
        <h2>Job Codes</h2>
        <p>Theses are the various job codes that are defined by HR and which may be associated with faculty.</p>
        <table class="data">
            <head>
                <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Tenure Track</th>
                    <th>Actions</th>
                </tr>
            </head>
            <body>
            {foreach item='jobCode' from=$jobCodes}
                <tr>
                    <td>{$jobCode->id}</td>
                    <td>{$jobCode->description|escape}</td>
                    <td>{if $jobCode->tenureTrack}<i class="halflings-icon ok"></i>{/if}</td>
                    <td><a href="admin/jobcodes/{$jobCode->id}">Edit</a></td>
                </tr>
            {foreachelse}
                <tr><td colspan="4">There are no Job Codes defined.</td></tr>
            {/foreach}
            </body>
        </table>
    </section>
    <div class="field">
        <label for="csv" class="field-label">Upload CSV</label>
        <input type="file" id="csv" name="csv">
    </div>
    <div class="controls">
        {generate_form_post_key}
        <input type="submit" class="command-button btn btn-primary" name="command[upload]" value="Upload">
    </div>
</form>
