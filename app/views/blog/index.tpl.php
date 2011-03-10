<h1><em>Syllabus</em> blog archive</h1>

{foreach from=$posts item=p name="posts"}
    {if $smarty.foreach.posts.first}
    <table cellpadding="0" cellspacing="0" summary="Listing of all blog postings and links to view the individual posts">
        <thead>
        <tr>
            <th scope="col" style="width: 100px;">Post Date</th>
            <th scope="col">Post Title</th>
        </tr>
        </thead>
        <tbody>
    {/if}
  
        <tr>
            <td>{$p.post_publish_date|date_format:'%D'}</td>
            <td><a href="blog/view/{$p.post_id}">{$p.post_title}</a></td>
        </tr>

    {if $smarty.foreach.posts.last}
        </tbody>
    </table>    
    {/if}
{foreachelse}

<div class="messages error">There are no blog postings.</div>

{/foreach}
