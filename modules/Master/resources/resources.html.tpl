<!DOCTYPE html>
{assign var="appName" value="Student Resources at SF State"}
<html>
	<head>
{*may not be needed*}		<meta charset="utf-8">
{*may not be needed*}    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>{if $pageTitle}{$pageTitle|escape} &mdash; {/if}{$appName|escape}</title>
		<base href="resources">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
		<link rel="stylesheet" type="text/css" href="assets/scss/app.scss.css">
		<link rel="stylesheet" type="text/css" href="assets/css/fontawesome-all.min.css">
		<link rel="stylesheet" type="text/css" media="print" href="assets/css/app-print.css">
		<link href='//fonts.googleapis.com/css?family=Montserrat:400,700|Lato:400,700,900' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" id="Lato-css" href="https://fonts.googleapis.com/css?family=Lato%3A1%2C100%2C300%2C400%2C400italic%2C700&amp;ver=4.9.9" type="text/css" media="all">
		<link rel="shortcut icon" type="image/x-icon" href="assets/images/logo.png" />
		<script>document.write('<link rel="stylesheet" type="text/css" href="assets/css/app-js.css" media="screen">');</script>
		<meta property="og:title" content="Student Resources at SF State" />
		<meta property="og:description" content="This website compiles together all the various resources that are available to students at SF State." />
		<meta name="twitter:title" content="Student Resources at SF State">
		<meta name="twitter:description" content="This website compiles together all the various resources that are available to students at SF State.">
		<meta property="og:image" content="assets/images/resources-icon.png" />
		<meta name="twitter:image:src" content="assets/images/resources-icon.png">
<style type="text/css">


</style>
	</head>

	<body id="resourcesMasterTemplate">
		<a href="#mainContent" class="sr-only sr-only-focusable">Skip Navigation</a>

    <div class="wrapper" id="viewTemplate">
        <!-- Page Content  -->
        <div id="content">

			<header class="at">

				<span id="goToTop" class="hidden" aria-hidden="true"></span>
				<div class="container">
				<nav class="navbar navbar-expand-lg navbar-dark">
					<div class="navbar-brand d-block-inline mr-auto mobile-brand">
	                	<a class="" href="https://sfsu.edu">SAN FRANCISCO STATE UNIVERSITY</a>
						<!-- &nbsp;|&nbsp; -->
						<small id="smallLink" class="pl-2">
							<a href="resources">Student Resources</a>
						</small>
					</div>
				</nav>
		    	</div>
			</header>	
			<div id="imagebar"></div>

			<main role="main" class="" id="mainContent"> 
				<!-- MAIN CONTENT -->
				{include file=$contentTemplate}
			</main>
        </div>
    </div>      

	<footer class="sticky-footer fixed-bottom" id="footer">
		<nav class="navbar at-footer">

			<div class="footer-row-2 container-fluid">
				<div class="container">
					<div class="row">
						<div id="contact-university" class="col-sm-6">
							<a href="https://sfsu.edu/"> <img src="assets/images/SFState_H_rgb.jpg" alt="San Francisco State University Logo" height="50" class="logo"></a>
							<ul class="list-unstyled">
								<li><a href="https://sfsu.edu/">San Francisco State University</a></li>
								<li class="first"><a href="https://www.calstate.edu/">A California State University Campus</a></li>
							</ul>
						</div>
						<div id="contact-local" class="col-sm-6">
							<ul class="list-unstyled">
								<li><strong><a href="https://at.sfsu.edu">Academic Technology</a></strong></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</nav>
	</footer>
		<script src="assets/js/app.js"></script>
		{if $gtagId}{literal}
		<script async src="https://www.googletagmanager.com/gtag/js?id={/literal}{$gtagId}{literal}"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		​
		  gtag('config', '{/literal}{$gtagId}{literal}');
		</script>
		{/literal}{/if}
	</body>
</html>
