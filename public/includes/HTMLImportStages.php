<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 14-04-19
 * Time: 10:09 PM
 */

namespace html_import;

/**
 * Class HTMLImportStages
 * Defines which import stages are available to be used.
 * @package html_import
 */
class HTMLImportStages {
	private $importHtml = true;
	private $importMedia = true;
	private $updateLinks = true;
	private $configureTemplate = true;
	private $addGDNHeaderAndFooter = false;

	/**
	 *
	 */
	function __construct() {
	}

	/**
	 * @param boolean $setTemplate
	 */
	public function setConfigureTemplate( $setTemplate ) {
		$this->configureTemplate = $setTemplate;
	}

	/**
	 * @return boolean
	 */
	public function doesConfigureTemplate() {
		return $this->configureTemplate;
	}

	/**
	 * @param boolean $addGDNHeaderAndFooter
	 */
	public function setAddGDNHeaderAndFooter( $addGDNHeaderAndFooter ) {
		$this->addGDNHeaderAndFooter = $addGDNHeaderAndFooter;
	}

	/**
	 * @return boolean
	 */
	public function doesAddGDNHeaderAndFooter() {
		return $this->addGDNHeaderAndFooter;
	}

	/**
	 * @param boolean $importHtml
	 */
	public function setImportHtml( $importHtml ) {
		$this->importHtml = $importHtml;
	}

	/**
	 * @return boolean
	 */
	public function doesImportHtml() {
		return $this->importHtml;
	}

	/**
	 * @param boolean $importMedia
	 */
	public function setImportMedia( $importMedia ) {
		$this->importMedia = $importMedia;
	}

	/**
	 * @return boolean
	 */
	public function doesImportMedia() {
		return $this->importMedia;
	}

	/**
	 * @param boolean $updateLinks
	 */
	public function setUpdateLinks( $updateLinks ) {
		$this->updateLinks = $updateLinks;
	}

	/**
	 * @return boolean
	 */
	public function doesUpdateLinks() {
		return $this->updateLinks;
	}

} 