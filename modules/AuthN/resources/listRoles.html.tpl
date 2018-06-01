<a href="admin">Administrate</a>
<h1>Roles and access levels. {if $showAll}Be careful!{/if}</h1>

{if $showAll}
<p class="opportunity">
	You are living dangerously. I would recommend going back to the <a href="admin/roles">normal list of roles and access levels</a>.
</p>
{/if}

<div class="row">
	<div class="col-xs-12 col-md-6">
<h2>Roles</h2>
<ul class="list-group">
{foreach item="role" from=$roleList}
	<li class="list-group-item"><a href="admin/roles/{$role->id}">{$role->name|escape}</a> <span class="minor detail">{$role->description|strip_tags}</span></li>
{/foreach}
	<li class="list-group-item"><a class="minor detail" href="admin/roles/new">Add new role</a></li>
</ul>
</div>

<div class="col-xs-12 col-md-6">
<h2>Access levels</h2>
<ul class="list-group">
{foreach item="accessLevel" from=$accessLevelList}
    <li class="list-group-item"><a href="admin/levels/{$accessLevel->id}">{$accessLevel->name|escape}</a> <span class="minor detail">{$accessLevel->description|strip_tags}</span></li>
{/foreach}
	<li class="list-group-item"><a class="minor detail" href="admin/levels/new">Add new access level</a></li>
</ul>
</div>
</div>


{if !$showAll}
<div class="row">
	<div class="col-xs-12">
		<p class="minor detail">
			If you really, really know what you're doing, there's a <a href="admin/roles/all">list of all roles (including internal ones)</a>.
			Tread carefully.
		</p>
	</div>
</div>
{/if}