<div class="col mb-5 p-3">
	{if $organizations[0]->routeName == 'colleges'}
		{assign var=mod value=3}
	<a href="{$organizations[0]->routeName}">
		<h3 class="display-4 mx-2 text-white bg-secondary p-3"><i class="fas fa-university mr-3"></i>{$organizations[0]->getOrganizationType()}s</h3>
	</a>
	{elseif $organizations[0]->routeName == 'departments'}
		{assign var=mod value=4}
	<a href="{$organizations[0]->routeName}">
		<h3 class="display-4 mx-2 text-white bg-secondary p-3"><i class="far fa-building mr-3"></i>{$organizations[0]->getOrganizationType()}s</h3>
	</a>
	{/if}
	
	<div class="card-group mb-2 px-2">
	{foreach $organizations as $organization}
		{if (($organization@index != 0) && (($organization@index) % $mod == 0))}
		</div><div class="card-group mb-2 px-2">
		{/if}
		
		{include file="partial:_organization.list.html.tpl"}

	{/foreach}
	</div>
</div>