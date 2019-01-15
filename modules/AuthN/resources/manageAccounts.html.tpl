<h1>Manage Accounts</h1>

<div class="column-container fluid">
    <div class="content">
        <div class="inner">
            {if $accountList}
            <div class="row">
                <div class="table-responsive col-xs-12 col-sm-9 col-md-9 col-lg-9">
                    <form method="post" action="{$smarty.server.REQUEST_URI|escape}">
	                    <table class="data sticky-header table table-striped">
	                        <thead>
	                            <tr>
	                                <th>Name</th>
	                                <th style="width:8em;">Username</th>
	                                <th style="width:15em;">E-mail address</th>
	                                <th style="width:8em;">Actions</th>
	                            </tr>
	                        </thead>
	                    
	                        <tbody>
	                            {foreach item="account" from=$accountList}
	                            <tr class="">
	                                <td>{$account->lastNameFirst|escape}</td>
	                                <td>{$account->username|escape}</td>
	                                <td>{$account->emailAddress|escape}</td>
	                            
	                                <td>
	                                    <div>
	                                        <a class="simple-button" href="admin/accounts/{$account->id}?returnTo={$smarty.server.REQUEST_URI|escape:url}"><i class="icon-pencil"></i> Edit</a>
	                                    </div>
	
	                                    <div>
	                                        <button type="submit" class="simple-button" name="command[become][{$account->id}]" value="Become"><i class="icon-user"></i> Become</button>
	                                    </div>
	                                </td>
	                            </tr>
	                            {/foreach}
	                        </tbody>
	                    </table>
                        {generate_form_post_key}
                    </form>
            	</div>
                <div class="manage-add-button col-xs-12 col-sm-3 col-md-3 col-lg-3">
                    <div>    
                        <a class="command-button btn btn-primary btn-block" href="admin/accounts/new">Create New Account</a>
                    </div>

                    <div class="sidebar">
                        <div id="account-content-filters" class="inner">
                            <h2>Filters</h2>
    
                            <form method="get" action="{$smarty.server.REQUEST_URI|escape}" role="form">
                                <div>
                                    <div class="form-group">
                                        <label for="manageAccounts-show" class="field-label">Accounts per page</label>
                                    
                                        <select id="manageAccounts-show" class="form-control" name="show">
                                            <option value="10"{if $resultsPerPage == 10} selected{/if}>10</option>
                                            <option value="20"{if $resultsPerPage == 20} selected{/if}>20</option>
                                            <option value="50"{if $resultsPerPage == 50} selected{/if}>50</option>
                                            <option value="100"{if $resultsPerPage == 100} selected{/if}>100</option>
                                            <option value="all"{if $resultsPerPage == all} selected{/if}>All</option>
                                        </select>
                                    </div>
                                
                                    <div class="form-group">
                                        <label for="manageAccounts-search" class="field-label">Search</label>
                                        <input type="text" name="q" class="form-control" id="manageAccounts-search" value="{$searchQuery|escape}">
                                    </div>
                                
                                    <div class="form-group controls">
                                        <button class="btn btn-default" type="submit" class="command-button btn btn-default" name="b" value="Filter">Filter</button>
                                        <a{if $hasFilters} href="admin/accounts"{/if} class="command-button btn btn-link alt{if !$hasFilters} disabled{/if}">Clear filters</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
        
        {include file=$pagination.template}
        {else}
        <div class="notice">No accounts to show.</div>
        {/if}
    </div>
</div>

