<div class="preview-container">
	<table class="table table-striped table-sm ">
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