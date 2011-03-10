<table summary="List of Policy items in the repository" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col" style="width: 20px;"><span class="icon"><span class="icon edit inline-block"></span><span class="text">Edit</span></span></th>
        <th scope="col" style="width: 20px;"><span class="icon"><span class="icon remove inline-block"></span><span class="text">Delete</span></span></th>
        <th scope="col">Policy Title</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$m.items item=i}
        <tr>
            <td>
                <a href="repository/edit/{$k}/{$i.policy_id}" class="icon">
                    <span class="icon edit inline-block"></span>
                    <span class="text">Edit</span>
                </a>
            </td>
            <td>
                <a href="repository/delete/{$k}/{$i.policy_id}" class="icon">
                    <span class="icon remove inline-block"></span>
                    <span class="text">Delete</span>
                </a>
            <td>{$i.policy_title}</td>
        </tr>
        {/foreach}
    </tbody>
</table>