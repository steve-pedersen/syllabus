<div class="container-fluid mt-3"> 
	{foreach $allOrganizations as $typeKey => $organizations}
		{include file="partial:_organizations.list.html.tpl"}
	{/foreach}
</div>