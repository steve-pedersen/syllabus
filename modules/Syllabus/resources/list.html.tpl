
<div class="welcome-module">
  <div class="album py-3 bg-light">
    <div class="container">
      <div class="row">    
        <div class="card-group mb-5 px-3">
          <div class="card mx-3">
          	<a href="syllabus/entity/999" class="text-center align-text-middle text-info h-100">
              <div class="card-body h-100">
		            <i class="h-50 mt-5 mb-1 fas fa-plus-circle fa-7x"></i>
		            <p class="h-50 text-center align-bottom text-info"><strong>Start a new syllabus from scratch</strong></p>
              </div>
            </a>
          </div>
	  {foreach from=$urls item=url key=i}
	    {if (($url@index != 0) && (($url@index + 1) % 3 == 0))}
		    </div>
		    <div class="card-group mb-5 px-3">
	    {/if}
          <div class="card mx-3" >
          	<a href="syllabus/entity/999/view/{$i}" class="text-center align-text-middle text-info h-100">
            	<div class="card-img-container">
                <img class="card-img-top" src="{$url}" class="img-fluid">
            	</div>
        	  </a>
            <div class="card-body">
              <p class="card-text"><strong>Syllabus title</strong><br>Description...</p>
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                  <a href="syllabus/entity/999/view/{$i}" class="btn btn-outline-info">View</a>
                  <button type="button" class="btn btn-outline-warning">Edit</button>
                </div>
              </div>
            </div>
    				<div class="card-footer text-muted">
    					<small class="text-muted">Last edited - some date, 2018</small>
    				</div>
          </div>
    {/foreach}
        </div>     
      </div>
    </div>
  </div>
</div>

{if $messages}
<div class="alert alert-danger">
  {foreach from=$messages item=message}
	  <span>{$message}</span>
  {/foreach}
</div>
{/if}