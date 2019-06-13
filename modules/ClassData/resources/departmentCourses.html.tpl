<h2>Department Courses for {$department->name|escape}</h2>
<a href="#" id="toggle-exempt" data-toggle="table" class="btn btn-primary">Hide Exempt</a>
<div class="column-container fluid">
    <div class="content">
        <div class="inner">
            <div class="row">
                <div class="table-responsive col-lg-12">
                    <table class="data sticky-header table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Short Name</th>
                                <th>Title</th>
                                <th>Instructors</th>
                            </tr>
                        </thead>

                        <tbody>
                        {foreach item="course" from=$courses}
                            <tr{if $course->isExempt} class="exempt"{/if}>
                                <td><a href="admin/sections/{$course->id}">{$course->id}</a></td>
                                <td>{$course->shortName}</td>
                                <td>{$course->title}</td>
                                <td>
                                {foreach item='instructor' from=$course->instructors}
                                    <a href="admin/users/{$instructor->id}">{$instructor->lastName}, {$instructor->firstName} ({$instructor->id})</a><br>
                                {/foreach}
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>

                    {include file=$pagination.template}
                </div>
            </div>
        </div>
    </div>
</div>