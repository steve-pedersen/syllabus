<h1>Wrong Username and/or Password</h1>
<p>
    The {$identityProvider->getDisplayName()|escape} was not able to find an
    account with the specified username and password. Please try
    <a href="login{if $selectedIdentityProvider}?idp={$selectedIdentityProvider|escape}{/if}">logging in again</a>.
</p>

{if !$soleProvider}{include file="partial:_wayf"}{/if}
