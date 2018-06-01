<a href="admin">Administrate</a> &gt; <a href="{$returnTo|escape|default:"admin/accounts"}">Accounts</a>
<h1>{if !$account->inDataSource}New account{else}Edit account: {$account->displayName|escape}{/if}.</h1>

{include file="$resourceDirectory/_settings.html.tpl" admin=true}
