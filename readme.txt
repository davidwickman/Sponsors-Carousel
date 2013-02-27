=== Sponsors Carousel ===
Contributors: Sergey Panasenko
Donate link: 
Tags: gallery, images, javascript, jquery, banners, sponsors, jcarousel
Requires at least: 2.6
Tested up to: 3.4.2
Stable tag: trunk

This plugin displays thumbnail images or banners in a carousel using jQuery jCarousel.


== Description ==

Sponsors Carousel implements the jCarousel as a WordPress plugin.

You can set internal link for all image or custom link for any image.

New functionality: Autoscroll!

The plugin uses the shortcode [sponsors_carousel].

It is is designed to be styled with CSS. Sample images for next/prev arrows are provided.

The plugin uses jQuery, and if your site doesn't already use jQuery, it'll add the script for you.

It was inspired by jCarousel by Jan Sorgalla.

In future release: 

* resize from admin panel.


== Installation ==

1. Upload the folder sponsors-carousel to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add <code><?php echo sponsors_carousel(); ?></code> in theme or [sponsors_carousel] on page.
4. Add image on Settings->Sponsor Carousel page
5. Set custom link in image *Caption* field

== Frequently Asked Questions ==

= How do I change the layout =
You can use CSS to change the look and feel of the layout. You can also create custom images for the next and prev arrows.


== Screenshots ==

1. This shows the default look of the carousel.

2. This shows admin panel.

== Changelog ==

* 1.0: First release.
* 1.01: Fix bag with add new image.
* 1.02: Fix bags with _media_button() (Thanks for <a href="http://wordpress.org/support/profile/karamba">karamba</a> and Adam Gillis) and i18s.
* 1.03: Fix crash adding images to posts/pages through the media upload (Thanks for Adam Gillis).
* 2.00: New functionality:open link in new window and autoscroll (Thanks for <a href="http://wordpress.org/support/topic/plugin-sponsors-carousel-another-patch-new-window-and-auto-scroll">elija</a> ). 
* 2.01: i18n update
* 2.02: Continuous Mode (Thanks for Sebastián Valerio G.)
