{if $singleSignOut}
    <h1>Logged out.</h1>
    <p>You have successfully logged out. Return to <a href="{$app->baseUrl('')}">the front page</a>.</p>
{else}
    <h1>Please close your browser.</h1>
    <p>To complete your logout and for maximum security, we strongly recommend you close
    your browser. If you leave your browser open, another person using this computer may be
    able to gain access to your other accounts you've logged in to recently.</p>
{/if}