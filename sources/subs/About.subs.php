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
 * This file contains functions that are specifically done by administrators.
 *
 */

if (!defined('ELKARTE'))
	die('No access...');

/**
 * Get the registration agreement
 * 
 * @param string $language
 * @return string
 */
function getRegistrationAgreement($language)
{
	// Have we got a localized one?
	if (file_exists(BOARDDIR . '/agreement.' . $language . '.txt'))
		return parse_bbc(file_get_contents(BOARDDIR . '/agreement.' . $language . '.txt'), true, 'agreement_' . $language);
	elseif (file_exists(BOARDDIR . '/agreement.txt'))
		return parse_bbc(file_get_contents(BOARDDIR . '/agreement.txt'), true, 'agreement');

	return '';
}

/**
 * It prepares credit and copyright information for the credits page or the admin page.
 * @todo two functions: split the data from loading templates and stuff, and remove the parameter.
 *  - a function for action=who;sa=credits, and another for the data needed for this and admin.
 *
 * @param bool $in_admin = false, if parameter is true the it will not load the sub-template nor the template file
 */
function loadCredits($in_admin = false)
{
	global $context, $smcFunc, $forum_copyright, $forum_version, $txt;

	// Don't blink. Don't even blink. Blink and you're dead.
	loadLanguage('Who');

	if ($in_admin)
	{
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['support_credits_title'],
			'help' => '',
			'description' => '',
		);
	}

	$context['credits'] = array(
		array(
			'pretext' => $txt['credits_intro'],
			'title' => $txt['credits_team'],
			'groups' => array(
				array(
					'title' => $txt['credits_groups_dev'],
					'members' => array(
						'Add this at some point',
					),
				),
			),
		),
	);

	// Give credit to any graphic library's, software library's, plugins etc
	$context['credits_software_graphics'] = array(
		'graphics' => array(
			'<a href="http://p.yusukekamiyamane.com/">Fugue Icons</a> | &copy; 2012 Yusuke Kamiyamane | These icons are licensed under a Creative Commons Attribution 3.0 License',
			'<a href="http://www.oxygen-icons.org/">Oxygen Icons</a> | These icons are licensed under <a href="http://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA 3.0</a>',
		),
		'fonts' => array(
			'<a href="http://openfontlibrary.org/en/font/dotrice">Dotrice</a> | &copy; 2010 <a href="http://hisdeedsaredust.com/">Paul Flo Williams</a> | This font is licensed under the SIL Open Font License, Version 1.1',
			'<a href="http://openfontlibrary.org/en/font/klaudia-and-berenika">Berenika</a> | &copy; 2011 wmk69 | This font is licensed under the SIL Open Font License, Version 1.1',
			'<a href="http://openfontlibrary.org/en/font/vshexagonica-v1-0-1">vSHexagonica</a> | &copy; 2012 T.B. von Strong | This font is licensed under the SIL Open Font License, Version 1.1',
			'<a href="http://openfontlibrary.org/en/font/press-start-2p">Press Start 2P</a> | &copy; 2012 Cody "CodeMan38" Boisclair | This font is licensed under the SIL Open Font License, Version 1.1',
			'<a href="http://openfontlibrary.org/en/font/architect-s-daughter">Architect\'s Daughter</a> | &copy; 2010 <a href="http://kimberlygeswein.com/">Kimberly Geswein</a> | This font is licensed under the SIL Open Font License, Version 1.1',
			'<a href="http://openfontlibrary.org/en/font/vds">VDS</a> | &copy; 2012 <a href="http://www.wix.com/artmake1/artmaker">artmaker</a> | This font is licensed under the SIL Open Font License, Version 1.1',
		),
		'software' => array(
			'<a href="http://www.simplemachines.org/">Simple Machines</a> | &copy; Simple Machines | Licensed under <a href="http://www.simplemachines.org/about/smf/license.php">The BSD License</a>',
			'<a href="http://jquery.org/">JQuery</a> | &copy; John Resig | Licensed under <a href="http://github.com/jquery/jquery/blob/master/MIT-LICENSE.txt">The MIT License (MIT)</a>',
			'<a href="http://cherne.net/brian/resources/jquery.hoverIntent.html">hoverIntent</a> | &copy; Brian Cherne | Licensed under <a href="http://en.wikipedia.org/wiki/MIT_License">The MIT License (MIT)</a>',
			'<a href="http://users.tpg.com.au/j_birch/plugins/superfish/">Superfish</a> | &copy; Joel Birch | Licensed under <a href="http://en.wikipedia.org/wiki/MIT_License">The MIT License (MIT)</a>',
			'<a href="http://www.sceditor.com/">SCEditor</a> | &copy; Sam Clarke | Licensed under <a href="http://en.wikipedia.org/wiki/MIT_License">The MIT License (MIT)</a>',
			'<a href="http://wayfarerweb.com/jquery/plugins/animadrag/">animaDrag</a> | &copy; Abel Mohler | Licensed under <a href="http://en.wikipedia.org/wiki/MIT_License">The MIT License (MIT)</a>',
		),
	);

	// Support for mods that use the <credits> tag via the package manager
	$context['credits_modifications'] = array();
	if (($mods = cache_get_data('mods_credits', 86400)) === null)
	{
		$mods = array();
		$request = $smcFunc['db_query']('substring', '
			SELECT version, name, credits
			FROM {db_prefix}log_packages
			WHERE install_state = {int:installed_mods}
				AND credits != {string:empty}
				AND SUBSTRING(filename, 1, 9) != {string:old_patch_name}
				AND SUBSTRING(filename, 1, 9) != {string:patch_name}',
			array(
				'installed_mods' => 1,
				'old_patch_name' => 'smf_patch',
				'patch_name' => 'elk_patch',
				'empty' => '',
			)
		);

		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$credit_info = unserialize($row['credits']);

			$copyright = empty($credit_info['copyright']) ? '' : $txt['credits_copyright'] . ' &copy; ' . $smcFunc['htmlspecialchars']($credit_info['copyright']);
			$license = empty($credit_info['license']) ? '' : $txt['credits_license'] . ': ' . $smcFunc['htmlspecialchars']($credit_info['license']);
			$version = $txt['credits_version'] . ' ' . $row['version'];
			$title = (empty($credit_info['title']) ? $row['name'] : $smcFunc['htmlspecialchars']($credit_info['title'])) . ': ' . $version;

			// build this one out and stash it away
			$mod_name = empty($credit_info['url']) ? $title : '<a href="' . $credit_info['url'] . '">' . $title . '</a>';
			$mods[] = $mod_name . (!empty($license) ? ' | ' . $license  : '') . (!empty($copyright) ? ' | ' . $copyright  : '');
		}
		cache_put_data('mods_credits', $mods, 86400);
	}
	$context['credits_modifications'] = $mods;

	$context['copyrights'] = array(
		'elkarte' => sprintf($forum_copyright, ucfirst(strtolower($forum_version))),
		/* Modification Authors:  You may add a copyright statement to this array for your mods.
			Copyright statements should be in the form of a value only without a array key.  I.E.:
				'Some Mod by Thantos &copy; 2010',
				$txt['some_mod_copyright'],
			But its better and simpler to just your package.xml and add in the <credits> <license> tags
		*/
		'mods' => array(
		),
	);

	// Support for those that want to use a hook as well
	call_integration_hook('integrate_credits');

	if (!$in_admin)
	{
		loadTemplate('Who');
		$context['sub_template'] = 'credits';
		$context['robot_no_index'] = true;
		$context['page_title'] = $txt['credits'];
	}
}