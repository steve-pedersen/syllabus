{assign var=sid value=$syllabus->id}
{assign var=shared value=false}
{if $syllabus->getShareLevel() == 'all'}
	{assign var=shared value=true}
{/if}

<div id="shareSyllabusWidget{$sid}" class="d-inline-block ml-auto my-1">

	<div class="btn-group" role="group" aria-label="Share and copy url" >
		<form action="syllabus/{$sid}/share" method="post" class="form" id="shareForm{$sid}">
			{if $returnTo}
				<input name="returnTo" type="hidden" value="{$returnTo}" form="shareForm{$sid}">
			{/if}
			{if $coursesView}
				<input name="syllabusId" type="hidden" value="{$sid}" form="shareForm{$sid}">
				<!-- <input name="command" type="hidden" value="{if $shared}unshare{else}share{/if}" form="shareForm{$sid}"> -->
			{/if}
			<input 
				id="shareToggler{$sid}" 
				type="checkbox" {if $shared}checked{/if} 
				data-toggle="toggle" 
				data-onstyle="{if $sharePage}success{else}outline-success{/if}" 
				data-on="{if $dataOnText}{$dataOnText}{else}Shared{/if}" 
				data-off="{if $dataOffText}{$dataOffText}{else}Not Shared{/if}" 
				data-style="slow" 
			{if !$sharePage}
				data-width="115"
				data-height="38"
			{/if}
				name="sid" 
				value="{$sid}"
				{if $coursesView}
					form="shareForm{$sid}" 
					onChange="this.form.submit()" 
				{else}
					form="shareForm{$sid}" 
					onChange="document.getElementById('shareForm'+{$sid}).submit()" 
				{/if}
			>
			<!-- onChange="submitCourseShare({$sid}, {if $shared}'unshare'{else}'share'{/if})"  -->
			{if $shared}
				<input type="hidden" name="command[unshare]" value="1" {if $coursesView}form="shareForm{$sid}"{else}form="shareForm{$sid}"{/if}>
			{else}
				<input type="hidden" name="command[share]" value="1" {if $coursesView}form="shareForm{$sid}"{else}form="shareForm{$sid}"{/if}>
			{/if}
		{generate_form_post_key}
		</form>
	</div>

	<div class="dropdown-menu share-dropdown pb-0 pt-1" aria-describedby="tooltip{$sid}" aria-labelledby="tooltip{$sid}" id="shareWidget{$sid}" style="min-width:20vw;">

		<div class="row px-3 pt-3 pb-1">
			<div class="col px-3">
			<h6>
				Share this syllabus link
				<span type="buttons" data-placement="bottom" class="btn-link" data-toggle="tooltip" data-display="static"  data-html="true" title="Your syllabus is available for viewing. <strong>Only enrolled students</strong> can view your syllabus with the link below." id="tooltip{$sid}">
			  	<i class="far fa-question-circle ml-2"></i>
				</span>
			</h6>

			<label class="" for="clickToCopy">Copy URL to clipboard:</label>
            <div class="input-group mb-0">
              	<input tabindex="0" form="" class="form-control" type="text" value="{$syllabus->viewUrl}" id="clickToCopy{$sid}" style="font-family:monospace;">
              	<div class="input-group-append">
                	<button class="btn btn-outline-white" form="" type="button" id="copyBtn{$sid}"><i class="far fa-copy"></i>
                	</button>
              	</div>
            </div>  
			</div>

		</div>
		<div class="d-block mb-1 font-w700 text-success text-center" id="copiedAlert{$sid}" style="opacity:0;">
			Link copied!
		</div>
		<div class="row px-3 mt-1" style="font-size:0.85rem;">
			<div class="col-6 px-0 pb-0 border-top border-right text-center">
				<a href="https://athelp.sfsu.edu/hc/en-us/articles/360033902033-Making-a-syllabus-available-to-students#linking-ilearn" target="_blank" class="d-block py-2 widget-link font-w700">
			        How to link in iLearn <i class="fas fa-external-link-alt ml-1"></i>
			    </a>
			</div>
			<div class="col-6 px-0 pb-0 border-top text-center">
				{if !$sharePage}
				<a 
					href="syllabus/{$sid}/share" 
					class="d-block py-2 widget-link font-w700 text-muted" 
					data-toggle="tooltip" 
					data-placement="bottom" 
					title="Advanced sharing coming soon">
					Advanced
				</a>
				{/if}
			</div>
		</div>
	</div>

	<button {if !$shared}disabled{/if} id="copyWidget" type="button" class="btn btn-black {if $shared}shared{/if} {if $dataOnText && $dataOffText}btn-lg{/if}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<!-- <i class="fas fa-share"></i> -->
		<i class="fas fa-chevron-circle-down {if $sharePage}fa-2x {if $shared}text-success{/if}{/if}"></i>
	</button>

</div>