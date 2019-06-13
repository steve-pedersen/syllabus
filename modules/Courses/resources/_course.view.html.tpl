{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Course Section - View --> 
<div class="col">
    <h4>{$realSection->title}</h4>
    <p>
    	{$realSection->classNumber} Section {$realSection->sectionNumber}<br>
		{$realSection->semester} {$realSection->year}
    </p>
</div>
<div class="col">
    {$realSection->description}
</div>
<!-- End Course Section - View -->