<h1>Cron jobs</h1>

<form method="post" action="">
{generate_form_post_key}
<table class="table table-bordered">
    <thead>
        <tr>
            <td><strong><u>Job name</strong></u></td>
            <td><strong><u>Instance of</strong></u></td>
            <td><strong><u>Module</strong></u></td>
            <td><strong><u>Last run</strong></u></td>
            <td><strong><u>Action</strong></u></td>
        </tr>
    </thead>
    <tbody>
{foreach item="job" from=$cronJobs}
        <tr>
            <td>{$job.name|escape}</td>
            <td>{$job.instanceOf|escape}</td>
            <td>{$job.module->id|escape}</td>
            <td>{$job.lastRun|escape}</td>
            <td>
                <input type="submit" class="btn btn-primary btn-sm" name="command[invoke][{$job.name|escape}]" value="Run now">
            </td>
        </tr>
{/foreach}
    </tbody>
</table>
</form>
