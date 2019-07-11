<div class="container">
	{foreach $sectionVersions as $i => $sectionVersion}
		
		{assign var=ext value=$sectionVersion->extension}
		<div class="section-container mt-4 mb-2">	
			<h1 class="section-title" id="section{$ext::getExtensionName()}{$i}">
				{if $sectionVersion->title}
					{$sectionVersion->title}
				{else}
					{$ext->getDisplayName()}
				{/if}
			</h1>
			{if $sectionVersion->description}
				<p class="section-description">{$sectionVersion->description}</p>
			{/if}

			<div class="col">
				{include file="{$ext->getOutputFragment()}"}
			</div>
		</div>
	{/foreach}
</div>