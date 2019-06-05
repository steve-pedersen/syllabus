{if $headerVars}
<section class="jumbotron at-jumbotron jumbotron-fluid text-left">
  <div class="container-fluid">
  	{if $headerVars['title']}
	    <h1 class="display-5">{$headerVars['title']} 
	    	{if $headerVars['subtitle']} - <small class="text-white"> {$headerVars['subtitle']}</small>{/if}
		</h1>
    {/if}
    {if $headerVars['description']}
    	<p class="lead">{$headerVars['description']}</p>
    {/if}
  </div>
</section>
{/if}