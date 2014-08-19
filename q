commit 1c3eaced3b9f96beccec40d53f7a4fee6a18d1b9
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Tue Aug 5 10:54:38 2014 -0700

    Trim the spaces from course id.

commit 650d97adea14ecd60073964365d48ad15a8d6a70
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Tue Aug 5 10:22:18 2014 -0700

    Fix the table on update page.

commit 128b7ae8c38243972a19582eef1a1248cb288276
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Tue Aug 5 10:22:02 2014 -0700

    Add drop statement.

commit 3d2ffbbe1cdcca4855028fa9a7ab2af237588b41
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Tue Aug 5 10:05:03 2014 -0700

    Change in section number format cause last digit to be cut off on import.

commit e66e2015d84d244d699a5b8548382885ab57e72e
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Tue Aug 5 09:37:35 2014 -0700

    Did not save update script sql.

commit 57fce8f5792238296fa3d2692a0d10e17ae99089
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Tue Aug 5 09:28:02 2014 -0700

    More fixes for syllabus.

commit 8138de23cb294e761c3ef862611d573625a283e2
Author: Saru Mehta <sarmehta88@gmail.com>
Date:   Mon Aug 4 16:07:05 2014 -0700

    Make changes to syllabus to work with class data

commit 36d3a814974b366ae73a51ff40c413d30b402979
Merge: c5984eb 654b795
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Fri Dec 6 13:16:16 2013 -0800

    Merge branch 'master' of git.at.sfsu.edu:syllabus

commit c5984ebafc3c23265b1ec590db9726ab14e88b63
Author: Pony Smith <pony@sfsu.edu>
Date:   Fri Dec 6 13:13:58 2013 -0800

    Changed smarty.const.CURRENT_URL to smarty.server.REQUEST_URI in every template.

commit 654b7957538375f7b2f4cc98a8a0c33dbb5560b4
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Fri Dec 6 11:51:42 2013 -0800

    smarty const call was causing php header injection vulnerability.

commit 2b0b50727048c4029119594f54373dc3feaa75b3
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Tue Jun 18 11:39:02 2013 -0700

    Remove rss link from front page.

commit 3e832a70cb29eafa97625255850c19dbf2d4f842
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Tue Jun 18 11:34:26 2013 -0700

    Changed the test on front page.

commit 6dc2e89a681cc66dabae5995a771685d1e2e04be
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Tue Jun 18 11:32:21 2013 -0700

    Bad HTML was breaking word export.

commit 7c22c18aee000e5a5547fae47319626f65ac79ac
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Fri Feb 3 16:30:26 2012 -0800

    Remove blog from front page of syllabus and remove link to blog archive from sidebar.

commit 1fc4b379a0ea61b1c08ca47a0e13b371f1d984a3
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Mon Jan 23 17:21:33 2012 -0800

    Website value was not being saved in user profile or course information.

commit f47584a8b5df3d05409b687b88d753a98a7c1c6a
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Mon Nov 21 14:27:27 2011 -0800

    Schedules edit form has a typo in it which made the date type not be preserved upon re-editing.

commit 39d3e851b98a088244968880784d9f3e7676adde
Author: Pony Smith <pony@sfsu.edu>
Date:   Tue Sep 6 13:14:14 2011 -0700

    Added the Schedule Due field to HTML allowed

commit cd1a1e6c543da8ecbd096ea9876fd25ca0ddc321
Author: Pony Smith <pony@sfsu.edu>
Date:   Fri Sep 2 15:14:35 2011 -0700

    Changed contact email address to 'online@sfsu.edu'

commit a834851a7aa525ede8ffd20c39c0d08986d0e3ad
Author: Pony Smith <pony@sfsu.edu>
Date:   Wed Aug 24 10:34:29 2011 -0700

    Modified default value in Assign Users form

commit ccab886f29b4422b23bc6615e5ce65a548ed2c94
Author: Pony Smith <pony@sfsu.edu>
Date:   Wed Aug 24 10:31:48 2011 -0700

    Adding template files for Test user creation and User assignment

commit 9ecaebb18ae975504d3d00f0b1a2ad624e19a38c
Author: Pony Smith <pony@sfsu.edu>
Date:   Wed Aug 24 10:25:21 2011 -0700

    Modified Syllabus import and added test users
    
    Mofidied the Syllabus import method in SystemModel to prevent updating the course description and overwriting faculty edits. Added a public method that can be run on sites in debug mode (non-production) which will create the four test IDP users in the Syllabus Users Table (T60000001 - T60000004).  T60000001 is also granted administrative permissions.  Finally, created an additional function in System Admin for assigning users to classes as Instructor or Student to help facilitate testing without having to manipulate the DB directly.

commit bf957de68d432f9aa8c0ed2ec53d3bf7a96e1dbc
Author: Pony Smith <pony@sfsu.edu>
Date:   Wed Jul 27 12:52:23 2011 -0700

    Modifications to Cron script

commit cf1553e9eda047451ce70773da8a4b98a13f0164
Author: Pony Smith <pony@sfsu.edu>
Date:   Wed Jul 27 11:02:55 2011 -0700

    Removed the script for deleting orphans from DB
    
    The portion of the cron script that removes syllabi for courses no longer in the SIMS snapshot
    has been removed until a more defined retention policy can be adopted

commit aacf3e93f891f836998415db03df66c0a874244c
Author: Pony Smith <pony@sfsu.edu>
Date:   Thu Jun 23 15:05:42 2011 -0700

    Fixed draft deletion permission error and added blog archiving
    
    Fixed a permission that was only allowing admin users to delete drafts.
    Added a 'post_archived' field to the blog table and allowed posts to be hidden / archived

commit 5630ec8a7f9438a52a23a78531e42df57bc17628
Author: Pony Smith <pony@sfsu.edu>
Date:   Wed Jun 1 10:26:27 2011 -0700

    Moved the system update function out of the IndexController and into a new SystemController

commit 4bdf0e454009072f9d450e37af8e2d7ff0652f65
Author: root <root@syllabus.dev.at.sfsu.edu>
Date:   Fri Apr 22 11:49:40 2011 -0700

    Modified the stats page

commit b3834f8a9de16146064a7b7716883e0a73279b51
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Mon Apr 11 16:49:40 2011 -0700

    Drafts were not being saved.

commit 1066794cc3fae5a82e25a53ab9d1dd4206f9d4d7
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Wed Apr 6 11:55:06 2011 -0700

    Added setting drafts permission to Permissions::buildUserPermissions.

commit c6e47d480a417c2fa85a6a2482ee892731407ab0
Author: Charles O'Sullivan <chsoney@sfsu.edu>
Date:   Tue Apr 5 11:54:08 2011 -0700

    getSyllabusInstructor did not return uder id.

commit d1c48a8c82119353c0a2ed34ae5aebaff510cf3a
Author: Charles O'Sullivan <cosullivan@syllabus.dev.at.sfsu.edu>
Date:   Tue Apr 5 11:02:51 2011 -0700

    getSyllabusInstructor on Syllabus Model did not execute query.

commit e487db3f9d77c1e861a9ac42da27a4fbb7383933
Author: psmith <psmith@syllabus.dev.at.sfsu.edu>
Date:   Tue Mar 22 16:53:15 2011 -0700

    Fixed problem with CURRENT_URL constant
    
    The CURRENT_URL constant was incorrectly including the query string passed from mod_rewrite.  The problem has been fixed.
    The use of that $_GET query string was also removed from Authenticate.class.php->isAuthenticated method and has been
    replaced with the now-correctly working CURRENT_URL constant

commit 736b82ad910961aabf69afd0d20ba74f4187265c
Author: psmith <psmith@syllabus.dev.at.sfsu.edu>
Date:   Tue Mar 22 16:15:50 2011 -0700

    Fixed error in SyllabusModel.class.php
    
    Fixed an error - foreach() expecting array in getStudentSyllabi()

commit 565fa603c66c8ef763ea65ade555cd1be3cbd2e6
Author: psmith <psmith@syllabus.dev.at.sfsu.edu>
Date:   Tue Mar 22 15:46:35 2011 -0700

    Fixed Shibboleth Logout
    
    Changed the Shibboleth logout to redirect directly to the idp logout page to ensure successful logout

commit 6cedaf3bf8a5df1334caf798e856a333461d61a5
Author: psmith <psmith@syllabus.dev.at.sfsu.edu>
Date:   Tue Mar 22 13:08:13 2011 -0700

    Additions to CKeditor
    
    Additions to CKeditor as well as adding a background image for navigation in IE

commit 74aa81ccbffba05af9dd1cb094d46bfe543a7a72
Author: psmith <psmith@syllabus.dev.at.sfsu.edu>
Date:   Tue Mar 22 11:05:59 2011 -0700

    Various AJAX fixes
    
    Fixed AJAX response to properfly format phone numbers
    Removed error-causing call to Utility object in rendering of TA information
    Various CSS tweaks for IE

commit 99e8478fcf6da3ea14f0cb84d353b19a13c41853
Author: psmith <psmith@syllabus.dev.at.sfsu.edu>
Date:   Wed Mar 16 09:56:45 2011 -0700

    Updated CKeditor version to fix bug with IE9
    
    New version of CKeditor as well as some smaller changes to improve rendering on various versions of IE

commit 63bb7fa96f885f86049ff86641200c06c38217e7
Author: Pony Smith <pony@sfsu.edu>
Date:   Thu Mar 10 13:14:56 2011 -0800

    Initial commit into Git.  This version of Syllabus updates the current verion's framework
