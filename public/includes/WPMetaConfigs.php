<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 5:42 PM
 */

namespace html_import;


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

	/**
	 * @param mixed $page_template
	 */
	public function setPageTemplate( $page_template ) {
		$this->page_template = $page_template;
	}

	/**
	 * @return mixed
	 */
	public function getPageTemplate() {
		return $this->page_template;
	}

	/**
	 * @param mixed $post_content
	 */
	public function setPostContent( $post_content ) {
		$this->post_content = $post_content;
	}

	/**
	 * @return mixed
	 */
	public function getPostContent() {
		return $this->post_content;
	}

	/**
	 *
	 */
	function __construct() {

	}

	/**
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function loadFromPostID($post_id) {
		$post_object = get_post($post_id, 'OBJECT');
		if (is_null($post_object)) {
			return false;
		} else {
			return $this->loadFromPostObject($post_object);
		}
	}

	/**
	 * @param \WP_Post $post
	 *
	 * @return bool
	 */
	public function loadFromPostObject(\WP_Post $post) {
		if (is_null($post)) {
			return false;
		}

		$this->post_id = $post->ID;
		$this->post_author = $post->post_author;
		$this->post_name = $post->post_name;
		$this->post_type = $post->post_type;
		$this->post_title = $post->post_title;
		$this->post_date = $post->post_date;
		// $post->post_date_gmt
		$this->post_content = $post->content;
		$this->post_excerpt = $post->post_excerpt;
		$this->comment_status = $post->comment_status;
		$this->ping_status = $post->ping_status;
		// $post->post_password
		$this->post_parent = $post->post_parent;
		// $post->post_modified
		// $post->post_modified_gmt
		// $post->comment_count
		$this->menu_order = $post->menu_order;

		return true;
	}

	/**
	 * @return array
	 */
	public function getPostArray() {
		$post_array = Array(

			'ID' => $this->post_id,
			'post_author' => $this->post_author,
			'post_name' => $this->post_name,
			'post_type' => $this->post_type,
			'post_title' => $this->post_title,
			'post_date' => $this->post_date,
			// 'post_date_gmt' $post->post_date_gmt
			'post_content' => $this->post_content,
			'post_excerpt' => $this->post_excerpt,
			'comment_status' => $this->comment_status,
			'ping_status' => $this->ping_status,
			// $post->post_password
			'post_parent' => $this->post_parent,
			// $post->post_modified
			// 'post_date_gmt' $post->post_modified_gmt
			// $post->comment_count
			'menu_order' => $this->menu_order,
			'post_category' => $this->post_category,
			'page_template' => $this->page_template

		);


		return $post_array;
	}

	public function updateWPPost() {
		// TODO: handle WP_Error object if set to true.
		$result = wp_insert_post($this->getPostArray(), true);
		return $result;
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
	public function getCommentStatus() {
		return $this->comment_status;
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
	public function getMenuOrder() {
		return $this->menu_order;
	}

	/**
	 * @param mixed $ping_status
	 */
	public function setPingStatus( $ping_status ) {
		// TODO: 'closed', 'open'
		$this->ping_status = $ping_status;
	}

	/**
	 * @return mixed
	 */
	public function getPingStatus() {
		return $this->ping_status;
	}

	/**
	 * @param mixed $post_author
	 */
	public function setPostAuthor( $post_author ) {
		//TODO: author ID
		$this->post_author = $post_author;
	}

	/**
	 * @return mixed
	 */
	public function getPostAuthor() {
		return $this->post_author;
	}

	/**
	 * @param mixed $post_category
	 */
	public function setPostCategory( $post_category ) {
		// TODO: array
		$this->post_category = $post_category;
	}

	/**
	 * @return mixed
	 */
	public function getPostCategory() {
		return $this->post_category;
	}

	/**
	 * @param mixed $post_date
	 */
	public function setPostDate( $post_date ) {
		// TODO: [ Y-m-d H:i:s ]
		$this->post_date = $post_date;
	}

	/**
	 * @return mixed
	 */
	public function getPostDate() {
		return $this->post_date;
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
	public function getPostExcerpt() {
		return $this->post_excerpt;
	}

	/**
	 * @param mixed $post_id
	 */
	public function setPostId( $post_id ) {
		// TODO: ID or null
		$this->post_id = $post_id;
	}

	/**
	 * @return mixed
	 */
	public function getPostId() {
		return $this->post_id;
	}

	/**
	 * @param mixed $post_name
	 */
	public function setPostName( $post_name ) {
		$this->post_name = sanitize_title_with_dashes($post_name);
	}

	/**
	 * @return mixed
	 */
	public function getPostName() {
		return $this->post_name;
	}

	/**
	 * @param mixed $post_parent
	 */
	public function setPostParent( $post_parent ) {
		// TODO: post ID
		$this->post_parent = $post_parent;
	}

	/**
	 * @return mixed
	 */
	public function getPostParent() {
		return $this->post_parent;
	}

	/**
	 * @param mixed $post_status
	 */
	public function setPostStatus( $post_status ) {
		// TODO: draft, publish, pending, future, private, customer registered
		$this->post_status = $post_status;
	}

	/**
	 * @return mixed
	 */
	public function getPostStatus() {
		return $this->post_status;
	}

	/**
	 * @param mixed $post_title
	 */
	public function setPostTitle( $post_title ) {
		$this->post_title = htmlspecialchars($post_title);
	}

	/**
	 * @return mixed
	 */
	public function getPostTitle() {
		return $this->post_title;
	}

	/**
	 * @param mixed $post_type
	 */
	public function setPostType( $post_type ) {
		// TODO: 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type
		$this->post_type = $post_type;
	}

	/**
	 * @return mixed
	 */
	public function getPostType() {
		return $this->post_type;
	}



} 