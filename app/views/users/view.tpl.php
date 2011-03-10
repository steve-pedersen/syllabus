<h1>{$user.user_fname} {$user.user_lname}</h1>

{if $enable_edit || $enable_perms || $enable_ghost}
<div class="save_row">
    {if $enable_edit}<a href="users/edit/{$user.user_id}" class="icon"><span class="icon edit inline-block"></span> Edit User</a> &nbsp; {/if}
    {if $enable_perms}<a href="users/permissions/{$user.user_id}" class="icon"><span class="icon perms inline-block"></span> Edit Permissions</a> &nbsp; {/if}
    {if $enable_ghost}<a href="users/ghost/{$user.user_id}" class="icon"><span class="icon ghost inline-block"></span> Ghost this user</a> &nbsp; {/if}
</div>
{/if}


<div class="label">First Name</div>
<div class="input">{$user.user_fname}</div>
<div style="clear: both;"></div>

<div class="label">Last Name</div>
<div class="input">{$user.user_lname}</div>
<div style="clear: both;"></div>

<div class="label">Display Name</div>
<div class="input">{$user.user_preferred_name}</div>
<div style="clear: both;"></div>

<div class="label">Preferred Email</div>
<div class="input">{$user.user_email}</div>
<div style="clear: both;"></div>

<div class="label">Office Location</div>
<div class="input">{$user.user_office}</div>
<div style="clear: both;"></div>

<div class="label">Office Phone Number</div>
<div class="input">{$user.user_phone}</div>
<div style="clear: both;"></div>

<div class="label">Mobile Phone Number</div>
<div class="input">{$user.user_mobile}</div>
<div style="clear: both;"></div>

<div class="label">Fax</div>
<div class="input">{$user.user_fax}</div>
<div style="clear: both;"></div>

<div class="label">Website</div>
<div class="input">{$user.user_website}</div>
<div style="clear: both;"></div>
