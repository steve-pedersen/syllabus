<div class="row viewer-main-container">

	<nav class="col-md-2 col-sm-12 col-xs-12 d-sm-block ml-auto anchor-links-sidebar-left bg-white text-dark">
		<div class="sidebar-sticky mt-5 py-3">
			<ul class="nav flex-column mt-5 text-right text-primary">
				<li class="nav-item sidebar-anchor-item">
					<a class="nav-link" href="{$smarty.server.REQUEST_URI}#goToTop">
					<strong>Go To Top</strong> <i class="fas fa-arrow-up pl-2"></i> 
					</a>
				</li>
			{foreach $sectionVersions as $i => $sectionVersion}
				{if ($sectionVersion->resolveSection()->id != $realSection->id) && $sectionVersion->isAnchored}
					{assign var=ext value=$sectionVersion->extension}
					{assign var=extName value=$ext::getExtensionName()}
				<li class="nav-item sidebar-anchor-item">
					<a class="nav-link" href="{$smarty.server.REQUEST_URI}#section{$extName}{$i}">
					{if $sectionVersion->title}{$sectionVersion->title}{else}{$ext->getDisplayName()}{/if}
					</a>
				</li>
				{/if}				
			{/foreach}
			</ul>
		</div>
	</nav>

	<main role="main" class="col-lg-10 col-sm-12 col-xs-12 ml-sm-auto mt-0 px-3"> 	

		<div class="row m-3">
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
				<span class="text-muted mx-2"><small>Last updated: {$syllabus->modifiedDate->format('F jS, Y - h:i a')}</small></span>
				<span class=""><a href="#">Print <i class="fas fa-print"></i></a></span>
			</div>	
		</div>

		<div class="syllabus-viewer p-5" id="syllabusViewer">		

			{foreach $sectionVersions as $i => $sectionVersion}
				{assign var=ext value=$sectionVersion->extension}
			<div class="section-container">
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
	</main>
	<div class="col-lg-1 col-md-0 spacer"></div>

</div>