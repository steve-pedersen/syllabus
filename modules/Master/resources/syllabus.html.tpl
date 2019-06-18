<!DOCTYPE html>
{assign var="appName" value=$app->configuration->appName}
<html>
	<head>
{*may not be needed*}		<meta charset="utf-8">
{*may not be needed*}    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>{if $pageTitle}{$pageTitle|escape} &mdash; {/if}{$appName|escape}</title>
		<base href="{$baseUrl|escape}/">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
		<!--Import Google Icon Font-->
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="assets/scss/app.scss.css">
		<!-- <link rel="stylesheet" type="text/css" href="assets/css/fontawesome-all.min.css"> -->
		<link type="text/css" href="assets/css/all.css" rel="stylesheet">
		<!-- <script defer src="assets/js/all.js"></script> -->
		<link rel="stylesheet" href="assets/js/highlight/styles/monokai-sublime.css">
		<link rel="stylesheet" type="text/css" media="print" href="assets/css/app-print.css">
		<link href='//fonts.googleapis.com/css?family=Montserrat:400,700|Lato:400,700,900' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" id="Lato-css" href="https://fonts.googleapis.com/css?family=Lato%3A1%2C100%2C300%2C400%2C400italic%2C700&amp;ver=4.9.9" type="text/css" media="all">
		<link rel="shortcut icon" type="image/x-icon" href="assets/icons/logo-3-128.png" />
		<script>document.write('<link rel="stylesheet" type="text/css" href="assets/css/app-js.css" media="screen">');</script>
	</head>

	<body>
		<a href="#mainContent" class="sr-only sr-only-focusable">Skip Navigation</a>

		<header class="at">
			<span id="goToTop" class="hidden" aria-hidden="true"></span>
			<nav class="navbar navbar-expand-lg navbar-light">
				<div class="navbar-brand d-block-inline col-md-3 col-lg-2 col-xl-2 d-flex justify-content-between">
					<a class="" href="{$baseUrl}">
						<img src="assets/icons/logo-5-256.png" width="41" height="41" class="d-inline-block mr-3" alt="Syllabus Logo" id="brandLogo"><span class="sidebar-text pr-2 brand-text">Syllabus</span></a>
				</div>				
				<button class="navbar-toggler mr-3" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
		</header>		

		{if $flashContent}
		<div id="user-message" class="alert alert-{$flashClass} alert-dismissable my-3 rounded-0 fade show" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<div class="primary">{$flashContent}</div>
		</div> 
		{/if}

		<div class="container-fluid" id="viewSyllabus">
			<div class="row">
							
				{if $headerPartial}
				<!-- <main role="main" class="col-md-9 col-lg-10 px-3 mt-0" id="mainContent"> -->
				<main role="main" class="col pr-3 mt-0 mb-3 min-vh-70" id="mainContent">
					{include file=$headerPartial headerVars=$headerVars}
				{else}
				<!-- <main role="main" class="col-md-9 col-lg-10 px-3 mt-3" id="mainContent"> -->
				<main role="main" class="col pr-3 mt-3 mb-3 min-vh-70" id="mainContent">
				{/if}

					<!-- MAIN CONTENT -->
					{include file=$contentTemplate}
				</main>

			</div>
		</div>       


		<script src="assets/js/app.js"></script>


		{if $analyticsCode}{literal}
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', '{/literal}{$analyticsCode}{literal}', 'auto');
			ga('send', 'pageview');
		</script>
		{/literal}{/if}

	</body>
</html>
