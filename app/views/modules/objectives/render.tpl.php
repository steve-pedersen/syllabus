<h2>{$m.module_custom_name}</h2>

{foreach from=$objectives item=i}
    <h3>{$i.objective_title}</h3>
    <div>{$i.objective_text}</div>
{foreachelse}
    <div class="message error">There are currently no objectives for this syllabus.</div>
{/foreach}