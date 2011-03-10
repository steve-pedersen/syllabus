<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Syllabus - {$page_title|strip_tags}</title>
<base href="{$smarty.const.BASEHREF}" />

{$css_includes}
<!--[if lte IE 7]> <link href="assets/css/ie.css" type="text/css" rel="stylesheet" /> <![endif]-->
{$js_includes}

</head>


<body>
<div id="skiplinks"><a href="{$smarty.const.CURRENT_URL}#content-anchor">Skip to Content</a></div>

<div id="header_container">
	<div id="header">
        <a href=""><img src="images/banner.png" alt="Syllabus Home" /></a>
	</div> <!-- / header -->
</div> <!-- / headerContainer -->

{$breadcrumbs}
{$navlinks}

<div id="page_container">
<div id="page">
	
	<div id="page_messages">
	{if $messages}
		{$messages}
	{else}
		<div class="message">&nbsp;</div>
	{/if}
	</div>
    
    <div id="page_content" {if isset($page_sidebar)}class="with-sidebar"{/if}>
        <a id="content-anchor"></a>
        {$page_content}
    </div> <!-- / page_content -->
    
    {if isset($page_sidebar)}
    <div id="page_sidebar">
    {$page_sidebar}
    </div> <!-- / page_sidebar -->
    {/if}    
    
    <div style="clear: both;"></div>
</div> <!-- / page -->
</div> <!-- / pageContainer -->


<div id="footer_container">
	<div id="footer">
		<div id="sfsuLogo"><a class="inline-block" href="http://www.sfsu.edu"><img src="images/sfsu_logo.png" alt="SFSU Home" /></a></div>
		<div>
            <div>San Francisco State University, 1600 Holloway Ave. San Francisco, <abbr title="California">CA</abbr>, 94132</div>
            <div>
                <a href="contact">Contact Syllabus</a>
                <a href="accessibility" class="last">Accessibility</a>
            </div>
        </div>
	</div> <!-- / footer -->
</div> <!-- / footerCont -->


<!-- Google Analytics code -->
{literal}
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-15830623-1");
pageTracker._trackPageview();
} catch(err) {}</script>
{/literal}
<!-- End Analytics code -->


</body>
</html>
