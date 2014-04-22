<?php
require_once( dirname( __FILE__ ) . '/../admin/includes/HtmlImportSettings.php' );
require_once( dirname( __FILE__ ) . '/includes/WPMetaConfigs.php' );
require_once( dirname( __FILE__ ) . '/includes/XMLHelper.php' );
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

		$settings = new \html_import\admin\HtmlImportSettings();
		if ( ! get_option( $settings::SETTINGS_NAME ) ) {
			$settings->saveToDB();
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

	private function getGridDirectorHeader($title) {
		$title = 'Grid Director Developer Network : '.$title;
		$subTitle = '';
		$header = '[vc_row][vc_column width="1/1"][mk_fancy_title tag_name="h2" style="true" color="#4a266d" size="24" font_weight="bolder" margin_top="0" margin_bottom="18" font_family="Ubuntu" font_type="google" align="left"]'.$title.'[/mk_fancy_title][mk_fancy_title tag_name="h2" style="false" color="#393836" size="24" font_weight="300" margin_top="0" margin_bottom="18" font_family="Ubuntu" font_type="google" align="left"]'.$subTitle.'[/mk_fancy_title][mk_padding_divider size="10" width="1/1" el_position="first last"][vc_column_text disable_pattern="false" align="left" margin_bottom="0" p_margin_bottom="20" width="1/1" el_position="first last"]';
		return $header;
	}

	private function getGridDirectorFooter() {
		$footer = '[/vc_column_text][mk_padding_divider size="20" width="1/1" el_position="first last"][/vc_column][/vc_row]';
		return $footer;
	}


	private function get_title( SimpleXMLElement $html_file ) {
		$title = '';
		foreach ( $html_file->head->title as $titleElement ) {
			$title = '' . $titleElement;
		}

		return $title;
	}

	private function get_body( SimpleXMLElement $html_file, $filepath, $html_post_lookup ) {
		// TODO: paths are all unix specific?

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
								if ($fullpath) {
									if ( array_key_exists( $fullpath, $html_post_lookup ) ) {
										$link_table[$path] = $fullpath;
									}
								}
								else {
									echo '<span>***could not update link '.$path.'</span><br>';
								}
							}
						}
					}
				}
			}
		}

		foreach ( $link_table as $link => $full_link ) {
			$post_id    = $html_post_lookup[$full_link];
			$post_link  = get_permalink( $post_id );
			$search_str = '/(\b[hH][rR][eE][fF]\s*=\s*")(\b' . preg_quote( $link, '/' ) . '\b)(")/';
			$body       = preg_replace( $search_str, '$1' . preg_quote( $post_link, '/' ) . '$3', $body );
		}


		return $body;
	}

	private function importAnHTML( $source_file, $stub_only = true, html_import\admin\HtmlImportSettings $settings, \html_import\WPMetaConfigs $parent_page = null, $category = null, $tag = null, $order = null, $html_post_lookup, $title = null ) {

		$pageMeta = new \html_import\WPMetaConfigs();

		$file_as_xml_obj = \html_import\XMLHelper::getXMLObjectFromFile( $source_file );

		if (is_null($title)) {
			$pageMeta->setPostTitle($this->get_title( $file_as_xml_obj ));
		} else {
			$pageMeta->setPostTitle($title);
		}
		$pageMeta->setPostName($pageMeta->getPostTitle());
		$post               = get_page_by_title( $pageMeta->getPostTitle() );
		if ( isset( $html_post_lookup ) ) {
			if ( array_key_exists( $source_file, $html_post_lookup ) ) { // stub was created during this cycle
				$pageMeta->setPostId($html_post_lookup[$source_file]);
			}
		} else {
			if ( ! is_null( $post ) ) { // post was previously created
				$pageMeta->setPostId($post->ID);
				echo '<li>Page with title '.$pageMeta->getPostTitle().' and ID '.$pageMeta->getPostId().' already exists, now tagged to be overwritten.</li>';
			}
		}
		$pageMeta->setPostStatus('publish');
		if ( !$stub_only ) {
			if (!is_null($file_as_xml_obj)) {
				$pageMeta->setPostContent($this->getGridDirectorHeader(htmlspecialchars($title)).$this->get_body( $file_as_xml_obj, dirname( $source_file ), $html_post_lookup ).$this->getGridDirectorFooter());
			}
		}
		$pageMeta->setPostType('page');
		$pageMeta->setCommentStatus('closed');
		$pageMeta->setPingStatus('closed');
		$pageMeta->setPostCategory($category);
		$pageMeta->setPostDate(date( 'Y-m-d H:i:s', filemtime( $source_file ) ));

		if (!is_null($parent_page)) {
			$pageMeta->setPostParent($parent_page->getPostId());
		}

		if ( isset ( $order ) ) {
			$pageMeta->setMenuOrder($order);
		}
		$pageMeta->setPostAuthor(wp_get_current_user()->ID);

		if ( is_null( $post ) ) {
			$updateResult = $pageMeta->updateWPPost();
			if ( is_wp_error( $updateResult ) ) {
				echo '<li>***Unable to create content ' . $pageMeta->getPostTitle() . ' from ' . $source_file . '</li>';
				return 0;
			} else {
				echo '<li>Stub post created from ' . $source_file . ' into post #' . $updateResult . ' with title ' . $pageMeta->getPostTitle() . '</li>';
				$pageMeta->setPostId($updateResult);
			}
		} else {
			$updateResult = $pageMeta->updateWPPost();
			if ( is_wp_error($updateResult) ) {
				echo '<li>***Unable to fill content ' . $pageMeta->getPostTitle() . ' from ' . $source_file . ': '.$updateResult->get_error_message().'</li>';
				return 0;
			} else {
				echo '<li>Content filled from ' . $source_file . ' into post #' . $updateResult . ' with title ' . $pageMeta->getPostTitle() . '</li>';
				$pageMeta->setPostId($updateResult);
			}
		}

		return $pageMeta;
	}

	private function importMedia( $post_id, $source_path, &$media_lookup ) {

		$body        = get_post( $post_id )->post_content;
		if (is_null($body) || strcmp('', $body) == 0) {
			echo '** the body for post '.$post_id.' was empty, no media to import.';
			return;
		}
		$media_table = Array();

		$file_as_xml_obj = \html_import\XMLHelper::getXMLObjectFromString($body);

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
								if ( array_key_exists( $fullpath, $media_lookup ) ) {
									$attach_id = $media_lookup[$fullpath];
									require_once( ABSPATH . 'wp-admin/includes/image.php' );
									$attach_data = wp_get_attachment_metadata( $attach_id );
									wp_update_attachment_metadata( $attach_id, $attach_data );
								} else {
									$filename = basename( $fullpath );
									$upload   = wp_upload_bits( $filename, null, file_get_contents( $fullpath ) );
									if ( $upload['error'] ) {
										echo '<li>***Unable to upload media file ' . $filename . '</li>';
									} else {
										echo '<li>' . $filename . ' media file uploaded.</li>';
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
										echo '<li>' . $filename . ' attached to post ' . $post_id . '</li>';
									}
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
									if ( array_key_exists( $fullpath, $media_lookup ) ) {
										$attach_id = $media_lookup[$fullpath];
										require_once( ABSPATH . 'wp-admin/includes/image.php' );
										$attach_data = wp_get_attachment_metadata( $attach_id );
										wp_update_attachment_metadata( $attach_id, $attach_data );
									} else {
										$filename = basename( $fullpath );

										$upload = wp_upload_bits( $filename, null, file_get_contents( $fullpath ) );
										if ( $upload['error'] ) {
											echo '<li>***Unable to upload media file ' . $filename . '</li>';
										} else {
											echo '<li>' . $filename . ' media file uploaded.</li>';
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
											echo '<li>' . $filename . ' attached to post ' . $post_id . '</li>';
										}
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
		if ( wp_update_post( $page ) > 0 ) {
			echo '<li>Post ' . $page['ID'] . ' updated with correct image links.</li>';
		} else {
			echo '<li>***Post ' . $page['ID'] . ' could not be updated with correct image links.</li>';
		}

	}

	private function processNode( DOMNode $node, $stubs_only = true, \html_import\WPMetaConfigs $parent_page = null, &$html_post_lookup, &$media_lookup, html_import\admin\HtmlImportSettings $settings ) {
		$attributes = $node->attributes;
		$title      = null;
		$src        = null;
		$category   = Array();
		$tag        = Array();
		$order      = 0;

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
							$src = realpath( dirname( $settings->getFileLocation()->getValue() ) . '/' . $src );
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

		if ( ! is_null( $category ) && is_array( $category ) ) {
			foreach ( $category as $index => $cat ) {
				$cat_id              = wp_create_category( trim( $cat ) );
				$categoryIDs[$index] = intval( $cat_id );
			}
		}
		if ( ! is_null( $tag ) && is_array( $tag ) ) {
			foreach ( $tag as $t ) {
				//TODO: support tags
			}
		}


		if ( isset( $src ) ) {
			if ( file_exists( $src ) ) {
				if ( $stubs_only ) {
					$parent_page = $this->importAnHTML( $src, true, $settings, $parent_page, $categoryIDs, null, $order, null, $title );
					$html_post_lookup[$src] = $parent_page->getPostId();
				} else {
					$parent_page = $this->importAnHTML( $src, false, $settings, $parent_page, $categoryIDs, null, $order, $html_post_lookup, $title );
					$this->importMedia( $parent_page->getPostId(), $src, $media_lookup );
					update_post_meta( $parent_page->getPostId(), '_wp_page_template', $settings->getTemplate()->getValue() );
				}
			} else {
				echo '<li>Unable to find ' . $src . '</li>';
			}
		}


		// recurse through children nodes
		$children = $node->childNodes;
		if ( isset( $children ) ) {
			for ( $i = 0; $i < $children->length; $i ++ ) {
				$this->processNode( $children->item( $i ), $stubs_only, $parent_page, $html_post_lookup, $media_lookup, $settings );
			}
		}
	}

	private function process_xml_file( $stubs_only = true, &$html_post_lookup = null, &$media_lookup, html_import\admin\HtmlImportSettings $settings ) {
		set_time_limit(520);
		if ( ! isset( $html_post_lookup ) ) {
			$html_post_lookup = Array();
		}

		$doc = new DOMDocument();
		$doc->load( $settings->getFileLocation()->getValue(), LIBXML_NOBLANKS );

		$nodelist = $doc->childNodes;

		$parent_page = new \html_import\WPMetaConfigs();
		$hasParent = $parent_page->loadFromPostID($settings->getParentPage()->getValue());
		if (!$hasParent) {
			$parent_page = null;
		}

		for ( $i = 0; $i < $nodelist->length; $i ++ ) {
			$this->processNode( $nodelist->item( $i ), $stubs_only, $parent_page, $html_post_lookup, $media_lookup, $settings );
		}

		return $html_post_lookup;
	}

	private function importFlareNode( $flare_path, $stubs_only = true, \html_import\WPMetaConfigs $parent_page = null, &$html_post_lookup, &$media_lookup, $orderNum, $pagePath, $pageTitle, html_import\admin\HtmlImportSettings $settings) {

		$title      = $pageTitle;
		$src = realpath( $flare_path . $pagePath );

		$category   = Array(); // TODO:
		$tag        = Array();
		$order      = $orderNum;

		if ( ! is_null( $category ) && is_array( $category ) ) {
			foreach ( $category as $index => $cat ) {
				$cat_id              = wp_create_category( trim( $cat ) );
				$categoryIDs[$index] = intval( $cat_id );
			}
		}
		if ( ! is_null( $tag ) && is_array( $tag ) ) {
			foreach ( $tag as $t ) {
				//TODO: support tags
			}
		}


		if ( isset( $src ) ) {
			if ( file_exists( $src ) ) {
				if ( $stubs_only ) {
					$parent_page = $this->importAnHTML( $src, true, $settings, $parent_page, $categoryIDs, null, $order, null, $title );
					$html_post_lookup[$src] = $parent_page->getPostId();
				} else {
					$parent_page = $this->importAnHTML( $src, false, $settings, $parent_page, $categoryIDs, null, $order, $html_post_lookup, $title );
					$this->importMedia( $parent_page->getPostId(), $src, $media_lookup );
					update_post_meta( $parent_page->getPostId(), '_wp_page_template', $settings->getTemplate()->getValue() );
				}
			} else {
				echo '<li>Unable to find ' . $src . '</li>';
			}
		}

		return $parent_page;
	}

	private function process_flare_node( $flare_path, &$index, Array $orderIds, Array $elementTails, Array $pages, $stubs_only = true, \html_import\WPMetaConfigs $parent_page = null, &$html_post_lookup = null, &$media_lookup, html_import\admin\HtmlImportSettings $settings ) {

		// TODO: this algorithm must be reimagined!  this is horrid and hacky!

		for ( $i = $index; $i < sizeof($orderIds); $i++) {
			$order = $orderIds[$i];
			$tail = $elementTails[$i];
			$pagePath = key($pages[$order]);
			$pageTitle = $pages[$order][$pagePath];

			$parent_page = $this->importFlareNode($flare_path, $stubs_only, $parent_page, $html_post_lookup, $media_lookup, $order, $pagePath, $pageTitle, $settings);
			//echo $index.' '.$order.' '.$pageTitle.'<br>';
			if (strcmp($tail, '}') == 0) {
				// next element is a sibling
				continue;
			} else if (strcmp($tail, ',n:[') == 0) {
				//next element is a child
				$i++;

				$levels = $this->process_flare_node($flare_path, $i, $orderIds, $elementTails, $pages, $stubs_only, $parent_page, $html_post_lookup, $media_lookup, $settings);
				if ($levels > 0) {
					$index = $i; // this is to keep the counter counting
					return $levels - 1;
				}
			} else {
				// move up
				$levels = substr_count($tail, '}');
				if ($i == (sizeof($orderIds) - 1)){
					$levels = $levels - 2;
				}
				$index = $i;
				return $levels -2; // return number of levels to return
			}

		}
		return 0;
	}

	private function process_flare_index( $flare_path, Array $orderIds, Array $elementTails, Array $pages, $stubs_only = true, &$html_post_lookup = null, &$media_lookup = null, html_import\admin\HtmlImportSettings $settings ) {

		set_time_limit(520);
		if ( ! isset( $html_post_lookup ) ) {
			$html_post_lookup = Array();
		}

		$index = 0;

		$parent_page = new \html_import\WPMetaConfigs();
		$hasParent = $parent_page->loadFromPostID($settings->getParentPage()->getValue());
		if (!$hasParent) {
			$parent_page = null;
		}
		$this->process_flare_node($flare_path, $index, $orderIds, $elementTails, $pages, $stubs_only, $parent_page,$html_post_lookup, $media_lookup, $settings);

		return $html_post_lookup;
	}

	public function import_html_from_xml_index( html_import\admin\HtmlImportSettings $settings ) {
		$media_lookup = Array();
		echo '<h2>Output from Import</h2><br>Please be patient</br>';
		if ( \html_import\XMLHelper::valid_xml_file( $settings->getFileLocation()->getValue() ) ) {
			echo '<ul>';
			$html_post_lookup = $this->process_xml_file( true, $html_post_lookup, $media_lookup, $settings );
			$this->process_xml_file( false, $html_post_lookup, $media_lookup, $settings );
			echo '</ul>';
		} else {
			echo 'Cannot find file '.$settings->getFileLocation()->getEscapedHTMLValue()."<br>Current path is ".getcwd().'<br>';
		}
	}

	private function findFile($filename, $root) {
		$allFiles = scandir(realpath($root));
		foreach ($allFiles as $file) {
			if ((strcmp($file, '.') == 0) || (strcmp($file, '..')) == 0) {
				continue;
			}
			if (strcmp($filename, $file) == 0) {
				return $root.'/'.$file;
			}
			if (is_dir($root.'/'.$file)) {
				$foundFile = $this->findFile($filename, $root.'/'.$file);
				if (!is_null($foundFile)) {
					return $foundFile;
				}
			}
		}
	}

	public function import_html_from_flare( $zip_to_upload, html_import\admin\HtmlImportSettings $settings) {
		/*
		 * $zip_to_uplaod is an array with elements:
		 * 	name
		 * 	type
		 * 	tmp_name
		 * 	error
		 * 	size
		 *
		 * .rar    application/x-rar-compressed, application/octet-stream
				.zip    application/zip, application/octet-stream
		 */
		echo '<h2>Output from Import</h2><br>Please be patient</br>';
		echo '<ul>';
		$mime_type = $zip_to_upload['type'];
	if ((strcmp('application/x-rar-compressed', $mime_type) == 0) || (strcmp('application/octet-stream', $mime_type) == 0) || (strcmp('application/zip', $mime_type) == 0) || (strcmp('application/octet-stream', $mime_type) == 0)) {
			$zip = new ZipArchive;
			$res = $zip->open($zip_to_upload['tmp_name']);
			if ($res === TRUE) {
				$upload_dir = wp_upload_dir();
				$path = $upload_dir['path'].'/import';
				$path_modifier = 1;
				while (file_exists($path.'-'.$path_modifier)) {
					$path_modifier++;
				}
				$extractSuccess = $zip->extractTo($path.'-'.$path_modifier);
				$closeSuccess = $zip->close();

				// TODO: parse to find index Toc.js
				$tocJS = $this->findFile('Toc.js', $path.'-'.$path_modifier);
				$tocContents = file_get_contents($tocJS);
				preg_match('/numchunks:([0-9]*?),/', $tocContents, $numChunksMatch);
				$numChunks = $numChunksMatch[1]; // TODO: deal with multiple chunks
				preg_match("/prefix:'(.*?)',/", $tocContents, $tocMatches);
				$chunkName = $tocMatches[1]; // TODO: handle alternate chunk file names

				$tocChunk0JS = $this->findFile('Toc_Chunk0.js', $path.'-'.$path_modifier);
				$tocChunkContents = file_get_contents($tocChunk0JS);
				// parses the chunk file and gets the list of all files to import
				preg_match_all("/('(.*)':\\{i:\\[(\\d*)\\],t:\\['(.*)?'\\],b:\\[''\\]\\})/U", $tocChunkContents, $regMatches);
				$length = sizeof($regMatches[2]);
				$fileList = Array();
				for ($i = 0; $i < $length; $i++) {
					// key is the identifier id, value is hash with key relative file location and value title
					$fileList[$regMatches[3][$i]] = Array($regMatches[2][$i] => $regMatches[4][$i]);
				}

				// now to walk the tree
				$matches = null;
				$returnValue = preg_match_all('/\\{i:(\\d*),c:(\\d*)(,n:\\[|[\\}\\]]*)/', $tocContents, $matches);
				$fileOrder = $matches[1];
				$elementTail = $matches[3];

				$media_lookup = Array();
				$html_post_lookup = $this->process_flare_index( $path.'-'.$path_modifier, $fileOrder, $elementTail, $fileList, true, $html_post_lookup, $media_lookup, $settings );
				$this->process_flare_index( $path.'-'.$path_modifier, $fileOrder, $elementTail, $fileList, false, $html_post_lookup, $media_lookup, $settings );

			} else {
				echo '<H4>Failed to read ZIP: failed, code :' . $res.'</H4>';
			}
		} else {
			echo '<H4>File uploaded is not ZIP or RAR</H4>';
		}
		echo '</ul>';
	}
}