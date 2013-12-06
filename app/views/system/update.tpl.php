<h1>Manually update database</h1>

<form action="{$smarty.server.REQUEST_URI}" method="post">
	<input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
    <input type="hidden" name="return_url" value="{$return_url}" />
    
    <div class="message warn">
    <p>
    Updating the system will manually import all data from the SIMS snapshots and normalize the
    database accordingly. This process entails:
    </p>
    
    <ul>
        <li>Adding new records to the <strong>users</strong>, <strong>syllabus</strong>, and <strong>enrollments</strong> tables</li>
        <li>Where those records already exist, they will be updated appropriately to account for new data</li>
        <li>All syllabi and syllabi module objects whose associated course no longer exists in the snapshot will be deleted from the database</li>
    </ul>
    
    <p>Are you sure you want to continue?</p>

    <div class="save_row">
        <input type="submit" name="command[systemUpdate]" value="Continue and Run Update" class="button" />
    </div>
	</div>
    
</form>


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

