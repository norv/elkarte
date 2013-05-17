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
	protected $default_sub_action = 'about';

	public function __construct()
	{
		loadLanguage('About');
		loadTemplate('About');
		require_once(SUBSDIR . '/About.subs.php');
	}

	protected function getSubActions()
	{
		$sub_actions = array(
			'about' => 'action_about',
			'register-agreement' => 'action_registration_agreement',
			'privacy' => 'action_privacy_policy',
			'credits' => 'action_credits',
			'staff' => 'action_staff',
			'contact' => 'action_contact',
		);

		call_integration_hook('integrate_about_subactions', array(&$sub_actions));

		return $sub_actions;
	}

	/**
	 * Send the call off to the sub action
	 * @todo put this in a parent class
	 */
	public function callSubAction()
	{
		$sub_actions = $this->getSubActions();

		$sub_action = isset($_GET['sa']) && isset($sub_actions[$_GET['sa']]) ? $sub_actions[$_GET['sa']] : $sub_actions[$this->default_sub_action];

		return $this->$sub_action();
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
		global $modSettings;

		require_once(SUBSDIR . '/Membergroups.subs.php');
		$staff_groups = !empty($modSettings['staff_groups']) ? explode(',', $modSettings['staff_groups']) : 1;

		$result = getMembersInGroups($staff_groups);

		$members = $result['members'];
		$groups = $result['groups'];
	}

	public function action_contact()
	{
		
	}
}