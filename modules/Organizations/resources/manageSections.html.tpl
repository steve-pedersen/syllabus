<div class="container-fluid">
<h1>Manage Sections</h1>
<form action="groups/{$group->id}/sections/edit" method="get" class="">
<div class="row">
	<div class="col p-3 ">
		<h3>Create new section:</h3>
	{foreach $sectionExtensions as $ext}
		<button class="px-2 mb-2 py-1 btn btn-outline-secondary rounded-pill" type="submit" name="type" value="{$ext->getExtensionName()}">
		<img src="{$ext->getDarkIcon()}" class="img-fluid mr-1" style="max-height:1.5em;margin-bottom:1px;">
			<span class="available pr-1">{$ext->getDisplayName()}</span>
		</button>
	{/foreach}
	</div>
</div> <!-- End row -->
</form>

<h3>Existing sections:</h3>
{if $sortedImportableSections}

<div class="accordion" id="sectionsAccordion">
	{assign var=expanded value=true}
	{foreach $sortedImportableSections as $type => $importableSections}
		{assign var=extension value=$importableSections[0]->getSectionExtension()}
	
	<div class="card border-bottom mb-3">
		<div class="card-header" id="heading{$type}">
			<button class="btn btn-link pl-0" type="button" data-toggle="collapse" data-target="#collapse{$type}" aria-expanded="true" aria-controls="collapse{$type}">
				<h4 class="mb-0">
					<img src="{$extension->getDarkIcon()}" class="img-fluid mr-2" style="max-height:2em;margin-bottom:5px;">{$type}
				</h4>
			</button>
		</div>

		<div id="collapse{$type}" class="collapse show" aria-labelledby="heading{$type}" data-parent="#sectionsAccordion">
			{assign var=expanded value=false}
			<div class="card-body-no">
				
				<table class="table table-sm ">
				<thead class="thead-darks">
					<tr class="">
						<th style="width:33%" scope="col" class="">Title</th>
						<th style="width:33%" scope="col" class="">Modified Date</th>
						<th style="width:33%" scope="col" class=""></th>
					</tr>
				</thead>
				<tbody>
			{foreach $importableSections as $importable}
				<tr class="">
					<td style="width:33%" class="align-middle">{$importable->title}</td>
					<td style="width:33%" class="align-middle">{$importable->modifiedDate->format('F jS, Y - h:i a')}</td>
					<td style="width:33%" class="align-middle text-center">
						<a href="groups/{$group->id}/sections/edit?s={$importable->id}&type={$type|lcfirst}" class="btn btn-info btn-sm">
							Edit
						</a>
					</td>
				</tr>
			{/foreach}
				</tbody>
				</table>
			</div>
		</div>
	</div>

	{/foreach}
</div>

{else}
	<p>You have no sections</p>
{/if}
</div>
