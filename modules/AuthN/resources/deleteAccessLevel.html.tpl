<a href="admin">Administrate</a> &gt; <a href="admin/roles">Roles and access levels</a> &gt; <a href="admin/levels/{$accessLevel->id}">{$accessLevel->name|escape}</a>
<h1>Delete access level &ldquo;{$accessLevel->name|escape}&rdquo;?</h1>

<p>Uh&hellip; This is a very bad idea. Ask someone to do it from the database, since there
currently is no way to reassign everything in an access level without some human intelligence
involved.</p>

<p>
	<a href="admin/levels/{$accessLevel->id}" class="action">Cancel</a>
</p>
