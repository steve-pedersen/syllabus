<h2>Class Information</h2>
<table summary="General Course Info" cellpadding="0" cellspacing="0">
    <tr>
        <th style="width: 20%;">Class Number</th>
        <td valign="top">{$syllabus.syllabus_class_number}</td>
    </tr>
    <tr>
        <th>Semester / Year</th>
        <td valign="top">
        {$syllabus.semester_name}
        {$syllabus.syllabus_class_year}        
        </td>
    </tr>
    <tr>
        <th valign="top">Class Title</th>
        <td valign="top">{$syllabus.syllabus_class_title}</td>
    </tr>
    <tr>
        <th valign="top">Class Description</th>
        <td valign="top">{$syllabus.syllabus_class_description}</td>
    </tr>
    <tr>
        <th valign="top">Instructor</th>
        <td valign="top">{$syllabus.syllabus_instructor}</td>
    </tr>
    <tr>
        <th valign="top">Office</th>
        <td valign="top">{$syllabus.syllabus_office}</td>
    </tr>
    
    {if !empty($syllabus.syllabus_office_hours)}
    <tr>
        <th valign="top">Office Hours</th>
        <td valign="top">{$syllabus.syllabus_office_hours}</td>
    </tr>
    {/if}

    {if !empty($syllabus.syllabus_phone)}
    <tr>
        <th valign="top">Phone</th>
        <td valign="top">{$syllabus.syllabus_phone}</td>
    </tr>
    {/if}
    
    {if !empty($syllabus.syllabus_mobile)}
    <tr>
        <th valign="top">Mobile Phone</th>
        <td valign="top">{$syllabus.syllabus_mobile}</td>
    </tr>
    {/if}

    {if !empty($syllabus.syllabus_email)}    
    <tr>
        <th valign="top">Email</th>
        <td valign="top">{$syllabus.syllabus_email}</td>
    </tr>
    {/if}

    {if !empty($syllabus.syllabus_website)}    
    <tr>
        <th valign="top">Website</th>
        <td valign="top">{$syllabus.syllabus_website}</td>
    </tr>
    {/if}
    
    {if !empty($syllabus.syllabus_fax)}
    <tr>
        <th valign="top">Fax</th>
        <td valign="top">{$syllabus.syllabus_fax}</td>
    </tr>
    {/if}
</table>
