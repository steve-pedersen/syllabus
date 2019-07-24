<!DOCTYPE html>
{assign var="appName" value=$app->configuration->appName}
<html>
	<head>
		<title>{if $pageTitle}{$pageTitle|escape} &mdash; {/if}{$appName|escape}</title>
		<base href="{$baseUrl|escape}/">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
		<!-- <link rel="stylesheet" type="text/css" href="assets/scss/app.scss.css"> -->
		<!-- <link rel="stylesheet" type="text/css" href="assets/css/fontawesome-all.min.css"> -->
		<!-- <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css"> -->
		<!-- <link rel="stylesheet" type="text/css" href="assets/css/export.css"> -->
		<!-- <link rel="stylesheet" type="text/css" media="print" href="assets/css/app-print.css"> -->
		<link href='//fonts.googleapis.com/css?family=Montserrat:400,700|Lato:400,700,900' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" id="Lato-css" href="https://fonts.googleapis.com/css?family=Lato%3A1%2C100%2C300%2C400%2C400italic%2C700&amp;ver=4.9.9" type="text/css" media="all">
		<link rel="shortcut icon" type="image/x-icon" href="assets/icons/logo_square_512-01.png" />
		<script>document.write('<link rel="stylesheet" type="text/css" href="assets/css/app-js.css" media="screen">');</script>
	</head>

	<body>
	    <div class="container" id="viewTemplate">
	        {include file=$contentTemplate}
	    </div>

		<script src="assets/js/app.js"></script>
	</body>
</html>
