<div class="drag-drop-upload text-center">
    <form class="box" id="dragDropUploadForm" method="post" action="{$action}" enctype="multipart/form-data">
        <div class="box__input">
        <svg class="box__icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z" /></svg>
    {if $singleFile}
        <input type="file" name="file" id="file" class="box__file" />
    {else}
        <input type="file" name="file[]" id="file" class="box__file" data-multiple-caption="#count# files selected" multiple />
    {/if}
        <label for="file"><strong>Choose a file</strong><span class="box__dragndrop"> or drag it here</span>.</label>
        <button class="box__button btn btn-primary" type="submit">Upload</button>
        </div>
<div id="progress">
    <div class="bar" style="width: 0%;"></div>
</div>
        <div class="box__uploading">Uploading&hellip;</div>
        <div class="box__success">Done! </div>
        <div class="box__error">Error! <span></span>. </div>
        {generate_form_post_key}
        <input type="hidden" name="uploadedBy" value="{$uploadedBy}">
    </form>
</div>


<!-- 

<div>
    
<div class="drag-drop-upload text-center">
    <form class="box" id="dragDropUploadForm" method="post" action="{$action}" enctype="multipart/form-data">
        <div class="box__input">
        <svg class="box__icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z" /></svg>
    {if $singleFile}
        <input type="file" name="file" id="file" class="box__file" />
    {else}
        <input type="file" name="file[]" id="file" class="box__file" data-multiple-caption="#count# files selected" multiple />
    {/if}
        <label for="file"><strong>Choose a file</strong><span class="box__dragndrop"> or drag it here</span>.</label>
        <button class="box__button btn btn-primary" type="submit">Upload</button>
        </div>
        <div class="box__uploading">Uploading&hellip;</div>
        <div class="box__success">Done! <a href="https://css-tricks.com/examples/DragAndDropFileUploading//?" class="box__restart" role="button">Upload more?</a></div>
        <div class="box__error">Error! <span></span>. </div>
        {generate_form_post_key}
        <input type="hidden" name="uploadedBy" value="{$uploadedBy}">
        <div class="form-group mt-5">
            <div class="col-xs-12">
                <input type="submit" name="command[upload]" id="saveResource" value="Upload Image" class="btn btn-secondary" />
            </div>
        </div> 
    </form>
</div>




 <div class="drag-drop-upload text-center">
    <form  method="post" action="{$action}" enctype="multipart/form-data">
        <div class="box__input">
        <svg class="box__icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z" /></svg>
        <div class="form-group upload-form mb-5">
            <label for="image" class="field-label field-linked">Upload thumbnail image</label>       
            <input class="form-control-file" type="file" name="file" id="image" />
            {foreach item='error' from=$errors.image}<div class="error">{$error}</div>{/foreach}
            <div class="col-xs-12 help-block text-center">
                <p id="type-error" class="bg-danger" style="display:none"><strong>There was an error with the type of file you are attempting to upload.</strong></p>
            </div>          
        </div>
        <div class="form-group mt-5">
            <div class="col-xs-12">
                <input type="submit" name="command[upload]" id="saveResource" value="Upload Image" class="btn btn-secondary" />
            </div>
        </div> 
        {generate_form_post_key}
        <input type="hidden" name="uploadedBy" value="{$uploadedBy}">
    </form>
</div>



</div>
 -->