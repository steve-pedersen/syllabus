<h2>{$m.module_custom_name}</h2>

{foreach from=$materials item=i name='materials'}

{if $smarty.foreach.materials.first}
<table cellpadding="0" cellspacing="0" summary="Listing of Materials for this course">
    <thead>
    <tr>
        <th scope="col" style="width: 30%;">Title</th>
        <th scope="col" style="width: 10%;">Required</th>
        <th scope="col">Notes</th>
    </tr>
    </thead>
    <tbody>
{/if}

    <tr>
        <th scope="row" valign="top">{$i.material_title}</th>
        <td valign="top">{if $i.material_required==1}Required{else}Optional{/if}</td>
        <td valign="top">{$i.material_info}</td>
    </tr>

{if $smarty.foreach.materials.last}
    </tbody>
</table>
{/if}

{foreachelse}

<div class="message error">There are currently no Materials for this syllabus.</div>
    
{/foreach}
