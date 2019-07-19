<div class="row">
    <div class="col">
        <h1 class="display-4">Accounts</h1>
    </div>
    {if $searchQuery}
    <div class="col">
        <p class="minor detail pull-right badge">Found {$totalAccounts} matching your search</p>
    </div>
    {/if}
</div>

<form method="get" action="{$smarty.server.REQUEST_URI|escape}" class="form-horizontal form-inline my-4">
<!-- <div class="form-row"> -->
    <div class="col">
        <div class="form-group">
            <label for="account-search" class="">Search accounts: </label>
            <input class="account-autocomplete form-control mx-sm-3 " type="text" id="account-search" name="sq" value="{$searchQuery|escape}">
            <label class="sr-only" for="account-search-button">Search</label>
            <input class="btn btn-info" type="submit" id="account-search-button" name="btn" value="Search">
            <div class="search-container"></div>
            {if $searchQuery}<a href="admin/accounts?sort={$sortBy|escape}&amp;dir={$dir|escape}&amp;limit={$limit|escape}">Remove search</a>{/if}
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="account-limit" class="">Accounts per page: </label>
            <select class="fancy-select-box form-control mx-sm-3 " id="account-limit" name="limit">
                <option value="20"{if $limit==20} selected="selected"{/if}>20</option>
                <option value="50"{if $limit==50} selected="selected"{/if}>50</option>
                <option value="100"{if $limit==100} selected="selected"{/if}>100</option>
                <option value="99999"{if $limit==99999} selected="selected"{/if}>unlimited</option>
            </select>
            <label class="sr-only" for="update-account-limit">Update</label>
            <input class="btn btn-info" type="submit" id="update-account-limit" name="btn" value="Update">
        </div>
    </div>
<!-- </div> -->
</form>



{if $pageCount > 1}
    <nav>
        <ul class="pagination">
        {foreach item="page" from=$pagesAroundCurrent}
            {if $page.current || $page.disabled}
                {assign var="unlink" value=true}
            {else}
                {assign var="unlink" value=false}
            {/if}
            <li{if $page.current} class="active"{elseif $page.disabled} class="disabled"{/if}>
                {if $page.separator}
                <span>
                    <span aria-hidden="true">&hellip;</span>
                </span>
                {else}
                <span>{l text=$page.display href=$page.href unlink=$unlink class="page-link"}<span class="sr-only">(current)</span></span>
                {/if}
            </li>
        {/foreach}
      </ul>
    </nav>
{/if}

<form method="post" action="{$smarty.server.REQUEST_URI|escape}">
    <div class="row">
        <div class="col">
            <table class="table table-bordered account-table">
                <thead class="thead-dark">
                    <tr>
                        <td ><a href="admin/accounts?sort=name&amp;dir={if $sortBy=="name"}{$oppositeDir}{else}asc{/if}&amp;limit={$limit|escape}&amp;sq={$searchQuery|escape}">Name</a></td>
                        <td ><a href="admin/accounts?sort=email&amp;dir={if $sortBy=="email"}{$oppositeDir}{else}asc{/if}&amp;limit={$limit|escape}&amp;sq={$searchQuery|escape}">E-mail address</td>
                        <td ><a href="admin/accounts?sort=uni&amp;dir={if $sortBy=="uni"}{$oppositeDir}{else}asc{/if}&amp;limit={$limit|escape}&amp;sq={$searchQuery|escape}">Username</td>
                        <td ><a href="admin/accounts?sort=login&amp;dir={if $sortBy=="login"}{$oppositeDir}{else}asc{/if}&amp;limit={$limit|escape}&amp;sq={$searchQuery|escape}">Last login</td>
                        <td>Status</td>
                        <td>Options</td>
                    </tr>
                </thead>
                
                <tbody>
            {foreach item="account" from=$accountList}
                    <tr class="{cycle values='even,odd'}">
                        <td><a class="text-capitalize" href="admin/accounts/{$account->id}?returnTo={$smarty.server.REQUEST_URI|escape|escape}">{$account->lastName|escape}, {$account->firstName|escape} {$account->middleName|escape}</a></td>
                        <td>{$account->emailAddress|escape}</td>
                        <td>{$account->username|escape|default:'<span class="detail">n/a</a>'}</td>
                        <td style="font-size:0.9rem;">{if $account->lastLoginDate}{$account->lastLoginDate->format('M j, Y')}{else}<span class="detail">never</span>{/if}</td>
                        <td>{if $account->isActive}Active{else}Inactive{/if}</td>
                        <td>
                            <a class="btn btn-info btn-sm" href="admin/accounts/{$account->id}?returnTo={$smarty.server.REQUEST_URI|escape|escape}">Edit</a>
                            <input class="btn btn-primary btn-sm" type="submit" name="command[become][{$account->id}]" value="Become" title="Switch to account {$account->displayName}">
                        </td>
                    </tr>
            {foreachelse}
                    <tr>
                        <td colspan="6" class="notice">
                            No accounts match your search criteria.
                        </td>
                    </tr>
            {/foreach}
                </tbody>
            </table>
            <div>{generate_form_post_key}</div>
        </div>
    </div>
</form>

{if $pageCount > 1}

    <nav>
      <ul class="pagination">
        {foreach item="page" from=$pagesAroundCurrent}
        
            {if $page.current || $page.disabled}
                {assign var="unlink" value=true}
            {else}
                {assign var="unlink" value=false}
            {/if}
            <li{if $page.current} class="active"{elseif $page.disabled} class="disabled"{/if}>
                {if $page.separator}
                <span>
                    <span aria-hidden="true">&hellip;</span>
                </span>
                {else}
                <span>{l text=$page.display href=$page.href unlink=$unlink class="page-link"}<span class="sr-only">(current)</span></span>
                {/if}
            </li>
        {/foreach}
      </ul>
    </nav>

{/if}

<br><hr>
<div class="new-account">
    <a href="admin/accounts/new" class="btn btn-success"><span class="glyphicon glyphicon-plus"> </span> Add new account</a>
</div>

