<h1>Orphaned Faculty</h1>

<table class="data">
    <thead>
        <tr>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        {foreach item='orphan' from=$orphans}
        <tr>
            <td>{$orphan->lastName}, {$orphan->firstName}</td>
            <td><a href="admin/users/{$orphan->id}">edit</a></td>
        </tr>
        {/foreach}
    </tbody>
</table>