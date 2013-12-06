<h1>Create Admin User for Testing</h1>

<form action="{$smarty.server.REQUEST_URI}" method="post">
	<input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
    
    <div class="message warn">
    <p>
    This will create a set of users for testing purposes. The users should be able to log in via Shibboleth on the SFSU Test IDP.
    </p>
		<h3>Admin User</h3>
		<ul>
			<li>T10000001</li>
		</ul>
		
		<h3>Normal Users</h3>
		<ul>
			<li>T20000001</li>
			<li>T30000001</li>
			<li>T40000001</li>
			<li>T50000001</li>
			<li>T60000001</li>
		</ul>
	
    
    <p>Are you sure you want to continue?</p>

    <div class="save_row">
        <input type="submit" name="command[createUsers]" value="Create Users" class="button" />
    </div>
	</div>
    
</form>