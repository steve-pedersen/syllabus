<h2>{$m.module_custom_name}</h2>

{foreach from=$policies item=i}
    <h3>{$i.policy_title}</h3>
    <div>{$i.policy_text}</div>
{foreachelse}
    <div class="message error">There are currently no Policies for this syllabus.</div>
{/foreach}