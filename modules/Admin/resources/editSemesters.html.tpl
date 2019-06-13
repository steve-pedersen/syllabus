
<div class="col">
<h1>Configure Semesters</h1>

<form action="{$smarty.server.REQUEST_URI}" method="post" autocomplete="off">

<h2 class="font-weight-light d-flex">Semesters
    <div class="ml-auto">
    {if $view == 'recent'}
        <a class="btn btn-sm btn-default" href="admin/semesters?view=all" role="button">View All</a>
    {else}
        <a class="btn btn-sm btn-default" href="admin/semesters?view=recent" role="button">View Recent</a>
    {/if}
    </div>
</h2>
    <div class="form-group">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center"></th>
                    <th>Semester</th>
                    <th>Start</th>
                    <th>End</th>
                    <th class=""></th>
                </tr>
            </thead>
            {foreach from=$semesters item='s'}
                <tr>
                    <input type="hidden" name="semesters[{$s->id}]" id="semesters-{$s->id}" value="{$s->id}" />
                    <td class="text-center"><input type="checkbox" name="semesters[active][{$s->id}]" id="active-{$s->id}" value={if $s->active}true checked{else}false{/if} /></td>
                    <td><label style="display: block;" for="semesters-{$s->id}">{$s->display}</label></td>
                    <td>{$s->startDate->format('M d, Y')}</td>
                    <td>{$s->endDate->format('M d, Y')}</td>
                    <td><input class="btn btn-danger btn-sm" type="submit" name="command[remove][{$s->id}]" id="command-remove" value="Remove" /></td>
                </tr>
            {foreachelse}
                <tr>
                    <td colspan="5">There are no semesters configured in this system.</td>
                </tr>
            {/foreach}
        </table>
    </div>
    {if $semesters}
    <div class="form-group commands d-flex">
        <input class="btn btn-success" type="submit" name="command[activate]" id="command-active" value="Activate Selected Semesters" />
        <!-- <input class="btn btn-danger ml-auto" type="submit" name="command[remove]" id="command-remove" value="Remove Selected Semesters" /> -->
    </div>
    {/if}
    

<br><hr>
<div class="col-xl-10 offset-xl-1">
    <h2 class="font-weight-light">Add a Semester</h2>
    <div class="form-group">
        <label for="term">Term:</label>
        <select class="form-control" name="term" id="term">
            {foreach item='term' from=$terms}
                <option value="{$term}">{$term}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label for="startDate">Start Date:</label>
        <input class="form-control datepicker" type="text" name="startDate" id="startDate" />
        {if $errors.startDate}<p class="error">{$errors.startDate}</p>{/if}
    </div>
    <div class="form-group">
        <label for="endDate">End Date:</label>
        <input class="form-control datepicker" type="text" name="endDate" id="endDate" />
        {if $errors.endDate}<p class="error">{$errors.endDate}</p>{/if}
    </div>
    <div class="form-check">
        <input class="form-check-input" name="active" type="checkbox" value=true checked id="activeSemester">
        <label class="form-check-label" for="activeSemester">
            Active Semester
        </label>
    </div>
    <hr>
    <div class="form-group commands">
        <input class="btn btn-primary" type="submit" name="command[add]" id="command-add" value="Create Semester" />
    </div>
</div>
{generate_form_post_key}
</form>

</div>