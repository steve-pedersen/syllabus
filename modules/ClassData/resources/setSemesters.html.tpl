<h1>Set active and visible semesters</h1>
<p>
    This setting controls what semesters the importer pays attention to. The
    web service only pays attentions to the changes made for courses and 
    enrollments in the semesters listed here. A semester takes the form
    YYYYS where S is 1 for Winter, 2 for Spring, 3 for Summer, and 4 for
    Fall.
</p>

<form method="post" action="">
    <div class="field">
        <label for="set-semesters" class="field-label">Semesters (comma-separated values of form <samp>YYYYS</samp>)</label>
        <input type="text" id="set-semesters" class="text-field form-control" name="semesters" value="{$semesters|escape}">
    </div>
    <div class="controls">
        {generate_form_post_key}
        <input type="submit" class="command-button btn btn-primary" name="command[set]" value="Update">
    </div>
</form>
