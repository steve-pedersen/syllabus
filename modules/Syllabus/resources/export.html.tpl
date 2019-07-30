<div class="container" id="printContainer">
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
				{include file="{$ext->getExportFragment()}"}
			</div>

		</div>
		{/foreach}

	</div>
</div>