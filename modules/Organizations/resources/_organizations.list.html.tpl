<div class="col mb-5 p-3">
	{if $organizations[0]->routeName == 'colleges'}
		{assign var=mod value=3}
	<a href="{$organizations[0]->routeName}" class="d-inline-block w-100">
		<h3 class="display-4 mx-2 text-white bg-secondary p-3 d-block-inline">
			<img src="assets/icons/college-icon1.png" class="img-fluid mr-3" alt="college icon" style="max-width:10%"><span class="align-bottom">{$organizations[0]->getOrganizationType()}s</span>
		</h3>
	</a>
	{elseif $organizations[0]->routeName == 'departments'}
		{assign var=mod value=4}
	<a href="{$organizations[0]->routeName}">
		<h3 class="display-4 mx-2 text-white bg-secondary p-3 d-block-inline">
			<img src="assets/icons/dept-icon3.png" class="img-fluid mr-3" alt="department icon" style="max-width:4%"><span class="align-bottom">{$organizations[0]->getOrganizationType()}s</span>
		</h3>
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