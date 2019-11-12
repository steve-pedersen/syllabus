{assign var=materials value=$importable->section->latestVersion->resolveSection()->materials}
<div class="preview-container">
	<table class="table table-striped table-sm ">
		<thead class="bg-primary text-white">
			<tr>
				<th scope="col">{$materials->header1}</th>
				<th scope="col">{$materials->header2}</th>
				<th scope="col">{$materials->header3}</th>
			</tr>			
		</thead>
		<tbody>
	{foreach $importable->section->latestVersion->resolveSection()->materials as $material}
		<tr>
			<td>
				<strong>
				{if $material->title}
					{$material->title|truncate:50}
				{elseif $material->url}
					{l href=$material->url text=$material->url}
				{/if}
				</strong>
			</td>
			<td>
				{if $material->required}<span class="text-danger">Required</span>{/if}
			</td>
			<td>
				{if $material->authors}<span class="">{$material->authors}. </span>{/if}
				{if $material->publisher}<span class="">{$material->publisher}</span>{/if}
			</td>
		</tr>
	{/foreach}
		</tbody>
	</table>
</div>