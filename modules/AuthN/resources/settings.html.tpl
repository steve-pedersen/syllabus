<h1>Account settings</h1>

<form method="post" action="{$smarty.server.REQUEST_URI|escape}" class="edit-form">
    <div>
        <div class="field">
            <label class="field-label field-linked" for="emailAddress">University e-mail address: </label>
            <div class="field-control">
                <input type="text" class="text-field vlong" disabled id="emailAddress" value="{$account->emailAddress|escape}">
                {* <a href="">Change your e-mail address of record</a> *}
            </div>
            {foreach item="error" from=$errorMap.emailAddress}<div class="error">{$error}</div>{/foreach}
        </div>
        
        <div class="field" id="name-field">
            <label class="field-label">Your name: </label>
            
            <div class="subfield">
                <label class="field-linked" for="first-name">First <span class="nonvisual-context">name:</span></label>
                <input type="text" class="field-control text-field" id="first-name" name="firstName" value="{$account->firstName|escape}">
            </div>
            
            <div class="subfield">
                <label class="field-linked" for="middle-name">Middle <span class="nonvisual-context">name or initial:</span></label>
                <input type="text" class="field-control text-field" id="middle-name" name="middleName" value="{$account->middleName|escape}">
            </div>
            
            <div class="subfield">
                <label class="field-linked" for="last-name">Last <span class="nonvisual-context">name:</span></label>
                <input type="text" class="field-control text-field" id="last-name" name="lastName" value="{$account->lastName|escape}">
            </div>
            
            {foreach item="error" from=$errorMap.firstName}<div class="error">{$error}</div>{/foreach}
            {foreach item="error" from=$errorMap.middleName}<div class="error">{$error}</div>{/foreach}
            {foreach item="error" from=$errorMap.lastName}<div class="error">{$error}</div>{/foreach}
        </div>
        
{foreach name="exts" item="settingsExtension" from=$settingsExtensionList}
{include file=$settingsExtension->getAccountSettingsTemplate() last=$smarty.foreach.exts.last}
{/foreach}
    </div>
    
    <div class="field">
        {generate_form_post_key}
        <input type="submit" class="command-button" name="command[save]" value="Save settings">
        <a href="{$returnTo|escape}">Cancel</a>
    </div>
</form>
