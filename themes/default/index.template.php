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
 * This template is, perhaps, the most important template in the theme. It
 * contains the main template layer that displays the header and footer of
 * the forum, namely with main_above and main_below. It also contains the
 * menu sub template, which appropriately displays the menu; the init sub
 * template, which is there to set the theme up; (init can be missing.) and
 * the linktree sub template, which sorts out the link tree.
 *
 * The init sub template should load any data and set any hardcoded options.
 *
 * The main_above sub template is what is shown above the main content, and
 * should contain anything that should be shown up there.
 *
 * The main_below sub template, conversely, is shown after the main content.
 * It should probably contain the copyright statement and some other things.
 *
 * The linktree sub template should display the link tree, using the data
 * in the $context['linktree'] variable.
 *
 * The menu sub template should display all the relevant buttons the user
 * wants and or needs.
 *
 */

/**
 * Initialize the template... mainly little settings.
 */
function template_init()
{
	global $settings;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	// The version this template/theme is for. This should probably be the version of the forum it was created for.
	$settings['theme_version'] = '1.0';

	// Use plain buttons - as opposed to text buttons?
	$settings['use_buttons'] = true;

	// Show sticky and lock status separate from topic icons?
	$settings['separate_sticky_lock'] = true;

	// Does this theme use the strict doctype?
	$settings['strict_doctype'] = false;

	// Set the following variable to true if this theme requires the optional theme strings file to be loaded.
	$settings['require_theme_strings'] = false;

	// This is used for the color variants.
	$settings['theme_variants'] = array('light', 'dark', 'basic');

	// If the following variable is set to true, the avatar of the last poster will be displayed on the board index and message index.
	$settings['avatars_on_indexes'] = false;

	// This is used in the main menu, to create a number next to the title of the menu, to indicate unread messages, moderation reports, etc.
	$settings['menu_numeric_notice'] = ' <span class="pm_indicator">%1$s</span>';

	// This slightly more complex array, instead, will deal with page indexes as frequently requested by Ant :P
	// Oh no you don't. :D This slightly less complex array now has cleaner markup. :P
	// @todo - God it's still ugly though. Can't we just have links where we need them, without all those spans?
	// How do we get anchors only, where they will work? Spans and strong only where necessary?
	$settings['page_index_template'] = array(
		'base_link' => '<a class="navPages" href="{base_link}" role="menuitem">%2$s</a>',
		'previous_page' => '<span class="previous_page" role="menuitem">{prev_txt}</span>',
		'current_page' => '<strong class="current_page" role="menuitem">%1$s</strong>',
		'next_page' => '<span class="next_page" role="menuitem">{next_txt}</span>',
		'expand_pages' => '<span class="expand_pages" role="menuitem" onclick="{onclick_handler}" onmouseover="this.style.cursor=\'pointer\';"><strong> ... </strong></span>',
		'all' => '<span class="all_pages" role="menuitem">{all_txt}</span>',
	);
}

/**
 * The main sub template above the content.
 */
function template_html_above()
{
	global $context, $settings, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>
	<title>', $context['page_title_html_safe'], '</title>';

	// Tell IE to render the page in standards not compatibility mode. really for ie >= 8
	// Note if this is not in the first 4k, its ignored, thats why its here
	if (isBrowser('ie'))
		echo '
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />';

	// load in any css from mods or themes so they can overwrite if wanted
	template_css();

	// Save some database hits, if a width for multiple wrappers is set in admin.
	if (!empty($settings['forum_width']))
		echo '
	<style>
		.wrapper {width: ', $settings['forum_width'], ';}
	</style>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="contents" href="', $scripturl, '" />', ($context['allow_search'] ? '
	<link rel="search" href="' . $scripturl . '?action=search" />' : '');

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss2;action=.xml" />
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['atom'], '" href="', $scripturl, '?type=atom;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['links']['next']))
		echo '<link rel="next" href="', $context['links']['next'], '" />';
	elseif (!empty($context['current_topic']))
		echo '<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	if (!empty($context['links']['prev']))
		echo '<link rel="prev" href="', $context['links']['prev'], '" />';
	elseif (!empty($context['current_topic']))
		echo '<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// load in any javascript files from mods and themes
	template_javascript();

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	// A little help for our friends
	echo '
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->';

	echo '
</head>
<body id="', $context['browser_body_id'], '" class="action_', !empty($context['current_action']) ? htmlspecialchars($context['current_action']) : (!empty($context['current_board']) ?
		'messageindex' : (!empty($context['current_topic']) ? 'display' : 'home')), !empty($context['current_board']) ? ' board_' . htmlspecialchars($context['current_board']) : '', '">';
}

/**
 * Section above the main contents of the page, after opening the body tag
 */
function template_body_above()
{
	global $context, $settings, $scripturl, $txt, $modSettings;

	// Go to top/bottom of page links,and skipnav link for a11y. @todo - Skipnav should have proper text string.
	echo '
	<a id="top" href="#skipnav">Skip navigation</a>
	<a href="#top" id="gotop" title="', $txt['go_up'], '">&#8593;</a>
	<a href="#bot" id="gobottom" title="', $txt['go_down'], '">&#8595;</a>';

	// Skip nav link.
	echo '
	<div id="top_section">
		<div class="wrapper">
			<p id="top_section_notice">';

	// If the user is logged in, display the time, or a maintenance warning for admins.
	// @todo - TBH I always intended the time/date to be more or less a place holder for more important things.
	// The maintenance mode warning for admins is an obvious one, but this could also be used for moderation notifications.
	// I also assumed this would be an obvious place for sites to put a string of icons to link to their FB, Twitter, etc.
	// This could still be done via conditional, so that administration and moderation notices were still active when applicable.
	if ($context['user']['is_logged'])
	{
		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
				<span class="notice">', $txt['maintain_mode_on'], '</span>';
		else
			echo $context['current_time'];
	}
	// Otherwise they're a guest. Ask them to either register or login.
	else
		echo sprintf($txt[$context['can_register'] ? 'welcome_guest_register' : 'welcome_guest'], $txt['guest_title'], $scripturl . '?action=login');

	echo '
			</p>';

	if ($context['allow_search'])
	{
		echo '
			<form id="search_form" action="', $scripturl, '?action=search2" method="post" accept-charset="UTF-8">
				<input type="text" name="search" value="" class="input_text" placeholder="', $txt['search'], '" />';

		// Using the quick search dropdown?
		if (!empty($modSettings['search_dropdown']))
		{
			$selected = !empty($context['current_topic']) ? 'current_topic' : (!empty($context['current_board']) ? 'current_board' : 'all');

			echo '
				<select name="search_selection">
					<option value="all"', ($selected == 'all' ? ' selected="selected"' : ''), '>', $txt['search_entireforum'], ' </option>';

			// Can't limit it to a specific topic if we are not in one
			if (!empty($context['current_topic']))
				echo '
				<option value="topic"', ($selected == 'current_topic' ? ' selected="selected"' : ''), '>', $txt['search_thistopic'], '</option>';

			// Can't limit it to a specific board if we are not in one
			if (!empty($context['current_board']))
				echo '
					<option value="board"', ($selected == 'current_board' ? ' selected="selected"' : ''), '>', $txt['search_thisbrd'], '</option>';

			if (!empty($context['additional_dropdown_search']))
				foreach ($context['additional_dropdown_search'] as $name => $engine)
					echo '
					<option value="', $name, '">', $engine['name'], '</option>';

			echo '
					<option value="members"', ($selected == 'members' ? ' selected="selected"' : ''), '>', $txt['search_members'], ' </option>
				</select>';
		}

		// Search within current topic?
		if (!empty($context['current_topic']))
			echo '
				<input type="hidden" name="', (!empty($modSettings['search_dropdown']) ? 'sd_topic' : 'topic'), '" value="', $context['current_topic'], '" />';
		// If we're on a certain board, limit it to this board ;).
		elseif (!empty($context['current_board']))
			echo '
				<input type="hidden" name="', (!empty($modSettings['search_dropdown']) ? 'sd_brd[' : 'brd['), $context['current_board'], ']"', ' value="', $context['current_board'], '" />';

		echo '
				<input type="submit" name="search2" value="', $txt['search'], '" class="button_submit', (!empty($modSettings['search_dropdown'])) ? ' with_select':'', '" />
				<input type="hidden" name="advanced" value="0" />
			</form>';
	}

	echo '
		</div>
		<div id="header" class="wrapper"', empty($context['minmax_preferences']['upshrink']) ? '' : ' style="display: none;" aria-hidden="true"', '>
			<h1 class="forumtitle">
				<a href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? $context['forum_name'] : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name'] . '" />', '</a>
			</h1>';

	echo '
			', empty($settings['site_slogan']) ? '<img id="logo" src="' . $settings['images_url'] . (!empty($context['theme_variant']) ? '/'. $context['theme_variant'] . '/logo_elk.png' : '/logo_elk.png' ) . '" alt="ElkArte Community" title="ElkArte Community" />' : '<div id="siteslogan" class="floatright">' . $settings['site_slogan'] . '</div>', '';

	// Show the menu here, according to the menu sub template.
	echo'
		</div>';

	// WAI-ARIA a11y tweaks have been applied here.
	echo '
		<div id="menu_nav" class="wrapper" role="navigation">
			', template_menu(), '
		</div>
	</div>
	<div id="wrapper" class="wrapper">
		<div id="upper_section"', empty($context['minmax_preferences']['upshrink']) ? '' : ' style="display: none;" aria-hidden="true"', '>
			<div class="user">';

	// Show log in form to guests.
	if (!empty($context['show_login_bar']))
	{
		echo '
				<script src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<form id="guest_form" action="', $scripturl, '?action=login2;quicklogin" method="post" accept-charset="UTF-8" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\', \'' . (!empty($context['login_token']) ? $context['login_token'] : '') . '\');"' : '', '>
					<input type="text" name="user" size="10" class="input_text" placeholder="', $txt['username'], '" />
					<input type="password" name="passwrd" size="10" class="input_password" placeholder="', $txt['password'], '" />
					<select name="cookielength">
						<option value="60">', $txt['one_hour'], '</option>
						<option value="1440">', $txt['one_day'], '</option>
						<option value="10080">', $txt['one_week'], '</option>
						<option value="43200">', $txt['one_month'], '</option>
						<option value="-1" selected="selected">', $txt['forever'], '</option>
					</select>
					<input type="submit" value="', $txt['login'], '" class="button_submit" />
					<div>', $txt['quick_login_dec'], '</div>';

		if (!empty($modSettings['enableOpenID']))
			echo '
					<br /><input type="text" name="openid_identifier" size="25" class="input_text openid_login" />';

		echo '
					<input type="hidden" name="hash_passwrd" value="" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="hidden" name="', $context['login_token_var'], '" value="', $context['login_token'], '" />
				</form>';
	}

	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		if (!empty($context['user']['avatar']))
			echo '
				<a href="', $scripturl, '?action=profile" class="avatar">', $context['user']['avatar']['image'], '</a>';
		echo '
				<ul>
					<li class="greeting">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span></li>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
					<li>', $context['unapproved_members_text'], '</li>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
					<li><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></li>';

		echo '
				</ul>';
	}

	echo'
			</div>';

	// Display either news fader and random news lines (not both). These now run most of the same mark up and CSS. Less complication = happier n00bz. :)
	// News fader is nixed when upper section is collapsed, just to save running the javascript all the time when it is not wanted.
	// Requires page refresh when upper section is expanded, to show the fader again. I think this is acceptable, but am open to suggestions.
	if(!empty($context['random_news_line']))
	{
		if (!empty($settings['show_newsfader']) && empty($context['minmax_preferences']['upshrink']))
		{
			echo '
			<div id="news">
				<h2>', $txt['news'], '</h2>
				', template_news_fader(), '
			</div>';
		}
		elseif (empty($settings['show_newsfader']) && !empty($settings['enable_news']))
		{
			echo '
			<div id="news">
				<h2>', $txt['news'], '</h2>
				<p id="news_line">', $context['random_news_line'], '</p>
			</div>';
		}
	}

	echo '
		</div>';

	// Show the navigation tree.
		theme_linktree();

	// The main content should go here. @todo - Skip nav link.
	echo '
		<div id="main_content_section"><a id="skipnav"></a>';
}

/**
 * Section down the page, before closing body
 */
function template_body_below()
{
	global $context, $settings, $scripturl, $txt, $modSettings;

	echo '
		</div>
	</div>';

	// Show the XHTML and RSS links, as well as the copyright.
	// Footer is full-width. Wrapper inside automatically matches admin width setting.
	echo '
	<div id="footer_section"><a id="bot"></a>
		<div class="wrapper">
			<ul>
				<li class="copyright">', theme_copyright(), '
				</li>
				<li><a id="button_xhtml" href="http://validator.w3.org/check?uri=referer" target="_blank" class="new_win" title="', $txt['valid_xhtml'], '"><span>', $txt['xhtml'], '</span></a></li>
				', !empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']) ? '<li><a id="button_rss" href="' . $scripturl . '?action=.xml;type=rss;limit=' . (!empty($modSettings['xmlnews_limit']) ? $modSettings['xmlnews_limit'] : 5) . '" class="new_win"><span>' . $txt['rss'] . '</span></a></li>' : '',
				(!empty($modSettings['badbehavior_enabled']) && !empty($modSettings['badbehavior_display_stats'])) ? '<li class="copyright">' . bb2_insert_stats() . '</li>' : '', '
			</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
			<p>', sprintf($txt['page_created_full'], $context['load_time'], $context['load_queries']), '</p>';

	echo '
		</div>
	</div>';
}

/**
 * Section down the page, at closing html tag
 */
function template_html_below()
{
	// load in any javascript that could be deferred to the end of the page
	template_javascript(true);

	echo '
</body>
</html>';
}

/**
 * Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
 * @param bool $force_show = false
 */
function theme_linktree($force_show = false)
{
	global $context, $settings, $shown_linktree, $scripturl, $txt;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	// @todo - Look at changing markup here slightly. Need to incorporate relevant aria roles.
	echo '
				<ul class="navigate_section">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	// Added a linktree class to make targeting dividers easy.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
					<li class="linktree', ($link_num == count($context['linktree']) - 1) ? '_last' : '', '">';

		// Dividers moved to pseudo-elements in CSS. @todo- rtl.css
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '">' . $tree['name'] . '</a>' : $tree['name'];

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		echo '
					</li>';
	}

	echo '
				</ul>';

	$shown_linktree = true;
}

/**
 * Show the menu up top. Something like [home] [help] [profile] [logout]...
 */
function template_menu()
{
	global $context, $settings, $txt;

	// WAI-ARIA a11y tweaks have been applied here.
	echo '
					<ul id="main_menu">';

	// The upshrink image, right-floated.
	echo '
						<li id="collapse_button" class="listlevel1 floatright">
							<a class="linklevel1"><img id="upshrink" src="', $settings['images_url'], '/upshrink.png" alt="*" title="', $txt['upshrink_description'], '" style="display: none;" />&nbsp;</a>
						</li>';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
						<li id="button_', $act, '" class="listlevel1', !empty($button['sub_buttons']) ? ' subsections" aria-haspopup="true"' : '"', '>
							<a class="linklevel1', !empty($button['active_button']) ? ' active' : '', '" href="', $button['href'], '" ', isset($button['target']) ? 'target="' . $button['target'] . '"' : '', '>', $button['title'], '</a>';

		// Any 2nd level menus?
		if (!empty($button['sub_buttons']))
		{
			echo '
							<ul class="menulevel2">';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
								<li class="listlevel2', !empty($childbutton['sub_buttons']) ? ' subsections" aria-haspopup="true"' : '"', '>
									<a class="linklevel2" href="', $childbutton['href'], '" ' , isset($childbutton['target']) ? 'target="' . $childbutton['target'] . '"' : '', '>', $childbutton['title'], '</a>';

				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
									<ul class="menulevel3">';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
										<li class="listlevel3">
											<a class="linklevel3" href="', $grandchildbutton['href'], '" ' , isset($grandchildbutton['target']) ? 'target="' . $grandchildbutton['target'] . '"' : '', '>', $grandchildbutton['title'], '</a>
										</li>';

					echo '
									</ul>';
				}

				echo '
								</li>';
			}

			echo '
							</ul>';
		}

		echo '
						</li>';
	}

	echo '
					</ul>';

	// Define the upper_section toggle in JavaScript.
	echo '
				<script><!-- // --><![CDATA[
					var oMainHeaderToggle = new elk_Toggle({
						bToggleEnabled: true,
						bCurrentlyCollapsed: ', empty($context['minmax_preferences']['upshrink']) ? 'false' : 'true', ',
						aSwappableContainers: [
							\'upper_section\',\'header\'
						],
						aSwapImages: [
							{
								sId: \'upshrink\',
								srcExpanded: elk_images_url + \'/upshrink.png\',
								altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
								srcCollapsed: elk_images_url + \'/upshrink2.png\',
								altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
							}
						],
						oThemeOptions: {
							bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
							sOptionName: \'minmax_preferences\',
							sSessionId: elk_session_id,
							sSessionVar: elk_session_var,
							sAdditionalVars: \';minmax_key=upshrink\'
						},
						oCookieOptions: {
							bUseCookie: elk_member_id == 0 ? true : false,
							sCookieName: \'upshrink\'
						}
					});
				// ]]></script>';
}

/**
 * Generate a strip of buttons.
 * @param array $button_strip
 * @param string $direction = ''
 * @param array $strip_options = array()
 */
function template_button_strip($button_strip, $direction = '', $strip_options = array())
{
	global $context, $txt;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons... now with cleaner markup (yay!).
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		// @todo this check here doesn't make much sense now (from 2.1 on), it should be moved to where the button array is generated
		// Kept for backward compatibility
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
								<li role="menuitem"><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="linklevel1 button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '>' . $txt[$value['text']] . '</a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	echo '
							<ul role="menubar" class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
								', implode('', $buttons), '
							</ul>';
}

/**
 * Show a box with an error message.
 */
function template_show_error($error_id)
{
	global $context;

	if (empty($error_id))
		return;

	$error = !empty($context[$error_id]) ? $context[$error_id] : null;

	echo '
					<div class="', (!isset($error['type']) ? 'infobox' : ($error['type'] !== 'serious' ? 'noticebox' : 'errorbox')), '" ', empty($error['errors']) ? ' style="display: none"' : '', ' id="', $error_id, '">';
	if (!empty($error['title']))
		echo '
						<dl>
							<dt>
								<strong id="', $error_id, '_title">', $error['title'], '</strong>
							</dt>
							<dd>';
	if (!empty($error['errors']))
	{
		echo '
								<ul class="error" id="', $error_id, '_list">';

		foreach ($error['errors'] as $key => $error)
			echo '
									<li id="', $error_id, '_', $key, '">', $error, '</li>';
		echo '
								</ul>';
	}
	if (!empty($error['title']))
		echo '
							</dd>
						</dl>';
	echo '
					</div>';
}

/**
 * Allows to select a board
 */
function template_select_boards($name, $label = '', $extra = '')
{
	global $context;

	if (!empty($label))
		echo '
	<label for="', $name, '">', $label, ' </label>';

	echo '
	<select name="', $name, '" id="', $name, '" ', $extra, ' >';

	foreach ($context['categories'] as $category)
	{
		echo '
		<optgroup label="', $category['name'], '">';

		foreach ($category['boards'] as $board)
			echo '
			<option value="', $board['id'], '"', !empty($board['selected']) ? ' selected="selected"' : '', !empty($context['current_board']) && $board['id'] == $context['current_board'] && $context['boards_current_disabled'] ? ' disabled="disabled"' : '', '>', $board['child_level'] > 0 ? str_repeat('==', $board['child_level'] - 1) . '=&gt; ' : '', $board['name'], '</option>';
		echo '
		</optgroup>';
	}

	echo '
	</select>';
}

/**
 * Another used and abused piece of template that can be found everywhere
 * @param string $button_strip index of $context to create the button strip
 * @param string $strip_direction direction of the button strip (see template_button_strip for details)
 * @param string $go index of $txt used to label the go up/down button < Deprecated (possible values go_up/go_down, other values can be used as well, though it may result in non working buttons)
 * @param array $options array of optional values, possible values:
 *                - 'top_button' (boolean) show/hide the go up/down button (@todo rename) < Deprecated
 *                - 'page_index' (string) index of $context where is located the pages index generated by constructPageIndex
 *                - 'page_index_markup' (string) markup for the page index, overrides 'page_index' and can be used if the page index code is not in the first level of $context
 *                - 'extra' (string) used to add html markup at the end of the template
 */
function template_pagesection($button_strip = false, $strip_direction = '', $go = 'go_up', $options = array())
{
	global $context, $modSettings, $txt;

	//if (!isset($options['top_button']))
	//	$options['top_button'] = !empty($modSettings['topbottomEnable']);

	if (!empty($options['page_index_markup']))
	// Hmmm. I'm a tad wary of having floatleft here but anyway............
	// @todo - Try using table-cell display here. Should do auto rtl support. Less markup, less css. :)
		$pages = '<div class="pagelinks floatleft" role="menubar">' . $options['page_index_markup'] . '</div>';
	else
	{
		if (!isset($options['page_index']))
			$options['page_index'] = 'page_index';
		$pages = empty($context[$options['page_index']]) ? '' : '<div class="pagelinks floatleft" role="menubar">' . $context[$options['page_index']] . '</div>';
	}

	if (!isset($options['extra']))
		$options['extra'] = '';

	// Also, would love to deprecate the old/top bottom buttons for something better (which I already know how to do). < Done. :)
	// @todo - Just leaving stuff commented for the moment, in preparation for the inevitable bleating and circular motion. :D
		echo '
			<div class="pagesection">
				', $pages;
		/*	<div class="pagesection">
				', $options['top_button'] ? '<a id="page' . ($go != 'go_up' ? 'top' : 'bot') . '" href="#' . ($go == 'go_up' ? 'top' : 'bot') . '" class="topbottom floatleft">' . $txt[$go] . '</a>' : '', $pages, '*/
		echo '
				', !empty($button_strip) && !empty($context[$button_strip]) ? template_button_strip($context[$button_strip], $strip_direction) : '',
				$options['extra'], '
			</div>';

}

/**
 * This is the newsfader
 */
function template_news_fader()
{
	global $settings, $options, $txt, $context;

	echo '
		<ul id="elkFadeScroller">
			<li>
				', implode('</li><li>', $context['news_lines']), '
			</li>
		</ul>
	<script src="', $settings['default_theme_url'], '/scripts/fader.js"></script>
	<script><!-- // --><![CDATA[

		// Create a news fader object.
		var oNewsFader = new elk_NewsFader({
			sFaderControlId: \'elkFadeScroller\',
			sItemTemplate: ', JavaScriptEscape('%1$s'), ',
			iFadeDelay: ', empty($settings['newsfader_time']) ? 5000 : $settings['newsfader_time'], '
		});
	// ]]></script>';
}