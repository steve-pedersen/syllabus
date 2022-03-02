
<h1>Syllabus Statistics
 - <small>{$display}</small>
</h1>

<form class="form-inline" role="form" id="filterForm">
<div class="container-fluid">
    <div class="form-group" style="padding-right:0em;">
		<select class="form-control" name="semester" id="selectedTerm">
		{foreach $semesters as $semester}
			<option value="{$semester->internal}" {if $internal == $semester->internal}selected{/if}>
				{$semester->display}
			</option>
		{/foreach}
		</select>  
    </div>


    <div class="form-group pull-right">    
        <button type="submit" class="btn btn-info filter-col">
            Apply
        </button>
        <a href="admin/syllabus/statistics" class="btn btn-link filter-col" id="clearFilters">
            Clear filters
        </a> 
    </div>
</div>
</form>

<div class="container-fluid">
	<h3>{$display}</h3>
	<dl class="row">
		<dt class="col-sm-4">Published Online Syllabi</dt>
		<dd class="col-sm-8">{$semesterOnline}</dd>
		<dt class="col-sm-4">Published File Syllabi</dt>
		<dd class="col-sm-8">{$semesterFiles}</dd>
		<dt class="col-sm-4">Distinct Instructors</dt>
		<dd class="col-sm-8">{$semesterInstructors}</dd>

	</dl>
	<h3>Totals</h3>
	<dl class="row">
		<dt class="col-sm-4">Total Published Online Syllabi</dt>
		<dd class="col-sm-8">{$totalOnline}</dd>
		<dt class="col-sm-4">Total Published File Syllabi</dt>
		<dd class="col-sm-8">{$totalFiles}</dd>
		<dt class="col-sm-4">Total Distinct Instructors</dt>
		<dd class="col-sm-8">{$totalInstructors}</dd>

	</dl>
</div>