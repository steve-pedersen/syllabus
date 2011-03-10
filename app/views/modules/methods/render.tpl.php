<h2>{$m.module_custom_name}</h2>

{foreach from=$methods item=i}
    <h3>{$i.method_title}</h3>
    <div>{$i.method_text}</div>
{foreachelse}
    <div class="message error">There are currently no Methods for this syllabus.</div>
{/foreach}
