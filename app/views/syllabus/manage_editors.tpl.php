<form action="{$smarty.const.CURRENT_URL}" method="post">
    {$smarty.const.SUBMIT_TOKEN_HTML}
    <input type="hidden" name="syllabus_id" value="{$syllabus.syllabus_id}" />
    
    <div style="margin-bottom: 2em;">
        <div class="message info">
        Add an editor for this syllabus.
        <label for="user_email" style="margin-left: 50px;">SFSU email<label><input type="text" name="user_email" id="user_email" style="width: 200px;" />
        <input type="submit" name="command[addSyllabusEditor]" class="button" value="Add Editor" />
        </div>
    </div>
</form>


<form action="{$smarty.const.CURRENT_URL}" method="post">
    {$smarty.const.SUBMIT_TOKEN_HTML}
    <input type="hidden" name="syllabus_id" value="{$syllabus.syllabus_id}" />
    
{foreach name=editors from=$editors item=e}

{if $smarty.foreach.editors.first}
<table summary="Current Editors for this syllabus" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th scope="col" width="5%"><input type="checkbox" class="check-all" id="editors" title="Select / Deselect All" /><label class="hideJS">Select / Deselect All Editors</label></th>
        <th scope="col" width="50%">Name</th>
        <th scope="col" width="45%">Email</th>
    </tr>
    </thead>
    <tbody>
{/if}

    <tr>
        <td><input type="checkbox" class="editors" name="remove_editors[]" id="{$e.user_id}" value="{$e.user_id}" /></td>
        <th scope="row"><label for="{$e.user_id}">{$e.user_lname}, {$e.user_fname}</label></td>
        <td>{$e.user_email}</td>
    </tr>

{if $smarty.foreach.editors.last}
    </tbody>
</table>
{/if}

{foreachelse}

<div class="message error">There are currently no other editors for this syllabus</div>

{/foreach}
    
<div class="save_row">
    <input type="submit" name="command[removeSyllabusEditors]" class="button" value="Remove Selected Editors" />
</div>

</form>
