{if !$userContext->account}

<div class="welcome-module">
	<div class="jumbotron">
		<h1 class="display-5">Welcome to the Syllabus Application</h1>
		{if $welcomeText}
			<p class="lead">{$welcomeText}</p>
		{else}
			<p class="lead">Create, maintain and share your SF State syllabus here.</p>
		{/if}
		<hr class="my-4">
		<p>Login to get started.</p>
		<a href="{$app->baseUrl('login?returnTo=/home')}" class="btn btn-primary btn-lg">Log In</a>
	</div>
</div>

{else}

<div class="welcome-module">
	<a href="{$app->baseUrl('syllabi')}" class="btn btn-primary">Go To Syllabi</a>
</div>

{/if}