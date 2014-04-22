<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:12 PM
 */

namespace html_import;


class UpdateLinksImportStage extends ImportStage {
	protected function isValid(HTMLImportStages $stagesSettings) {
		return $stagesSettings->doesUpdateLinks();
	}

	protected function performStage(HTMLImportStages $stagesSettings, WPMetaConfigs &$meta, $body, &$html_post_lookup = null) {

		$bodyXML = XMLHelper::getXMLObjectFromString($body);
		$filepath = dirname($meta->getSourcePath());

		$link_table = Array();
		$all_links  = $bodyXML->xpath( '//a[@href]' );
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

		$meta->setPostContent($body);

	}

} 