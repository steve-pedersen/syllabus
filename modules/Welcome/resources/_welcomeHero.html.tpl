<section class="jumbotron at-jumbotron text-center">
  <div class="container">
    <h1 class="jumbotron-heading">Welcome to the Syllabus Application</h1>

{if !$userContext->account}
    
    {if $welcomeText}
      <p class="lead">{$welcomeText}</p>
    {else}
      <p class="lead">Create, maintain and share your SF State syllabus here.</p>
    {/if}
    <hr class="my-4">
    <p>Login to get started.</p>
    <a href="{$app->baseUrl('login?returnTo=/home')}" class="btn btn-primary btn-lg">Log In</a>
 
{else}
	<p class="lead">Create, maintain and share your SF State syllabus here.</p>
	<hr>
    <a href="{$app->baseUrl('syllabi')}" class="btn btn-primary">Create New Syllabus</a>
    <a href="{$app->baseUrl('syllabi')}" class="btn btn-secondary">Link with iLearn</a>

{/if} 

  </div>
</section>