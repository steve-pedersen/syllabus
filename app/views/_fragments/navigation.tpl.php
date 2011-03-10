{foreach name=site_nav from=$site_nav item=i}

    
    {if $smarty.foreach.site_nav.first}
    <div id="site_nav_container">
        <div id="site_nav">
            <ul class="inline-block nav">
    {/if}
    
    {if is_null($i.link)}
            <li class="inline-block no-link">{$i.text}</li>
    {else}
            <li class="inline-block">
                <a href="{$i.link}">{$i.text}</a>
                <span class="arrow-point"></span>
                <span class="arrow-border"></span>
            </li>
    {/if}
    
    {if $smarty.foreach.site_nav.last}
            </ul>
            <div id="login_status">
            {if $smarty.session.user_id}
                <em>Logged in as <strong>{$smarty.session.user_fname} {$smarty.session.user_lname}</strong></em>
                <a href="users/edit/{$smarty.session.user_id}">Account Settings</a>
                <a href="logout">Logout</a>
            {else}
                <a href="login">Login</a>
            {/if}
            </div>
        </div>
    </div>
    {/if}

{/foreach}




{foreach name=admin_nav from=$admin_nav item=i}

    
    {if $smarty.foreach.admin_nav.first}
        <div id="admin_nav_container">
            <div id="admin_nav">
            <ul class="nav inline-block">
    {/if}
    
    {if is_null($i.link)}
            <li class="inline-block no-link">{$i.text}</li>
    {else}
            <li class="inline-block">
                <a href="{$i.link}">{$i.text}</a>
                <span class="arrow-point"></span>
                <span class="arrow-border"></span>
            </li>
    {/if}
    
    {if $smarty.foreach.admin_nav.last}
            </ul>
        {if $smarty.session.in_ghost_mode}
            <div id="ghost_status">
            You are currently in Ghost Mode
            <a href="users/unghost?return_url={$smarty.const.CURRENT_URL}">Exit Ghost Mode</a>
            </div>
        {/if}
            </div>
        </div>
    {/if}

{/foreach}
