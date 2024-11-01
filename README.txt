=== Shutterpress Gallery ===
Contributors: shutterpressgallery
Donate link: https://shutterpress.io
Tags: gallery, photography, elementor, masonry, lightbox, slideshow, portfolio, lightgallery, photo gallery, photo download, photo sharing, photographer, artist
Requires at least: 5.3
Tested up to: 6.6.2
Requires PHP: 7.0
Stable tag: 1.2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
**ShutterPress Gallery** is designed specifically for photographers and visual artists who want to share their work online and also share their work with their clients. With a focus on flexibility and style, this plugin allows you to create professional image galleries with just a few clicks. Whether you want to display a portfolio or highlight your latest project, ShutterPress gives you all the tools you need to make your photography stand out.

The plugin revolves around a dedicated gallery menu option, giving you a streamlined workflow for managing your collections. From there, you can display your galleries with their own link or on any page or post using a shortcode, a custom Gutenberg block or an Elementor Widget, all of which integrate seamlessly with the WordPress editor. ShutterPress supports stunning **masonry** and **grid** layouts and includes a powerful **favourite image** feature to engage your audience.

ShutterPress Gallery also offers a responsive, lightweight experience that ensures your images look sharp and load fast, whether on desktop or mobile. Photographers can effortlessly create captivating galleries, complete with lightbox effects, zoom options, **image download** functionality, and the ability to filter favourite images.

**Why Photographers Love ShutterPress:**
- **Favourite Feature:** Allow visitors to "favourite" individual images in the gallery, and then filter them to just view their favourites.
- **Masonry & Grid Layouts:** Choose between stylish masonry layouts or clean grid options to best showcase your photography.
- **Lightbox Integration:** Engage viewers with zoomable images and thumbnail previews using a modern lightbox interface.
- **Image Download:** Allow users to download high-quality versions of your images directly from your website.
- **Dynamic Filtering:** Let your audience filter and view only their favourite images, creating a personalized viewing experience.
- **Options** Favourite images, Downloading and the lightbox are all optional and can be turned on individually for each gallery. For example, turn them off for portfolio galleries and turn them on for client galleries. 

**Shutterpress Pro**
 A **Pro version** of ShutterPress Gallery is coming soon, packed with even more powerful features tailored for professional photographers. The Pro version will include watermarking, password protection and woocommerce integration with more customization options to take your galleries to the next level. Sign up at [shutterpress.io](https://shutterpress.io) to be notified when it's released. 

Whether you're a professional photographer or an enthusiast, **ShutterPress Gallery** is the perfect tool for transforming your WordPress site into a visual gallery that showcases your unique creative vision.

== Installation ==
1. Upload the `shutterpress-gallery` folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Start creating stunning galleries with the SP Galleries menu option in the admin area.

== Usage ==
1. Manage your galleries through the SP Galleries menu option. Create a new gallery, upload your photos, and customize the layout.
2. View the gallery on your site at it's own URL, your-website.com/gallery/your-gallery-name.
3. **Shortcode:** Use the shortcode `[shutterpress_gallery id="your-gallery-id"]` to display a gallery on any page or post.
4. **Gutenberg Block:** Alternatively, use the ShutterPress Gallery block in the Gutenberg editor to add a gallery directly to your post or page.
5. **Elementor Widget:** If you're using Elementor, you can add a gallery widget to any page or post.

== Frequently Asked Questions ==

= How do I create a gallery? =
You can create galleries through the SP Galleries menu option in the WordPress admin dashboard. Once created, you can display them directly at their own URL or by using a shortcode or the Gutenberg block.

= Can I choose different layouts for my galleries? =
Yes, you can select between a **masonry** layout for a more artistic and free-flowing feel or a **grid** layout for a more structured display.

= Can users download the images? =
Yes, viewers can download images making it easy to access high-quality versions of your photos.

= How do I display a gallery on a page or post? =
Use the shortcode `[shutterpress_gallery id="your-gallery-id"]` or the ShutterPress Gallery Gutenberg block to embed your gallery anywhere.

= What does the "favourite image" feature do? =
The favourite image feature allows users to select and store their favourite image. Logged-in users' preferences are saved in the database, while guests' preferences are stored in cookies.

= Can I password protect client galleries? =
This can be done with the upcoming Shutterpress Pro Plugin or by using other wordpress plugins.

== Screenshots ==
1. ShutterPress Gallery gallery admin page.
4. Masonry layout example.
5. Grid layout example.
6. Favourite image feature in action.

== Changelog ==

= 1.2.3 =
* New - Color picker in the setting page for the default color
* Update - Delays gallery render until inline css is loaded
* Update - Adds more classes to gallery buttons
* Fix - Issue where images in the media library couldn't be deleted 

= 1.2.2 =
* Fix - Icon CSS

= 1.2.1 =
* Fix - CSS issue where icons didn't show in mobile devices
* Fix - Namespace for WP_Query

= 1.2.0 =
* New - Adds a new elementor widget.
* New - Adds options to the Gutenberg block and shortcode
* New - Allows use of custom buttons in Elementor and Gutenberg
* New - Adds image sorting in the gallery admin page
* New - Allows multiple galleries on a page
* New - Adds namespace to all classes
* Fix - Syncs favourite image data from cookie storage after logging in
* Fix - Responsive layouts not rendering correctly at smaller sizes
* Fix - Checks for favourite images before filtering
* Fix - Makes better use of Render Class to render each gallery
* Update - Changes class for liked images to sp-gallery-liked
* Update - Moves Ajax functions to a new Class

== Upgrade Notice ==

= 1.2.3 =
Important fix for bug that prevented image deletion.

= 1.2.0 =
* New - Adds additional functionality and improves performance

= 1.0.0 =
Initial release.

== License ==
This plugin is licensed under the GPLv2 or later. You can modify and redistribute it under the terms of the GNU General Public License.