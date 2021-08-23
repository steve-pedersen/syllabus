
<div class="row viewer-main-container">

	<nav class="navbar navbar-expand-lg navbar-dark mobile-anchor-links" id="stickyNavbar">

		<button class="navbar-toggler mr-3 d-block mx-auto text-light p-3" type="button" data-toggle="collapse" data-target="#anchorLinksCollapse" aria-controls="anchorLinksCollapse" aria-expanded="false" aria-label="Toggle navigation">
			<i class="fas fa-anchor mr-2"></i> Jump to...
		</button>

		<div class="collapse navbar-collapse" id="anchorLinksCollapse">

			{if $appReturn}
			<a href="{$appReturn}" class="btn btn-block btn-info btn-lg nav-btn app-return-link mt-3">
				<strong>Return to iLearn</strong>
			</a>
			{/if}

			<ul class="navbar-nav ml-auto">
				<li class="nav-item sidebar-anchor-item">
					<a class="nav-link my-3" href="{$smarty.server.REQUEST_URI}#goToTop">
					<strong class="text-info"><i class="fas fa-arrow-up pr-2"></i> Go To Top</strong>
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

	<nav class="col-2 ml-auto anchor-links-sidebar-left bg-white text-dark mt-3">
		<div class="sidebar-sticky ">
			{if $appReturn}
			<div class="app-return py-1 bg-info text-center">
				<a href="{$appReturn}" class="nav-link app-return-link text-dark">
					<strong>Return to iLearn</strong>
				</a>
			</div>
			{/if}
			<ul class="nav flex-column text-right text-primary mt-2 pb-3">

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

	<!-- <main role="main" class="col-lg-10 col-sm-12 col-xs-12 ml-sm-auto mt-0 px-3"> -->
	<main role="main" class="col-lg-10 col-md-12 col-sm-12 ml-sm-auto mt-0 px-3" id="viewerContainer">
		{if $instructorView && $canChangeShare}
		<div class="row col">
			{include file="partial:_shareWidget.html.tpl"}
		</div>
		{/if}
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
				{if $editable && false}<span class=""><a class="btn btn-secondary btn-sm" href="syllabus/{$syllabus->id}">Back to Edit</a></span>{/if}
				<span class="text-muted mx-2 d-inline-block">
					<small>Last updated: {$syllabus->modifiedDate->format('F jS, Y - h:i a')}</small>
				</span>
				{assign var=tokenParams value=''}
				{if $token}
					{assign var=tokenParams value="?token=$token"}
				{elseif $tempLink}
					{assign var=tokenParams value="?temp=true"}
				{/if}
				<span class="d-inline-block">
					<a href="{$routeBase}syllabus/{$syllabus->id}/print{$tokenParams}"><i class="fas fa-print"></i> Print</a>
				</span>
				<span class="ml-3 d-inline-block">
					<a href="{$routeBase}syllabus/{$syllabus->id}/word{$tokenParams}"><i class="far fa-file-word"></i> Download as Word</a>
				</span>
			</div>	
		</div>

		<div class="syllabus-viewer p-lg-5 p-md-3 p-sm-2 p-xs-1" id="syllabusViewer">		

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

				<div class="section-content pt-3 dont-break-out" style="max-width:100%;">
					{include file="{$ext->getOutputFragment()}"}
				</div>

			</div>
			{/foreach}

		</div>
	</main>
	<div class="col-lg-1 col-md-0 spacer"></div>

</div>
<script>
    const copyToClipboard = function (e) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($('#viewUrl').val()).select();
        document.execCommand("copy");
        $temp.remove();
        $('#copiedAlert').show().removeClass('fade').hide(3500);
        // var $share = $('#shareContainer');
        // var $flash = $("<p class='alert alert-success'>")
    }
</script>