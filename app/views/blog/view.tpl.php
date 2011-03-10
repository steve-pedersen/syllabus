<div class="blog_post">
    <h1 class="blog_header">{$post.post_title}</h1>
    
    <p class="blog_post_info">
        Posted by <strong>{$post.user_fname} {$post.user_lname}</strong> on {$post.post_publish_date|date_format:"%A, %B %e, %Y"}
    <p>
    <p>
        {$post.post_text}
    </p>
</div>
