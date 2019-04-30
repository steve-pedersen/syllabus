<h1>#{$course->catalog_number} &mdash; {$course->department|escape}{$course->course_number|escape} {$course->title|escape}</h1>

<dl class="metadata">
    <dt>Administrative unit</dt>
    <dd>{if $course->admin_unit == 'E'}Extended{else}Regular{/if} university ({$course->admin_unit})</dd>

    <dt>Course status</dt>
    <dd>{if $course->course_status == 'U'}Unauthorized{else}Authorized{/if} ({$course->course_status|escape})</dd>

    <dt>Authorization period</dt>
    <dd>Begin {$course->begin_period} &ndash; End {$course->end_period}</dd>

    <dt>Department</dt>
    <dd>{$course->department|escape}</dd>

    <dt>Course number</dt>
    <dd>{$course->course_number|escape}</dd>

    <dt>Course number suffix</dt>
    <dd>{$course->suffix|escape|default:"&mdash;"}</dd>

    <dt>Title</dt>
    <dd>{$course->title|escape}</dd>

    <dt>Bulletin (expanded) title</dt>
    <dd>{$course->expanded_title|escape}</dd>

    <dt>Description</dt>
    <dd>{$course->description|escape}</dd>

    <dt>Prerequisites</dt>
    <dd>{$course->prerequisites|escape}</dd>
    
    <dt>Course level</dt>
    <dd>
        {if $course->course_level == 1}
            Lower division
        {elseif $course->course_level == 2}
            Upper division
        {elseif $cousre->course_level == 3}
            Graduate
        {else}
            Unknown
        {/if}
        ({$course->course_level|escape})
    </dd>

    <dt>Course type</dt>
    <dd>
        {$course->ge|escape}
        {if $course->generic == 'G'}
            Generic (container)
        {elseif $course->variable == 'V'}
            Generic (topic)
        {elseif $course->variable == 'X'}
            Experimental
        {/if}

        {if $course->course_number == 699 || $course->course_number == 899}
            Independent study (699/899)
        {elseif $course->course_number < 100}
            Remedial
        {else}
            Normal
        {/if}
    </dd>

    <dt>Grading method</dt>
    <dd>{$course->grading_method|escape}</dd>

    <dt>Unit(s)</dt>
    <dd>
        {$course->unitsFrom|escape}
        {if $course->unitsFrom != $course->unitsTo}&ndash; {$course->unitsTo|escape}{/if}
    </dd>

    <dt>Repeatability</dt>
    <dd>
        {if $course->repeated_allowed}
            Repeatable {$course->max_num_repeat} for {$course->max_repeat_units} unit(s)
        {else}
            May not be repeated for credit
        {/if}
    </dd>

    <dt>Cross-listed with</dt>
    <dd>{$course->xlist_catalog_number|escape|default:"None"}</dd>

    <dt>Paired with</dt>
    <dd>{$course->paired_course|escape|default:"None"}</dd>
</dl>