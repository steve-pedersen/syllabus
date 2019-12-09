<div class="container-fluid">
	<h1>Email Settings</h1>
	<form method="post" action="{$smarty.server.REQUEST_URI}">
		<div class="row email-row testing-row mt-5 border-top py-4 bg-light">
			<div class="col-lg-7 col-xs-12">
			<h3 class="">Debug Testing Mode</h3>
				<p class="alert alert-info"><strong>Note, this is most likely for AT use only.</strong> Turning on testing will make it so that ALL email will be sent only to the "Debug testing address". If no testing address is specified, but testing is turned on, <u>email will fail to send to anyone</u>.</p>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 d-flex">
				<div class="form-group testingOnly ml-auto">
					<label for="testingOnly">Turn Testing On</label><br>
					<input type="checkbox"  name="testingOnly" id="testingOnly" value="{if $testingOnly}1{/if}" {if $testingOnly}checked aria-checked="true"{/if} />						
				</div>
			</div>
			<div class="col-lg-3 col-md-10 col-sm-10 col-sm-10 ">
				<div class="form-group">
					<label for="testAddress">Debug testing address</label>
					<input type="email" class="form-control" name="testAddress" id="testAddress" value="{$testAddress}" placeholder="e.g. testaddress@gmail.com" />				
				</div>
			</div>
	
		</div>
	<div class="row">
			<div class="col-12">
				<div class="form-group">
					<label for="defaultAddress">Default email address</label>
					<input type="email" class="form-control" name="defaultAddress" id="defaultAddress" value="{$defaultAddress}" placeholder="ilearn@sfsu.edu..." />				
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="form-group">
					<label for="signature">Email Signature</label>
					<textarea name="signature" id="signature" class="wysiwyg wysiwyg-syllabus-full" rows="5" placeholder="  ---<br>Department Name">{$signature}</textarea>		
				</div>
			</div>
		</div>

		<div class="controls">
			<button type="submit" name="command[save]" class="btn btn-primary">Save</button>
			<a href="admin" class="btn btn-default pull-right">Cancel</a>
		</div>
		{generate_form_post_key}
	</form>
</div>