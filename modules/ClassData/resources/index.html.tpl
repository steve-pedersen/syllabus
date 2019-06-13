<div class="col">
    <div class="col-xs-12">
        <h1>ClassData module</h1>
        <p>Set the information for the ClassData/SIMS service.</p>
    </div>
</div>

<form class="form-horizontal" action="{$smarty.server.REQUEST_URI}" method="post">
    <div class="data-form col">
        <div class="information-panes">
            <div id="primary-information" class="show-first primary-information-fields information-fields">
                <div class="form-group">
                    <div class="col-xs-12">
                        <label for="classdata-api-url">ClassData URL</label>
                    
                        <input class="form-control" type="text" name="classdata-api-url" id="classdata-api-url" value="{$classdataApiUrl}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <label for="classdata-api-key">ClassData API Key</label>
                    
                        <input class="form-control" type="text" name="classdata-api-key" id="classdata-api-key" value="{$classdataApiKey}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <label for="classdata-api-secret">ClassData API Secret</label>
                    
                        <input class="form-control" type="text" name="classdata-api-secret" id="classdata-api-secret" value="{$classdataApiSecret}">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12">
                {generate_form_post_key}
                <input class="btn btn-primary" type="submit" name="command[save]" value="Save" />
                <a class="btn btn-link" href="admin">Cancel</a>
            </div>
        </div>
    </div>
</form>