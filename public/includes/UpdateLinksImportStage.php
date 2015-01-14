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
 * Class UpdateLinksImportStage
 * Stage that will update all the local links in a webpage to reference the appropriate page in Wordpres.
 * @package html_import
 */
class UpdateLinksImportStage extends ImportStage {
	protected function isValid( HTMLImportStages $stagesSettings ) {
		return $stagesSettings->doesUpdateLinks();
	}

	/**
	 * Function that performs the action of updating the local links on the webpage.
	 *
	 * @param WebPage          $webPage
	 * @param HTMLImportStages $stagesSettings
	 * @param WPMetaConfigs    $meta
	 * @param null             $html_post_lookup
	 *
	 * @return null
	 */
	protected function performStage( WebPage $webPage, HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, &$html_post_lookup = null ) {
		$body = $meta->getPostContent();
		if ( !is_null( $html_post_lookup ) ) {
			$bodyXML  = XMLHelper::getXMLObjectFromString( $body );
			$filepath = dirname( $meta->getSourcePath() );

			$link_table = Array();
			// get a list of all the links in the page and iterate through them
			$all_links = $bodyXML->xpath( '//a[@href]' );
			// TODO: encapsulate this in a function use XMLHelper::getAllHRefsFromHTML as a start
			if ( $all_links ) {
				foreach ( $all_links as $link ) {
					// iterate the link's attributes to find the HREF value
					foreach ( $link->attributes() as $attribute => $value ) {
						$path = '' . $value;
						if ( 0 == strcasecmp( 'href', $attribute ) ) { // TODO: handle foo.html#rar
							if ( !preg_match( '/^[a-zA-Z].*:.*/', $path ) ) { // TODO: need to handle foo.html without handling http://...
								// only handle files that end in .html or .htm
								if ( preg_match( '/\.([hH][tT][mM][lL]?)$/', $path ) ) {
									// if the file the path links to has been imported already, then it will exist in the lookup and we can update the link, otherwise leave the link alone
									$fullpath = $webPage->getFullPath( $path );
									if ( $fullpath ) {
										if ( array_key_exists( $fullpath, $html_post_lookup ) ) {
											$link_table[$path] = $fullpath;
										}
									} else {
										echo '<span>***could not update link ' . $path . '</span><br>';
									}
								}
							}
						}
					}
				}
			}

			// after building a list of all the links to update and what to update them to, we can do a change in the html file as a whole to catch all references
			foreach ( $link_table as $link => $full_link ) {
				$post_id   = $html_post_lookup[$full_link];
				$post_link = get_permalink( $post_id );
				echo 'Updating ' . $link . ' with ' . $post_link . '<br>';
				$search_str = '/(\b[hH][rR][eE][fF]\s*=\s*")([\b\.\/]*' . preg_quote( $link, '/' ) . '\b)(")/';
				$body       = preg_replace( $search_str, '$1' . preg_quote( $post_link, '/' ) . '$3', $body );
			}

			$meta->setPostContent( $body );

		}
	}

} 