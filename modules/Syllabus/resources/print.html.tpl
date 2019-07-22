<div class="container" id="printContainer">
	<button onClick="window.print()" class="btn btn-primary print-button"><i class="fas fa-print pr-2"></i> Print</button>
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