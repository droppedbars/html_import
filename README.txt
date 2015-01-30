=== Website Importer ===
Contributors: droppedbars
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=D7J2RY6828E7N
Tags: import,HTML,website,webpage,flare,xml
Requires at least: 3.8.1
Tested up to: 4.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Given an index file, uploads entire websites into Wordpress.

== Description ==

Website Importer can import entire websites into Wordpress provided they contain a specialized index file.  The index file can either be an index output by Flare Madcap, or an XML file that is specifically defined for this importer.

The Website Importer will relink internal links, upload and relink media files, and apply templates and categories to the page.  The import can be from either a website that is local to the Wordpress installation, or uploaded as a zip file.  If it is uploaded as a zip file and is a Flare Madcap import, the importer will find the relevant Flare Madcap index.  If it is uploaded as a zip file and the is using an XML index, the importer requires that the index file be named index.xml and located at the root of the zip file.

Initial plugin development funded by Gliderline Solutions (http://www.gliderline.com/).

== Installation ==

* Minimum tested/supported WordPress version: 3.8.1
* Minimum tested PHP version: 5.4.25
* Other requirements:
* Javascript required for Settings Page
* PHP must not be running in safe mode

= Through your WordPress Installation =

1. Click 'Plugins > Add New'
1. Search for 'Website Importer'
1. Activate Website Importer from your Plugins page
1. Click 'Settings > Website Importer'
1. Enter in your brands that require trademark symbols, select the trademark symbol and click 'Save Changes'

= Through WordPress.org =

1. Download Website Importer
1. Upload or copy the 'Website Importer' directory to your '/wp-content/plugins/' directory using your preferred method
1. Activate Website Importer from your Plugins page
1. Click 'Settings > Website Importer'
1. Enter in your brands that require trademark symbols, select the trademark symbol and click 'Save Changes'

== Frequently Asked Questions ==

= What do I do if my import doesn't work? =

If possible, provide the entire import attempted (index and html files) and the errors that occurred.

= How do I use categories for pages? =

In order to create and manage categories for pages, another plugin is required.  "Post Tags and Categories for Pages" has been used in testing in conjunction with this plugin.

= Where does the index file have to be? =

When importing a locally located website using an XML import, the index file must be referenced directly with all file locations relative to that index file.  For a locally located website using a Flare import, the root path of the Flare website can be referenced directly: if the Toc.js is referenced then all file links within Toc.js must be relative to the Toc.js location.
If uploading a zip file containing the website, then for an XML import the index.xml file must be in the root of the zip file and named index.xml.  For an uploaded zip of a Flare website, the TOCs can be anywhere and will be found automatically.

== Screenshots ==

1. The settings screen of the plugin while importing using an XML index.

== Changelog ==

= 1.0 =
* First release, supports a custom XML index and basic Flare imports.

== XML Index Format ==

`<?xml version="1.0" encoding="utf-8"?>
<knowledgebase version="1.0">
  <document title="File Title" src="index.html" category="cat1" order="0">
    <document title="File Title 2" src="secondFile.html" category="cat1,cat2" order="0">
    </document>
  </document>
</knowledgebase>
</xml>`

* documents can be nested to define a parent child relationship.
* order defines menu listing orders if multiple pages exist under one parent.
* category can be applied to pages but may require an additional plugin to be usable.  Multiple categories are deliminted by a comma.
* src is the path to the html file and relative to the index.xml.

Settings defined in the XML file will override settings contained in the plugin settings page.

== Roadmap ==
The roadmap will be based on user demand and feedback.  Please provide feedback on what development would be important to you.  Some possibilities:

* Option to import as a post
* Overwriting of media files on subsequent imports or during filename conflicts.
* Option to create new pages vs overwrite existing pages of the same name.
* Option to set the category of the import based on the category the top level page has
* Support for tags.
* Support for categories and tags for media files.
* Import from confluence index files.
* Import from Subversion.
* Ability to change content in HTML (eg, replacing tags, or sections of text).
* Read in page information (such as tags or content) from custom-defined meta tags.
If you find this plugin useful, or find that it could be useful with certain additions, please let me know.

== Known Issues ==
* Madcap Flare imports only support one chunk file, and the TOC must be named Toc.js and only the first chunk file will be loaded (using name [TOC_CHUNK_NAME]0.js).
* During large Flare imports, the output text may stop printing even though the import completes.
* Links that link to within the current page (thispage.html#section) are not relinked.
* Relative links that do not specify http:// or https:// will not be relinked.
* Pages with titles that look like tags (uses < and >) may not appear in the 2015 theme menu.  It works in the 2014 theme menu.
* The incorrect category may get attached to the imported page if there are multiple categories with the same name.
* index.xml must be at the root of the zip file, and not in a sub-directory for an uploaded XML zip import.
* Uploading a Flare website that using a local path must reference the root directory of the Flare website, it cannot point to the Toc.js otherwise the webpage locations stored in Toc.js cannot be found by the importer.