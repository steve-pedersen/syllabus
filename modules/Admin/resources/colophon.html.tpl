<div class="section">
    <h2>Table of contents!!</h2>
    {foreach key="dn" item="moduleList" from=$moduleMap}
        <p>Modules from {$dn|escape}</p>
        <ul class="bullet columnar">
        {foreach item="module" from=$moduleList}
            <li><a href="admin/colophon#module-{$module->id}">{$module->name|escape}</a></li>
        {/foreach}
        </ul>
    {/foreach}
</div>

{foreach name="dns" key="dn" item="moduleList" from=$moduleMap}
{foreach name="mods" item="module" from=$moduleList}
<div class="section{if $smarty.foreach.dns.last && $smarty.foreach.mods.last} last{/if}">
<h2 id="module-{$module->id}">{$module->name} v.{$module->version}{if strpos($module->id, ':core:')}<span class="minor"> (framework)</span>{/if}</h3>
<p class="detail">{$module->id} created by {foreach name="authors" item="author" from=$module->authors}{$author|escape}{if !$smarty.foreach.authors.last}, {/if}{/foreach}. {$module->copyright}.</p>
<p>{$module->description}</p>
{if !empty($module->classes)}
<h4>Classes</h4>
<table class="data table table-striped">
    <thead>
        <tr>
            <th style="width:24em;">Class name</th>
            <th>Path</th>
        </tr>
    </thead>
    
    <tbody>
{foreach key="className" item="classPath" from=$module->getClasses()}
        <tr>
            <td>{$className|escape}</td>
            <td>{$classPath|escape}</td>
        </tr>
{/foreach}
    </tbody>
</table>
{/if}
{if !empty($module->extensionPoints)}
<h3>Extension points</h4>
<table class="data table table-striped">
    <thead>
        <tr>
            <th style="width:10em;">Name</th>
            <th>Description</th>
            <th style="width:24em;">Registered extensions</th>
        </tr>
    </thead>
    
    <tbody>
{foreach item="point" from=$module->extensionPoints}
        <tr>
            <td>{$point->getUnqualifiedName()}</td>
            <td>{$point->getDescription()}</td>
            <td>
                <ul class="bullet">
                {foreach key="extName" item="extInfo" from=$point->getExtensionDefinitions()}
                    <li>{$extInfo[1]->classPrefix|str_replace:"":$extName|escape} <span class="de-emphasized detail">from <a href="admin/colophon#module-{$extInfo[1]->id}">{$extInfo[1]->name}</a></span></li>
                {foreachelse}
                    <li>None</li>
                {/foreach}
                </ul>
            </td>
        </tr>
{/foreach}
    </tbody>
</table>
{/if}
</div>
{/foreach}
{/foreach}