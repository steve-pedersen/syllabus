<fieldset class="section{if $last} last{/if}">
    <legend>Change {if $account == $viewer}your{/if} password</legend>
    {if $account == $viewer}
    <p>
        To change your password, enter your new password in the fields
        below. Your new password must be at least eight characters long
        and must contain at least one digit and one English letter. To
        retain your current password, just leave the fields blank.
    </p>
    {else}
    <p>
        To change the password for logging in as this account using a
        PasswordAuthentication identity provider, enter a new password
        in the fields below. To leave this account's password the same,
        leave the fields blank. Passwords must be at least eight characters
        long and must contain at least one digit and one English letter.
    </p>
    {/if}
    
    <div class="form-group">
        <input type="hidden" name="passwordSentinel" value="{$passwordSentinel|escape}">
        <label for="password" class="field-label field-linked">New password (leave blank to retain current password): </label>
        <input type="password" class="form-control" name="password" id="password" autocomplete="off">
        
        {foreach item="error" from=$errorMap.password}
        <div class="error">
            <p class="text-error">
                {$error}
            </p>
        </div>
        {/foreach}
    </div>
    
    <div class="form-group">
        <label for="passwordConfirm" class="field-label field-linked">Confirm new password (must be exactly the same as above): </label>
        <input type="password" class="form-control" name="passwordConfirm" id="passwordConfirm" autocomplete="off">
    </div>
</fieldset>
