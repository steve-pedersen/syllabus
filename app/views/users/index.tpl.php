<h1>Search Users</h1>

{include file="_fragments/users_search.tpl.php"}

<hr />

{foreach from=$users item=u name=users}
{if $smarty.foreach.users.first}
<table summary="List of all users" cellpadding="0" cellspacing="0" style="width: auto;">
    <thead>
    <tr>
        <th scope="col" style="width: 200px;">User Id</th>
        <th scope="col" style="width: 200px;">User</th>
        <th scope="col" style="width: 200px;">Email</th>
    </tr>
    </thead>
    <tbody>
{/if}

    <tr>
        <td>{$u.user_id}</td>
        <th scope="row"><a href="users/view/{$u.user_id}">{$u.user_lname}, {$u.user_fname}</a></th>
        <td>{$u.user_email}</td>
    </tr>
    
{if $smarty.foreach.users.last}
    </tbody>
</table>
{/if}
{/foreach}

{$search_message}