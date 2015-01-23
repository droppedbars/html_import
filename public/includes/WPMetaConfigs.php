<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 5:42 PM
 */

namespace html_import;

require_once( dirname( __FILE__ ) . '/indices/WebPageSettings.php' );

use html_import\indices\WebPage;

/**
 * Class WPMetaConfigs
 * Represents all of the meta configs for a Wordpress post/page.  It also handles saving to the Wordpress database, loading from the WordPress database and building up from the plugin settings and a WebPage.
 * @package html_import
 */
class WPMetaConfigs {
	private $post_title = '';
	private $post_name = '';
	private $post_id = '';
	private $post_status = '';
	private $post_type = '';
	private $comment_status = '';
	private $ping_status = '';
	private $post_category = Array();
	private $post_excerpt = '';
	private $post_date = '';
	private $post_parent = '';
	private $menu_order = '';
	private $post_author = '';
	private $post_content = '';
	private $page_template = '';
	private $source_path = '';

	/**
	 * @return string
	 */
	public function getSourcePath() {
		return $this->source_path;
	}

	/**
	 * @param string $source_path
	 */
	public function setSourcePath( $source_path ) {
		$this->source_path = $source_path;
	}

	/**
	 * @return mixed
	 */
	public function getPageTemplate() {
		return $this->page_template;
	}

	/**
	 * @param mixed $page_template
	 */
	public function setPageTemplate( $page_template ) {
		$this->page_template = $page_template;
	}

	/**
	 * @return mixed
	 */
	public function getPostContent() {
		return $this->post_content;
	}

	/**
	 * @param mixed $post_content
	 */
	public function setPostContent( $post_content ) {
		$this->post_content = $post_content;
	}

	/**
	 * Inserts the post into Wordpress and returns the resulting post ID.
	 * @return int|\WP_Error
	 */
	public function updateWPPost() {
		// TODO: handle WP_Error object if set to true.
		$postArray = $this->getPostArray();
		$result    = wp_insert_post( $postArray, true );
		if ( !is_wp_error( $result ) ) {
			$this->setPostId( $result );
		}

		return $result;
	}

	/**
	 * Returns the meta data as a standard Wordpress Post Array.
	 * @return array
	 */
	public function getPostArray() {
		$post_array = Array(

				'ID'             => $this->post_id,
				'post_author'    => $this->post_author,
				'post_name'      => $this->post_name,
				'post_type'      => $this->post_type,
				'post_title'     => $this->post_title,
				'post_date'      => $this->post_date,
			// 'post_date_gmt' $post->post_date_gmt
				'post_content'   => $this->post_content,
				'post_excerpt'   => $this->post_excerpt,
				'comment_status' => $this->comment_status,
				'ping_status'    => $this->ping_status,
				'post_status'    => $this->post_status,
			// $post->post_password
				'post_parent'    => $this->post_parent,
			// $post->post_modified
			// 'post_date_gmt' $post->post_modified_gmt
			// $post->comment_count
				'menu_order'     => $this->menu_order,
				'post_category'  => $this->post_category,
				'page_template'  => $this->page_template

		);


		return $post_array;
	}

	/**
	 * @return mixed
	 */
	public function getCommentStatus() {
		return $this->comment_status;
	}

	/**
	 * @param mixed $comment_status
	 */
	public function setCommentStatus( $comment_status ) {
		$this->comment_status = $comment_status;
	}

	/**
	 * @return mixed
	 */
	public function getMenuOrder() {
		return $this->menu_order;
	}

	/**
	 * @param mixed $menu_order
	 */
	public function setMenuOrder( $menu_order ) {
		$this->menu_order = $menu_order;
	}

	/**
	 * @return mixed
	 */
	public function getPingStatus() {
		return $this->ping_status;
	}

	/**
	 * @param mixed $ping_status
	 */
	public function setPingStatus( $ping_status ) {
		if ( !is_string( $ping_status ) ) {
			throw new \InvalidArgumentException( "Post status must be a string" );
		}
		if ( ( $ping_status != 'closed' ) && ( $ping_status != 'open' ) ) {
			throw new \InvalidArgumentException( "Post status must be one of: closed, open" );
		}
		$this->ping_status = $ping_status;
	}

	/**
	 * @return mixed
	 */
	public function getPostAuthor() {
		return $this->post_author;
	}

	/**
	 * @param mixed $post_author
	 */
	public function setPostAuthor( $post_author ) {
		if ( !is_integer( $post_author ) && ( !is_null( $post_author ) ) ) {
			throw new \InvalidArgumentException( "Post author ID must be an integer or null." );
		}
		$this->post_author = $post_author;
	}

	/**
	 * @return mixed
	 */
	public function getPostCategory() {
		return $this->post_category;
	}

	/**
	 * @param Array $post_category
	 */
	public function setPostCategory( Array $post_category ) {
		if ( !is_array( $post_category ) && ( !is_null( $post_category ) ) ) {
			throw new \InvalidArgumentException( "Post category must be an array or null." );
		}

		$this->post_category = $post_category;
	}

	/**
	 * @return mixed
	 */
	public function getPostDate() {
		return $this->post_date;
	}

	/**
	 * Sets the date and time in the appropriate format for Wordpress.  If null is passed in, then the current datetime as calculated by time() will be used.
	 *
	 * @param integer $post_date
	 */
	public function setPostDate( $post_date ) {
		if ( !is_integer( $post_date ) && ( !is_null( $post_date ) ) ) {
			throw new \InvalidArgumentException( "Post date must be an integer or null." );
		}
		if ( is_null( $post_date ) ) {
			$datetime = time();
		} else {
			$datetime = $post_date;
		}

		$this->post_date = date( 'Y-m-d H:i:s', $datetime );
	}

	/**
	 * @return mixed
	 */
	public function getPostExcerpt() {
		return $this->post_excerpt;
	}

	/**
	 * @param mixed $post_excerpt
	 */
	public function setPostExcerpt( $post_excerpt ) {
		$this->post_excerpt = $post_excerpt;
	}

	/**
	 * @return mixed
	 */
	public function getPostId() {
		return $this->post_id;
	}

	/**
	 * @param mixed $post_id
	 */
	public function setPostId( $post_id ) {
		if ( !is_integer( $post_id ) && ( !is_null( $post_id ) ) ) {
			throw new \InvalidArgumentException( "Post ID must be an integer or null." );
		}
		$this->post_id = $post_id;
	}

	/**
	 * @return mixed
	 */
	public function getPostName() {
		return $this->post_name;
	}

	/**
	 * Given a name for the post, sets it to the meta data but sanitized with dashes instead of spaces.
	 *
	 * @param mixed $post_name
	 */
	public function setPostName( $post_name ) {
		$this->post_name = sanitize_title_with_dashes( $post_name );
	}

	/**
	 * @return mixed
	 */
	public function getPostParent() {
		return $this->post_parent;
	}

	/**
	 * @param mixed $post_parent
	 */
	public function setPostParent( $post_parent ) {
		if ( !is_integer( $post_parent ) && ( !is_null( $post_parent ) ) ) {
			throw new \InvalidArgumentException( "Post parent ID must be an integer or null." );
		}
		$this->post_parent = $post_parent;
	}

	/**
	 * @return mixed
	 */
	public function getPostStatus() {
		return $this->post_status;
	}

	/**
	 * @param mixed $post_status
	 */
	public function setPostStatus( $post_status ) {
		if ( !is_string( $post_status ) && !is_null( $post_status ) ) {
			throw new \InvalidArgumentException( "Post status must be a string or null." );
		}
		if ( ( $post_status != 'publish' ) && ( $post_status != 'pending' ) && ( $post_status != 'draft' ) && ( $post_status != 'auto-draft' ) && ( $post_status != 'future' ) && ( $post_status != 'inherit' ) && ( $post_status != 'trash' ) ) {
			throw new \InvalidArgumentException( "Post status must be one of: publish, pending, draft, auto-draft, future, inherit, trash" );
		}

		$this->post_status = $post_status;
	}

	/**
	 * @return mixed
	 */
	public function getPostType() {
		return $this->post_type;
	}

	/**
	 * @param mixed $post_type
	 */
	public function setPostType( $post_type ) {
		if ( !is_string( $post_type ) && !is_null( $post_type ) ) {
			throw new \InvalidArgumentException( "Post type must be a string or null." );
		}
		// typical values 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type
		$this->post_type = $post_type;
	}

	/**
	 * Builds meta data based on a loaded WebPage, and HtmlImportSettings from the plugin.
	 *
	 * @param admin\HtmlImportSettings $globalSettings
	 * @param WebPage                  $webPage
	 * @param null                     $post_id
	 * @param null                     $parent_page_id
	 */
	public function buildConfig( admin\HtmlImportSettings $globalSettings, WebPage $webPage, $post_id = null, $parent_page_id = null ) {


		if ( !is_null( $post_id ) ) {
			$this->loadFromPostID( $post_id );
		}

		if ( is_null( $webPage ) ) {
			$file_as_xml_obj = null;
		} else {
			$file_as_xml_obj = XMLHelper::getXMLObjectFromString( $webPage->getContent() );
			if ( !is_null( $file_as_xml_obj ) ) {
				$this->setPostContent( $file_as_xml_obj->body->asXML() );
				$this->setPostTitle( $this->getTitleFromTag( $file_as_xml_obj ) );
			}
		}

		$this->setPostName( $this->getPostTitle() );
		$this->setPostStatus( 'publish' );
		$this->setPostType( 'page' );
		$this->setCommentStatus( 'closed' );
		$this->setPingStatus( 'closed' );

		$categoryIDs      = null;
		$overrideSettings = $webPage->getSettings();
		if ( !is_null( $overrideSettings ) ) {
			$categoryIDs = $overrideSettings->getCategoryIds();
		}
		// TODO: need to determine if index can override by providing no categories, and what that means
		if ( ( is_null( $overrideSettings ) ) || ( is_null( $categoryIDs ) ) || ( sizeof( $categoryIDs ) <= 0 ) ) {
			$category    = $globalSettings->getCategories()->getValuesArray();
			$categoryIDs = null;
			if ( !is_null( $category ) && is_array( $category ) ) {
				foreach ( $category as $index => $cat ) {
					$cat_id              = get_cat_ID( trim( $cat ) );
					$categoryIDs[$index] = intval( $cat_id );
				}
			}
		}


		$this->setPostCategory( $categoryIDs );

		// TODO need a way to track the date and time of the original file
		//if ( ! is_null($source_file)) {
		//	$this->setPostDate( date( 'Y-m-d H:i:s', filemtime( $source_file ) ) );
		//} else {
		$this->setPostDate( null );
		//}
		if ( !is_null( $parent_page_id ) ) {
			$this->setPostParent( $parent_page_id );
		}

		$order = $webPage->getOrderPosition();
		if ( isset ( $order ) ) {
			$this->setMenuOrder( $order );
		}
		$this->setPostAuthor( wp_get_current_user()->ID ); // TODO: should be in the settings object
		$this->setPageTemplate( $globalSettings->getTemplate()->getValue() );


	}

	/**
	 * Loads all of the meta data for a post given the post's ID.  Returns false of there is no post with that ID.
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function loadFromPostID( $post_id ) {
		$post_object = get_post( $post_id, 'OBJECT' );
		if ( is_null( $post_object ) ) {
			return false;
		} else {
			return $this->loadFromPostObject( $post_object );
		}
	}

	/**
	 * Loads all of the meta data give an WP_Post object.  If there is no object, returns null.
	 *
	 * @param \WP_Post $post
	 *
	 * @return bool
	 */
	public function loadFromPostObject( \WP_Post $post ) {
		if ( is_null( $post ) ) {
			return false;
		}

		$this->post_id     = $post->ID;
		$this->post_author = $post->post_author;
		$this->post_name   = $post->post_name;
		$this->post_type   = $post->post_type;
		$this->post_title  = $post->post_title;
		$this->post_date   = $post->post_date;
		// $post->post_date_gmt
		$this->post_content   = $post->content;
		$this->post_excerpt   = $post->post_excerpt;
		$this->comment_status = $post->comment_status;
		$this->ping_status    = $post->ping_status;
		$this->post_status    = $post->post_status;
		// $post->post_password
		$this->post_parent = $post->post_parent;
		// $post->post_modified
		// $post->post_modified_gmt
		// $post->comment_count
		$this->menu_order = $post->menu_order;

		return true;
	}

	/**
	 * Given a SimpleXMLElement, extracts the <TITLE> element and sets the title from that.
	 *
	 * @param \SimpleXMLElement $html_file
	 *
	 * @return string
	 */
	private function getTitleFromTag( \SimpleXMLElement $html_file ) {
		$title = '';
		foreach ( $html_file->head->title as $titleElement ) {
			$title = '' . $titleElement;
		}

		return $title;
	}

	/**
	 * @return mixed
	 */
	public function getPostTitle() {
		return $this->post_title;
	}

	/**
	 * @param mixed $post_title
	 */
	public function setPostTitle( $post_title ) {
		if ( !is_string( $post_title ) && !is_null( $post_title ) ) {
			throw new \InvalidArgumentException( "Post title must be a string or null." );
		}
		$this->post_title = htmlspecialchars( $post_title );
	}

} 