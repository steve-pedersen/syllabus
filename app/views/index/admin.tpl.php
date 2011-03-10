<h1>Admin Dashboard</h1>


{if $show_users_link}
<a href="users" class="button_large">
    <span class="icon users inline-block"></span>
    Users
</a>
{/if}

{if $show_repository_link}
<a href="repository" class="button_large">
    <span class="icon repository inline-block"></span>
    Repository
</a>
{/if}

{if $show_blog_link}
<a href="blog/manage" class="button_large">
    <span class="icon edit inline-block"></span>
    Blog
</a>
{/if}

{if $show_system_link}
<a href="system" class="button_large">
    <span class="icon system inline-block"></span>
    System
</a>
{/if}

