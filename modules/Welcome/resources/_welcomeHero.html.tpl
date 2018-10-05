<section class="jumbotron at-jumbotron text-center">
  <div class="container">
    

{if !$userContext->account}
<h1 class="jumbotron-heading">Welcome to the Syllabus Tool</h1>
 
    <p class="lead">Create, maintain and share your SF State syllabus here.</p>
    <hr class="my-4">
    <p class="login">Login to get started.</p>
    <a href="{$app->baseUrl('login?returnTo=/home')}" class="btn btn-primary btn-lg">Log In</a>
 
{else}

<h1 class="jumbotron-heading text-left">Available templates</h1>

<hr>
<!--  <p class="lead">Create, maintain and share your SF State syllabus here.</p>
  <hr>
    <a href="{$app->baseUrl('syllabi')}" class="btn btn-primary">Create New Syllabus</a>
    <a href="{$app->baseUrl('syllabi')}" class="btn btn-secondary">Link with iLearn</a> -->



  <div class="card-group mb-1 px-1" style="max-height:220px;">

    <div class="card mx-4" style="border: 1px solid #d5f1ef;">
      <a href="#" class="text-center btn btn-block h-100">
      <div class="mx-auto pt-3"><i class="mb-3 fas fa-plus-circle fa-2x"></i><br>Blank<br>Template</div>
      </a>
    </div>

    <div class="card mx-4" style="border: 1px solid #d5f1ef;">
      <a href="#" class="text-center align-text-middle text-info ">
      <div class="card-img-container"><img class="card-img" src="assets/images/thumb02.png" class="img-responsive"></div>
      </a>
    </div>
    <div class="card mx-4" style="border: 1px solid #d5f1ef;">
      <a href="#" class="text-center align-text-middle text-info ">
      <div class="card-img-container"><img class="card-img" src="assets/images/thumb03.png" class="img-responsive"></div>
      </a>
    </div>
    <div class="card mx-4" style="border: 1px solid #d5f1ef;">
      <a href="#" class="text-center align-text-middle text-info ">
      <div class="card-img-container"><img class="card-img" src="assets/images/thumb04.png" class="img-responsive"></div>
      </a>
    </div>
    <div class="card mx-4" style="border: 1px solid #d5f1ef;">
      <a href="#" class="text-center align-text-middle text-info ">
      <div class="card-img-container"><img class="card-img" src="assets/images/thumb01.png" class="img-responsive"></div>
      </a>
    </div> 
    <div class="card mx-4" style="border: 1px solid #d5f1ef;">
      <a href="#" class="text-center align-text-middle text-info ">
      <div class="card-img-container"><img class="card-img" src="assets/images/thumb05.png" class="img-responsive"></div>
      </a>
    </div>
    <div class="card mx-4" style="border: 1px solid #d5f1ef;">
      <a href="#" class="text-center align-text-middle text-info ">
      <div class="card-img-container"><img class="card-img" src="assets/images/thumb06.png" class="img-responsive"></div>
      </a>
    </div>


  </div>

<!-- <hr> -->

{/if} 

  </div>
</section>