<h2>Audit Log</h2>

<div class="column-container fluid">
    <div class="content">
        <div class="inner">
            <div class="row">
                <div class="table-responsive col-xs-12 col-sm-9 col-md-9 col-lg-9">
                    <table class="data sticky-header">
                        <thead>
                            <tr>
                                <th style="width:12em;">Date</th>
                                <th style="width:7em;">Type</th>
                                {if $type != 'user'}<th>Course</th>{/if}
                                {if $type != 'course'}<th>User</th>{/if}
                                {if $type != 'user' && $type != 'course'}
                                    <th style="width:6em;">Role</th>
                                {/if}
                            </tr>
                        </thead>

                        <tbody>
                        {foreach item="audit" from=$audits}
                            <tr class="{cycle values=even,odd}">
                                <td>{$audit->dt->format('m/d/Y g:ia')}</td>
                                <td>
                                    {if $audit->type == "add-course"}
                                        Create Course
                                    {elseif $audit->type == "drop-course"}
                                        Delete Course
                                    {elseif $audit->type == "update-course"}
                                        Update Course
                                    {elseif $audit->type == "add-user"}
                                        Create User
                                    {elseif $audit->type == "drop-user"}
                                        Delete User
                                    {elseif $audit->type == "update-user"}
                                        Update User
                                    {elseif $audit->type == "add-enroll"}
                                        Enroll
                                    {elseif $audit->type == "drop-enroll"}
                                        Drop Enroll
                                    {else}
                                        {$audit->type|escape}
                                    {/if}
                                </td>

                                {if $type != 'user'}
                                    <td>
                                        {if $audit->course_id}
                                            {l href="admin/sections/`$audit->course_id`" text=$audit->course_id}
                                            {if array_key_exists($audit->course_id, $courseMap)}
                                                {assign var="cid" value=$audit->course_id}
                                                {assign var="course" value=$courseMap[$cid]}
                                                <a href="admin/sections/{$audit->course_id|escape}" class="minor detail">
                                                    {$courseMap[$cid]|escape}
                                                </a>
                                            {else}
                                                <span class="minor detail">(deleted)</span>
                                            {/if}
                                        {else}
                                            &mdash;
                                        {/if}
                                    </td>
                                {/if}

                                {if $type != 'course'}
                                    <td>
                                        {if $audit->user_id}
                                            {l href="admin/users/`$audit->user_id`" text=$audit->user_id}
                                            {if array_key_exists($audit->user_id, $userMap)}
                                                {assign var="uid" value=$audit->user_id}
                                                {assign var="user" value=$userMap[$uid]}
                                                <a href="admin/users/{$audit->user_id|escape}" class="minor detail">
                                                    {$userMap[$uid]|escape}
                                                </a>
                                            {else}
                                                <span class="minor detail">(deleted)</span>
                                            {/if}
                                        {else}
                                            &mdash;
                                        {/if}
                                    </td>
                                {/if}

                                {if $type != 'user' && $type != 'course'}
                                    <td>{$audit->extraData|ucfirst|escape|default:"&mdash;"}</td>
                                {/if}
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>

                    {include file=$pagination.template}
                </div>

                <div class="sidebar col-xs-12 col-sm-3 col-md-3 col-lg-3">
                    <div id="account-content-filters" class="inner">
                        <h2>Filters</h2>
                        <form method="get" action="{$smarty.server.REQUEST_URI|escape}">
                            <div>
                                <p>
                                    {if $pagination.itemCount > 1}
                                        Showing entries {math equation="x + 1" x=$pagination.itemRange[0]} &ndash; {$pagination.itemRange[1]} of {$pagination.itemCount}
                                    {elseif $pagination.itemCount == 1}
                                        Only one entry
                                    {else}
                                        No entries to show
                                    {/if}
                                    {if $startDateObj}
                                        between {$startDateObj->format('m/d/Y g:ia')} &ndash;
                                    {else}
                                        on or before
                                    {/if}
                                    {$endDateObj->format('m/d/Y g:ia')}.
                                </p>

                                <div class="field">
                                    <label for="filter-start" class="field-label field-linked">
                                        Date range
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="filter-start" name="start" placeholder="Start date" title="Start date" value="{$startDate|escape}">
                                        <span class="input-group-addon">&nbsp;&ndash;&nbsp;</span>
                                        <input type="text" class="form-control" id="filter-end" name="end" placeholder="End date" title="End date" value="{$endDate|escape}">
                                    </div>
                                </div>
                                {if $start_error}<div class="error">{$start_error}</div>{/if}
                                {if $end_error}<div class="error">{$end_error}</div>{/if}

                                <div class="field">
                                    <label for="filter-courseId" class="field-label field-linked">
                                        Course ID or Short Name
                                    </label>
                                    <input type="text" id="filter-courseId" name="courseId" class="form-control" value="{$courseId|escape}">
                                    {foreach item="err" from=$errorMap.courseId}<div class="error">{$err}</div>{/foreach}
                                </div>

                                <div class="field">
                                    <label for="filter-userId" class="field-label field-linked">
                                        User ID or Name
                                    </label>
                                    <input type="text" id="filter-userId" name="userId" class="form-control" value="{$userId|escape}">
                                    <p class="help-block">For name try "First Last" or "Last, First"</p>
                                    {foreach item="err" from=$errorMap.userId}<div class="error">{$err}</div>{/foreach}
                                </div>

                                <div class="field">
                                    <label for="filter-type" class="field-label field-linked">
                                        Type
                                    </label>
                                    <select id="filter-type" name="type" class="form-control">
                                        <option value="">All</option>
                                        <option value="course"{if $type=="course"} selected{/if}>Courses</option>
                                        <option value="user"{if $type=="user"} selected{/if}>Users</option>
                                        <option value="enrollments"{if $type=="enrollments"} selected{/if}>Enrollments</option>
                                        <option value="student"{if $type=="student"} selected{/if}>Students</option>
                                        <option value="instructor"{if $type=="instructor"} selected{/if}>Instructors</option>
                                    </select>
                                    {foreach item="err" from=$errorMap.type}<div class="error">{$err}</div>{/foreach}
                                </div>

                                <div class="field controls">
                                    <a href="admin/audit?p={$page|escape}" class="command-button btn alt{if !$hasFilters} disabled{/if}">Clear</a>
                                    <input type="submit" class="command-button btn btn-primary" name="b" value="Filter">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>