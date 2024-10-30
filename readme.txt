=== Countdown to Next Post ===
Contributors: Scott Mulligan
Tags: countdown, next post, upcoming post, time until next post, upcoming post countdown, next post, date of upcoming post
Requires at least: 2.6
Tested up to: 2.8.1
Stable tag: 1.0

This plugin will display a countdown timer that counts down towards your next scheduled post.

== Description ==

This plugin will display a countdown timer that counts down towards your next scheduled post. 

The countdown to your next post can be added as a widget, manually to your sidebar, or into any post or page.

This plugin is perfect for people who write posts in advance and schedule them to be posted at a later time. This plugin will automatically search the wordpress database for the next scheduled post and display a countdown so your readers know exactly when it will be posted.

== Installation ==

Download and move "Countdown to Next Post" into your Wordpress plugins directory.

Activate this plugin on your admin panel.

= Widget =

You can activate this plugin as a sidebar widget.

= Manually Insert into Sidebar =

Instead of using the widget, you can also manually add the following code into your sidebar.php file:

`<li>
<h2>Next Post</h2>
<ul>
<li>
<?php function_exists('scott_timer')?scott_timer():NULL; ?>
</li>
</ul>
</li><li id='countdown'><h2>Countdown:</h2>
<ul>`

= Insert into a page or post =

If you want to insert the "Countdown to Next Post" into a page or post, you can add the following shortcode into the HTML editor for a post or page.

`[countdown_to_next_post]`

== Frequently Asked Questions ==

= I deleted a post that was scheduled, but it still has the countdown for that deleted post. =

Yes, I am working on updating the code to fix this but there is an easy work around for now! If you have decided to delete a scheduled post then the WordPress database will not be updated right away. What you need to do is go and re-save any post and the WordPress database will be immediately updated.

= I am not getting a countdown. All I see is "No scheduled Posts". =

For this plugin to create a countdown timer there needs to be atleast one post scheduled to be posted at a future time. If you do not have any posts scheduled then you will not see a countdown because there is nothing to countdown to. I would suggest scheduling a test post so you can play around with the plugin and get the formatting how you want it.  

= How do I schedule a post to be posted at a later time? =

When you have completed writing a post, you have the option of posting it right away or scheduling it to post at a later time. You can do this by clicking on the "edit" link beside "Publish immediately" which is just above the save and publish buttons.

== Screenshots ==
1. Example on a test blog
