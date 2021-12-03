<!-- Instructors Section -->
<div class="card card-outline-secondary rounded-0">
<div class="card-body sort-container" id="instructorsSection">

{if $realSection->instructors}
    {foreach $realSection->instructors as $i => $instructor}
        {assign var=instructorId value="{$instructor->id}"}
        {assign var=sortOrder value="{str_pad($i+1, 3, '0', STR_PAD_LEFT)}"}
<div class="container-fluid mb-2" id="instructorContainer{$instructorId}">
<div class="row sort-item">
    <div class="tab-content col-11 px-0" id="toggleEditViewTab{$i}">

<div class="tab-pane fade border {if $instructor@last}show active{/if}" id="nav-edit-{$i}" role="tabpanel" aria-labelledby="nav-edit-{$i}-tab">
    <div class="mb-2 mx-0 d-flex flex-row bg-light p-3 dragdrop-handle">
        <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
    </div>  
    <input type="hidden" name="section[real][{$instructorId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$i+1}-sort-order">

    <div class="d-flex justify-content-end">
        <button type="submit" aria-label="Delete" class="btn btn-link text-danger my-0 mx-2" name="command[deletesectionitem][Syllabus_Instructor_Instructor][{$instructorId}]" id="{$instructorId}">
            <i class="fas fa-trash-alt mr-1"></i>Delete
        </button>
    </div>

    {assign var=usingProfilePhoto value=false}
    <div class="form-row p-3 row-3">
        <div class="col-md-6">
        <div class="col-md-12 p-3 bg-light">
            <h5>Choose existing profile photo for this instructor</h5>
            <span class="text-muted mb-3">You can update your own photo by <a href="profile/{$viewer->id}"><strong>editing your profile</strong></a></span>
            <select class="form-control profile-image-selector" id="instructor-{$instructorId}" name="section[real][{$instructorId}][image_id]">
                <option value="" {if !$instructor->image}selected{/if}>Select an instructor / image</option>
                {foreach $instructorProfiles as $profile}
                <option value="{$profile->image_id}" id="profile-{$profile->id}" {if $instructor->image_id && $instructor->image_id == $profile->image_id}selected {assign var=usingProfilePhoto value=true}{/if}>{$profile->name}</option>
                {/foreach}
                {if !$usingProfilePhoto && $instructor->image}
                    <option value="{$instructor->image->id}" selected>{$instructor->image->remoteName}</option>
                {/if}
            </select>
        </div>
        <div class="col-md-12 p-3 bg-light image-container">
            {foreach $instructorProfiles as $profile}
                <div class="card profile-cards-instructor-{$instructorId} {if $instructor->image_id && $instructor->image_id == $profile->image_id}{else}hidden{/if}" {if $profile->image}style="max-width: 200px;"{/if} id="profile-{$profile->id}-card">
                    {if $profile->image}

                    <div class="card-img-top">
                        <img src="{$profile->image->imageSrc}" class="img-fluid instructor-{$instructorId}-image" id="">
                    </div>
                    {else}
                    <div class="card-body">
                        <p class="">No profile image uploaded for this instructor.</p>
                        <p>You can update your own photo by <a href="profile/{$viewer->id}"><strong>editing your profile</strong></a></p>
                    </div>
                    {/if}
                </div>
            {/foreach}
        </div>
        </div>
        <div class="col-md-6 bg-light">
            <div class="col-md-12 p-3 ">
                <h5>Upload a new photo for this instructor</h5>
                <span class="text-muted mb-3">If this instructor does not have a profile photo you can upload an image to be used for this syllabus.</span>
                <input type="hidden" name="instructorId" value="{$instructorId}">
                <div class="mt-3">
                    <input type="file" name="file" id="instructorPhoto{$instructorId}" class="box__file" form="uploadInstructorPhoto" />
                    <button class="box__button btn btn-primary mt-3 submitInstructorPhoto" data-instructor-id="{$instructorId}" type="submit" form="uploadInstructorPhoto">Upload Now</button>
                    <span class="text-danger mt-2" id="uploadErrorMessage{$instructorId}" style="display:none;" >You must choose a file to upload.</span>
                </div>
                <div class="mt-2">
                    {if $instructor->image && !$usingProfilePhoto}
                    <img src="{$instructor->imageSrc}" style="max-width:200px;" class="img-fluid" id="instructorUploadedImage{$instructorId}">
                    {else}
                    <img src="" style="max-width:200px; display:none;" class="img-fluid" id="instructorUploadedImage{$instructorId}">
                    {/if}
                </div>
            </div>
        </div>
    </div>

    <div class="form-row px-3 row-1">
        <div class="col-md-3 mb-3 name">
            <label for="name">Name</label>
            <input type="text" class="form-control required" name="section[real][{$instructorId}][name]" placeholder="Full name" value="{$instructor->name}" required>
        </div>
        <div class="col-md-3 mb-3 email">
            <label for="email">Email</label>
            <input type="text" class="form-control" name="section[real][{$instructorId}][email]" placeholder="" value="{$instructor->email}">
        </div>
        <div class="col-md-3 mb-3 title">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="section[real][{$instructorId}][title]" placeholder="e.g. Assistant Professor" value="{$instructor->title}">
        </div>
        <div class="col-md-3 mb-3 credentials">
            <label for="credentials">Credentials</label>
            <input type="text" class="form-control" name="section[real][{$instructorId}][credentials]" placeholder="e.g. Ph.D., MBA" value="{$instructor->credentials}">
        </div>
    </div>
    <div class="form-row px-3 row-2">
        <div class="col-md-4 mb-3 office">
            <label for="office">Office</label>
            <input type="text" class="form-control" name="section[real][{$instructorId}][office]" placeholder="e.g. LIB 220" value="{$instructor->office}">
        </div>
        <div class="col-md-4 mb-3 website">
            <label for="website">Website</label>
            <input type="text" class="form-control" name="section[real][{$instructorId}][website]" value="{$instructor->website}">
        </div>
        <div class="col-md-4 mb-3 phone">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" name="section[real][{$instructorId}][phone]" value="{$instructor->phone}">
        </div>
    </div>
    <div class="form-row px-3 row-3">
        <div class="col-md-6 mb-3 office-hours">
            <label for="officeHours">Office Hours</label>
            <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$instructorId}][officeHours]" rows="3">{$instructor->officeHours}</textarea>
        </div>
        <div class="col-md-6 mb-3 about">
            <label for="about">About Me</label>
            <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$instructorId}][about]" rows="3">{$instructor->about}</textarea>
        </div>
    </div>

</div>

<div class="tab-pane fade {if !$instructor@last}show active{/if} d-inline-block w-100 px-3" id="nav-view-{$i}" role="tabpanel" aria-labelledby="nav-view-{$i}-tab">
    <div class="row py-2 bg-light border dragdrop-handle rounded">
        <div class="col-1 dragdrop-handle align-middle">
            <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
        </div>
        <div class="col-1 text-truncate"><strong>#{$i+1}</strong></div>
        <div class="col-5 text-truncate"><strong>{$instructor->name|truncate:50}</strong></div>
        <div class="col-5 text-truncate ">{l href="mailto:{$instructor->email}" text=$instructor->email}</div>
    </div>   
</div>

    </div>
    <div class="nav nav-tabs col-1 toggle-edit d-inline-block border-0" role="tablist">
        <a class="btn btn-sm btn-info py-2 {if $instructor@last}active{/if}" id="nav-edit-{$i}-tab" data-toggle="tab" href="#nav-edit-{$i}" role="tab" aria-controls="nav-edit-{$i}" aria-selected="false">Edit #{$i+1}</a>
        <a class="btn btn-sm btn-secondary py-2 {if !$instructor@last}active{/if}" id="nav-view-{$i}-tab" data-toggle="tab" href="#nav-view-{$i}" role="tab" aria-controls="nav-view-{$i}" aria-selected="true">Minimize #{$i+1}</a>
    </div>
</div>
</div>

    {/foreach}


{/if}


{if $realSection->instructors}
    {assign var=i value="{count($realSection->instructors)}"}
    {assign var=sortOrder value="{count($realSection->instructors)}"}
{else}
    {assign var=i value="0"}
    {assign var=sortOrder value="1"}
    {if $defaultInstructor}
        {assign var=defaultName value="{$defaultInstructor->fullName}"}
        {assign var=defaultEmail value="{$defaultInstructor->emailAddress}"}
    {/if}
    {if $profileData && (!$defaultName || !$defaultEmail)}
        {assign var=defaultName value="{$profileData->name}"}
        {assign var=defaultEmail value="{$profileData->email}"}
    {/if}
{/if}
{assign var=instructorId value="new-{$i}"}
        
<div class="sort-item mt-3 border p-2" id="newSortItem{$i}" {if $realSection->instructors}style="display:none;" hidden{/if}>
<div class="mb-2 d-flex flex-row bg-white p-2 dragdrop-handle">
     <i class="fas fa-bars text-dark" data-toggle="tooltip" data-placement="top" title="Click and drag to change the order."></i>
</div>
<input type="hidden" name="section[real][{$instructorId}][sortOrder]" value="{$sortOrder}" class="sort-order-value" id="form-field-{$sortOrder}-sort-order">

    <div class="form-row p-3 row-3 profile-image-container">
        <div class="col-md-6">
        <div class="col-md-12 p-3 bg-light">
            <h5>Choose existing profile photo for this instructor</h5>
            <span class="text-muted mb-3">You can update your own photo by <a href="profile/{$viewer->id}"><strong>editing your profile</strong></a></span>
            <select class="form-control profile-image-selector" id="instructor-{$instructorId}" name="section[real][{$instructorId}][image_id]">
                <option value="" selected>Select an instructor</option>
                {foreach $instructorProfiles as $profile}
                {if $profile->image}
                    <option value="{$profile->image_id}" id="profile-{$profile->id}">{$profile->name}</option>
                {else}
                    <option value="{$profile->image_id}" id="profile-{$profile->id}" disabled>{$profile->name} (No profile image)</option>
                {/if}
                {/foreach}
            </select>
        </div>
        <div class="col-md-12 p-3 bg-light image-container">
            {foreach $instructorProfiles as $profile}
                <div class="card profile-cards-instructor-{$instructorId} hidden" {if $profile->image}style="max-width: 200px;"{/if} id="profile-{$profile->id}-card">
                    {if $profile->image}
                    <div class="card-img-top">
                        <img src="{$profile->image->imageSrc}" class="img-fluid" id="instructor-{$instructorId}-image">
                    </div>
                    {else}
                    <div class="card-body">
                        <p class="">No profile image uploaded for this instructor.</p>
                        <p>You can update your own photo by <a href="profile/{$viewer->id}"><strong>editing your profile</strong></a></p>
                    </div>
                    {/if}
                </div>
            {/foreach}
        </div>
        </div>
        <div class="col-md-6 bg-light">
            <div class="col-md-12 p-3 ">
                <h5>Upload a new photo for this instructor</h5>
                <span class="text-muted mb-3">If this instructor does not have a profile photo already you can upload one here.</span>
                <!-- <input type="hidden" name="instructorId" value="{$instructorId}"> -->
                <div class="mt-3">
                    <input type="file" name="file{$instructorId}" id="instructorPhoto{$instructorId}" class="box__file" form="uploadInstructorPhoto" />
                    <button class="box__button btn btn-primary mt-3 submitInstructorPhoto new" data-instructor-id="{$instructorId}" type="submit" form="uploadInstructorPhoto">Upload Now</button>
                    <span class="text-danger mt-2" id="uploadErrorMessage{$instructorId}" style="display:none;" >You must choose a file to upload.</span>
                </div>
                <div class="mt-2">
                    {if $instructor->image && !$usingProfilePhoto}
                    <img src="{$instructor->imageSrc}" style="max-width:200px;" class="img-fluid uploadedImage{$instructorId}" id="instructorUploadedImage{$instructorId}">
                    {else}
                    <img src="" style="max-width:200px; display:none;" class="img-fluid uploadedImage{$instructorId}" id="instructorUploadedImage{$instructorId}">
                    {/if}
                </div>
            </div>
        </div>
    </div>


<div class="form-row px-3 row-1">
    <div class="col-md-3 mb-3 name">
        <label for="name">Name</label>
        <input type="text" class="form-control" name="section[real][{$instructorId}][name]" value="{if $defaultName}{$defaultName}{/if}">
    </div>
    <div class="col-md-3 mb-3 email">
        <label for="email">Email</label>
        <input type="text" class="form-control" name="section[real][{$instructorId}][email]" value="{if $defaultEmail}{$defaultEmail}{/if}">
    </div>
    <div class="col-md-3 mb-3 title">
        <label for="title">Title</label>
        <input type="text" class="form-control" id="title" name="section[real][{$instructorId}][title]" placeholder="e.g. Assistant Professor" value="{if $profileData->title}{$profileData->title}{/if}">
    </div>
    <div class="col-md-3 mb-3 credentials">
        <label for="credentials">Credentials</label>
        <input type="text" class="form-control" name="section[real][{$instructorId}][credentials]" placeholder="e.g. Ph.D., MBA" value="{if $profileData->credentials}{$profileData->credentials}{/if}">
    </div>
</div>
<div class="form-row px-3 row-2">
    <div class="col-md-4 mb-3 office">
        <label for="office">Office</label>
        <input type="text" class="form-control" name="section[real][{$instructorId}][office]" placeholder="e.g. LIB 220" value="{if $profileData->office}{$profileData->office}{/if}">
    </div>
    <div class="col-md-4 mb-3 website">
        <label for="website">Website</label>
        <input type="text" class="form-control" name="section[real][{$instructorId}][website]" value="{if $profileData->website}{$profileData->website}{/if}">
    </div>
    <div class="col-md-4 mb-3 phone">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" name="section[real][{$instructorId}][phone]" value="{if $profileData->phone}{$profileData->phone}{/if}">
    </div>
</div>
<div class="form-row px-3 row-3">
    <div class="col-md-6 mb-3 office-hours">
        <label for="officeHours">Office Hours</label>
        <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$instructorId}][officeHours]" rows="3">{if $profileData->officeHours}{$profileData->officeHours}{/if}</textarea>
    </div>
    <div class="col-md-6 mb-3 about">
        <label for="about">About Me</label>
        <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="section[real][{$instructorId}][about]" rows="3">{if $profileData->about}{$profileData->about}{/if}</textarea>
    </div>
</div>


</div>  


       
<div class="form-group d-flex flex-row-reverse mt-4">
    <input class="btn btn-light" id="addInstructorsSectionItemBtn" type="submit" name="command[addsectionitem][{$realSectionClass}]" value="+ Add Another Instructor" />
</div>

</div>
</div>
<!-- End Instructors Section -->