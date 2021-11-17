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
{if $courseSchedule && $scheduleData}
	<h4>Schedule Information</h4>
	<ul>
	{foreach $scheduleData as $sched}
		<li>{$sched.facility.description}  &mdash;  
			<strong class="mr-2">{$sched.info.stnd_mtg_pat|replace:'R':'Th'}</strong> 
			{$sched.info.start_time} to {$sched.info.end_time}
		</li>
	{/foreach}
	</ul>
{/if}
</div>