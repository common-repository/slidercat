=== sliderCat ===
Contributors: damiroquai
Donate link: http://wordpress.transformnews.com
Tags: slide, slider, slideshow, carousel, flexslider, plugin, animation, jquery, responsive, responsive slider, image slider, youtube slider, shortcode, video slider
Requires at least: 3.5.1
Tested up to: 4.8
Stable tag: 1.0
License: GPLv2 or later


Simple responsive shortcode slideshow based on popular FlexSlider with powerful css3 animations on title and content.


== Description ==

Plugin is built using Wordpress custom post type and custom taxonomy to store slideshows, individual slides and their settings. Featured images are used as Slide images, Title and Content Boxes are used like an animated objects. Tag slugs are used as categories to group slides into separated slideshows and than show them with Shortcode in content. Youtube and Vimeo slides are supported in responsive 16:9 format. Using default but extended WordPress post system you can quickly create multiple animated sliders in just a few minutes.



**sliderCat Settings**

There are three group of settings, General Settings, individual Slideshow Settings (edit slideshow) and individual Slide Settings (add / edit slide).

**General Settings** are located in Dashboard / sliderCat / General Settings.

General Settings: 
Enable / Disable (sliderCat CSS, animation CSS, jQuery Flexslider script, jQuery Easing script, jQuery MouseWheel script),
Thumb, Medium and Large Image Size (width, height, crop).


**Slideshow Settings** - Each slideshow have separate options which can be accessed in Dashboard / sliderCat / Slideshows / your slideshow name (Edit). Choose between fade or slide effect, direction, enable or disable video… Also your Shortcode will be shown here, so you can copy it and use in your content (multiple slides per page are possible).

Slideshow Settings:
Name, Slug, Select Image Size, Select Transition (fade or slide), Transition Speed in mS, Direction, Easing, Reverse, Pause Button, Smooth Height, Randomize, Video, Carousel Item Width, Carousel min. Items, Carousel max. items, Enable Mousewheel, As Navigation For, Sync.


**Slide Settings** located in Dashboard / sliderCat / Add New Slide (or All Slides / Edit Slide).

Slide Settings:
Title, Content Boxes x 2, Slideshows(tags), Featured Image(slide image), Overall Slide Duration in mS, Slide Link URL, Link target, Youtube / Vimeo URL,
Title: (Enable Title, Link URL, Link Target, Position, Font Color, Size, Wrap, Max Width, Custom Class, In Animation, In Animation Delay, Out Animation, Out Animation Delay),
Content x 2: (Position, Font Color, Size, Wrap, Max Width, Custom Class, In Animation, In Animation Delay, Out Animation, Out Animation Delay).


Scripts used and thanks to:
jQuery FlexSlider v2.2.2 by WooThemes, Animate.css by Daniel Eden, jQuery MouseWheel 3.1.12 by Brandon Aaron, jQuery Easing v1.3 by Tyler Smith.

Plugin Home: http://wordpress.transformnews.com/plugins/slidercat-responsive-shortcode-slideshow-783

== Installation ==

Install like any other plugin. After install activate.

1. Go to Dashboard / sliderCat / General Settings to set image sizes to your desired dimensions.

2. Go to Dashboard / sliderCat / Slideshows and Add New Slideshow. Edit slideshow for more options and to copy slideshow shortcode.

3. Go to Dashboard / sliderCat / Add New Slide (assign it to previously created slideshow).

4. Create new post or widget and paste shortcode to content (for widgets shortcode should be enabled in your theme functions.php).

== Frequently Asked Questions ==


== Screenshots ==

1.  screenshot-1.png shows General Settings.
2.  screenshot-2.png shows Slideshow Settings.
3.  screenshot-3.png shows Slide Settings.


== Changelog ==


= 1.0 =
* First release of sliderCat