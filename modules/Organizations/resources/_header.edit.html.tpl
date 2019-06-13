<!-- Hero -->
<!-- TODO: integrate these breadcrumbs with addBreadcrumb() functionality -->
<div class="bg-body-light editor-header">
    <div class="content content-full">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <h1 class="flex-sm-fill h3 my-2">
                {$headerVars['title']}
                {if $headerVars['subtitle']} - <small class="font-size-base font-w400 text-muted">{$headerVars['subtitle']}</small>{/if}
            </h1>
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-alt">
                    <li class="breadcrumb-item" aria-current="page">
                        <a class="link-fx" href="home">Home</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<!-- END Hero -->