<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-20
 * Time: 9:08 PM
 */

namespace html_import;


class XMLHelper {
	public static function getXMLObjectFromString( $source_string) {
		$doc                      = new \DOMDocument();
		$doc->strictErrorChecking = false;
		libxml_use_internal_errors( true ); // some ok HTML will generate errors, this masks them, pt 1/2
		$doc->loadHTML( $source_string/*, LIBXML_HTML_NOIMPLIED */); // TODO server uses 5.3.28, this is added in 5.4
		libxml_clear_errors(); // some ok HTML will generate errors, this masks them, pt 2/2
		$file_as_xml_obj = simplexml_import_dom( $doc );
		return $file_as_xml_obj;
	}

	public static function getXMLObjectFromFile( $source_file ) {
		$doc                      = new \DOMDocument();
		$doc->strictErrorChecking = false;
		libxml_use_internal_errors( true ); // some ok HTML will generate errors, this masks them, pt 1/2
		$doc->loadHTMLFile( $source_file/*, LIBXML_HTML_NOIMPLIED */);// TODO server uses 5.3.28, this is added in 5.4
		libxml_clear_errors(); // some ok HTML will generate errors, this masks them, pt 2/2
		$simple_xml = simplexml_import_dom( $doc );

		return $simple_xml;
	}

	// TODO: find an appropriate place for this
	// lifted from: http://php.net/manual/en/function.file-exists.php
	public static function url_exists($url) {
		// Version 4.x supported
		$handle   = curl_init($url);
		if (false === $handle)
		{
			return false;
		}
		curl_setopt($handle, CURLOPT_HEADER, false);
		curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
		curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox
		curl_setopt($handle, CURLOPT_NOBODY, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
		$connectable = curl_exec($handle);
		curl_close($handle);
		return $connectable;
	}

	// TODO: this should be beefed up to actually validate that it's an XML
	public static function valid_xml_file( $xml_path ) {
		if (filter_var($xml_path, FILTER_VALIDATE_URL)) { // if URL
			return self::url_exists( $xml_path );
		} else if ( file_exists( $xml_path ) ) {
			return true;
		}

		return false;
	}

	/**
	 * source from: http://stackoverflow.com/questions/8163298/how-do-i-change-xml-tag-names-with-php
	 * @param $xml
	 * @param $old
	 * @param $new
	 *
	 * @return mixed
	 */
	public static function renameTags($xml, $old, $new)
	{
		// TODO: safer to do this via the DOM, but cannot guarantee good XML, and may not be full HTML
		$count = null;
		$returnValue = preg_replace('/(<.*?\\/?)\\b'.$old.'\\b(.*?>)/is', '$1'.$new.'$2', $xml, -1, $count);
		return $returnValue;
	}
} 