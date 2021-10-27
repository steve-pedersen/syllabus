{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Course Section - View --> 
{if $realSection->title}
<div class="col">
	<dl class="row">
		<dt class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
			<h3 class="real-section-title course-info-title">{$realSection->title}</h3>
		</dt>
		<dd class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Class Number</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					{$realSection->classNumber}
				</dd>
			</dl>
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Section</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					{$realSection->sectionNumber}
				</dd>
			</dl>
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Semester</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">{$realSection->semester} {$realSection->year}</dd>
			</dl>
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Description</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">{$realSection->description}</dd>
			</dl>
		</dd>
	</dl>

{if $courseSchedule && $scheduleData}
	<dl class="row">
		<dt class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
			<h4 class="real-section-title course-info-title">Schedule Information</h4>
		</dt>
		<dd class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
			{foreach $scheduleData as $sched}
			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">{$sched.facility.description}</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					<strong class="mr-2">{$sched.info.stnd_mtg_pat|replace:'R':'Th'}</strong> 
					{$sched.info.start_time} to {$sched.info.end_time}
				</dd>
			</dl>
<!-- 			<dl class="row mb-0">
				<dt class="col-xl-3 col-lg-4 col-md-5 col-sm-12">Schedule</dt>
				<dd class="col-xl-9 col-lg-8 col-md-7 col-sm-12">
					<strong class="mr-2">{$sched.info.stnd_mtg_pat}</strong> 
					{$sched.info.start_time} to {$sched.info.end_time}
				</dd>
			</dl> -->
			{/foreach}
		</dd>
	</dl>
{/if}

</div>
{/if}
<!-- End Course Section - View -->