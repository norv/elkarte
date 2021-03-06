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
 */

// The main calendar - January, for example.
function template_main()
{
	global $context, $txt, $scripturl, $modSettings;

	echo '
		<div id="calendar">
			<div id="month_grid">
				', template_show_month_grid('prev'), '
				', template_show_month_grid('current'), '
				', template_show_month_grid('next'), '
			</div>
			<div id="main_grid">
				', $context['view_week'] ? template_show_week_grid('main') : template_show_month_grid('main');

	// Show some controls to allow easy calendar navigation.
	echo '
				<form id="calendar_navigation" action="', $scripturl, '?action=calendar" method="post" accept-charset="UTF-8">';
					template_button_strip($context['calendar_buttons'], 'right');
	echo '
					<select name="month">';

	// Show a select box with all the months.
	foreach ($txt['months'] as $number => $month)
		echo '
						<option value="', $number, '"', $number == $context['current_month'] ? ' selected="selected"' : '', '>', $month, '</option>';
	echo '
					</select>
					<select name="year">';

	// Show a link for every year.....
	for ($year = $modSettings['cal_minyear']; $year <= $modSettings['cal_maxyear']; $year++)
		echo '
						<option value="', $year, '"', $year == $context['current_year'] ? ' selected="selected"' : '', '>', $year, '</option>';
	echo '
					</select>
					<input type="submit" class="button_submit" value="', $txt['view'], '" />';

	echo '
				</form>
			</div>
		</div>';
}

// Template for posting a calendar event.
function template_event_post()
{
	global $context, $txt, $scripturl, $modSettings;

	// Start the javascript for drop down boxes...
	echo '
		<form action="', $scripturl, '?action=calendar;sa=post" method="post" name="postevent" accept-charset="UTF-8" onsubmit="submitonce(this);smc_saveEntities(\'postevent\', [\'evtitle\']);" style="margin: 0;">';

	if (!empty($context['event']['new']))
		echo '
			<input type="hidden" name="eventid" value="', $context['event']['eventid'], '" />';

	// Start the main table.
	echo '
		<div id="post_event">
			<div class="cat_bar">
				<h3 class="catbg">
					', $context['page_title'], '
				</h3>
			</div>';

	if (!empty($context['post_error']['messages']))
	{
		echo '
			<div class="errorbox">
				<dl class="event_error">
					<dt>
						', $context['error_type'] == 'serious' ? '<strong>' . $txt['error_while_submitting'] . '</strong>' : '', '
					</dt>
					<dt class="error">
						', implode('<br />', $context['post_error']['messages']), '
					</dt>
				</dl>
			</div>';
	}

	echo '
			<div class="roundframe">
				<fieldset id="event_main">
					<legend><span', isset($context['post_error']['no_event']) ? ' class="error"' : '', '>', $txt['calendar_event_title'], '</span></legend>
					<input type="text" name="evtitle" maxlength="255" size="70" value="', $context['event']['title'], '" class="input_text" />
					<div class="smalltext" style="white-space: nowrap;">
						<input type="hidden" name="calendar" value="1" />', $txt['calendar_year'], '
						<select name="year" id="year" onchange="generateDays();">';

	// Show a list of all the years we allow...
	for ($year = $modSettings['cal_minyear']; $year <= $modSettings['cal_maxyear']; $year++)
		echo '
							<option value="', $year, '"', $year == $context['event']['year'] ? ' selected="selected"' : '', '>', $year, '&nbsp;</option>';

	echo '
						</select>
						', $txt['calendar_month'], '
						<select name="month" id="month" onchange="generateDays();">';

	// There are 12 months per year - ensure that they all get listed.
	for ($month = 1; $month <= 12; $month++)
		echo '
							<option value="', $month, '"', $month == $context['event']['month'] ? ' selected="selected"' : '', '>', $txt['months'][$month], '&nbsp;</option>';

	echo '
						</select>
						', $txt['calendar_day'], '
						<select name="day" id="day">';

	// This prints out all the days in the current month - this changes dynamically as we switch months.
	for ($day = 1; $day <= $context['event']['last_day']; $day++)
		echo '
							<option value="', $day, '"', $day == $context['event']['day'] ? ' selected="selected"' : '', '>', $day, '&nbsp;</option>';

	echo '
						</select>
					</div>
				</fieldset>';

	if (!empty($modSettings['cal_allowspan']) || $context['event']['new'])
		echo '
				<fieldset id="event_options">
					<legend>', $txt['calendar_event_options'], '</legend>
					<div class="event_options smalltext">
						<ul class="event_options">';

	// If events can span more than one day then allow the user to select how long it should last.
	if (!empty($modSettings['cal_allowspan']))
	{
		echo '
							<li>
								', $txt['calendar_numb_days'], '
								<select name="span">';

		for ($days = 1; $days <= $modSettings['cal_maxspan']; $days++)
			echo '
									<option value="', $days, '"', $context['event']['span'] == $days ? ' selected="selected"' : '', '>', $days, '&nbsp;</option>';

		echo '
								</select>
							</li>';
	}

	// If this is a new event let the user specify which board they want the linked post to be put into.
	if ($context['event']['new'])
	{
		echo '
							<li>
								', $txt['calendar_link_event'], '
								<input type="checkbox" style="vertical-align: middle;" class="input_check" name="link_to_board" checked="checked" onclick="toggleLinked(this.form);" />
							</li>
							<li>
								', template_select_boards('board', $txt['calendar_post_in'], 'onchange="this.form.submit();"'), '
							</li>';
	}

	if (!empty($modSettings['cal_allowspan']) || $context['event']['new'])
		echo '
						</ul>
					</div>
				</fieldset>';

	echo '
				<input type="submit" value="', empty($context['event']['new']) ? $txt['save'] : $txt['post'], '" class="button_submit" />';
	// Delete button?
	if (empty($context['event']['new']))
		echo '
				<input type="submit" name="deleteevent" value="', $txt['event_delete'], '" onclick="return confirm(\'', $txt['calendar_confirm_delete'], '\');" class="button_submit" />';

	echo '
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				<input type="hidden" name="eventid" value="', $context['event']['eventid'], '" />

			</div>
		</div>
		</form>';
}

// Display a monthly calendar grid.
function template_show_month_grid($grid_name)
{
	global $context, $settings, $txt, $scripturl, $modSettings;

	if (!isset($context['calendar_grid_' . $grid_name]))
		return false;

	$calendar_data = &$context['calendar_grid_' . $grid_name];

	if (empty($calendar_data['disable_title']))
	{
		echo '
				<h2 class="category_header">';

		if (empty($calendar_data['previous_calendar']['disabled']) && $calendar_data['show_next_prev'])
			echo '
					<a href="', $calendar_data['previous_calendar']['href'], '" class="previous_month">&#171;</a>';

		if (empty($calendar_data['next_calendar']['disabled']) && $calendar_data['show_next_prev'])
			echo '
					<a href="', $calendar_data['next_calendar']['href'], '" class="next_month">&#187;</a>';

		if ($calendar_data['show_next_prev'])
			echo '
					', $txt['months_titles'][$calendar_data['current_month']], ' ', $calendar_data['current_year'];
		else
			echo '
					<a href="', $scripturl, '?action=calendar;year=', $calendar_data['current_year'], ';month=', $calendar_data['current_month'], '">', $txt['months_titles'][$calendar_data['current_month']], ' ', $calendar_data['current_year'], '</a>';

		echo '
				</h2>';
	}

	echo '
				<table class="calendar_table">';

	// Show each day of the week.
	if (empty($calendar_data['disable_day_titles']))
	{
		echo '
					<tr class="table_head">';

		if (!empty($calendar_data['show_week_links']))
			echo '
						<th>&nbsp;</th>';

		foreach ($calendar_data['week_days'] as $day)
		{
			echo '
						<th class="days" scope="col">', !empty($calendar_data['short_day_titles']) ? (Util::substr($txt['days'][$day], 0, 1)) : $txt['days'][$day], '</th>';
		}
		echo '
					</tr>';
	}

	/* Each week in weeks contains the following:
		days (a list of days), number (week # in the year.) */
	foreach ($calendar_data['weeks'] as $week)
	{
		echo '
					<tr>';

		if (!empty($calendar_data['show_week_links']))
			echo '
						<td class="windowbg2 weeks">
							<a href="', $scripturl, '?action=calendar;viewweek;year=', $calendar_data['current_year'], ';month=', $calendar_data['current_month'], ';day=', $week['days'][0]['day'], '">&#187;</a>
						</td>';

		/* Every day has the following:
			day (# in month), is_today (is this day *today*?), is_first_day (first day of the week?),
			holidays, events, birthdays. (last three are lists.) */
		foreach ($week['days'] as $day)
		{
			// If this is today, make it a different color and show a border.
			echo '
						<td class="', $day['is_today'] ? 'calendar_today' : 'windowbg', ' days">';

			// Skip it if it should be blank - it's not a day if it has no number.
			if (!empty($day['day']))
			{
				// Should the day number be a link?
				if (!empty($modSettings['cal_daysaslink']) && $context['can_post'])
					echo '
							<a href="', $scripturl, '?action=calendar;sa=post;month=', $calendar_data['current_month'], ';year=', $calendar_data['current_year'], ';day=', $day['day'], ';', $context['session_var'], '=', $context['session_id'], '">', $day['day'], '</a>';
				else
					echo '
							', $day['day'];

				// Is this the first day of the week? (and are we showing week numbers?)
				if ($day['is_first_day'] && $calendar_data['size'] != 'small')
					echo ' - <a href="', $scripturl, '?action=calendar;viewweek;year=', $calendar_data['current_year'], ';month=', $calendar_data['current_month'], ';day=', $day['day'], '">', $txt['calendar_week'], ' ', $week['number'], '</a>';

				// Are there any holidays?
				if (!empty($day['holidays']))
					echo '
							<div class="holiday">', $txt['calendar_prompt'], ' ', implode(', ', $day['holidays']), '</div>';

				// Show any birthdays...
				if (!empty($day['birthdays']))
				{
					echo '
							<div>
								<span class="birthday">', $txt['birthdays'], '</span>';

					/* Each of the birthdays has:
						id, name (person), age (if they have one set?), and is_last. (last in list?) */
					$use_js_hide = empty($context['show_all_birthdays']) && count($day['birthdays']) > 15;
					$count = 0;
					foreach ($day['birthdays'] as $member)
					{
						echo '
									<a href="', $scripturl, '?action=profile;u=', $member['id'], '"><span class="fix_rtl_names">', $member['name'], '</span>', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] || ($count == 10 && $use_js_hide)? '' : ', ';

						// Stop at ten?
						if ($count == 10 && $use_js_hide)
							echo '<span class="hidelink" id="bdhidelink_', $day['day'], '">...<br /><a href="', $scripturl, '?action=calendar;month=', $calendar_data['current_month'], ';year=', $calendar_data['current_year'], ';showbd" onclick="document.getElementById(\'bdhide_', $day['day'], '\').style.display = \'\'; document.getElementById(\'bdhidelink_', $day['day'], '\').style.display = \'none\'; return false;">(', sprintf($txt['calendar_click_all'], count($day['birthdays'])), ')</a></span><span id="bdhide_', $day['day'], '" style="display: none;">, ';

						$count++;
					}
					if ($use_js_hide)
						echo '
								</span>';

					echo '
							</div>';
				}

				// Any special posted events?
				if (!empty($day['events']))
				{
					echo '
							<div class="lefttext">
								<span class="event">', $txt['events'], '</span><br />';

					/* The events are made up of:
						title, href, is_last, can_edit (are they allowed to?), and modify_href. */
					foreach ($day['events'] as $event)
					{
						// If they can edit the event, show an icon they can click on....
						if ($event['can_edit'])
							echo '
								<a class="modify_event" href="', $event['modify_href'], '"><img src="' . $settings['images_url'] . '/icons/calendar_modify.png" alt="*" title="' . $txt['modify'] . '" /></a>';

						if ($event['can_export'])
							echo '
								<a class="modify_event" href="', $event['export_href'], '"><img src="' . $settings['images_url'] . '/icons/calendar_export.png" alt=">" title="' . $txt['save'] . '"/></a>';


						echo '
								', $event['link'], $event['is_last'] ? '' : '<br />';
					}

					echo '
							</div>';
				}
			}

			echo '
						</td>';
		}

		echo '
					</tr>';
	}

	echo '
				</table>';
}

// Or show a weekly one?
function template_show_week_grid($grid_name)
{
	global $context, $settings, $txt, $scripturl, $modSettings;

	if (!isset($context['calendar_grid_' . $grid_name]))
		return false;

	$calendar_data = &$context['calendar_grid_' . $grid_name];

	// Loop through each month (At least one) and print out each day.
	foreach ($calendar_data['months'] as $month_data)
	{
		echo '
				<h2 class="category_header">';

		if (empty($calendar_data['previous_calendar']['disabled']) && $calendar_data['show_next_prev'] && empty($done_title))
			echo '
					<span class="previous_month"><a href="', $calendar_data['previous_week']['href'], '">&#171;</a></span>';

		if (empty($calendar_data['next_calendar']['disabled']) && $calendar_data['show_next_prev'] && empty($done_title))
			echo '
					<span class="next_month"><a href="', $calendar_data['next_week']['href'], '">&#187;</a></span>';

		echo '
					<a href="', $scripturl, '?action=calendar;month=', $month_data['current_month'], ';year=', $month_data['current_year'], '">', $txt['months_titles'][$month_data['current_month']], ' ', $month_data['current_year'], '</a>', empty($done_title) && !empty($calendar_data['week_number']) ? (' - ' . $txt['calendar_week'] . ' ' . $calendar_data['week_number']) : '', '
				</h2>';

		$done_title = true;

		echo '
				<ul class="weeklist">';

		foreach ($month_data['days'] as $day)
		{
			echo '
					<li class="windowbg">
						<h4>';

			// Should the day number be a link?
			if (!empty($modSettings['cal_daysaslink']) && $context['can_post'])
				echo '
							<a href="', $scripturl, '?action=calendar;sa=post;month=', $month_data['current_month'], ';year=', $month_data['current_year'], ';day=', $day['day'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['days'][$day['day_of_week']], ' - ', $day['day'], '</a>';
			else
				echo '
							', $txt['days'][$day['day_of_week']], ' - ', $day['day'];

			echo '
						</h4>
						<div class="', $day['is_today'] ? 'calendar_today' : 'windowbg2', ' weekdays">';

			// Are there any holidays?
			if (!empty($day['holidays']))
				echo '
							<div class="smalltext holiday">', $txt['calendar_prompt'], ' ', implode(', ', $day['holidays']), '</div>';

			// Show any birthdays...
			if (!empty($day['birthdays']))
			{
				echo '
							<div class="smalltext">
								<span class="birthday">', $txt['birthdays'], '</span>';

				/* Each of the birthdays has:
					id, name (person), age (if they have one set?), and is_last. (last in list?) */
				foreach ($day['birthdays'] as $member)
					echo '
								<a href="', $scripturl, '?action=profile;u=', $member['id'], '"><span class="fix_rtl_names">', $member['name'], '</span>', isset($member['age']) ? ' (' . $member['age'] . ')' : '', '</a>', $member['is_last'] ? '' : ', ';
				echo '
							</div>';
			}

			// Any special posted events?
			if (!empty($day['events']))
			{
				echo '
							<div class="smalltext">
								<span class="event">', $txt['events'], '</span>';

				/* The events are made up of:
					title, href, is_last, can_edit (are they allowed to?), and modify_href. */
				foreach ($day['events'] as $event)
				{
					// If they can edit the event, show a star they can click on....
					if ($event['can_edit'])
						echo '
								<a href="', $event['modify_href'], '"><img src="' . $settings['images_url'] . '/icons/calendar_modify.png" alt="*" /></a> ';

					echo '
								', $event['link'], $event['is_last'] ? '' : ', ';
				}

				echo '
							</div>';
			}

			echo '
						</div>
					</li>';
		}

		echo '
				</ul>';
	}
}