{assign var=realSection value=$sectionVersion->resolveSection()}

<!-- <h3 class="real-section-title course-info-title">{$realSection->title}</h3> -->
<div class="real-section-content course-info-content">
	<h3>{$realSection->title}</h3>
	<dl>
		<dt>Class Number</dt>
		<dd>{$realSection->classNumber}</dd>
		<dt>Section</dt>
		<dd>{$realSection->sectionNumber}</dd>
		<dt>Semester</dt>
		<dd>{$realSection->semester} {$realSection->year}</dd>
		<dt>Description</dt>
		<dd>{$realSection->description}</dd>
	</dl>
</div>