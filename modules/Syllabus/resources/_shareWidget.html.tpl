{assign var=shared value=false}
{if $syllabus->getShareLevel() == 'all'}
	{assign var=shared value=true}
{/if}

<div class="btn-group ml-1" role="group" aria-label="Share and copy url">
	
	<form action="syllabus/{$syllabus->id}/share" method="post" class="form">
	{if !$shared}
		<button name="command[share]" type="submit" class="btn btn-outline-primary">
			Share
		</button>
	{else}
		<button name="command[unshare]" type="submit" class="btn btn-outline-white">
			Unshare
		</button>
	{/if}
	{generate_form_post_key}
	</form>

	<!-- <div class="position-relative"> -->
	<div class="dropdown-menu share-dropdown pb-0 pt-1" aria-labelledby="copyWidget" id="shareWidget{$syllabus->id}" style="min-width:19vh;">

		<div class="row px-3 pt-3 pb-1">
			<div class="col px-3">
			<h6>
				Share this syllabus
				<span type="buttons" data-placement="bottom" class="btn-link" data-toggle="tooltip" data-display="static"  data-html="true" title="Your syllabus is available for viewing. <strong>Only enrolled students</strong> can view your syllabus with the link below.">
			  	<i class="far fa-question-circle ml-2"></i>
				</span>
			</h6>

			<label class="" for="clickToCopy">Copy URL to clipboard:</label>
            <div class="input-group mb-0">
              	<input form="" class="form-control" type="text" value="{$syllabus->viewUrl}" id="clickToCopy{$syllabus->id}" />
              	<div class="input-group-append">
                	<button class="btn btn-outline-white" form="" type="button" id="copyBtn{$syllabus->id}"><i class="far fa-copy"></i>
                	</button>
              	</div>
            </div>  
			</div>

		</div>
		<div class="d-block mb-1 font-w700 text-success text-center" id="copiedAlert{$syllabus->id}" style="opacity:0;">
			Link copied!
		</div>
		<div class="row px-3 mt-1">
			<div class="col-6 px-0 pb-0 border-top border-right text-center">
				<!-- <a href="#" class="d-block py-2 widget-link font-w700">Test 1</a> -->
			</div>
			<div class="col-6 px-0 pb-0 border-top text-center">
				<a href="syllabus/{$syllabus->id}/share" class="d-block py-2 widget-link font-w700">Advanced</a>
			</div>
		</div>

	</div>
	<!-- </div> -->

	<button {if !$shared}disabled{/if} id="copyWidget" type="button" class="btn btn-{if !$shared}outline-dark{else}dark{/if}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<i class="fas fa-caret-down"></i>
	</button>

</div>

<!-- <script>
    function copyToClipboard (e) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($('#viewUrl').val()).select();
        document.execCommand("copy");
        $temp.remove();
        $('#copiedAlert').show().hide(3500);
    }

</script> -->


<!--   <form class="px-4 py-3">
    <div class="form-group">
      <label for="exampleDropdownFormEmail1">Email address</label>
      <input type="email" class="form-control" id="exampleDropdownFormEmail1" placeholder="email@example.com">
    </div>
    <div class="form-group">
      <label for="exampleDropdownFormPassword1">Password</label>
      <input type="password" class="form-control" id="exampleDropdownFormPassword1" placeholder="Password">
    </div>
    <div class="form-group">
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="dropdownCheck">
        <label class="form-check-label" for="dropdownCheck">
          Remember me
        </label>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Sign in</button>
  </form>
  <div class="dropdown-divider"></div>
  <a class="dropdown-item" href="#">New around here? Sign up</a>
  <a class="dropdown-item" href="#">Forgot password?</a> -->