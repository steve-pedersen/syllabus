{assign var=organizationType value=$organization->getOrganizationType()}
<a href="{$organizationType|lower}s/create" class="btn btn-link">Create New {$organizationType}</a>