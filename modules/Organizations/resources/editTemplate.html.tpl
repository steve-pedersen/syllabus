{if !$template}

<div class="card border-0">
	<div class="card-body">
		<h2 class="card-title">Choose your starting template starting point</h2>
		<!-- <p class="card-text border-bottom pb-3">This class currently does not have a syllabus associated with it.</p> -->
		<div class="row p-3">
		{if $pastTemplates}
			<div class="col-3">
				<div class="form-group text-right">
					<i class="d-block far fa-file fa-3x mr-2 mb-2"></i>
					<a class="btn btn-success stretched-link" href="syllabus/start?{$orgType}={$organization->id}">Create New</a>
				</div>
			</div>

			<div class="col-1">
				<div class="row row-divided">
					<div class="col-xs-6 column-one">
					</div>
					<div class="vertical-divider">OR</div>
					<div class="col-xs-6 column-two">
					</div>
				</div>
			</div>

			<div class="col-8 form-group mb-3 ">
				<label class="sr-only" for="templateSyllabusOption">Choose option for this {$organization->organizationType} template</label>
				<label class="ml-2 " for="templateSyllabusOption"><i class="far fa-copy fa-3x"></i></label>
				<div class="input-group">
				<select name="options" class="form-control " id="templateSyllabusOption">
					<option value="" default>Choose past template to start from...</option>
				{foreach $pastTemplates as $pastTemplate}
					<option value="{$pastTemplate->id}">{$pastTemplate->title}, &nbsp;<small>Created: {$pastTemplate->createdDate->format('Y m, d')}</small></option>
				{/foreach}
				</select>
				<div class="input-group-append">
					<input class="btn btn-primary btn-sm" type="submit" name="command[clone]" value="Submit" />
				</div>
				</div>
			</div>
		{else}
			<div class="col">
				<div class="form-group text-center">
					<i class="d-block far fa-file fa-7x mr-2 mb-2"></i>
					<a class="btn btn-success stretched-link" href="syllabus/start?{$orgType}={$organization->id}">Create New</a>
				</div>
			</div>
		{/if}
		</div>
	</div>
</div>

{else}

<h1>Fill out basic info</h1>

{/if}