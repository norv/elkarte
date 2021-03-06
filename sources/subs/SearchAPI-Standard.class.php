<?php

/**
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.0 Alpha
 *
 */

if (!defined('ELK'))
	die('No access...');

/**
 * SearchAPI-Standard.class.php, Standard non full index, non custom index search
 */
class Standard_Search
{
	/**
	 * This is the last version of ElkArte that this was tested on, to protect against API changes.
	 *
	 * @var type
	 */
	public $version_compatible = 'ElkArte 1.0 Alpha';

	/**
	 * This won't work with versions of ElkArte less than this.
	 *
	 * @var type
	 */
	public $min_elk_version = 'ElkArte 1.0 Alpha';

	/**
	 * Standard search is supported by default.
	 * @var type
	 */
	public $is_supported = true;

	/**
	 * Method to check whether the method can be performed by the API.
	 *
	 * @param type $methodName
	 * @param type $query_params
	 * @return boolean
	 */
	public function supportsMethod($methodName, $query_params = null)
	{
		// Always fall back to the standard search method.
		return false;
	}
}