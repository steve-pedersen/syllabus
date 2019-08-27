<div class="container-fluid my-syllabi-overview mb-5">

<div class="row">
	<div class="{if $syllabi}col-8{else}col-12{/if}">
		<h2>My Syllabi: <small class="text-muted">Recently Modified</small></h2>
		<p class="text-muted">
			Create a new syllabus or access your most recently modified syllabi.
			{if $syllabi && count($syllabi) > 3} Click "see more" to show more results.{/if}
		</p>
	</div>
	{if $syllabi}
<!-- 	<div class="col-4">
		<div class="text-right">
			<span type="buttons" data-placement="bottom" class="btn-link" data-toggle="tooltip" data-html="true" title="To share your syllabus with students, click the <strong>Options</strong> button on a syllabus below and then <strong>Share</strong>">
			  <i class="far fa-question-circle mr-1"></i> How to share a syllabus with Students?
			</span>
		</div>	
	</div> -->
	{/if}
</div>

	<div class="row mb-3">
		<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 px-2">
			<div class="card start-card">
				<a href="syllabus/start" class="text-center stretched-link align-text-middle text-success start-syllabus">
				<div class="card-body text-center align-text-middle text-success h-100">
					<i class="mt-5 mb-5 fas fa-plus-circle fa-7x"></i>
					<p class="text-center align-bottom text-success mt-3"><strong>Start a new syllabus</strong></p>
				</div>
				</a>
			</div>
		</div>

	{assign var=btnEdit value=true}
	{assign var=btnClone value=true}
	{assign var=btnView value=true}
	{assign var=cropSize value="13"}
	{assign var=hasSeeMore value=false}
	{assign var=overview value=true}
	
{foreach $syllabi as $i => $syllabus}

	{if $i == 3}
		{assign var=hasSeeMore value=true}
	<!-- </div> END row1 -->
<div class="collapse row px-3" id="seeMoreSyllabi">
	<!-- <div class="row mb-3"> -->
	{elseif ($i > 4 && ($i % 4) == 0)}
	<!-- </div> END row2 -->
	<!-- <div class="row mb-3"> -->
	{/if}
		<div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 px-2 my-2">
			{include file="partial:_syllabusCard.html.tpl"}
		</div>
{foreachelse}
	<!-- <p>You have no syllabi yet!</p> -->
{/foreach}
	</div> <!-- END row3 -->
{if $hasSeeMore}
</div> <!-- END collapse -->
<div class="float-right mb-5">
	<a class="collapsed ml-auto see-more-toggle" id="seeMoreToggle2" data-toggle="collapse" href="#seeMoreSyllabi" aria-expanded="false" aria-controls="seeMoreSyllabi"></a>
</div>
{/if}
</div>
