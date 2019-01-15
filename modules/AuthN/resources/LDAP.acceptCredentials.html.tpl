<h1>Login with your {$userLabel|escape}.</h1>
<p>
    Please login with your {$identityProvider->getDisplayName()}
    credentials.
</p>
<form method="post" action="{$postAction|escape}" class="prominent data">
    <div class="field">
        <label for="ldap-username" class="field-label field-linked">{$userLabel|escape|default:"Username"}:</label>
        <input type="text" class="field-control text-field" id="ldap-username" name="username">
    </div>
    <div class="field">
        <label for="ldap-password" class="field-label field-linked">Password:</label>
        <input type="password" class="field-control text-field" id="ldap-password" name="password">
    </div>
    <div class="field">
        <input class="command-button" type="submit" name="command[login]" value="Login">
    </div>
</form>
