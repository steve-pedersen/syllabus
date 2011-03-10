<div class="module_title_bar self_clear">
    <h2 class="module_title" id="{$module}_title">{$m.module_custom_name}</h2>
    <div class="module_sort_controls">
        <a href="" class="expand-source expand-collapse-all-modules" rel="{$module}_items_container">&#x25BC;</a>
        <span class="hideJS module_order_field">
            <label for="sort_modules[{$module}]">Order</label>
            <input type="text" size="3" name="sort_modules[{$module}]" id="sort_modules[{$module}]" value="{$smarty.foreach.modules_loop.iteration}" />
        </span>
        <a href="" class="enableJS move-up move-link icon" rel="module_sort_parent"><span class="icon up inline-block"></span><span class="text">Move up</span></a>
        <a href="" class="enableJS move-down move-link icon" rel="module_sort_parent"><span class="icon down inline-block"></span><span class="text">Move down</span></a>
        <a href="{$smarty.const.BASEHREF}syllabus/edit_module/{$syllabus.syllabus_id}/{$module}" class="colorbox icon"><span class="icon edit inline-block"></span><span class="text">Edit</span></a>
        <a href="syllabus/remove_module/{$syllabus.syllabus_id}/{$module}" class="colorbox icon"><span class="icon remove inline-block"></span><span class="text">Remove</span></a>
    </div>
</div>
