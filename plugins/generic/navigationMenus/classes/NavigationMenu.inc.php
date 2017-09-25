<?php

/**
 * @file classes/NavigationMenu.inc.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.navigationMenus
 * @class NavigationMenu
 * Data object representing a NavigationMenu.
 */

class NavigationMenu extends DataObject {
	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}

	//
	// Get/set methods
	//

	/**
	 * Get context ID
	 * @return string
	 */
	function getContextId(){
		return $this->getData('contextId');
	}

	/**
	 * Set context ID
	 * @param $contextId int
	 */
	function setContextId($contextId) {
		return $this->setData('contextId', $contextId);
	}


	/**
	 * Set NavigationMenu title
	 * @param string string
	 * @param locale
	 */
	function setTitle($title, $locale) {
		return $this->setData('title', $title, $locale);
	}

	/**
	 * Get NavigationMenu title
	 * @param locale
	 * @return string
	 */
	function getTitle($locale) {
		return $this->getData('title', $locale);
	}

	/**
	 * Get Localized NavigationMenu title
	 * @return string
	 */
	function getLocalizedTitle() {
		return $this->getLocalizedData('title');
	}

	/**
	 * Set page content
	 * @param $content string
	 * @param locale
	 */
	function setContent($content, $locale) {
		return $this->setData('content', $content, $locale);
	}

	/**
	 * Get page content
	 * @param locale
	 * @return string
	 */
	function getContent($locale) {
		return $this->getData('content', $locale);
	}

	/**
	 * Get "localized" content
	 * @return string
	 */
	function getLocalizedContent() {
		return $this->getLocalizedData('content');
	}

	/**
	 * Get NavigationMenu path string
	 * @return string
	 */
	function getPath() {
		return $this->getData('path');
	}

	 /**
	  * Set page path string
	  * @param $path string
	  */
	function setPath($path) {
		return $this->setData('path', $path);
	}
}

?>
