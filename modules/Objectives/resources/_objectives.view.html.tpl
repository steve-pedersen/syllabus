{assign var=realSection value=$sectionVersion->resolveSection()}
<!-- Objectives Section - View --> 
<div class="col">
    <h4>{$realSection->title}</h4>
</div>
<div class="col">
    {$realSection->description}
</div>
<!-- End Objectives Section - View -->