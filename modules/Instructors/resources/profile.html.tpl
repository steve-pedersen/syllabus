{if $fillFromSyllabus}
	{assign var=profile value=$profileData['instructor']}
{/if}
<div class="container-fluid" id="profileContainer">
	<h1 class="pb-2">
		My Profile - <small>{$viewer->fullName}</small>
	</h1>
	<div class="wrap pb-2 mb-3"><div class="left"></div><div class="right"></div></div>
	
<div class="card mb-5 border-0 ml-3" style="max-width: 1000px;">
  <div class="row no-gutters ">
    <div class="col-md-4 text-center" id="profileImageContainer">
        <!-- <span class="d-inline-block"></span> -->
        <div class="centered text-center">
        <img src="{$profileImage}" class="card-img mw-100" alt="{if $profile->name}{$profile->name}{else}Placeholder profile image{/if}" id="profileImage" >
        </div>
    </div>
    <div class="col-md-8 pl-md-5 pl-sm-2">
      {include file="{$ctrl->getDragDropUploadFragment()}" action="profile/{$account->id}/upload" singleFile=true uploadedBy={$account->id}}
        <div class="row mt-4">
        <div class="col">
        <p class="text-muted"><strong>Coming soon:</strong> use your profile image in your syllabus!</p>
        </div>  
        </div>
    </div>
  </div>
    
</div>

    <form method="post" action="{$smarty.server.REQUEST_URI|escape}">
    <div class="form-row px-3 row-1">
        <div class="col-md-3 mb-3 name">
            <label for="name">Name</label>
            <input type="text" class="form-control required" name="name" placeholder="Full name" value="{$profile->name}" required>
        </div>
        <div class="col-md-3 mb-3 email">
            <label for="email">Email</label>
            <input type="text" class="form-control" name="email" placeholder="" value="{$profile->email}">
        </div>
        <div class="col-md-3 mb-3 title">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="e.g. Assistant Professor" value="{$profile->title}">
        </div>
        <div class="col-md-3 mb-3 credentials">
            <label for="credentials">Credentials</label>
            <input type="text" class="form-control" name="credentials" placeholder="e.g. Ph.D., MBA" value="{$profile->credentials}">
        </div>
    </div>
    <div class="form-row px-3 row-2">
        <div class="col-md-4 mb-3 office">
            <label for="office">Office</label>
            <input type="text" class="form-control" name="office" placeholder="e.g. LIB 220" value="{$profile->office}">
        </div>
        <div class="col-md-4 mb-3 website">
            <label for="website">Website</label>
            <input type="text" class="form-control" name="website" value="{$profile->website}">
        </div>
        <div class="col-md-4 mb-3 phone">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" name="phone" value="{$profile->phone}">
        </div>
    </div>
    <div class="form-row px-3 row-3">
        <div class="col-md-6 mb-3 office-hours">
            <label for="officeHours">Office Hours</label>
            <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="officeHours" rows="5">{$profile->officeHours}</textarea>
        </div>
        <div class="col-md-6 mb-3 about">
            <label for="about">About Me</label>
            <textarea class="form-control wysiwyg wysiwyg-syllabus-standard" name="about" rows="5">{$profile->about}</textarea>
        </div>
    </div>
	
	<div class="my-3">
		<input type="submit" name="command[save]" value="Save Profile" class="ml-3 btn btn-success">
	</div>

    	{generate_form_post_key}
	</form>
</div>