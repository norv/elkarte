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
 * A bunch of actions to deal with agreements, legal stuff, and general info
 *
 */

if (!defined('ELKARTE'))
	die('No access...');

class About_Controller
{
	public function __construct()
	{
		loadTemplate('About');
		require_once(SUBSDIR . '/About.subs.php');
	}

	public function action_about()
	{
		
	}

	public function action_registration_agreement()
	{
		global $context, $user_info;

		$context['agreement'] = getRegistrationAgreement($user_info['language']);
	}

	public function action_privacy_policy()
	{
		
	}

	public function action_credits()
	{
		loadCredits();
	}

	public function action_staff()
	{
		
	}

	public function action_contact()
	{
		
	}
}