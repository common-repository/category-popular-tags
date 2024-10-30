=== Category Popular Tags ===
Contributors: kusimo
Tags: category, tags,popular
Donate link: https://www.paypal.me/smoothkush
Requires at least: 3.5
Tested up to: 5.2.4
Stable tag: 1.0
Requires PHP: 5.6
License: GPLv2

Display popular tags on achieve/category page of your theme using sortcode or by calling a function.

== Description ==
You can make use of category popular tag to display most used tag on your blog. This will give list of frequently used tag anywhere you place the shortcode or funtion in your theme.

For example [popular_category_tags count=10 type="popular-tags" category_id="1"]. 
You can also call this function from your theme file: cush_category_popular_tag() for basic output.

When using the shortcode type is required. category id, count is not required. if no category id suplied, the plugin will default to current post category available.
For example [popular_category_tags type="popular-tags" ]. 

You can find category tags options under Settings page
Settings -> Category Tags

Credit : (https://wordpress.stackexchange.com/questions/261617/display-most-popular-tags-of-category)


== Installation ==

   1.  Upload the plugin files to the /wp-content/plugins/plugin-name directory, or install the plugin through the WordPress plugins screen directly.
   2.  Activate the plugin through the ‘Plugins’ screen in WordPress


== Frequently Asked Questions ==
= How do I use popular category plugin in my theme? =

In WordPress theme directory /wp-content/theme/your-theme/, open category.php and page this function   where you want the popular tags to appear.

To use shortcode paste this  where you want the tags to appear. If no count is specified, the count found on settings page will be used. 

= How do I show popular tags from a specific category only? =

Use this shortcode [popular_category_tags count=10 type="popular-tags" category_id="1"]. In the shortcode category_id is the id of the category you want to show.

= Can this be used on single post? =

Yes. You can add the shortcode to single post.




== Screenshots ==
1. You will find settings under settings
2. Display options
3. Front end display