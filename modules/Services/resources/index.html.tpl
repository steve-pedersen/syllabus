<div class="row">
    <div class="col">
        <h1>Services module - <small>Screenshotter</small></h1>
        <p>Set the information for the Screenshotter service.</p>
    </div>
</div>

<form class="form-horizontal" action="{$smarty.server.REQUEST_URI}" method="post">
    <div class="data-form">
        <div class="information-panes">
            <div id="primary-information" class="show-first primary-information-fields information-fields">
                <div class="form-group">
                    <div class="col-10">
                        <label for="screenshotter-api-url">Screenshotter URL</label>                   
                        <input class="form-control" type="text" name="screenshotter-api-url" id="screenshotter-api-url" value="{$screenshotterApiUrl}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-10">
                        <label for="screenshotter-api-key">Screenshotter API Key</label>                 
                        <input class="form-control" type="text" name="screenshotter-api-key" id="screenshotter-api-key" value="{$screenshotterApiKey}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-10">
                        <label for="screenshotter-api-secret">Screenshotter API Secret</label>
                        <input class="form-control" type="text" name="screenshotter-api-secret" id="screenshotter-api-secret" value="{$screenshotterApiSecret}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-10">
                        <label for="screenshotter-default-img-name">Screenshotter Default Image Name</label>
                        <input class="form-control" type="text" name="screenshotter-default-img-name" id="screenshotter-default-img-name" value="{$screenshotterDefaultImgName}" aria-describedby="screenshotter-default-img-name">
                        <small id="screenshotter-default-img-name" class="form-text text-muted">
                          Add a description for this setting here....
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <div class="col">
                {generate_form_post_key}
                <input class="btn btn-primary" type="submit" name="command[save]" value="Save" />
                <a class="btn btn-link" href="admin">Cancel</a>
            </div>
        </div>
    </div>
</form>