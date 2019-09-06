<div class="container" id="printContainer">

	<button onClick="window.print()" class="btn btn-primary btn-lg print-button">
		<i class="fas fa-print pr-2"></i> Print
	</button>

	<div class="row my-3 rounded pb-3 px-0 no-print">
		<div class="left col-lg-6 mt-3">
			{foreach $breadcrumbList as $crumb}
			<span class="breadcrumb-item {if $crumb@last}active{elseif $crumb@first}first{/if}">
				{if $crumb@last}
					{$crumb.text}
				{else}
					{l text=$crumb.text href=$crumb.href}
				{/if}
			</span>
			{/foreach}
		</div>
		<div class="text-right col-lg-6 px-2 mt-3">
			{if $editable}<span class=""><a class="btn btn-secondary btn-sm" href="syllabus/{$syllabus->id}">Back to Edit</a></span>{/if}
			<span class="text-muted mx-2 d-inline-block">
				<small>Last updated: {$syllabus->modifiedDate->format('F jS, Y - h:i a')}</small>
			</span>
			<span class="ml-3 d-inline-block">
				<a href="{$routeBase}syllabus/{$syllabus->id}/word{if $token}?token={$token}{/if}"><i class="far fa-file-word"></i> Download as Word</a>
			</span>
		</div>	
	</div>

	<hr class="fancy-line-4 my-4 no-print">

	<div class="syllabus-viewer" id="syllabusViewer">		

		{foreach $sectionVersions as $i => $sectionVersion}
			{assign var=ext value=$sectionVersion->extension}
		<div class="section-container {if !$sectionVersion@first}pt-3{/if} my-3 {if !$sectionVersion@first}border-top{/if}">
			<h2 class="section-title" id="section{$ext::getExtensionName()}{$i}">
				{if $sectionVersion->title}
					{$sectionVersion->title}
				{else}
					{$ext->getDisplayName()}
				{/if}
			</h2>
			{if $sectionVersion->description}
				<p class="section-description ">{$sectionVersion->description}</p>
			{/if}

			<div class="section-content pt-3">
				{include file="{$ext->getOutputFragment()}"}
			</div>

		</div>
		{/foreach}

	</div>
</div>