<div class="row">
    <div class="col">
        <h1>Services module - <small>AT Office API</small></h1>
        <p>Set the information for the AT Office API service.</p>
    </div>
</div>

<form class="form-horizontal" action="{$smarty.server.REQUEST_URI}" method="post">
    <div class="data-form">
        <div class="information-panes">
            <div id="primary-information" class="show-first primary-information-fields information-fields">
                <div class="form-group">
                    <div class="col-10">
                        <label for="atoffice-api-url">AT Office API URL</label>                   
                        <input class="form-control" type="text" name="atoffice-api-url" id="atoffice-api-url" value="{$atofficeApiUrl}">
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