{foreach from=$posts item=p}
    
    <div class="blog_post">
        <h2 class="blog_header{if $p.post_important == 1} important{/if}">
            {if $p.post_important == 1}<img src="images/post_important.png" alt="Important Message" />{/if}
            {$p.post_title}
        </h2>
        <p class="blog_post_info">
            Posted on {$p.post_publish_date|date_format:"%A, %B %e, %Y"}
        <p>
        <p>
            {$p.post_text}
        </p>
    </div>

{foreachelse}
    
    <h1><em>Syllabus</em> is here</h1>
    <p>
    <em>Syllabus</em> is the online syllabus-building tool created by <a href="http://at.sfsu.edu" class="popup">Academic Technology</a>.
    The tool has undergone a huge upgrade this summer, based heavily on feedback from Spring 2010 pilot participants.
    The new system is sleeker, faster and easier to use.  With <em>Syllabus</em>, you can:
    </p>
    
    <ul>
        <li>Easily publish syllabi in multiple accessible formats;</li>
        <li>Link directly to your syllabi from the matching courses in <a href="http://ilearn.sfsu.edu" class="popup">iLearn</a>;</li>
        <li>Set permissions on your syllabi to restrict who may view them;</li>
        <li>And many more functions</li>
    </ul>
    
    <p>
    If you are a faculty member, you may begin building your online syllabi by <a href="syllabus">Logging in</a>. If you have questions
    about <em>Syllabus</em>, you please <a href="contact">contact the <em>Syllabus</em> team</a>.
    </p>

{/foreach}