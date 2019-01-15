<h1>Permanently Delete {$account->fullName}?</h1>

<form method="post" action="{$smarty.server.REQUEST_URI|escape}">
    <p>
        Deletion of an account is permanent and non-reversible.
        Deleting an account may delete content owned by that
        account, including comments, uploads, etc. This data
        may be public!
    </p>
    <p>
        {generate_form_post_key}
        <input type="submit" class="command-button dangerous" name="command[delete]" value="Delete {$account->firstName|escape}">
        <a href="{$returnTo|escape}">Cancel</a>
    </p>
</form>
