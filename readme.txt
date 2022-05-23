=== RichText Extension ===
Contributors: wildworks
Tags: gutenberg,rich text,highlighter,formatting
Donate link: https://www.paypal.me/thamanoJP
Requires at least: 5.6
Tested up to: 5.9
Stable tag: 1.3.0
Requires PHP: 7.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds useful decoration features to the Gutenberg RichText editor toolbar.

== Description ==
RichText Extension adds useful decorating features to gutenberg rich text editor.

= Highlighter =

Draw a marker on the inline text.
You can set the color, thickness, and opacity of the marker to you can create various marker styles.
Up to four types of settings can be made.

= Font size =

Change inline text size.
The size is specified as a percentage of the base font size, and up to four types of settings can be made.

= Underline =
Create underline.
**Note: The underline specifications have changed from version 2.0.0. Try clearing the format if existing underlines do not work.**

= Clear format =
Removes all formatting.

== Installation ==
1. Upload the `add-richtext-toolbar-button` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the \'Plugins\' menu in WordPress.

== Changelog ==

= 1.3.0 =
* Testes to WordPress 5.9

= 1.2.3 =
* Clean: update npm packages and build tool

= 1.2.2 =
* Clean: Update npm packages
* Remove: Bundled language files

= 1.2.1 =
* Doc: Update tested up to

= 1.2.0 =
* Testes to WordPress 5.8
* Clean: update npm packages

= 1.1.9 =
* Testes to WordPress 5.7

= 1.1.8 =
* Clean: Update deploy flow (github actions)
* Fix: Wrong plugin version
* Clean: update npm packages and organize development env

= 1.1.7 =
* Clean: Refactoring, Replace deprecated components with recommended ones
* Fix: Problem about installation error in PHP 8
* Tested to PHP 8

= 1.1.6 =
* Change: toolbar icon from dashicon to svg icon

= 1.1.4 =
* Remove: gulpfile.js
* Clean: Update npm packages, add .wp-env, and refactor the development environment
* Clean: Remove composer.lock and package-lock.json from .gitignore
* Clean: Remove the compiled JS files
* Clean: Remove the compiled CSS files
* Update development setting
* Add: composer
* Delete: package-lock.json

= 1.1.3 =
* Doc: Correct a notation error in the translation

= 1.1.2 =
* Fix: metabox warning error

= 1.1.1 =
* Fix: Problem about uninstallation process

= 1.1.0 =
* Remove: activation check

= 1.0.2 =
* Fix: Problem that style is not reflected on first installation
* Fix: Problem that preview does not switch when status is changed

= 1.0.1 =
* Update: build process
* Update: translation

= 1.0.0 =
* Initial release
