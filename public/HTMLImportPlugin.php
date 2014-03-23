<?php
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

		if ( ! get_option( 'htim_importer_options' ) ) {
			$site_options_arr = array( );
			// update the database with the default option values
			update_site_option( 'htim_importer_options', $site_options_arr );
		}

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
	 * Ensure the provided file exists
	 *
	 * @param $xml_path
	 *
	 * @return bool
	 */
	private function valid_xml_file( $xml_path ) {
		if ( file_exists( $xml_path ) ) {
			return true;
		}

		return false;
	}

	private function get_title( SimpleXMLElement $html_file ) {
		$title = '';
		foreach ( $html_file->head->title as $titleElement ) {
			$title = '' . $titleElement;
		}

		return $title;
	}

	private function get_body( SimpleXMLElement $html_file, $filepath, $html_post_lookup ) {
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
		// TODO: paths are all unix specific

		$body = $html_file->body->asXML();

		$link_table = Array();
		$all_links  = $html_file->xpath( '//a[@href]' );
		// TODO: encapsulate this in a function
		if ( $all_links ) {
			foreach ( $all_links as $link ) {

				foreach ( $link->attributes() as $attribute => $value ) {
					$path = '' . $value;
					if ( 0 == strcasecmp( 'href', $attribute ) ) {
						if ( ! preg_match( '/^[a-zA-Z].*:.*/', $path ) ) {
							if ( preg_match( '/\.([hH][tT][mM][lL]?)$/', $path ) ) { // if html or htm
								if ( $path[0] != '/' ) {
									$fullpath = realpath( $filepath . '/' . $path );
								} else {
									$fullpath = $path;
								}
								if ( array_key_exists( $fullpath, $html_post_lookup ) ) {
									$link_table[$path] = $fullpath;
								}
							}
						}
					}
				}
			}
		}


		// TODO: returns the link based on the page id, not the permalink
		foreach ( $link_table as $link => $full_link ) {
			$post_id    = $html_post_lookup[$full_link];
			$post_link  = get_permalink( $post_id );
			$search_str = '/(\b[hH][rR][eE][fF]\s*=\s*")(\b' . preg_quote( $link, '/' ) . '\b)(")/';
			$body       = preg_replace( $search_str, '$1' . preg_quote( $post_link, '/' ) . '$3', $body );
		}


		return $body;
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

	/**
	 * @param      $source_file string must be the absolute path of the file to import
	 * @param bool $stub_only
	 * @param null $parent_page_id
	 * @param null $category
	 * @param null $tag
	 * @param null $order
	 * @param      $html_post_lookup
	 *
	 * @return int|WP_Error
	 */
	private function importAnHTML( $source_file, $stub_only = true, $parent_page_id = null, $category = null, $tag = null, $order = null, $html_post_lookup ) {
		// TODO: handle images (URL vs local files)

		$file_as_xml_obj = $this->getXMLObject( $source_file );

		$page               = Array();
		$page['post_title'] = $this->get_title( $file_as_xml_obj );
		$page['post_name']  = sanitize_title_with_dashes( $page['post_title'] );
		$post               = get_page_by_title( $page['post_title'] );
		if ( isset( $html_post_lookup ) ) {
			if ( array_key_exists( $source_file, $html_post_lookup ) ) { // stub was created during this cycle
				$page['ID'] = $html_post_lookup[$source_file];
			}
		} else {
			if ( ! is_null( $post ) ) { // post was previously created
				$page['ID'] = $post->ID;
			}
		}
		if ( $stub_only ) {
			$page['post_status'] = 'publish';
		} else {
			$page['post_status']  = 'publish';
			$page['post_content'] = $this->get_body( $file_as_xml_obj, dirname( $source_file ), $html_post_lookup );
		}
		$page['post_type']      = 'page';
		$page['comment_status'] = 'closed';
		$page['ping_status']    = 'closed';
		$page['post_category']  = $category;
		$page['post_excerpt']   = ''; // TODO, pull from meta
		$page['post_date']      = date( 'Y-m-d H:i:s', filemtime( $source_file ) );
		if ( isset( $parent_page_id ) ) {
			$page['post_parent'] = $parent_page_id;
		}
		if ( isset ( $order ) ) {
			$page['menu_order'] = $order;
		}
		$page['post_author'] = wp_get_current_user()->ID;

		if ( is_null( $post ) ) {
			$page_id = wp_insert_post( $page );
			if ( is_wp_error( $page_id ) ) {
				// TODO: handle error
				echo 'post did not post';
			}
		} else {
			$page_id = wp_update_post( $page );
		}

		return $page_id;
	}

	private function importMedia( $post_id, $source_path, &$media_lookup ) {

		$body        = get_post( $post_id )->post_content;
		$media_table = Array();

		$doc                      = new DOMDocument();
		$doc->strictErrorChecking = false;
		libxml_use_internal_errors( true ); // some ok HTML will generate errors, this masks them, pt 1/2
		$doc->loadHTML( $body, LIBXML_HTML_NOIMPLIED );
		libxml_clear_errors(); // some ok HTML will generate errors, this masks them, pt 2/2
		$file_as_xml_obj = simplexml_import_dom( $doc );


		// import img srcs
		$all_imgs = $file_as_xml_obj->xpath( '//img[@src]' );
		if ( $all_imgs ) {
			foreach ( $all_imgs as $img ) {

				foreach ( $img->attributes() as $attribute => $value ) {
					$path = '' . $value;
					if ( 0 == strcasecmp( 'src', $attribute ) ) {
						// TODO: this is duplicated above, refactor it out
						if ( ! preg_match( '/^[a-zA-Z].*:.*/', $path ) ) { // if it's local
							if ( ( ! is_null( $media_lookup ) && ( ! array_key_exists( $path, $media_table ) ) ) ) {

								if ( $path[0] != '/' ) {
									$fullpath = realpath( dirname( $source_path ) . '/' . $path );
								} else {
									$fullpath = $path;
								}
								if (array_key_exists($fullpath, $media_lookup)) {
									$attach_id   = $media_lookup[$fullpath] ;
									require_once( ABSPATH . 'wp-admin/includes/image.php' );
									$attach_data = wp_get_attachment_metadata( $attach_id );
									wp_update_attachment_metadata( $attach_id, $attach_data );
								} else {
									$filename = basename( $fullpath );
									$upload   = wp_upload_bits( $filename, null, file_get_contents( $fullpath ) );
									// TODO: handle error, $upload is array with keys file (system path), url, error
									$wp_filetype = wp_check_filetype( basename( $upload['file'] ), null );
									$attachment  = array(
											'guid'           => $upload['file'],
											'post_mime_type' => $wp_filetype['type'],
											'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $upload['file'] ) ),
											'post_content'   => '',
											'post_status'    => 'inherit' );
									$attach_id   = wp_insert_attachment( $attachment, $upload['file'], $post_id );
									require_once( ABSPATH . 'wp-admin/includes/image.php' );
									$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
									wp_update_attachment_metadata( $attach_id, $attach_data );
									$media_lookup[$fullpath] = $attach_id;
									$media_table[$path]      = $fullpath;
								}
							}
						}
					}
				}
			}
		}

		// linked media
		$all_links = $file_as_xml_obj->xpath( '//a[@href]' );
		// TODO: encapsulate this in a function
		if ( $all_links ) {
			foreach ( $all_links as $link ) {

				foreach ( $link->attributes() as $attribute => $value ) {
					$path = '' . $value;
					if ( 0 == strcasecmp( 'href', $attribute ) ) {
						if ( ! preg_match( '/^[a-zA-Z].*:.*/', $path ) ) {

							if ( preg_match( '/\.(png|bmp|jpg|jpeg|gif|pdf|doc|docx|mp3|ogg|wav)$/', strtolower( $path ) ) ) { // media png,bmp,jpg,jpeg,gif,pdf,doc,docx,mp3,ogg,wav
								if ( ( ! is_null( $media_lookup ) ) ) {
									if ( $path[0] != '/' ) {
										$fullpath = realpath( dirname( $source_path ) . '/' . $path );
									} else {
										$fullpath = $path;
									}
									if (array_key_exists($fullpath, $media_lookup)) {
										$attach_id   = $media_lookup[$fullpath] ;
										require_once( ABSPATH . 'wp-admin/includes/image.php' );
										$attach_data = wp_get_attachment_metadata( $attach_id );
										wp_update_attachment_metadata( $attach_id, $attach_data );
									} else {
										$filename = basename( $fullpath );

										$upload = wp_upload_bits( $filename, null, file_get_contents( $fullpath ) );
										// TODO: handle error, $upload is array with keys file (system path), url, error
										$wp_filetype = wp_check_filetype( basename( $upload['file'] ), null );
										$attachment  = array(
												'guid'           => $upload['file'],
												'post_mime_type' => $wp_filetype['type'],
												'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $upload['file'] ) ),
												'post_content'   => '',
												'post_status'    => 'inherit' );
										$attach_id   = wp_insert_attachment( $attachment, $upload['file'], $post_id );
										require_once( ABSPATH . 'wp-admin/includes/image.php' );
										$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
										wp_update_attachment_metadata( $attach_id, $attach_data );
										$media_lookup[$fullpath] = $attach_id;

										$media_table[$path] = $fullpath;
									}
								}
							}
						}
					}
				}
			}
		}

		foreach ( $media_table as $media_item => $full_media_path ) {
			$media_id   = $media_lookup[$full_media_path];
			$media_url  = wp_get_attachment_url( $media_id );
			$search_str = '/(\b[iI][mM][gG]\s*[^>]*\s+[sS][rR][cC]\s*=\s*")(\b' . preg_quote( $media_item, '/' ) . '\b)(")/';
			$body       = preg_replace( $search_str, '$1' . preg_quote( $media_url, '/' ) . '$3', $body ); // img src
			$body       = preg_replace( '/(\b[hH][rR][eE][fF]\s*=\s*")(\b' . preg_quote( $media_item, '/' ) . '\b)(")/', '$1' . preg_quote( $media_url, '/' ) . '$3', $body ); // a href
		}

		$page['ID']           = $post_id;
		$page['post_content'] = $body;
		wp_update_post( $page );
	}

	private function processNode( $xml_path, DOMNode $node, $stubs_only = true, &$html_post_lookup, &$media_lookup, $parent_id = null, $template_name = '' ) {
		$attributes = $node->attributes;
		$title      = null;
		$src        = null;
		$category   = Array();
		$tag        = Array();
		$order      = 0;
		$my_id      = $parent_id;

		if ( isset( $attributes ) ) {
			for ( $i = 0; $i < $attributes->length; $i ++ ) {
				$attribute = $attributes->item( $i )->nodeName;
				switch ( $attribute ) {
					case 'title':
						$title = $attributes->item( $i )->nodeValue;
						break;
					case 'src':
						$src = $attributes->item( $i )->nodeValue;
						if ( $src[0] != '/' ) {
							$src = realpath( dirname( $xml_path ) . '/' . $src );
						}
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
		if ( ! is_null( $category ) && is_array( $category ) ) {
			foreach ( $category as $index => $cat ) {
				$cat_id              = wp_create_category( trim( $cat ) );
				$categoryIDs[$index] = intval( $cat_id );
			}
		}
		foreach ( $tag as $t ) {
		}


		// TODO: handle title, tags, categories and ordering
		if ( isset( $src ) ) {
			// TODO: validate source file
			if ( $stubs_only ) {
				$my_id                  = $this->importAnHTML( $src, true, $parent_id, $categoryIDs, null, $order, null );
				$html_post_lookup[$src] = $my_id;
				// TODO: set the template
			} else {
				$my_id = $this->importAnHTML( $src, false, $parent_id, $categoryIDs, null, $order, $html_post_lookup );
				$this->importMedia( $my_id, $src, $media_lookup );
				//TODO: set the template
			}
		}


		// recurse through children nodes
		$children = $node->childNodes;
		if ( isset( $children ) ) {
			for ( $i = 0; $i < $children->length; $i ++ ) {
				$this->processNode( $xml_path, $children->item( $i ), $stubs_only, $html_post_lookup, $media_lookup, $my_id, $template_name );
			}
		}
	}


	private function process_xml_file( $xml_path, $stubs_only = true, &$html_post_lookup = null, &$media_lookup, $parent_page_id, $template_name ) {
		if ( ! isset( $html_post_lookup ) ) {
			$html_post_lookup = Array();
		}

		$doc = new DOMDocument();
		$doc->load( $xml_path, LIBXML_NOBLANKS );

		$nodelist = $doc->childNodes;
		for ( $i = 0; $i < $nodelist->length; $i ++ ) {
			$this->processNode( $xml_path, $nodelist->item( $i ), $stubs_only, $html_post_lookup, $media_lookup, $parent_page_id, $template_name );
		}

		return $html_post_lookup;
	}

	public function import_html_from_xml_index( $xml_path, $parent_page_id, $template_name ) {
		$media_lookup = Array();
		if ( $this->valid_xml_file( $xml_path ) ) {
			$html_post_lookup = $this->process_xml_file( $xml_path, true, $html_post_lookup, $media_lookup, $parent_page_id, $template_name );
			$this->process_xml_file( $xml_path, false, $html_post_lookup, $media_lookup, $parent_page_id, $template_name );
		}
	}

}
