<h1 class="form_header">{$page_header}</h1>

<form action="{$smarty.const.CURRENT_URL}" method="post">
	<input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
    <input type="hidden" name="post_id" value="{$post.post_id}" />
    <input type="hidden" name="return_url" value="{$return_url}" />
    
    <div class="label"><label for="post_title">Post Title</label></div>
    <div class="input">
        <input type="text" name="post_title" id="post_title" maxlength="100" value="{$post.post_title}" style="width: 300px;" />
        <span class="form_note" style="margin-left: 7px;">Max 100 characters</span>
    </div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="post_text">Post Text</label></div>
    <div class="input"><textarea name="post_text" id="post_text" class="make_ckeditor" rows="*" cols="*" style="width: 400px; height: 100px;">{$post.post_text}</textarea></div>
    <div style="clear: both;"></div>
    
    <div class="label"><label for="post_publish_date">Publish Date</label></div>
    <div class="input">
        <input type="text" name="post_publish_date" id="post_publish_date" value="{$post.post_publish_date|date_format:"%D"}" style="width: 100px;" />
        <span class="form_note" style="margin-left: 7px;">
        This marks the date the post will become visible to public.  If no date is entered, the post will be published immediately.
        The date will also appear on the post and will be used for date sorting.
        </span>
    </div>
    <div style="clear: both;"></div>
    
    <div class="save_row">
        <div class="label">&nbsp;</div>
        <div class="input">
            <input type="submit" name="command[{$command}]" class="button" value="Save Post" />
            <a href="blog/manage" class="cancel_link">Cancel</a>
        </div>
        <div style="clear: both;"></div>
    </div>
</form>
