<div class="row">
    {if $searchQuery}
    <div class="col">
        <p class="minor detail badge badge-{if $totalAccounts > 0}success{else}danger{/if}">Found {$totalAccounts} matching your search</p>
    </div>
    {/if}
</div>

<form method="get" action="{$smarty.server.REQUEST_URI|escape}" class="form-horizontal form-inline my-4">
    <div class="col">
        <div class="form-group">
            <label for="account-search" class="">Search accounts: </label>
            <input class="form-control mx-sm-3 " type="text" id="account-search" name="sq" value="{$searchQuery|escape}">
            <label class="sr-only" for="account-search-button">Search</label>
            <input class="btn btn-info" type="submit" id="account-search-button" name="btn" value="Search">
            <div class="search-container"></div>
            {if $searchQuery}<a href="{$organization->routeName}/{$organization->id}/users?sort={$sortBy|escape}&amp;dir={$dir|escape}&amp;limit={$limit|escape}" class="ml-3"> Remove search</a>{/if}
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
            <li {if $page.current} class="page-item active" {elseif $page.disabled} class="page-item disabled"{/if}>
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
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <td scope="col">Name</td>
                        <td scope="col">E-mail address</td>
                        <td scope="col">Username</td>
                        <td scope="col">Last login</td>
                        <td scope="col">Roles</td>
                        <td scope="col">Options</td>
                    </tr>
                </thead>
                
                <tbody>

            {foreach $accountList as $account}
                    <tr class="">
                        <td scope="row"><a class="text-capitalize" href="{$organization->routeName}/{$organization->id}/users/{$account->id}?returnTo={$smarty.server.REQUEST_URI|escape|escape}">{$account->lastName|escape}, {$account->firstName|escape} {$account->middleName|escape}</a></td>
                        <td>{$account->emailAddress}</td>
                        <td>{$account->username|default:'<span class="detail">n/a</a>'}</td>
                        <td style="font-size:9pt;">{if $account->lastLoginDate}{$account->lastLoginDate->format('M j, Y')}{else}<span class="detail">never</span>{/if}</td>
                        <td>
                            {foreach $organization->getUserRoles($account, true) as $role => $displayName}
                                {$displayName}{if !$displayName@last}, {/if}
                            {/foreach}
                        </td>
                        <td>
                            <a class="btn btn-info btn-sm" href="{$organization->routeName}/{$organization->id}/users/{$account->id}?returnTo={$smarty.server.REQUEST_URI|escape|escape}">Edit</a>
                        </td>
                    </tr>
            {foreachelse}
                    <tr>
                        <td scope="row" colspan="6" class="notice">
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
            <li {if $page.current} class="page-item active" {elseif $page.disabled} class="page-item disabled"{/if}>
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
