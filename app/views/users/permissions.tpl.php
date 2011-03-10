<h1><a href="users/view/{$user.user_id}">{$user.user_fname} {$user.user_lname}</a></h1>

<form action="{$smarty.const.CURRENT_URL}" method="post">
    {$smarty.const.SUBMIT_TOKEN_HTML}
    <input type="hidden" name="user_id" value="{$user.user_id}" />

    <fieldset><legend>Permissions</legend>
        
        <p>
        <input type="checkbox" name="permissions[]" id="permissions_edit_users" value="{$smarty.const.PERM_USERS_EDIT}" {if $perms.edit_users} checked="checked" {/if} />
        <label for="permissions_edit_users">Edit Users</label>
        </p>
        
        <p>
        <input type="checkbox" name="permissions[]" id="permissions_perms" value="{$smarty.const.PERM_USERS_PERMS}" {if $perms.edit_permissions} checked="checked" {/if} />
        <label for="permissions_perms">Edit User Permissions</label>
        </p>
        
        <p>
        <input type="checkbox" name="permissions[]" id="permissions_ghost_users" value="{$smarty.const.PERM_USERS_GHOST}" {if $perms.ghost_users} checked="checked" {/if} />
        <label for="permissions_ghost_users">Can Ghost Users</label>
        </p>
        
        <p>
        <input type="checkbox" name="permissions[]" id="permissions_repository" value="{$smarty.const.PERM_REPOSITORY}" {if $perms.edit_repository} checked="checked" {/if} />
        <label for="permissions_repository">Manage Repository Items</label>
        </p>
        
        <p>
        <input type="checkbox" name="permissions[]" id="permissions_blog" value="{$smarty.const.PERM_BLOG}" {if $perms.blog} checked="checked" {/if} />
        <label for="permissions_blog">Manage Blog Postings</label>
        </p>
        
        <p>
        <input type="checkbox" name="permissions[]" id="permissions_drafts" value="{$smarty.const.PERM_DRAFTS}" {if $perms.drafts} checked="checked" {/if} />
        <label for="permissions_drafts">Create Syllabus Drafts <span class="form_note">(granted automatically to instructors)</span></label>
        </p>
       
       {if $can_set_admin}
        <p>
        <input type="checkbox" name="permissions[]" id="permissions_admin" value="{$smarty.const.PERM_ADMIN}" {if $perms.admin} checked="checked" {/if} />
        <label for="permissions_admin">Grant administrator access</label>
        </p>
        {/if}
        
        <div class="save_row">
        <input type="submit" name="command[editUserPermissions]" value="Save Changes" class="button submitButton" />
        <a href="users/view/{$user.user_id}" class="cancel_link">Cancel</a>
        </div>
    </fieldset>
    
</form>
