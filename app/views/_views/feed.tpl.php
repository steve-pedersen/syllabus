<?xml version="1.0" encoding="iso-8859-1"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    
    <title>Syllabus @ SF State Blog</title>
    <description>News and Announcements for Syllabus @ SF State</description>
    <link>http://syllabus.sfsu.edu</link>
    <pubDate>{$pub_date}</pubDate>
    <lastBuildDate>{$pub_date}</lastBuildDate>
    <ttl>3600</ttl>
    <atom:link href="{$smarty.const.BASEHREF}blog/feed" rel="self" type="application/rss+xml" />    

    {foreach from=$posts item=p}
    
    <item>
        <title><![CDATA[ {$p.post_title} ]]></title>
        <description><![CDATA[ {$p.post_text} ]]></description>
        <link>{$smarty.const.BASEHREF}blog/view/{$p.post_id}</link>
        <guid isPermaLink="true">{$smarty.const.BASEHREF}blog/view/{$p.post_id}</guid>
    </item>
    
    {foreachelse}
    
    <item>
        <title>No Blog Posts</title>
        <description>There are currently no blog posts for Syllabus @ SF State</description>
        <link>{$smarty.const.BASEHREF}</link>
    </item>
    
    {/foreach}
    
</channel>
</rss>