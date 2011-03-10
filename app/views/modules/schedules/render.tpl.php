<h2>{$m.module_custom_name}</h2>

{foreach from=$schedules item=i name='schedule'}

{if $smarty.foreach.schedule.first}
<table summary="Class schedule" cellpadding="2" cellspacing="0">
    <thead>
    <tr>
        <th scope="col" style="width: 100px;">Date</th>
        <th scope="col" style="width: 250px;">Notes</th>
        <th scope="col" style="width: 150px;">Due</th>
    </tr>
    </thead>
    <tbody>
{/if}

    <tr>
        <th scope="row" valign="top">
            {if $i.schedule_period=='w'}Week of {/if}
            {$i.schedule_date}
        </th>
        <td valign="top">{$i.schedule_desc}</td>
        <td valign="top">{$i.schedule_due}</td>
    </tr>
    
{if $smarty.foreach.schedule.last}
    </tbody>
</table>
{/if}

{foreachelse}

<div class="message error">There is currently no schedule for this syllabus.</div>

{/foreach}
