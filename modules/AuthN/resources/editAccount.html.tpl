<h1>{if !$account->inDataSource}New account{else}Edit account: {$account->username|escape}{/if}. +=+</h1>

<form method="post" action="{$smarty.server.REQUEST_URI|escape}">

            <div class="form-group">
                <label class="field-label field-linked" for="account-username">Username</label>
                <div class="field-control">
                    <input type="text" class="form-control" id="account-username" name="username" value="{$account->username|escape}">
                </div>
                {foreach item="error" from=$errorMap.emailAddress}<div class="error">{$error}</div>{/foreach}
            </div>

            <div class="form-group">
                <label class="field-label field-linked" for="account-emailAddress">Email Address</label>
                <div class="field-control">
                    <input type="text" class="form-control" id="account-emailAddress" name="emailAddress" value="{$account->emailAddress|escape}">
                </div>
                {foreach item="error" from=$errorMap.emailAddress}<div class="error">{$error}</div>{/foreach}
            </div>
            
            <div class="form-group subfield-container">
                <div class="subfield">
                    <label class="field-linked" for="account-first-name">First<span class="nonvisual-context">name:</span></label>
                    <input type="text" class="form-control" id="account-first-name" name="firstName" value="{$account->firstName|escape}">
                </div>
                
                <div class="subfield">
                    <label class="field-linked" for="account-middle-name">Middle<span class="nonvisual-context">name or initial:</span></label>
                    <input type="text" class="form-control" id="account-middle-name" name="middleName" value="{$account->middleName|escape}">
                </div>
                
                <div class="subfield">
                    <label class="field-linked" for="account-last-name">Last<span class="nonvisual-context">name:</span></label>
                    <input type="text" class="form-control" id="account-last-name" name="lastName" value="{$account->lastName|escape}">
                </div>
                
                {foreach item="error" from=$errorMap.firstName}
                <div class="error">
                    <p class="text-error">{$error}</p>
                </div>
                {/foreach}

                {foreach item="error" from=$errorMap.middleName}
                <div class="error">
                    <p class="text-error">{$error}</p>
                </div>
                {/foreach}

                {foreach item="error" from=$errorMap.lastName}
                <div class="error">
                    <p class="text-error">{$error}</p>
                </div>
                {/foreach}
            </div>
            
        {foreach name="exts" item="settingsExtension" from=$settingsExtensionList}
            {include file=$settingsExtension->getAccountSettingsTemplate() last=$smarty.foreach.exts.last}
        {/foreach}

        
        <div class="form-group controls">
            {generate_form_post_key}
            <button type="submit" class="command-button btn btn-primary" name="command[save]">Save Settings</button>
            
            {if $account->inDataSource && $account != $viewer}
            <a class="btn btn-danger" href="admin/accounts/{$account->id}/delete">Delete</a>
            {/if}

            <a class="btn btn-default" href="{$returnTo|escape}">Cancel</a>
        </div>
</form>
