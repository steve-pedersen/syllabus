<h1>Class Data Defaults</h1>

<p>Default values for properties which may not be included in a particular entity.</p>

<form action="" method="post" role="form">
	<div class="form-group">
		<label for="cs-default-email">Email address</label>
		<input type="email" class="form-control" id="cs-default-email" name="cs-default-email" value="{$email}" placeholder="Enter Email">
	</div>
	{generate_form_post_key}
	<button type="submit" class="btn btn-primary" name="command[save]">Submit</button>
	<a href="admin" class="btn btn-link">Cancel</a>
</form>