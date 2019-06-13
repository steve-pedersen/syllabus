<h1>Delete Contact</h1>

<p>Are you sure you want to delete {$contact->firstName} {$contact->lastName}?</p>

<form method="post" action="">
    <div class="controls">
        {generate_form_post_key}
        <input type="submit" class="command-button btn btn-danger" name="command[confirm]" value="Delete">
        <a href="admin/contacts/{$contact->id}" class="btn btn-default">Cancel</a>
    </div>
</form>