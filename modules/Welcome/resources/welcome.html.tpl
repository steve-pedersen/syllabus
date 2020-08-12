{if $welcomeText}
<div class="container-fluid bg-dark text-white pt-5 pb-3">
	<div class="container">
		<div class="row px-lg-4 mx-lg-4">
			<!-- <div class="col " style=""></div> -->
			<div class="col text-center welcome-text px-lg-5 mx-lg-5" style="">{$welcomeText}</div>
			<!-- <div class="col " style=""></div> -->
		</div>
	</div>
</div>
{/if}

<div class="container text-center my-5">
	<h1 class="mb-3">Online Syllabus Tool</h1>
	<a class="login-button btn btn-primary btn-lg" href="{$app->baseUrl('login')}">Log into the New Syllabus</a>
</div>

{if $welcomeTextBottomColumn1 || $welcomeTextBottomColumn2}
<div id="welcomeTextBottom" class="container-fluid mt-4 p-xl-5 p-lg-4 p-3 bg-context light" style="background-color:#f4f6f9;">
	<!-- <div class="container"> -->
		<div class="row">
			{if $welcomeTextBottomColumn1}
			<div class="{if $welcomeTextBottomColumn2}col-sm-6 border-right{else}col-12{/if}">
				<div class="container welcome-text-bottom-column-1" id="welcome-text-bottom-column-1">
					{$welcomeTextBottomColumn1}
				</div>
			</div>
			{/if}
			{if $welcomeTextBottomColumn2}
			<div class="col-sm-6">
				<div class="container welcome-text-bottom-column-2" id="welcome-text-bottom-column-2">
					{$welcomeTextBottomColumn2}		
				</div>
			</div>
			{/if}
		</div>
	<!-- </div> -->
</div>
{/if}