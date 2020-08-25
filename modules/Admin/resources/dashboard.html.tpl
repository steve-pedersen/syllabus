<div class="col">
<h1>Admin Dashboard</h1>

{foreach item="section" from=$sectionList}
    {if $section.template}
        {include file=$section.template}
    {else}
    <div class="row mb-4">
        <div class="col card px-0">
            <h2 class="card-header">{$section.name|escape}</h2>
            <ul class="list-group list-group-flush">
            {foreach item="item" from=$section.itemList}
                <li class="list-group-item">
                    {if $item.allowHtml}
                        {$item.text}
                    {else}
                        {l href=$item.href text=$item.text}
                    {/if}
                </li>
            {/foreach}
            </ul>
        </div>
    </div>
    {/if}
{/foreach}
</div>