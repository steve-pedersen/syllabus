<div class="container" id="syllabusViewer">
	<!-- <h1 class="text-center py-3">{$syllabusVersion->title}</h1> -->
	{foreach $sectionVersions as $i => $sectionVersion}
		
		{assign var=ext value=$sectionVersion->extension}
		<div class="section-container pt-3 my-5 {if !$sectionVersion@first}border-top{/if}">
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