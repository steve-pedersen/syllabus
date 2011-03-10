<h1>Manage Repository Items</h1>


{foreach from=$repository name="tabs" item=m key=k}
    {if $smarty.foreach.tabs.first}<ul class="tabs" id="repository_tabs">{/if}
    <li id="tab-{$k}"><a href="{$smarty.const.CURRENT_URL}#tabcontent-{$k}">{$m.name}</a></li>
    {if $smarty.foreach.tabs.last}</ul>{/if}
{/foreach}


{foreach from=$repository item=m key=k}
    
    <div id="tabcontent-{$k}">
        <h2 class="tab-header">{$m.name}</h2>
        <a href="repository/create/{$k}" class="icon"><span class="icon add inline-block"></span> Create a new <strong>{$m.name}</strong> repository item</a>
        {if ($m.items|@count)}
            {include file="modules/$k/repository_browse.tpl.php"}
        {else}
            <div class="message info">
            There are no repository items for this module
            </div>
        {/if}
    </div>

{/foreach}