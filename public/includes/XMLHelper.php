<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-20
 * Time: 9:08 PM
 */

namespace html_import;


/**
 * Class XMLHelper
 * Assorted helper functions for handling XML files.
 * @package html_import
 */
class XMLHelper {
	/**
	 * Returns a SimpleXMLElement object given an XML contained in a string.
	 *
	 * @param $source_string
	 *
	 * @return \SimpleXMLElement
	 */
	public static function getXMLObjectFromString( $source_string ) {
		$doc                      = new \DOMDocument();
		$doc->strictErrorChecking = false;
		libxml_use_internal_errors( true ); // some ok HTML will generate errors, this masks them, pt 1/2
		$xmlValid = @$doc->loadHTML( $source_string/*, LIBXML_HTML_NOIMPLIED */ ); // server uses 5.3.28, this is added in 5.4
		if ( $xmlValid ) {
			libxml_clear_errors(); // some ok HTML will generate errors, this masks them, pt 2/2
			$file_as_xml_obj = simplexml_import_dom( $doc );

			return $file_as_xml_obj;
		} else {
			echo 'Empty string used for XML source<br>';

			return null;
		}
	}

	/**
	 * Returns a SimpleXMLElement object given a path to an XML file.
	 *
	 * @param $source_file
	 *
	 * @return \SimpleXMLElement
	 */
	public static function getXMLObjectFromFile( $source_file ) {
		$doc                      = new \DOMDocument();
		$doc->strictErrorChecking = false;
		libxml_use_internal_errors( true ); // some ok HTML will generate errors, this masks them, pt 1/2
		$doc->loadHTMLFile( $source_file/*, LIBXML_HTML_NOIMPLIED */ );// server uses 5.3.28, this is added in 5.4
		libxml_clear_errors(); // some ok HTML will generate errors, this masks them, pt 2/2
		$simple_xml = simplexml_import_dom( $doc );

		return $simple_xml;
	}

	// TODO: find an appropriate place for this

	/**
	 * Verifies that a file exists at the path provided, whether it's a URL or a local file.
	 *
	 * @param $xml_path
	 *
	 * @return bool|mixed
	 */
	public static function valid_xml_file( $xml_path ) {
		if ( filter_var( $xml_path, FILTER_VALIDATE_URL ) ) { // if URL
			return self::url_exists( $xml_path );
		} else {
			if ( file_exists( $xml_path ) ) {
				return true;
			}
		}

		return false;
	}

	// TODO: this should be beefed up to actually validate that it's an XML

	/**
	 * Check to determine if the provided URL resolves to a real file.  Returns true if so, false otherwise.
	 * source from: http://php.net/manual/en/function.file-exists.php
	 *
	 * @param $url
	 *
	 * @return mixed
	 */
	public static function url_exists( $url ) {
		// Version 4.x supported
		$handle = curl_init( $url );
		if ( false === $handle ) {
			return false;
		}
		curl_setopt( $handle, CURLOPT_HEADER, false );
		curl_setopt( $handle, CURLOPT_FAILONERROR, true );  // this works
		curl_setopt( $handle, CURLOPT_HTTPHEADER, Array( "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15" ) ); // request as if Firefox
		curl_setopt( $handle, CURLOPT_NOBODY, true );
		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, false );
		$connectable = curl_exec( $handle );
		curl_close( $handle );

		return $connectable;
	}

	/**
	 * Given an HTML file in a string, returns an array listing all of the URLs from the <a href>s.
	 *
	 * @param $contentAsXML
	 *
	 * @return array
	 */
	public static function getAllHRefsFromHTML( $contentAsXML ) {
		$all_links   = $contentAsXML->xpath( '//a[@href]' );
		$arrayOfURLs = [ ];
		if ( $all_links ) {
			foreach ( $all_links as $link ) {

				foreach ( $link->attributes() as $attribute => $value ) {
					$path = '' . $value;
					if ( 0 == strcasecmp( 'href', $attribute ) ) {
						$arrayOfURLs[] = $path;
					}
				}
			}
		}

		return $arrayOfURLs;
	}

	/**
	 * Given a string containing an XML file, it will replace all $old tags with $new tags.
	 * source from: http://stackoverflow.com/questions/8163298/how-do-i-change-xml-tag-names-with-php
	 *
	 * @param $xml
	 * @param $old
	 * @param $new
	 *
	 * @return mixed
	 */
	public static function renameTags( $xml, $old, $new ) {
		// TODO: safer to do this via the DOM, but cannot guarantee good XML, and may not be full HTML
		$count       = null;
		$returnValue = preg_replace( '/(<.*?\\/?)\\b' . $old . '\\b(.*?>)/is', '$1' . $new . '$2', $xml, - 1, $count );

		return $returnValue;
	}
} 