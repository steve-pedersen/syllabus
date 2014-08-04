
<h1>Manually update database</h1>


<form action="{$smarty.server.REQUEST_URI}" method="post">
    <input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
    
    <input type="hidden" name="return_url" value="{$return_url}" />
    
    <div class="save_row">
        <input type="submit" name="command[systemUpdate]" value="Class Data" class="button" />

    </div>
    </div>
</form>

<form action="{$smarty.server.REQUEST_URI}" method="post">
	<input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
    
    <input type="hidden" name="return_url" value="{$return_url}" />
    Enter valid semesters separated by commas: <input type="text" name="semesters_string" value="{$default}"><br>
    

    <div class="message warn">
    <p>
    Updating the system will set all the semesters entered into the database. Any ids not in the string will be deleted. Leave textfield empty to delete all ids.
    </p>
    <p>Are you sure you want to continue?</p>

    <div class="save_row">
        <input type="submit" name="command[setSemesters]" value="Set Semesters" class="button" />

    </div>
    </div>
</form>

<!--print out table before fetching from SIMS-->
    <div id="tabcontent-drafts">
            <h2 class="tab-header"> Select Semesters Options</h2>
            
            {if !is_null($semesters) }
            <form action="{$smarty.server.REQUEST_URI}" method="post">
                <input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
                <input type="hidden" name="return_url" value="system" />
                
                <table summary="Semesters Visib Activ" cellpadding="0" cellspacing="0">
                    <thead>
                    <tr>
                        
                        <th scope="col"style="width: 25px;">Semester Name</th>
                        <th scope="col" style="width: 25px;">Select Visibility</th>
                        <th scope="col" style="width: 25px;">Select Activity</th>
                        <th scope="col" style="width: 2px;"></th>
                                     
                    </tr>
                    </thead>
                    <tbody>
                        
                        {foreach from=$semesters item=semester}
    
                        <tr>
                            

                            <th scope="row">{$semester.semester_name}{$semester.semester_year}</th>
                            <td><input type="checkbox" name="vid[]" id="{$semester.semester_id}" {$semester.semester_visibility} value="{$semester.semester_id}"><label> Visible</label></td>
                            <td><input type="checkbox" name="aid[]" id="{$semester.semester_id}" {$semester.semester_activity} value="{$semester.semester_id}"><label> Active</label></td>
                            <td><input type="hidden" name="sem[]" value="{$semester.semester_id}" /></td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>

                <div class="save_row">
                <input type="submit" name="command[saveChanges]" value="Save Changes" class="button" />

                </div>
            </form>
            {else}
                <div class="message info">
                You do not currently have any semesters entered.  You can update the table entering the semesters above.
                </div>
            {/if}
        </div>


<table>


{if $show_merge_legacy}

<h2>Merge data from legacy database</h2>
<form action="{$smarty.server.REQUEST_URI}" method="post">
	<input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
    <input type="hidden" name="return_url" value="{$return_url}" />
    
    <div class="message warn">
    <p>
    Merging data from the legacy database will copy and reorganize all syllabi from the legacy system into the new database
    structure of the new system.  <strong>This should only be done once immediately after installing the new system</strong>.
    This process may take a long time.  Please only click the submit button once.
    </p>
    
    <p>Are you sure you want to continue?</p>

    <div class="save_row">
        <input type="submit" name="command[systemMergeFromLegacy]" value="Merge Legacy Database" class="button" />
    </div>
    
	</div>
</form>

{/if}

