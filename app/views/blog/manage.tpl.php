<h1>Manage blog posts</h1>

<a href="blog/create" class="icon"><span class="icon inline-block add"></span> Create a new Blog Post</a>

{foreach name=posts from=$posts item=p}

    {if $smarty.foreach.posts.first}
    <form action="{$smarty.const.CURRENT_URL}" method="post">
	{$smarty.const.SUBMIT_TOKEN_HTML}
    <input type="hidden" name="return_url" value="blog/manage" />

    <table summary="Blog posts and controls to manage them" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
            <th scope="col" style="width: 15px;"><label for="posts" class="hideJS">Select all Posts</label><input type="checkbox" id="posts" class="check-all" /></th>
            <th scope="col" style="width: 25px;"><span class="icon"><span class="icon inline-block sticky_on"><span><span class="text">Sticky</span></span></th>
            <th scope="col" style="width: 25px;"><span class="icon"><span class="icon inline-block important_on"><span><span class="text">Important</span></span></th>
            <th scope="col" style="width: 100px;">Publish Date</th>
            <th scope="col" style="width: 600px;">Post Title</th>
            <th scope="col">Posted by</th>
        </thead>
        <tbody>
    {/if}
    
        <tr>
            <td>
                <input type="checkbox" name="posts[]" id="post-{$p.post_id}" value="{$p.post_id}" class="posts" />
                <label class="hideJS" for="post-{$p.post_id}">Mark this post for bulk action</label>
            </td>
            <td>
                {if $p.post_sticky == 1}
                    {assign var='sticky_class' value='sticky_on'}
                    {assign var='sticky_text' value='This post is sticky'}
                {else}
                    {assign var='sticky_class' value='sticky_off'}
                    {assign var='sticky_text' value='This post is not sticky'}
                {/if}
                <span class="icon"><span class="icon {$sticky_class} inline-block"></span><span class="text">{$sticky_text}</span></span>
            </td>
            <td>
                {if $p.post_important == 1}
                    {assign var='important_class' value='important_on'}
                    {assign var='important_text' value='This post is marked as important'}
                {else}
                    {assign var='important_class' value='important_off'}
                    {assign var='important_text' value='This post is not marked as important'}
                {/if}
                <span class="icon"><span class="icon {$important_class} inline-block"></span><span class="text">{$important_text}</span></span>
            </td>
            <td>{$p.post_publish_date|date_format:'%D'}</td>
            <th scope="row"><a href="blog/edit/{$p.post_id}">{$p.post_title}</a></th>
            <td>{$p.user_fname} {$p.user_lname}</td>
    
    {if $smarty.foreach.posts.last}
        </tbody>
    </table>
    
    <div class="save_row">
        <label for="bulk_action">With Selected</label>
        <select name="bulk_action" id="bulk_action" style="width: 200px;">
            <option value="" disabled="disabled" selected="selected">Select action</option>
            <option value="sticky">Make Sticky</option>
            <option value="unsticky">Remove Sticky</option>
            <option value="important">Make Important</option>
            <option value="unimportant">Remove Important</option>
            <option value="delete">Delete</option>
        </select>
        <input type="submit" name="command[changeStatus]" class="button" value="Submit" />
    </div>
    {/if}

{foreachelse}

<div class="message error">There are currently no blog posts in the system</div>

{/foreach}