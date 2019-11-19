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
    Sorry, you do not have permission to access the requested resource. <br>
    If this is a mistake, or you wish to request special access to create syllabi, 
    please contact our support team at 
    <a href="mailto:ilearn@sfsu.edu?subject=Syllabus Access Request&body=(Please include your department and position at SF State)">
    ilearn@sfsu.edu</a>.
</p>
{/if}