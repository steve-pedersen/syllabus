<h1>{$page_header}</h1>

<ul id="syllabus_sharing" class="tabs">
    <li id="tab_edit"><a href="{$smarty.const.CURRENT_URL}#tabcontent-edit"><strong>Who can edit this syllabus?</strong></a></li>
    <li id="tab_view"><a href="{$smarty.const.CURRENT_URL}#tabcontent-view"><strong>Who can see this syllabus?</strong></a></li>
    <li id="tab_share"><a href="{$smarty.const.CURRENT_URL}#tabcontent-share"><strong>Share this syllabus</strong></a></li>
</ul>

<div id="tabcontent-edit">
    <h2 class="tab-header">Who can edit this syllabus?</h2>
    {include file='syllabus/manage_editors.tpl.php'}
</div>

<div id="tabcontent-view">
    <h2 class="tab-header">Who can view this syllabus?</h2>
    {include file='syllabus/manage_viewers.tpl.php'} 
</div>

<div id="tabcontent-share">
    <h2 class="tab-header">Share this syllabus</h2>
    {include file='syllabus/manage_sharing.tpl.php'}
</div>
