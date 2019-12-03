<div class="container-fluid mt-3">
	{include file="partial:_organizations.list.html.tpl"}
	{if $groups && $pAdmin}
	<div class="pt-3 border-top">
		<h2>Groups</h2>
		<a href="groups/new/edit" class="btn btn-success">+ Create New Group</a>
	</div>
	{/if}
</div>