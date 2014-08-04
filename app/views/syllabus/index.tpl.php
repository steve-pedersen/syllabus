<h1>My Syllabi</h1>

{if $has_syllabi || $has_drafts_permission}
    <ul class="tabs" id="tabs_syllabi">
    {foreach name=tabs_year from=$syllabi item=ty key=tyk}
        {foreach name=tabs_sem from=$ty item=ts key=tsk}
            {if $tsk == 1}{assign var='semester_name' value='Winter'}{/if}
            {if $tsk == 3}{assign var='semester_name' value='Spring'}{/if}
            {if $tsk == 5}{assign var='semester_name' value='Summer'}{/if}
            {if $tsk == 7}{assign var='semester_name' value='Fall'}{/if}            
            <li id="tab_{$tyk}{$tsk}"><a href="{$smarty.server.REQUEST_URI}#tabcontent-{$tyk}{$tsk}"><strong>{$semester_name} {$tyk}</strong></a></li>
        {/foreach}
    {/foreach}
    
    {if $has_drafts_permission }
    <li id="tab_drafts"><a href="{$smarty.server.REQUEST_URI}#tabcontent-drafts"><strong>Drafts</strong></a></li>
    {/if}
    </ul>
    
    
    {foreach name=tabs_year from=$syllabi item=ty key=tyk}
        {foreach name=tabs_sem from=$ty item=ts key=tsk}
            {if $tsk == 1}{assign var='semester_name' value='Winter'}{/if}
            {if $tsk == 3}{assign var='semester_name' value='Spring'}{/if}
            {if $tsk == 5}{assign var='semester_name' value='Summer'}{/if}
            {if $tsk == 7}{assign var='semester_name' value='Fall'}{/if}            
            <div id="tabcontent-{$tyk}{$tsk}">
            <h2 class="tab-header">{$semester_name} {$tyk}</h2>
            <table summary="Syllabi from the {$semester_name} {$tyk} semester" cellpadding="0" cellspacing="0">
                <thead>
                <tr>
                    <th scope="col" width="7%"><abbr title="Semester">Sem</abbr></th>
                    <th scope="col" width="7%">Year</th>
                    <th scope="col" width="15">Course Number</th>
                    <th scope="col" width="40%">Course Name</th>            
                    <th scope="col" width="25%">Instructor</th>
                </tr>
                </thead>
                <tbody>
                    {foreach name=class from=$ts item=c key=id}
                    <tr>
                        <td>{$semester_name}</td>
                        <td>{$tyk}</td>
                        <td>{$c.syllabus_class_number} &ndash; {"%02d"|sprintf:$c.syllabus_class_section}</td>
                        <td><a href="syllabus/view/{$c.syllabus_id}">{$c.syllabus_class_title}</a></td>
                        <td>{$c.user_preferred_name}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            </div>
        {/foreach}
    {/foreach}
    
    {if $has_drafts_permission }
        <div id="tabcontent-drafts">
            <h2 class="tab-header">Drafts</h2>
            <div class="save_row"><a href="syllabus/draft" class="icon"><span class="icon inline-block add"></span> Create Draft</a></div>
            
            {if !is_null($drafts) }
            <form action="{$smarty.server.REQUEST_URI}" method="post">
                <input type="hidden" name="submit_token" value="{$smarty.const.SUBMIT_TOKEN}" />
                <input type="hidden" name="return_url" value="syllabus" />
                
                <table summary="Draft Syllabi" cellpadding="0" cellspacing="0">
                    <thead>
                    <tr>
                        <th scope="col" style="width: 15px;"><input type="checkbox" class="check-all" id="drafts" class="enableJS" /><label class="hideJS" for="drafts">Select Drafts</label></th>
                        <th scope="col">Draft Name</th>            
                    </tr>
                    </thead>
                    <tbody>
                        {foreach name=drafts from=$drafts item=c key=id}
                        <tr>
                            <td><input type="checkbox" name="drafts[]" id="{$c.syllabus_id}" class="drafts" value="{$c.syllabus_id}" /><label class="hideJS" for="{$c.syllabus_id}">Select this draft</label></td>
                            <th scope="row"><a href="syllabus/view/{$c.syllabus_id}">{$c.syllabus_class_title}</a></th>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
                
                <div class="save_row">
                    <input type="submit" class="button" name="command[deleteDrafts]" value="Delete Selected Drafts" />
                    <span class="form_note">
                </div>
            </form>
            {else}
                <div class="message info">
                You do not currently have any drafts.  You can create a new draft using the link above.
                </div>
            {/if}
        </div>
    {/if}
{else}

    <div class="message info">
        <p>
        Welcome to your syllabi page.  This page normally shows all of the classes at <abbr title="San Francisco State University">SF State</abbr> which you
        </p>
        <ul>
            <li>Have taken or are currently taking as a student</li>
            <li>Have taught or are currently teaching</li>
        </ul>
        <p>
        The <em>Syllabus</em> system does not have any classes which fit those criteria for you.  If you think a mistake has been made, please
        <a href="contact">contact the <em>Syllabus</em> team</a> and we'll see if we can solve the problem.
        </p>
    </div>    

{/if}