<div class="container-fluid">
<h1 class="">{if $campaign->id}Edit{else}New{/if} Submission Campaign</h1>
<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>

<p class="lead mt-3">
	Submission campaigns should be started for each upcoming semester in which you want instructors to submit their syllabi. Each campaign is associated with a semester.
</p>

<form action="{$smarty.server.REQUEST_URI|escape}" method="post" class="mt-3">
	
    <div class="form-row py-3 row-2">
        <div class="col-md-4 mb-3 office">
            <label for="semester">Choose a semester for this campaign</label>
            
		{if !$campaign->id}
            <select name="semester" class="form-control">
            	{foreach $semesters as $semester}
					{assign var=available value=true}
					{foreach $campaignSemesters as $semesterId}
						{if $semesterId == $semester->id}
							{assign var=available value=false}
						{/if}
					{/foreach}
				<option value="{$semester->id}" {if !$available}disabled{/if} {if $semester->id == $activeSemester->id}selected{/if}>
					{$semester->display}{if !$available} [This semester already being used in a campaign]{/if}
				</option>
            	{/foreach}
            </select>
		{else}
            <select name="semester" class="form-control">
				<option value="{$campaign->semester->id}" selected>
					{$campaign->semester->display} [Can't change semesters once campaign is created]
				</option>
            </select>			
		{/if}
        </div>
        <div class="col-md-4 mb-3 website">
            <label for="dueDate">Syllabi submission due date</label>
            <input type="text" class="form-control datepicker" name="dueDate" value="{if $campaign->dueDate}{$campaign->dueDate->format('m/d/Y')}{/if}" placeholder="MM/DD/YYYY">
        </div>
		<div class="col-md-4 pl-md-4">
			<label for="required">Approval required?</label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="required" id="required1" value="1" {if $campaign->required || !$campaign->id}checked{/if}>
				<label class="form-check-label" for="required1">
					Yes
				</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="required" id="required2" value="0" {if !$campaign->required && $campaign->id}checked{/if}>
				<label class="form-check-label" for="required2">
					No
				</label>
			</div>
			<small id="requiredHelpBlock" class="form-text text-muted">
				When set to <strong>required</strong>, departments must review and approve faculty syllabus submissions. If set to no, then syllabi will automatically be approved on submission.
			</small>
		</div>
    </div>
    <div class="form-row py-3 row-3">
        <div class="col-md-12 mb-3 about">
            <label for="about">Description or any instructions for faculty about submitting their syllabi.</label>
            <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="description" rows="4">{$campaign->description}</textarea>
        </div>
    </div>		

	<div class="form-group my-5 d-flex">
		{generate_form_post_key}
		<button type="submit" class="command-button btn btn-primary " name="command[save]">Save Campaign</button>
		<a class="btn btn-default  mr-auto" href="{$routeBase}submissions">Cancel</a>
		{if $pManager}
			<button type="submit" class="command-button btn btn-danger  ml-auto" name="command[delete]">Delete This Campaign</button>
		{/if}
	</div>
</form>


{if $campaign->log}
<div class="p-3 mt-3 border-top">
	<h2>Activity log</h2>
	<div>
		<ul>
			{$campaign->log}
		</ul>
	</div>
</div>
{/if}

</div>
