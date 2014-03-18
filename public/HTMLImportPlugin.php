<?php
/**
 * Plugin Name.
 *
 * @package   HTMLImportPlugin
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `HTMLImportPluginAdmin.php`
 *
 * @package HTMLImportPlugin
 * @author  Your Name <email@example.com>
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
	protected $plugin_slug = 'html-import';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

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

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

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
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

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
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
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
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	private function valid_xml_file( $xml_path ) {
		// @TODO: stub, should validate file properly
		return true;
	}

	private function get_title( SimpleXMLElement $html_file ) {
		$title = '';
		foreach ( $html_file->head->title as $titleElement ) {
			$title = '' . $titleElement;
		}

		return $title;
	}

	private function get_body_attributes( SimpleXMLElement $body ) {
		$saved_attributes = Array();

		foreach ( $body->attributes() as $attr ) {
			if ( ( strcmp( 'class', $attr->getName() ) ) || ( strcmp( 'style', $attr->getName() ) ) ) {
				array_push( $saved_attributes, $attr );
			}
		}

		return $saved_attributes;
	}

	private function get_body( SimpleXMLElement $html_file ) {
// TODO: this was the start of converting a <BODY> to a <DIV> while retaining all class and style attributes
//		$body = ''.$html_file->body->asXML();
//		$main_div = new SimpleXMLElement('<div></div>');
//
//
//		foreach ($this->get_body_attributes($html_file->body) as $attr) {
//			$main_div->addAttribute($attr->getName(), ''.$attr);
//		}
//
//

		return $html_file->body->asXML();
	}

	private function getXMLObject( $source_file ) {
		$doc                      = new DOMDocument();
		$doc->strictErrorChecking = false;
		libxml_use_internal_errors( true ); // some ok HTML will generate errors, this masks them, pt 1/2
		$doc->loadHTMLFile( $source_file, LIBXML_HTML_NOIMPLIED );
		libxml_clear_errors(); // some ok HTML will generate errors, this masks them, pt 2/2
		$simple_xml = simplexml_import_dom( $doc );

		return $simple_xml;
	}

	private function importAnHTML( $source_file, $parent_page_id = null, $category = null, $tag = null ) {
		set_time_limit( 540 );
		$file_as_xml_obj = $this->getXMLObject( $source_file );

		$page                   = Array();
		$page['post_title']     = $this->get_title( $file_as_xml_obj );
		$page['post_content']   = $this->get_body( $file_as_xml_obj ); // TODO: pass in mapping and update links at same time
		$page['post_status']    = 'publish';
		$page['post_type']      = 'page';
		$page['comment_status'] = 'closed';
		$page['ping_status']    = 'closed';
		$page['post_category']  = ''; // TODO, pull from xml index
		$page['post_excerpt']   = ''; // TODO, pull from meta
		$page['post_date']      = date( 'Y-m-d H:i:s', filemtime( $source_file ) );
		if ( isset( $parent_page_id ) ) {
			$page['post_parent'] = $parent_page_id;
		}
		$page['menu_order']  = ''; // TODO, pull from xml index
		$page['post_author'] = wp_get_current_user()->ID;

		// TODO: handle updating page if it already exists
		$page_id = wp_insert_post( $page );
		if ( is_wp_error( $page_id ) ) {
			// TODO: handle error
			echo 'post did not post';
		}

		return $page_id;
	}

	private function processNode( DOMNode $node, $parent_id = null ) {
		$attributes = $node->attributes;
		$title      = null;
		$src        = null;
		$category   = Array();
		$tag        = Array();
		$order      = 0;
		$my_id      = null;

		if ( isset( $attributes ) ) {
			for ( $i = 0; $i < $attributes->length; $i ++ ) {
				$attribute = $attributes->item( $i )->name;
				switch ( $attribute ) {
					case 'title':
						$title = $attributes->item( $i )->nodeValue;
						break;
					case 'src':
						$src = $attributes->item( $i )->nodeValue;
						break;
					case 'category':
						$category = explode( ',', $attributes->item( $i )->nodeValue );
						break;
					case 'tag':
						$tag = explode( ',', $attributes->item( $i )->nodeValue );
						break;
					case 'order':
						$order = $attributes->item( $i )->nodeValue;
						break;
					/* future cases
					case 'overwrite-existing':
						break;
					*/
					default:
						break;
				}
			}
		}
		//TODO: category and tag may be null or empty
		//TODO: probably need to trim the exploded text
		foreach ( $category as $cat ) {
		}
		foreach ( $tag as $t ) {
		}

		// TODO: handle title, tags, categories and ordering
		if ( isset( $src ) ) {
			// TODO: validate source file
			$my_id = $this->importAnHTML( $src, $parent_id );
			// TODO: import the post
			// TODO: hand images
		}


		// recurse through children nodes
		$children = $node->childNodes;
		if ( isset( $children ) ) {
			for ( $i = 0; $i < $children->length; $i ++ ) {
				$this->processNode( $children->item( $i ), $my_id );
			}
		}
	}


	private function process_xml_file( $xml_path ) {
		$doc = new DOMDocument();
		$doc->load( $xml_path, LIBXML_NOBLANKS );

		$nodelist = $doc->childNodes;
		for ( $i = 0; $i < $nodelist->length; $i ++ ) {
			$this->processNode( $nodelist->item( $i ) );
		}
	}

	public function import_html_from_xml_index( $xml_path ) {
		if ( $this->valid_xml_file( $xml_path ) ) {
			$this->process_xml_file( $xml_path );
		}
	}

}
