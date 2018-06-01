<h1>Welcome module</h1>

<p>Determine text to show on the front page.</p>

<form action="{$smarty.server.REQUEST_URI}" method="post" class="data-entry">
    <div class="content-column">
        <div class="information-panes">
            <div id="primary-information" class="show-first primary-information-fields information-fields">
                <div class="field">
                    <div class="field-label-wrapper">
                        <label class="field-label field-linked" for="welcome-text">Welcome Text</label>
                    </div>
                    <div class="field-control-wrapper field">
                        <textarea class="form-control" name="welcome-text" id="welcome-text" style="height:auto;" rows="10">{$welcomeText}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="commands">
            {generate_form_post_key}
            {if $module->inDatasource}<input type="hidden" name="module[id]" value="{$module->id}" />{/if}
            <button class="btn btn-primary" type="submit" name="command[save]">Save</button>
            <a class="btn btn-secondary" href="admin">cancel</a>
        </div>
    </div>
</form>