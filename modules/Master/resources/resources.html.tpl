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
		<meta property="og:title" content="Syllabus Tool at SF State" />
		<meta property="og:description" content="There is a new way to create and share your syllabus with your students! Users are encouraged to explore the new Syllabus application, build out syllabi, or transfer content from the existing tool into the new one." />
		<meta name="twitter:title" content="Syllabus Tool at SF State">
		<meta name="twitter:description" content="There is a new way to create and share your syllabus with your students! Users are encouraged to explore the new Syllabus application, build out syllabi, or transfer content from the existing tool into the new one.">
		<meta property="og:image"
		      content="assets/icons/logo_square_128-01.png" />
		<meta name="twitter:image:src"
		      content="assets/icons/logo_square_128-01.png">
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
	                	<a class="" href="{$baseUrl}{if $viewer}/syllabi{/if}">
						<span class="sidebar-text pr-2 brand-text">Student Resources</span></a>
					</div>

					<button class="navbar-toggler mr-3 ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav ml-auto">


						</ul>
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
