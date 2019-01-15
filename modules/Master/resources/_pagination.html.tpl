{if $pagination.pageCount}
<div>
    <ul class="pagination">
        {if $paginationNextPrevious !== false}
        <li class="previous special">{l href=$pagination.previous text="&lt;"}</li>
        {/if}
        
        {if $paginationFirstLast !== false && $pagination.pageRange[0] > 1}
        <li class="first special">{l href=$pagination.first text="1"}</li>
        {if $pagination.pageRange[0] > 2}<li><span>&hellip;</span></li>{/if}
        {/if}
        
        {foreach key="pageNum" item="pageHref" from=$pagination.pages}
        <li{if !$pageHref} class="current"{/if}>
            {l href=$pageHref text=$pageNum}
        </li>
        {/foreach}
        
        {if $paginationFirstLast !== false && $pagination.pageRange[1] != $pagination.pageCount}
        {if $pagination.pageCount-1 > $pagination.pageRange[1]}<li><span>&hellip;</span></li>{/if}
        <li class="last special">{l href=$pagination.last text=$pagination.pageCount}</li>
        {/if}
        
        {if $paginationNextPrevious !== false}
        <li class="next special">{l href=$pagination.next text="&gt;"}</li>
        {/if}
    </ul>
</div>
{/if}
