{assign var=orgType value=$organization->getOrgType()}
<a href="{$orgType|lower}s/create" class="btn btn-link">Create New {$orgType}</a>