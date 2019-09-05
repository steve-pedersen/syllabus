
<h1>Syllabus Roles</h1>
<div class="row">
	<div class="col-xs-12 col-md-6">
	<h2>Roles</h2>
	<ul class="list-group">
	{foreach item="role" from=$roleList}
		<li class="list-group-item"><a href="admin/syllabus/roles/{$role->id}">{$role->name|escape}</a> <span class="minor detail">{$role->description|strip_tags}</span></li>
	{/foreach}
		<li class="list-group-item"><a class="minor detail" href="admin/syllabus/roles/new">Add new role</a></li>
	</ul>
	</div>
</div>