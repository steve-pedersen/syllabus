<div class="module_item_controls {$module}_sort_controls">
    <span class="hideJS item_order_field">
        <label for="sort_items[{$module}][{$item_id}]">Display Order</label>
        <input type="text" size="2" name="sort_items[{$module}][{$item_id}]" id="sort_items[{$module}][{$item_id}]" value="{$smarty.foreach.items_loop.iteration}" />
    </span>

    <a href="" class="enableJS move-up move-link icon" rel="{$module}_sort_parent"><span class="icon up inline-block"></span><span class="text">Move up</span></a>
    <a href="" class="enableJS move-down move-link icon" rel="{$module}_sort_parent"><span class="icon down inline-block"></span><span class="text">Move down</span></a>
    <a href="syllabus/edit_item/{$syllabus_id}/{$module}/{$item_id}" class="colorbox icon"><span class="icon edit inline-block"></span><span class="text">Edit</span></a>
    <a href="syllabus/remove_item/{$syllabus_id}/{$module}/{$item_id}" class="colorbox icon"><span class="icon remove inline-block"></span><span class="text">Remove</span></a>
</div>
