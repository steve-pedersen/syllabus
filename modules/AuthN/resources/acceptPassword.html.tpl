<div class="col col-sm-8 col-md-6 col-lg-4 mx-auto mb-5">
    <h1 class="text-center display-4">Syllabus Tool Login</h1>
    <div class="card mx-auto">
        <div class="card-header">Login</div>
        <div class="card-body">
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