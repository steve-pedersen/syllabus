<div class="col-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
    <h1>Syllabus Tool Login</h1>
    <div class="panel-padding panel panel-default">
        <div class="panel-heading">Login</div>
        <div class="panel-body">
                <form method="post" action="login/complete/sfsu-pw" class="prominent data">
                    <div class="form-group">
                        <input type="text" class="form-control" id="password-username" name="username" placeholder="Username/Email" alt="Username/Email">
                    </div>
    
                    <div class="form-group">
                        <input type="password" class="form-control" id="password-password" name="password" placeholder="Password" alt="password">
                    </div>
    
                        
                    <div class="form-group">
                        {generate_form_post_key}
                        <button class="command-button btn btn-primary" type="submit" name="command[login]">Login</button>
                    </div>
                </form>
        </div>
    </div>
</div>