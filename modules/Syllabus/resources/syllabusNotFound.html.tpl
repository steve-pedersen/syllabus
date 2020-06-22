<div class="container p-4">
<h1>Syllabus Not Found</h1>
<div class="wrap pb-2"><div class="left"></div><div class="right"></div></div>

{if $courseSection}
<p class="p-3">
	You are attempting to view a syllabus for: <br><strong>{$courseSection->getFullSummary()}</strong>
</p>
{/if}
<p class="p-3">
	<strong>There isn't a syllabus associated with this course.</strong> Please contact your instructor for more information.
</p>
</div>