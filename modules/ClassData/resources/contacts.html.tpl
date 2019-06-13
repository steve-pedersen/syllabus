<h1>Manage Department Contacts</h1>

<div class="column-container fluid">
    <div class="content">
        <div class="inner">
            <div class="row">
                <div class="table-responsive col-xs-12 col-sm-9 col-md-9 col-lg-9">
                    {if $contacts}
                    <form method="post" action="{$smarty.server.REQUEST_URI|escape}">
                        <table class="data">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th style="width:12em;">Actions</th>
                                </tr>
                            </thead>
                        
                            <tbody>
                                {foreach item="contact" from=$contacts}
                                <tr class="{cycle values=even,odd}">
                                    <td>{$contact->id|escape}</td>
                                    <td>{$contact->firstName|escape}</td>
                                    <td>{$contact->lastName|escape}</td>
                                    <td>{$contact->emailAddress|escape}</td>
                                    <td>{foreach item='department' from=$contact->departments}{$department->name}<br>{foreachelse}-{/foreach}</td>
                                
                                    <td>
                                        <div>
                                            <a class="btn btn-warning btn-xs" href="admin/contacts/{$contact->id}"><i class="halflings-icon white pencil"></i> Edit</a>
                                            <a class="btn btn-danger btn-xs" href="admin/contacts/{$contact->id}/delete"><i class="halflings-icon white remove"></i> Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                        {generate_form_post_key}
                    </form>
                    {else}
                    <div class="notice">No contacts to show.</div>
                    {/if}
                </div>
                
                <div class="manage-add-button col-xs-12 col-sm-3 col-md-3 col-lg-3">
                    <div>    
                        <a class="command-button btn btn-primary btn-block" href="admin/contacts/new">Create New Contact</a>
                    </div>
                    <h3>Import</h3>
                    <form action="admin/contacts/import" method="post">
                    <div class="input-group">
                        <input type="text" class="form-control" id="import-id" name="importId" placeholder="Import from SFSU ID" alt="Import user with SFSU ID">
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-default" name="command[import]">Import</button>
                        </div>
                    </div>
                    {generate_form_post_key}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
