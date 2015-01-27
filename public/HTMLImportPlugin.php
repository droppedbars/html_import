<?php
require_once( dirname( __FILE__ ) . '/../admin/includes/HtmlImportSettings.php' );
require_once( dirname( __FILE__ ) . '/includes/WPMetaConfigs.php' );
require_once( dirname( __FILE__ ) . '/includes/XMLHelper.php' );
require_once( dirname( __FILE__ ) . '/includes/FileHelper.php' );
require_once( dirname( __FILE__ ) . '/includes/HTMLImportStages.php' );
require_once( dirname( __FILE__ ) . '/includes/GridDeveloperHeaderFooterImportStage.php' );
require_once( dirname( __FILE__ ) . '/includes/ImportHTMLStage.php' );
require_once( dirname( __FILE__ ) . '/includes/UpdateLinksImportStage.php' );
require_once( dirname( __FILE__ ) . '/includes/MediaImportStage.php' );
require_once( dirname( __FILE__ ) . '/includes/SetTemplateStage.php' );
require_once( dirname( __FILE__ ) . '/includes/HTMLFullImporter.php' );
require_once( dirname( __FILE__ ) . '/includes/FolderImporter.php' );
require_once( dirname( __FILE__ ) . '/includes/HTMLStubImporter.php' );
require_once( dirname( __FILE__ ) . '/includes/indices/WebsiteIndex.php' );
require_once( dirname( __FILE__ ) . '/includes/indices/FlareWebsiteIndex.php' );
require_once( dirname( __FILE__ ) . '/includes/indices/CustomXMLWebsiteIndex.php' );
require_once( dirname( __FILE__ ) . '/includes/indices/WebPage.php' );
require_once( dirname( __FILE__ ) . '/includes/retriever/FileRetriever.php' );
require_once( dirname( __FILE__ ) . '/includes/retriever/LocalAndURLAndURLFileRetriever.php' );

/**
 * Plugin Name.
 *
 * @package   HTMLImportPlugin
 * @author    Patrick Mauro <patrick@mauro.ca>
 * @license   GPL-2.0+
 * @link      http://patrick.mauro.ca
 * @copyright 2014 Patrick Mauro
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `HTMLImportPluginAdmin.php`
 *
 * @package HTMLImportPlugin
 * @author  Patrick Mauro <patrick@mauro.ca
 */
class HTMLImportPlugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'htim-html-import';

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide       True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Populates the default values of the plugins settings to the database or the current site.
	 */
	private static function populate_default_settings() {
		$settings = new \html_import\admin\HtmlImportSettings();
		if ( !get_option( $settings::SETTINGS_NAME ) ) {
			$settings->saveToDB();
		}
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		self::populate_default_settings();
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide       True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			self::populate_default_settings();
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * Base function to import a website into Wordpress.  Takes $settings that have been set in the plugin's settings page, and uses $_FILES to access uploaded files (if required).  In the end all pages and media should be imported into Wordpress with internal links updated.
	 *
	 * @param \html_import\admin\HtmlImportSettings $settings
	 */
	public function importHTMLFiles( html_import\admin\HtmlImportSettings $settings ) {
		echo '<h2>Output from Import</h2><br>Please be patient</br>';
		echo '<ul>';

		if ($settings->validate()) {

			// TODO: prefer to pass this value in rather than use global
			$zip_to_upload = $_FILES['file-upload'];
			if ( strcmp( 'upload', $settings->getImportSource()->getValue() ) == 0 ) {
				$mime_type = $zip_to_upload['type'];
				echo 'uploading file of mime-type ' . $mime_type . ' <br>';
				if ( $this->isFileMimeTypeCompressed( $mime_type ) ) {
					// echo 'mime-type: '.$mime_type;
					$filePath = $this->decompressAndUploadFiletoSite( $zip_to_upload );
					if ( !is_null( $filePath ) ) {
						$this->routeImportToCorrectImporter( $filePath, $settings );
						// clean up the files that were unzipped
						html_import\FileHelper::delTree( $filePath );
					}
				} else {
					echo 'File is not a ZIP file.';
				}
			} else {
				$this->routeImportToCorrectImporter( $settings->getFileLocation()->getValue(), $settings );
			}
		} else {
			echo 'The required parameters were not set.<br>';
		}
		echo '</ul>';
	}

	/**
	 * Tests the provided Mime type to determine if it s a compressed file, such as RAR or ZIP.  Returns true if it is.
	 *
	 * @param $mime_type
	 *
	 * @return bool
	 */
	private function isFileMimeTypeCompressed( $mime_type ) {
		// TODO: better would be to actually check the zip rather than just assume the mime-type is correct
		return ( ( strcmp( 'application/x-rar-compressed', $mime_type ) == 0 ) || ( strcmp( 'application/octet-stream', $mime_type ) == 0 ) || ( strcmp( 'application/zip', $mime_type ) == 0 ) || ( strcmp( 'application/x-zip-compressed', $mime_type ) == 0 ) );
	}

	/**
	 * Takes the path to a zip file, decompresses it and drops it into the Uploads directory of the Wordpress installation.  The sub path will be /import-# where # is a sequential number, incremented if there is another /import-# directory in the uploads directory.  A string is returned containing the path to the contents of the zip.
	 *
	 * @param $zip_to_upload
	 *
	 * @return null|string
	 */
	private function decompressAndUploadFiletoSite( $zip_to_upload ) {
		$zip           = new ZipArchive;
		$zipOpenResult = $zip->open( $zip_to_upload['tmp_name'] );
		if ( $zipOpenResult === TRUE ) {
			$upload_dir    = wp_upload_dir();
			$path          = $upload_dir['path'] . '/import';
			$path_modifier = 1;
			while ( file_exists( $path . '-' . $path_modifier ) ) {
				$path_modifier ++;
			}
			$resultingPath = $path . '-' . $path_modifier;

			$extractSuccess = $zip->extractTo( $path . '-' . $path_modifier );
			if ( $extractSuccess !== FALSE ) {
				$closeSuccess = $zip->close();
				if ( $closeSuccess === FALSE ) {
					echo '*** Could not close the zip archive.';
				}

				return $resultingPath;
			} else {
				echo '*** Could not extract the zip archive.';

				return null;
			}
		} else {
			echo '*** Could not close the zip file. Error: ' . $zipOpenResult;

			return null;
		}
	}

	/**
	 * Determines if the import is an XML or flare index and ensures it is imported accordingly.
	 *
	 * @param                                       $filePath
	 * @param \html_import\admin\HtmlImportSettings $settings
	 */
	private function routeImportToCorrectImporter( $filePath, html_import\admin\HtmlImportSettings $settings ) {
		$importType = $settings->getIndexType()->getValue();

		if ( strcmp( 'flare', $importType ) == 0 ) {
			$this->import_html_from_flare( $filePath, $settings );
		} else {
			if ( strcmp( 'xml', $importType ) == 0 ) {
				$this->import_html_from_xml_index( $filePath, $settings );
			} else {
				echo '*** Unsupported import type: ' . $importType . '<br>';
			}
		}
	}

	/**
	 * Begins the process of importing a website that is defined through a flare index file.  $filePath points to the index file, and $settings contains all of the settings to be applied to imported pages.  At the end of the import all of the pages listed in the index file will be imported into wordpress and have their parent, and categories defined by the $settings.
	 *
	 * @param                                       $filePath
	 * @param \html_import\admin\HtmlImportSettings $settings
	 */
	private function import_html_from_flare( $filePath, html_import\admin\HtmlImportSettings $settings ) {
		$localFileRetriever = new \droppedbars\files\LocalAndURLFileRetriever( $filePath );
		$flareIndex         = new \html_import\indices\FlareWebsiteIndex( $localFileRetriever );
		$flareIndex->buildHierarchyFromWebsiteIndex();
		// TODO: note, the retriever is built with the directory, and the index found afterwards

		$media_lookup     = Array();
		$html_post_lookup = Array();
		// perform the import twice, once to create stub files, and once to put in the contents and update links between files.
		$html_post_lookup = $this->importFromWebsiteIndex( $flareIndex, true, $html_post_lookup, $media_lookup, $settings );
		$this->importFromWebsiteIndex( $flareIndex, false, $html_post_lookup, $media_lookup, $settings );
	}

	/**
	 * Iterates through the contents of a website index and imports the WebPages contained.  This function should be run twice, once to create a stub ($stubs_only = true) and once to create the full page. The reason for this is that local links cannot be updated unless a page has already been imported and has a Wordpress page_id.  All imported media files are returned in $media_lookup, and $html_post_lookup maintains a list of all pages that have been imported and their page_ids.
	 *
	 * @param \html_import\indices\WebsiteIndex     $siteIndex
	 * @param bool                                  $stubs_only
	 * @param null                                  $html_post_lookup
	 * @param null                                  $media_lookup
	 * @param \html_import\admin\HtmlImportSettings $settings
	 *
	 * @return array|null
	 */
	private function importFromWebsiteIndex( \html_import\indices\WebsiteIndex $siteIndex, $stubs_only = true, &$html_post_lookup = null, &$media_lookup = null, html_import\admin\HtmlImportSettings $settings ) {

		set_time_limit( 520 ); // timeout of 520 seconds
		if ( !isset( $html_post_lookup ) ) {
			$html_post_lookup = Array();
		}

		// go through all the files in the hierarchy and import them.  Start at the base and keep going until all files are read in.
		$siteIndex->setToFirstFile();
		$fileNode = $siteIndex->getNextHTMLFile();

		while ( !is_null( $fileNode ) ) {
			$this->importAndRecordWebPage( $fileNode, $stubs_only, $html_post_lookup, $media_lookup, $settings );
			$fileNode = $siteIndex->getNextHTMLFile();
		}

		return $html_post_lookup;
	}

	/**
	 * Ensures the provided WebPage is imported into WordPress.  $stubs_only will force the import to create a stub (only creates the page with a title, no content) or do a full import.  A record of the page being imported is stored in $html_post_lookup which is returned updated.  $media_lookup maintains a list of all local media files that have been imported, and it is updated during the course of this import and returned.  The HtmlImportSettings defines various settings to apply to the imported page.
	 *
	 * @param \html_import\indices\WebPage          $webPage
	 * @param bool                                  $stubs_only
	 * @param                                       $html_post_lookup
	 * @param                                       $media_lookup
	 * @param \html_import\admin\HtmlImportSettings $settings
	 *
	 * @return \html_import\WPMetaConfigs
	 */
	private function importAndRecordWebPage( \html_import\indices\WebPage $webPage, $stubs_only = true, &$html_post_lookup, &$media_lookup, html_import\admin\HtmlImportSettings $settings ) {

		$category = $settings->getCategories()->getValuesArray();
		$tag      = Array();

		// build up the categories from the settings;
		if ( !is_null( $category ) && is_array( $category ) ) {
			foreach ( $category as $index => $cat ) {
				$cat_id              = get_cat_ID( trim( $cat ) );
				$categoryIDs[$index] = intval( $cat_id );
			}
		}
		// placeholder, in the future build up the tags
		if ( !is_null( $tag ) && is_array( $tag ) ) {
			foreach ( $tag as $t ) {
				//TODO: support tags
			}
		}

		$parent_page = new \html_import\WPMetaConfigs();
		// get the id of the parent selected in the settings page
		$hasParent      = $parent_page->loadFromPostID( $settings->getParentPage()->getValue() );
		$parent_page_id = null;
		if ( $hasParent ) {
			$parent_page_id = $settings->getParentPage()->getValue();
		}

		// determine if the page itself was loaded from the index with a parent page, then override the id if so
		$parentWebPage = $webPage->getParent();
		if ( !is_null( $parentWebPage ) ) {
			if ( !is_null( $wp_id = $parentWebPage->getWPID() ) ) {
				$parent_page_id = $wp_id;
			}
		}

		// create the appropriate importer and perform the import
		// TODO: this creation is a candidate to be replaced with a factory
		$stages   = new \html_import\HTMLImportStages();
		$postMeta = $this->importAnHTML( $webPage, $settings, $parent_page_id, $html_post_lookup );
		if ( !$webPage->isFolder() ) {
			if ( $stubs_only ) {
				$stubImport = new html_import\HTMLStubImporter( $settings, $stages );
				$stubImport->import( $webPage, $postMeta, $html_post_lookup, $media_lookup );
			} else {
				$fullImport = new html_import\HTMLFullImporter( $settings, $stages );
				$fullImport->import( $webPage, $postMeta, $html_post_lookup, $media_lookup );
			}
		} else {
			$folderImport = new html_import\FolderImporter( $settings, $stages );
			$folderImport->import( $webPage, $postMeta, $html_post_lookup, $media_lookup );
		}

		return $postMeta;
	}

	// TODO: candidate to be made into a factory

	/**
	 * Imports a WebPage object into Wordpress, using the provided HtMLImportSettings, and assigning it to be a child of the post with the id defined in $parent_page_id.  $html_post_lookup is used to determine if the page had already been created by this session's import.
	 *
	 * @param \html_import\indices\WebPage          $webPage
	 * @param \html_import\admin\HtmlImportSettings $globalSettings
	 * @param                                       $parent_page_id
	 * @param                                       $html_post_lookup
	 *
	 * @return \html_import\WPMetaConfigs
	 */
	private function importAnHTML( \html_import\indices\WebPage $webPage, html_import\admin\HtmlImportSettings $globalSettings, $parent_page_id, $html_post_lookup ) {

		$title = $webPage->getTitle();

		$pageMeta = new \html_import\WPMetaConfigs();
		$post_id  = null;

		// determine if the page has already been imported, search by post title
		$post = get_page_by_title( htmlspecialchars( $title ) );// TODO: bad form, its saved with htmlspecialchars so need to search using that.  Need to find a way to not require this knowledge
		if ( isset( $html_post_lookup ) ) {
			// check to see if there's been an import of this page already, if so get its ID from the lookup
			if ( array_key_exists( $webPage->getFullPath(), $html_post_lookup ) ) {
				$post_id = $html_post_lookup[$webPage->getFullPath()];
			} else {
				// the post wasn't imported during this import, but a post already exists with its title.  Use it.
				if ( !is_null( $post ) ) {
					$post_id = $post->ID;
					echo '<li>Page with title ' . $title . ' and ID ' . $post_id . ' already exists, now tagged to be overwritten.</li>';
				}
			}
		}
		$pageMeta->buildConfig( $globalSettings, $webPage, $post_id, $parent_page_id );

		if ( !is_null( $title ) ) {
			$pageMeta->setPostTitle( $title );
		}

		return $pageMeta;
	}

	/**
	 * Begins the process of importing a website that is defined through an XML index file.  $filePath points to the index file, and $settings contains all of the settings to be applied to imported pages.  At the end of the import all of the pages listed in the index file will be imported into Wordpress and have their parent, and categories defined by the $settings.
	 *
	 * @param                                       $filePath
	 * @param \html_import\admin\HtmlImportSettings $settings
	 */
	private function import_html_from_xml_index( $filePath, html_import\admin\HtmlImportSettings $settings ) {
		$directory = $filePath;
		if ( !is_dir( $directory ) ) {
			$directory = dirname( $filePath );
		}
		$localFileRetriever = new \droppedbars\files\LocalAndURLFileRetriever( $directory );
		$xmlIndex           = new \html_import\indices\CustomXMLWebsiteIndex( $localFileRetriever );

		// the importer will use a default index name of index.xml unless otherwise is provided.  This checks the provided path, if it is a file, it assumes that that file is the index file.
		$indexFile = null;
		if ( !is_dir( $filePath ) ) {
			$indexFile = basename( $filePath );
		}

		$xmlIndex->buildHierarchyFromWebsiteIndex( $indexFile );

		$media_lookup     = Array();
		$html_post_lookup = Array();
		// perform the import twice, once to create stub files, and once to put in the contents and update links between files.
		$html_post_lookup = $this->importFromWebsiteIndex( $xmlIndex, true, $html_post_lookup, $media_lookup, $settings );
		$this->importFromWebsiteIndex( $xmlIndex, false, $html_post_lookup, $media_lookup, $settings );
	}
}