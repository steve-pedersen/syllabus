<!-- Container Section -->
<div class="card card-outline-secondary rounded-0">
    <div class="card-body">

    </div>
    {if $currentSectionVersion->dateCreated}
    <div class="card-footer text-muted">
        <small class="text-muted">Date modified - {$currentSectionVersion->dateCreated->format('Y m, d')}</small>
    </div>
    {/if}
</div>
<!-- End Container Section -->