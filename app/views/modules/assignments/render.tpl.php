<h2>{$m.module_custom_name}</h2>

{foreach from=$assignments item=i name='assignments'}

{if $smarty.foreach.assignments.first}
<table summary="Class assignments" cellpadding="2" cellspacing="0">
    <thead>
    <tr>
        <th scope="col" style="width: 200px;">Assignment</th>
        <th scope="col" style="width: 50px;">Value</th>
        <th scope="col" style="width: 250px;">Notes</th>
    </tr>
    </thead>
    <tbody>
{/if}

    <tr>
        <th scope="row" valign="top">{$i.assignment_title}</th>
        <td valign="top">{$i.assignment_value}</td>
        <td valign="top">{$i.assignment_desc}</td>
    </tr>

{if $smarty.foreach.assignments.last}
    </tbody>
</table>
{/if}

{foreachelse}    

<div class="messages error">There are currently no assignments for this syllabus.</div>

{/foreach}
