<h2>{$m.module_custom_name}</h2>

{foreach from=$tas item=i name='tas'}

{if $smarty.foreach.tas.first}
<table cellpadding="0" cellspacing="0" summary="Listing of Teaching Assistants for this course">
    <thead>
    <tr>
        <th scope="col" style="width: 200px;">Name</th>
        <th scope="col">Email</th>
    </tr>
    </thead>
    <tbody>
{/if}

    <tr>
        <th scope="row">{$i.ta_name}</th>
        <td>{Utility->buildEmailLink p1=$i.ta_email}</td>
    </tr>

{if $smarty.foreach.tas.last}
    <tbody>
</table>
{/if}

{foreachelse}

<div class="message error">There are currently no <abbr title="Teaching Assistants">TAs</abbr> for this syllabus.</div>

{/foreach}
