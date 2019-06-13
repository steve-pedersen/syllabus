<h1>Manage Departments</h1>

<div class="column-container fluid">
    <div class="content">
        <div class="inner">
            <div class="row">
                <div class="table-responsive col-xs-12 col-sm-9 col-md-9 col-lg-9">
                    {if $departments}
                    <form method="post" action="{$smarty.server.REQUEST_URI|escape}">
                        <table class="data">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Subunit</th>
                                    <th style="width:11em;">Actions</th>
                                </tr>
                            </thead>
                        
                            <tbody>
                                {foreach item="department" from=$departments}
                                <tr class="{cycle values=even,odd}">
                                    <th scope="row">{$department->name|escape}</th>
                                    <td>{foreach item='contact' from=$department->contacts}{$contact->displayName|escape}<br>{foreachelse}-{/foreach}</td>
                                    <td>
                                    {if $department->subunit}
                                        {$department->subunit->m_sName}
                                    {else}
                                    -
                                    {/if}
                                    </td>
                                
                                    <td>
                                        <div>
                                            <a class="btn btn-warning btn-xs" href="admin/departments/{$department->id}"><i class="halflings-icon white pencil"></i> Edit</a>
                                            <a class="btn btn-info btn-xs" href="admin/departments/{$department->id}/courses"><i class="halflings-icon white list"></i> View</a>
                                        </div>
                                    </td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                        {generate_form_post_key}
                    </form>
                    {else}
                    <div class="notice">No departments to show.</div>
                    {/if}
                </div>
                
                <div class="manage-add-button col-xs-12 col-sm-3 col-md-3 col-lg-3">
                    <div>    
                        <a class="command-button btn btn-primary btn-block" href="admin/departments/new">Create New Department</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
