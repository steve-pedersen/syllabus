<!DOCTYPE html>
{assign var="appName" value=$app->configuration->appName}
<html>
	<head>
{*may not be needed*}		<meta charset="utf-8">
{*may not be needed*}    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>{if $pageTitle}{$pageTitle|escape} &mdash; {/if}{$appName|escape}</title>
		<base href="{$baseUrl|escape}/">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
		<link rel="stylesheet" type="text/css" href="assets/scss/app.scss.css">
		<link rel="stylesheet" type="text/css" href="assets/css/fontawesome-all.min.css">
		<link rel="stylesheet" type="text/css" media="print" href="assets/css/app-print.css">
		<link href='//fonts.googleapis.com/css?family=Montserrat:400,700|Lato:400,700,900' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" id="Lato-css" href="https://fonts.googleapis.com/css?family=Lato%3A1%2C100%2C300%2C400%2C400italic%2C700&amp;ver=4.9.9" type="text/css" media="all">
		<link rel="shortcut icon" type="image/x-icon" href="assets/icons/logo_square_512-01.png" />
		<script>document.write('<link rel="stylesheet" type="text/css" href="assets/css/app-js.css" media="screen">');</script>
	</head>

	<body>
		<a href="#mainContent" class="sr-only sr-only-focusable">Skip Navigation</a>

    <div class="wrapper" id="viewTemplate">
        <!-- Page Content  -->
        <div id="content">

			<header class="at">
				<span id="goToTop" class="hidden" aria-hidden="true"></span>
				<!-- <div class="container-fluid"> -->
				<nav class="navbar navbar-expand-lg navbar-light">
					<div class="navbar-brand d-block-inline mr-auto mobile-brand">
	                	<a class="" href="{$baseUrl}">
						<img src="assets/icons/logo_square_512-01.png" width="48" height="48" class="d-inline-block mr-3" alt="Syllabus Logo" id="brandLogo"><span class="sidebar-text pr-2 brand-text">Syllabus</span></a>
					</div>

					<button class="navbar-toggler mr-3 ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav ml-auto">
						{if $viewer}
							<li class="nav-item">
								<span class="navbar-text mr-3">Hello, {$userContext->account->firstName|escape}</span>
							</li>
							{if $pAdmin}
							<li class="nav-item">
								<a class="nav-link" href="admin"><i class="fas fa-cog"></i> Administrate</a>
							</li>
							{/if}

							<li class="nav-item mobile-link border-top">
								<a class="nav-link" href="syllabi">
									My Syllabi
								</a>
							</li>
						{if !$isStudent}
							<li class="nav-item mobile-link {if $page == 'start'}active{/if}">
								<a class="nav-link" href="syllabus/start">
									Create New Syllabus
								</a>
							</li>
							<li class="nav-item mobile-link {if $page == 'overview'}active{/if}">
								<a class="nav-link" href="syllabi?mode=overview">
									Overview
								</a>
							</li>
							<li class="nav-item mobile-link {if $page == 'courses'}active{/if}">
								<a class="nav-link" href="syllabi?mode=courses">
									Courses
								</a>
							</li>
							{if $privilegedOrganizations || $pAdmin}
								<li class="nav-item mobile-link border-top">
									<a class="nav-link" href="organizations" id="sidebarMyOrganizations">
										My Organizations
									</a>
								</li>
								{assign var=departments value=$privilegedOrganizations['departments']}
								{if (!empty($departments) && count($departments) > 1) || $pAdmin}
									<li class="nav-item mobile-link {if $page == 'departments'}active{/if}">
										<a class="nav-link" href="departments">
											Departments
										</a>
									</li>
								{elseif !empty($departments) && count($departments) == 1}
									{foreach $departments as $dept}
									<li class="nav-item mobile-link {if $page == 'departments'}active{/if}">
										<a class="nav-link" href="departments/{$dept->id}">
											{$dept->name}
										</a>
									</li>
									{/foreach}
								{/if}

								{assign var=colleges value=$privilegedOrganizations['colleges']}
								{if (!empty($colleges) && count($colleges) > 1) || $pAdmin}
									<li class="nav-item mobile-link {if $page == 'colleges'}active{/if}">
										<a class="nav-link" href="colleges">
											Colleges
										</a>
									</li>
								{elseif !empty($colleges) && count($colleges) == 1}
									{foreach $colleges as $college}
									<li class="nav-item mobile-link {if $page == 'colleges'}active{/if}">
										<a class="nav-link" href="colleges/{$college->id}">
											<span class="sidebar-text">
												{$college->name}
											</span>
										</a>
									</li>
									{/foreach}
								
								{/if}
							{/if}
							{/if}
						{else}
							<li class="nav-item">
								<a class="login-button nav-link" href="{$app->baseUrl('login')}">Login</a>
							</li>
						{/if} 
						</ul>
					{if $viewer}
						<form method="post" action="logout" class="form logout-form p-2">
							<button class="btn btn-outline-primary logout " type="submit" name="command[logout]" id="logout-button" value="Logout">Logout</button>
						</form>
					{/if}
					</div>
				</nav>
		    	<!-- </div> -->
			</header>	

			<main role="main" class="col pr-3 my-5" id="mainContent"> 
				<!-- MAIN CONTENT -->
				{include file=$contentTemplate}
			</main>
        </div>
    </div>      
		<script src="assets/js/app.js"></script>
	</body>
</html>
