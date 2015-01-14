<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:12 PM
 */

namespace html_import;

use html_import\indices\WebPage;

require_once( dirname( __FILE__ ) . '/ImportStage.php' );
require_once( dirname( __FILE__ ) . '/HTMLImportStages.php' );
require_once( dirname( __FILE__ ) . '/WPMetaConfigs.php' );
require_once( dirname( __FILE__ ) . '/indices/WebPage.php' );

/**
 * Class MediaImportStage
 * Stage that is responsible for scraping media files from a WebPage, uploading them into Wordpress and resetting the links in the webpage to point to the new location.
 * @package html_import
 */
class MediaImportStage extends ImportStage {
	protected function isValid( HTMLImportStages $stagesSettings ) {
		return $stagesSettings->doesImportMedia();
	}

	/**
	 * Performs the stage action of uploading media files and updating the WebPage accordingly.
	 *
	 * @param WebPage          $webPage
	 * @param HTMLImportStages $stagesSettings
	 * @param WPMetaConfigs    $meta
	 * @param null             $media_lookup
	 *
	 * @return null
	 */
	protected function performStage( WebPage $webPage, HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, &$media_lookup = null ) {

		$post_id = $meta->getPostId();
		$body    = $meta->getPostContent();
		if ( is_null( $body ) || strcmp( '', $body ) == 0 ) {
			echo '** the body for post ' . $post_id . ' was empty, no media to import.';

			return;
		}
		$media_table = Array();

		$file_as_xml_obj = XMLHelper::getXMLObjectFromString( $body );

		// import img srcs
		$all_imgs = $file_as_xml_obj->xpath( '//img[@src]' );
		if ( $all_imgs ) {
			foreach ( $all_imgs as $img ) {

				foreach ( $img->attributes() as $attribute => $value ) {
					$path = '' . $value;
					if ( 0 == strcasecmp( 'src', $attribute ) ) {
						// TODO: this is duplicated below, refactor it out
						if ( !preg_match( '/^[a-zA-Z].*:.*/', $path ) ) { // if it's local
							if ( ( !is_null( $media_lookup ) && ( !array_key_exists( $path, $media_table ) ) ) ) {

								$fullpath = $webPage->getFullPath( $path );
								if ( array_key_exists( $fullpath, $media_lookup ) ) {
									$attach_id = $media_lookup[$fullpath];
									require_once( ABSPATH . 'wp-admin/includes/image.php' );
									$attach_data = wp_get_attachment_metadata( $attach_id );
									wp_update_attachment_metadata( $attach_id, $attach_data );
									$media_table[$path] = $fullpath;
								} else {
									$filename = basename( $fullpath );
									$upload   = wp_upload_bits( $filename, null, $webPage->getLinkContents( $path ) );
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
						if ( !preg_match( '/^[a-zA-Z].*:.*/', $path ) ) {

							if ( preg_match( '/\.(png|bmp|jpg|jpeg|gif|pdf|doc|docx|mp3|ogg|wav)$/', strtolower( $path ) ) ) { // media png,bmp,jpg,jpeg,gif,pdf,doc,docx,mp3,ogg,wav
								if ( ( !is_null( $media_lookup ) ) ) {
									/*if ( $path[0] != '/' ) {
										$fullpath = realpath( dirname( $meta->getSourcePath() ) . '/' . $path );
									} else {
										$fullpath = $path;
									}*/
									$fullpath = $webPage->getFullPath( $path );
									if ( array_key_exists( $fullpath, $media_lookup ) ) {
										$attach_id = $media_lookup[$fullpath];
										require_once( ABSPATH . 'wp-admin/includes/image.php' );
										$attach_data = wp_get_attachment_metadata( $attach_id );
										wp_update_attachment_metadata( $attach_id, $attach_data );
										$media_table[$path] = $fullpath;

									} else {
										$filename = basename( $fullpath );

										$upload = wp_upload_bits( $filename, null, $webPage->getLinkContents( $path ) );
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
			$search_str = '/(\b[iI][mM][gG]\s*[^>]*\s+[sS][rR][cC]\s*=\s*")([\b\/\.]*' . preg_quote( $media_item, '/' ) . '\b)(")/';
			$body       = preg_replace( $search_str, '$1' . preg_quote( $media_url, '/' ) . '$3', $body ); // img src
			$body       = preg_replace( '/(\b[hH][rR][eE][fF]\s*=\s*")(\b' . preg_quote( $media_item, '/' ) . '\b)(")/', '$1' . preg_quote( $media_url, '/' ) . '$3', $body ); // a href
		}

		$meta->setPostContent( $body );
		echo '<li>Post ' . $post_id . ' updated with correct image links.</li>';

	}

} 