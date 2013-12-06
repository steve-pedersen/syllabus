<h1>{$page_header}</h1>

<ul id="syllabus_backup_restore" class="tabs">
    <li id="tab_backup"><a href="{$smarty.server.REQUEST_URI}#tabcontent-backup"><strong>Backup</strong></a></li>
    <li id="tab_restore"><a href="{$smarty.server.REQUEST_URI}#tabcontent-restore"><strong>Restore</strong></a></li>
</ul>

<div id="tabcontent-backup">
    <h2 class="tab-header">Backup Syllabus</h2>
    {include file='syllabus/backup.tpl.php'}
</div>

<div id="tabcontent-restore">
    <h2 class="tab-header">Restore Syllabus</h2>
    {include file='syllabus/restore.tpl.php'} 
</div>
