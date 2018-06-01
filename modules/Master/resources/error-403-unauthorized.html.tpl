{if !$viewer}
<h1>Login for Access</h1>
<p>
{if $didLogin}
    You have been automatically logged out due to your session expiring. To
    access the requested resource, you must <a href="login{if $selectedIdentityProvider}?idp={$selectedIdentityProvider|escape}{/if}">login back in</a>.
{else}
    The requested resource requires permissions that you do not currently have.
    To access the requested resource, you must <a href="login{if $selectedIdentityProvider}?idp={$selectedIdentityProvider|escape}{/if}">login</a>.
{/if}

{if !$soleProvider}{include file="partial:_wayf"}{/if}
</p>
{else}
<h1>Unauthorized</h1>
<p>
    Sorry, you do not have permission to access the requested resource. If this
    is a mistake, please contact <a href="help/support">our support team</a>.
</p>
{/if}