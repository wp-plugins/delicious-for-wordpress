=== del.icio.us for Wordpress ===
Tags: del.icio.us, delicious, bookmarks
Requires at least: 2.0
Tested up to: 2.3
Stable tag: trunk

del.icio.us for WordPress displays your latest del.icio.us bookmarks in your WordPress blog.

== Description ==

del.icio.us for WordPress displays your latest del.icio.us bookmarks in your WordPress blog.

**Features**

    * Simply
    * Customizable
    * Widget ready
    * Uses Wordpress resources (no extra files needed)
    * No options page (yes, it’s a feature)
    * Displays bookmarks tags (optional)
    * Filter bookmarks by tag(s) (optional)
    
**Usage**

If you use WordPress widgets, just drag the widget into your sidebar and configure.
If widgets aren’t your thing, use the following code to display your latest bookmarks:

`<?php delicious_bookmarks("username"); ?>`

del.icio.us for WordPress also has several configurable options. Here’s what you can configure:

`<?php delicious_bookmarks("username", [bookmarks], [list], [timestamp], [display-tags], ["filter-tags"]); ?>`

    * username: your del.icio.us username
    * bookmarks: number of bookmarks to show
    * list: show bookmarks in a unordered list
    * timestamp: show a relative timestamp
    * display-tags: show bookmark tags
    * filter-tags: only show bookmarks under the given tag(s). Format: cat+dog+fish

Only username is required. The other parameters will take this default values:

`<?php delicious_bookmarks(‘username’, 5, true, true, false, ”); ?>`

This is: 5 bookmarks, in a list, with timestamps, no display tags and no filtering tags.

So, if I wanted to show my last 5 del.icio.us bookmarks, not in a list, with timestamps, no tags and filtering by wordpress and plugin tags I would use the following:

`<?php delicious_bookmarks("username", 5, false, true, false, "wordpress+plugin"); ?>

**Customization**

The plug in provides the following CSS classes:

    * ul.delicious: the main ul (if list is activated)
    * li.delicious-item: the ul items (if list is activated)
    * p.delicious-bookmark: each one of the paragraphs (if tracks > 1)
    * span.delicious-timestamp: the timestamp span class
    * a.delicious-link: the bookmark link class
    * div.delicious-tags: the tags container div
    * a.delicious-link-tag: the tag link class

== Installation ==

Drop delicious.php into /wp-content/plugins/ and activate the plug in.

== Version History ==

1.3.2 - 2007/09/26
 
     * Fixed Widget configuration errors. Props to Dave B. 

1.3.1 - 2007/09/09

    * Fixed more HTML encoding issues. Props to Kyle. 

1.3 - 2007/09/08

    * Added display description option. Suggested by Kyle.
    * Fixed some HTML encoding issues. Props to Dave. 
    
1.2 - 2007/09/05

    * Added tag filter option. Suggested by Geoffrey Harder

1.1 - 2007/08/28

    * Added display tag support

1.0 - 2007/08/22

    * Initial Release

**Credits**

Ronald Heft - The plugin is highly based in his Pownce for Wordpress, so the major part of the credits goes to him.

**Contact**

Suggestion, fixes, rants, congratulations, gifts et al to rick[at]jinlabs.com