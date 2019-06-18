{assign var=realSection value=$sectionVersion->resolveSection()}

<h2 class="real-section-title course-info-title">{$realSection->title}</h2>
<div class="real-section-content course-info-content">
	<dl class="">
		<dt class="class-number-label">Class Number</dt>
		<dd class="class-number">{$realSection->classNumber}</dd>
		<dt class="section-label">Section</dt>
		<dd class="section">{$realSection->sectionNumber}</dd>
		<dt class="semester-label">Semester</dt>
		<dd class="semester">{$realSection->semester} {$realSection->year}</dd>
		<dt class="description-label">Description</dt>
		<dd class="description">
			<p>{$realSection->description}</p>
		</dd>
	</dl>
</div>
