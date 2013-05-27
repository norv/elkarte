<?php
/* WARNING: You don't need to run or use this file.  Run upgrade.php script instead. */

class Upgrade_Elk_1_0
{
	protected $steps = array();

	function __construct()
	{
		$this->steps = array(
			'add_new_settings' => array(
				'description' => 'Adding new settings...',
				'substeps' => array(
					'add_login_history' => array(
						'name' => 'add_login_history',
						'description' => 'Adding login history...',
						'function' => 'step_add_login_history'
						),
					'copy_current_backup_packages_setting' => array(
						'description' => 'Copy current backup packages setting',
						'function' => 'step_copy_current_backup_packages_setting'
						),
					'add_settings' => array(
						'description' => 'Add new settings to settings table...',
						'function' =>'step_add_settings'
						)
					)
				),
			'update_legacy_attachments' => array(
				'description' => 'Updating legacy attachments...',
				'substeps' => array(
					'convert_legacy_attach' => array(
						'description' => 'Converting legacy attachments',
						'function' => 'step_convert_legacy_attachments'
						)
					)
				),
			'support_ipv6' => array(
				'description' => 'Adding support for IPv6...',
				'substeps' => array(
					'add_ban_items' => array(
						'description' => 'Adding new columns to ban items...',
						'function' => 'step_add_ban_items'
						),
					'change_ban_items' => array(
						'description' => 'Changing existing columns to ban items...',
						'function' => 'step_change_ban_items'
						)
					),
				),
			'support_credits' => array(
				'description' => 'Adding support for <credits> tag in package manager...',
				'substeps' => array(
					'add_columns_log_packages' => array(
						'description' => 'Adding new columns to log_packages ..',
						'function' => 'step_add_columns_log_packages'
						)
					)
				),
			'more_space_for_sessionids' => array(
				'description' => 'Adding more space for session IDs...',
				'substeps' => array(
					'alter_sessionid' => array(
						'description' => 'Altering the session_id columns...',
						'function' => 'step_alter_sessionid'
						)
					)
				),
			'support_move_topic' => array(
				'description' => 'Adding support for MOVED topics enhancements...',
				'substeps' => array(
					'add_new_topics_columns' => array(
						'description' => 'Adding new columns to topics ..',
						'function' => 'add_new_topics_columns'
						)
					)
				),
			'new_scheduled_tasks' => array(
				'description' => 'Adding new scheduled tasks...',
				'substeps' => array(
					'add_new_scheduled_tasks' => array(
						'description' => 'Adding new scheduled tasks...',
						'function' => 'step_new_scheduled_tasks'
						)
					)
				),
			'support_deny_boards' => array(
				'description' => 'Adding support for deny boards access...',
				'substeps' => array(
					'add_new_boards_columns' => array(
						'description' => 'Adding new columns to boards...',
						'function' => 'step_add_new_boards_columns'
						)
					)
				),
			'support_topic_disregard' => array(
				'description' => 'Adding support for topic disregard...',
				'substeps' => array(
					'new_log_topics_columns' => array(
						'description' => 'Adding new columns to boards...',
						'function' => 'step_new_log_topics_columns'
						)
					)
				),
			'support_cf_on_memberlist' => array(
				'description' => 'Adding support for custom profile fields on memberlist...',
				'substeps' => array(
					'new_custom_fields_columns' => array(
						'description' => 'Adding new columns to custom profile fields...',
						'function' => 'step_new_custom_fields_columns'
						)
					)
				),
			'fix_mail_queue' => array(
				'description' => 'Fixing mail queue for long messages...',
				'substeps' => array(
					'alter_mail_queue' => array(
						'description' => 'Altering mail_queue table...',
						'function' => 'step_alter_mail_queue'
						)
					)
				),
			'name_changes' => array(
				'description' => 'Name changes...',
				'substeps' => array(
					'alter_stars_icons' => array(
						'description' => 'Altering the membergroup stars to icons...',
						'function' => 'step_alter_stars_icons'
						)
					)
				),
			'support_drafts' => array(
				'description' => 'Adding support for drafts...',
				'substeps' => array(
					'create_draft_table' => array(
						'description' => 'Creating draft table',
						'function' => 'step_create_draft_table'
						),
					'add_draft_permissions' => array(
						'description' => 'Adding draft permissions...',
						'function' => 'step_add_draft_permissions'
						)
					)
				),
			'messenger_fields_changes' => array(
				'description' => 'Messenger fields...',
				'substeps' => array(
					'new_custom_fields' => array(
						'description' => 'Insert new custom fields...',
						'function' => 'step_new_custom_fields'
						),
					 'move_messenger_fields' => array(
						'description' => 'Move existing values...',
						'function' => 'step_move_messenger_fields'
						),
					'drop_old_columns' => array(
						'description' => 'Drop the old cols...',
						'function' => 'step_drop_old_columns'
						)
					)
				),
			'add_gravatar' => array(
				'description' => 'Adding gravatar permissions...',
				'substeps' => array(
					'add_gravatar' => array(
						'description' => 'Adding gravatar permissions...',
						'function' => 'step_add_gravatar'
						)
					)
				),
			'update_urls' => array(
				'description' => 'Updating URLs information...',
				'substeps' => array(
					'change_to_elk_urls' => array(
						'description' => 'Changing URL to Elk package server..',
						'function' => 'step_change_to_elk_urls'
						)
					)
				),
			'support_follow_up' => array(
				'description' => 'Adding follow-up support...',
				'substeps' => array(
					'create_follow_ups' => array(
						'description' => 'Creating follow-up table...',
						'function' => 'step_create_follow_ups'
						)
					)
				),
			'update_antispam' => array(
				'description' => 'Updating antispam questions...',
				'substeps' => array(
					'create_antispam_table' => array(
						'description' => 'Creating antispam questions table...',
						'function' => 'step_create_antispam_table'
						),
					'move_antispam' => array(
						'description' => 'Move existing values...',
						'function' => 'step_move_antispam'
						)
					)
				),
			'support_maillist' => array(
				'description' => 'Adding support for mailing list...',
				'substeps' => array(
					'create_postby_emails_table' => array(
						'description' => 'Creating postby_emails table...',
						'function' => 'step_create_postby_emails_table'
						),
					'create_postby_emails_error_table' => array(
						'description' => 'Creating postby_emails_error table...',
						'function' => 'step_create_postby_emails_error_table'
						),
					'create_postby_emails_filters_table' => array(
						'description' => 'Creating postby_emails_filters table...',
						'function' => 'step_create_postby_emails_filters_table'
						),
					'new_log_activity_columns' => array(
						'description' => 'Adding new columns to log_activity...',
						'function' => 'step_new_log_activity_columns'
						),
					'new_mail_queue_columns' => array(
						'description' => 'Adding new columns to mail_queue...',
						'function' => 'step_new_mail_queue_columns'
						),
					'update_board_profiles' => array(
						'description' => 'Updating board profiles...',
						'function' => 'step_update_board_profiles'
						)
					)
				),
		);
	}

	function steps()
	{
		return $this->steps;
	}

	function count_categories()
	{
		return count($this->steps);
	}

	function count_substeps()
	{
		$count = 0;
		foreach ($this->steps as $step)
		{
			$count += count($step['substeps']);
		}
		return $count;
	}

	function substep_for($id_step, $id_substep)
	{
		$step = $this->steps($id_step);
		if (isset($step['substeps'][$id_substep]))
			$substep = $step['substeps'][$id_substep];
		else
			$substep = -1;

		return $substep;
	}

	function step_add_login_history()
	{
		//
		$db = database();
		$db->query('', '
			CREATE TABLE IF NOT EXISTS {$db_prefix}member_logins (
				id_login int(10) NOT NULL auto_increment,
				id_member mediumint(8) NOT NULL,
				time int(10) NOT NULL,
				ip varchar(255) NOT NULL default \'\',
				ip2 varchar(255) NOT NULL default \'\',
				PRIMARY KEY id_login(id_login),
				KEY id_member (id_member),
				KEY time (time)
			) ENGINE=MyISAM;
		',
		array());
	}

	function step_copy_current_backup_packages_setting()
	{
		global $modSettings;

		$db = database();
		if (!isset($modSettings['package_make_full_backups']) && isset($modSettings['package_make_backups']))
			$db->query("
				INSERT INTO {$db_prefix}settings
					(variable, value)
				VALUES
					('package_make_full_backups', '" . $modSettings['package_make_backups'] . "')");

	}

	function step_add_settings()
	{
		$db = database();
		$db->query('', '
			INSERT IGNORE INTO {$db_prefix}settings
				(variable, value)
			VALUES
				(\'avatar_default\', \'0\'),
				(\'gravatar_rating\', \'g\'),
				(\'xmlnews_limit\', 5),
				(\'visual_verification_num_chars\', \'6\'),
				(\'enable_disregard\', 0),
				(\'jquery_source\', \'local\');
		');
	}

	function step_convert_legacy_attachments()
	{
		global $modSettings, $step_progress;

		$db = database();

		$request = $db->query('','
			SELECT MAX(id_attach)
			FROM {$db_prefix}attachments');
		list ($step_progress['total']) = $db->fetch_row($request);
		$db->free_result($request);

		$_GET['a'] = isset($_GET['a']) ? (int) $_GET['a'] : 0;
		$step_progress['name'] = 'Converting legacy attachments';
		$step_progress['current'] = $_GET['a'];

		// We may be using multiple attachment directories.
		if (!empty($modSettings['currentAttachmentUploadDir']) && !is_array($modSettings['attachmentUploadDir']))
			$modSettings['attachmentUploadDir'] = unserialize($modSettings['attachmentUploadDir']);

		$is_done = false;
		while (!$is_done)
		{
			nextSubStep($substep);

			$request = $db->query('', "
				SELECT id_attach, id_folder, filename, file_hash
				FROM {$db_prefix}attachments
				WHERE file_hash = ''
				LIMIT $_GET[a], 100");

			// Finished?
			if ($db->num_rows($request) == 0)
				$is_done = true;

			while ($row = $db->fetch_assoc($request))
			{
				// The current folder.
				$current_folder = !empty($modSettings['currentAttachmentUploadDir']) ? $modSettings['attachmentUploadDir'][$row['id_folder']] : $modSettings['attachmentUploadDir'];

				// The old location of the file.
				$old_location = getLegacyAttachmentFilename($row['filename'], $row['id_attach'], $row['id_folder']);

				// The new file name.
				$file_hash = getAttachmentFilename($row['filename'], $row['id_attach'], $row['id_folder'], true);

				// And we try to move it.
				rename($old_location, $current_folder . '/' . $row['id_attach'] . '_' . $file_hash);

				// Only update thif if it was successful.
				if (file_exists($current_folder . '/' . $row['id_attach'] . '_' . $file_hash) && !file_exists($old_location))
					$db->query('', "
						UPDATE {$db_prefix}attachments
						SET file_hash = '$file_hash'
						WHERE id_attach = $row[id_attach]");
			}
			$db->free_result($request);

			$_GET['a'] += 100;
			$step_progress['current'] = $_GET['a'];
		}

		unset($_GET['a']);
	}

	function step_add_ban_items()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE {$db_prefix}ban_items
			ADD COLUMN ip_low5 smallint(255) unsigned NOT NULL DEFAULT '0',
			ADD COLUMN ip_high5 smallint(255) unsigned NOT NULL DEFAULT '0',
			ADD COLUMN ip_low6 smallint(255) unsigned NOT NULL DEFAULT '0',
			ADD COLUMN ip_high6 smallint(255) unsigned NOT NULL DEFAULT '0',
			ADD COLUMN ip_low7 smallint(255) unsigned NOT NULL DEFAULT '0',
			ADD COLUMN ip_high7 smallint(255) unsigned NOT NULL DEFAULT '0',
			ADD COLUMN ip_low8 smallint(255) unsigned NOT NULL DEFAULT '0',
			ADD COLUMN ip_high8 smallint(255) unsigned NOT NULL DEFAULT '0';"
		);
	}

	function step_change_ban_items()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE {$db_prefix}ban_items
			CHANGE ip_low1 ip_low1 smallint(255) unsigned NOT NULL DEFAULT '0',
			CHANGE ip_high1 ip_high1 smallint(255) unsigned NOT NULL DEFAULT '0',
			CHANGE ip_low2 ip_low2 smallint(255) unsigned NOT NULL DEFAULT '0',
			CHANGE ip_high2 ip_high2 smallint(255) unsigned NOT NULL DEFAULT '0',
			CHANGE ip_low3 ip_low3 smallint(255) unsigned NOT NULL DEFAULT '0',
			CHANGE ip_high3 ip_high3 smallint(255) unsigned NOT NULL DEFAULT '0',
			CHANGE ip_low4 ip_low4 smallint(255) unsigned NOT NULL DEFAULT '0',
			CHANGE ip_high4 ip_high4 smallint(255) unsigned NOT NULL DEFAULT '0';
		");
	}

	function step_add_columns_log_packages()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE {$db_prefix}log_packages
			ADD COLUMN credits varchar(255) NOT NULL DEFAULT '';
		");
	}

	function step_alter_sessionid()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE {$db_prefix}log_online
			CHANGE `session` `session` varchar(64) NOT NULL DEFAULT '';

		ALTER TABLE {$db_prefix}log_errors
			CHANGE `session` `session` char(64) NOT NULL default '                                                                ';

		ALTER TABLE {$db_prefix}sessions
			CHANGE `session_id` `session_id` char(64) NOT NULL;
		");
	}

	function step_add_new_topics_columns()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE {$db_prefix}topics
			ADD COLUMN redirect_expires int(10) unsigned NOT NULL default '0',
			ADD COLUMN id_redirect_topic mediumint(8) unsigned NOT NULL default '0';
		");
	}

	function step_new_scheduled_tasks()
	{
		$db = database();

		$db->query('', "
		INSERT INTO {$db_prefix}scheduled_tasks
			(next_time, time_offset, time_regularity, time_unit, disabled, task)
		VALUES
			(0, 120, 1, 'd', 0, 'remove_temp_attachments');
		INSERT INTO {$db_prefix}scheduled_tasks
			(next_time, time_offset, time_regularity, time_unit, disabled, task)
		VALUES
			(0, 180, 1, 'd', 0, 'remove_topic_redirect');
		INSERT INTO {$db_prefix}scheduled_tasks
			(next_time, time_offset, time_regularity, time_unit, disabled, task)
		VALUES
			(0, 240, 1, 'd', 0, 'remove_old_drafts');
		INSERT INTO {$db_prefix}scheduled_tasks
			(next_time, time_offset, time_regularity, time_unit, disabled, task)
		VALUES
			(0, 0, 6, 'h', 0, 'remove_old_followups');
		INSERT INTO {$db_prefix}scheduled_tasks
			(next_time, time_offset, time_regularity, time_unit, disabled, task)
		VALUES
			(0, 360, 10, 'm', 0, 'maillist_fetch_IMAP');
		");
	}

	function step_new_boards_columns()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE {$db_prefix}boards
			ADD COLUMN deny_member_groups varchar(255) NOT NULL DEFAULT '';
		");
	}

	function step_new_log_topics_columns()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE {$db_prefix}log_topics
			ADD COLUMN disregarded tinyint(3) NOT NULL DEFAULT '0';
		");
	}

	function step_new_custom_fields_columns()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE {$db_prefix}custom_fields
			ADD COLUMN show_memberlist tinyint(3) NOT NULL DEFAULT '0';
		");
	}

	function step_alter_mail_queue()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE {$db_prefix}mail_queue
			CHANGE body body mediumtext NOT NULL;
		");
	}

	function step_alter_stars_icons()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE {$db_prefix}membergroups
			CHANGE `stars` `icons` varchar(255) NOT NULL DEFAULT '';
		");
	}

	function step_create_draft_table()
	{
		$db = database();

		$db->query('', "
		CREATE TABLE IF NOT EXISTS {$db_prefix}user_drafts (
			id_draft int(10) unsigned NOT NULL auto_increment,
			id_topic mediumint(8) unsigned NOT NULL default '0',
			id_board smallint(5) unsigned NOT NULL default '0',
			id_reply int(10) unsigned NOT NULL default '0',
			type tinyint(4) NOT NULL default '0',
			poster_time int(10) unsigned NOT NULL default '0',
			id_member mediumint(8) unsigned NOT NULL default '0',
			subject varchar(255) NOT NULL default '',
			smileys_enabled tinyint(4) NOT NULL default '1',
			body mediumtext NOT NULL,
			icon varchar(16) NOT NULL default 'xx',
			locked tinyint(4) NOT NULL default '0',
			is_sticky tinyint(4) NOT NULL default '0',
			to_list varchar(255) NOT NULL default '',
			outbox tinyint(4) NOT NULL default '0',
			PRIMARY KEY id_draft(id_draft),
			UNIQUE id_member (id_member, id_draft, type)
			) ENGINE=MyISAM COLLATION=utf8_general_ci;
	");
	}

	function step_add_draft_permissions()
	{
		$db = database();

		// We cannot do this twice
		// @todo this won't work when you upgrade from smf
		if (@$modSettings['elkVersion'] < '1.0')
		{
			// Anyone who can currently post unapproved topics we assume can create drafts as well ...
			$request = upgrade_query("
				SELECT id_group, id_profile, add_deny, permission
				FROM {$db_prefix}board_permissions
				WHERE permission = 'post_unapproved_topics'");
			$inserts = array();
			while ($row = mysql_fetch_assoc($request))
			{
				$inserts[] = "($row[id_group], $row[id_profile], 'post_draft', $row[add_deny])";
				$inserts[] = "($row[id_group], $row[id_profile], 'post_autosave_draft', $row[add_deny])";
			}
			mysql_free_result($request);

			if (!empty($inserts))
				upgrade_query("
					INSERT IGNORE INTO {$db_prefix}board_permissions
						(id_group, id_profile, permission, add_deny)
					VALUES
						" . implode(',', $inserts));

			// Next we find people who can send PM's, and assume they can save pm_drafts as well
			$request = upgrade_query("
				SELECT id_group, add_deny, permission
				FROM {$db_prefix}permissions
				WHERE permission = 'pm_send'");
			$inserts = array();
			while ($row = mysql_fetch_assoc($request))
			{
				$inserts[] = "($row[id_group], 'pm_draft', $row[add_deny])";
				$inserts[] = "($row[id_group], 'pm_autosave_draft', $row[add_deny])";
			}
			mysql_free_result($request);

			if (!empty($inserts))
				upgrade_query("
					INSERT IGNORE INTO {$db_prefix}permissions
					(id_group, permission, add_deny)
					VALUES
					" . implode(',', $inserts));
		}
	}

	function step_new_custom_fields()
	{
		$db = database();

		$db->query('', "

		");
	}

	function step_move_messenger_fields()
	{
		$db = database();

		// We cannot do this twice
		// @todo this won't work when you upgrade from smf
		if (@$modSettings['elkVersion'] < '1.0')
		{
			$request = upgrade_query("
				SELECT id_member, aim, icq, msn, yim
				FROM {$db_prefix}members");
			$inserts = array();
			while ($row = mysql_fetch_assoc($request))
			{
				if (!empty($row[aim]))
					$inserts[] = "($row[id_member], -1, 'cust_aim', $row[aim])";

				if (!empty($row[icq]))
					$inserts[] = "($row[id_member], -1, 'cust_icq', $row[icq])";

				if (!empty($row[msn]))
					$inserts[] = "($row[id_member], -1, 'cust_msn', $row[msn])";

				if (!empty($row[yim]))
					$inserts[] = "($row[id_member], -1, 'cust_yim', $row[yim])";
			}
		mysql_free_result($request);

		if (!empty($inserts))
			upgrade_query("
				INSERT INTO {$db_prefix}themes
					(id_member, id_theme, variable, value)
				VALUES
					" . implode(',', $inserts));
		}
	}

	function step_drop_old_columns()
	{
		$db = database();

		$db->query('', "
		ALTER TABLE `{$db_prefix}members`
			DROP `icq`,
			DROP `aim`,
			DROP `yim`,
			DROP `msn`;
		");
	}

	function step_add_gravatar()
	{
		// Don't do this twice!
		// @todo this won't work from smf (2.1)
		if (@$modSettings['elkVersion'] < '1.0')
		{
			// Try find people who probably can use remote avatars.
			$request = upgrade_query("
				SELECT id_group, add_deny, permission
				FROM {$db_prefix}permissions
				WHERE permission = 'profile_remote_avatar'");
			$inserts = array();
			while ($row = mysql_fetch_assoc($request))
			{
				$inserts[] = "($row[id_group], 'profile_gravatar', $row[add_deny])";
			}
			mysql_free_result($request);

			if (!empty($inserts))
				upgrade_query("
					INSERT IGNORE INTO {$db_prefix}permissions
						(id_group, permission, add_deny)
					VALUES
						" . implode(',', $inserts));
		}
	}

	function step_change_to_elk_urls()
	{
		$db = database();

		$db->query("
		UPDATE {$db_prefix}package_servers
			SET url = 'https://github.com/elkarte/addons/tree/master/packages'
			WHERE url = 'http://custom.simplemachines.org/packages/mods';
		");
	}

	function step_create_follow_ups()
	{
		$db = database();

		$db->query("
		CREATE TABLE IF NOT EXISTS {$db_prefix}follow_ups (
			follow_up int(10) NOT NULL default '0',
			derived_from int(10) NOT NULL default '0',
  			PRIMARY KEY (follow_up, derived_from)
		) ENGINE=MyISAM{$db_collation};
		");
	}

	function step_create_antispam_table()
	{
		$db = database();

		$db->query("
		CREATE TABLE IF NOT EXISTS {$db_prefix}antispam_questions (
			id_question tinyint(4) unsigned NOT NULL auto_increment,
			question text NOT NULL,
			answer text NOT NULL,
			language varchar(50) NOT NULL default '',
			PRIMARY KEY (id_question),
			KEY language (language(30))
		) ENGINE=MyISAM{$db_collation};
		");
	}

	function step_move_antispam()
	{
		global $language;

		$db = database();

		$request = upgrade_query("
			SELECT id_comment, recipient_name as answer, body as question
			FROM {$db_prefix}log_comments
			WHERE comment_type = 'ver_test'");
		if (mysql_num_rows($request) != 0)
		{
			$values = array();
			$id_comments = array();
			while ($row = mysql_fetch_assoc($request))
			{
				upgrade_query("
					INSERT INTO {$db_prefix}antispam_questions
						(answer, question, language)
					VALUES
						('" . serialize(array($row['answer'])) . "', '" . $row['question'] . "', '" . $language . "')");
				upgrade_query("
					DELETE FROM {$db_prefix}log_comments
					WHERE id_comment  = " . $row['id_comment'] . "
					LIMIT 1");
			}
		}
	}

	function step_create_postby_emails_table()
	{
		$db = database();

		$db->query("
		CREATE TABLE IF NOT EXISTS {$db_prefix}postby_emails (
			id_email varchar(50) NOT NULL,
			time_sent int(10) NOT NULL default '0',
			email_to varchar(50) NOT NULL,
			PRIMARY KEY (id_email)
		) ENGINE=MyISAM{$db_collation};
		");
	}

	function step_create_postby_emails_error_table()
	{
		$db = database();

		$db->query("
		CREATE TABLE IF NOT EXISTS {$db_prefix}postby_emails_error (
			id_email int(10) NOT NULL auto_increment,
			error varchar(255) NOT NULL default '',
			data_id varchar(255) NOT NULL default '0',
			subject varchar(255) NOT NULL default '',
			id_message int(10) NOT NULL default '0',
			id_board smallint(5) NOT NULL default '0',
			email_from varchar(50) NOT NULL default '',
			message_type char(10) NOT NULL default '',
			message mediumtext NOT NULL default '',
			PRIMARY KEY (id_email),
		) ENGINE=MyISAM{$db_collation};
		");
	}

	function step_create_postby_emails_filters_table()
	{
		$db = database();

		$db->query("
		CREATE TABLE IF NOT EXISTS {$db_prefix}postby_emails_filters (
			id_filter int(10) NOT NULL auto_increment,
			filter_style char(5) NOT NULL default '',
			filter_type varchar(255) NOT NULL default '',
			filter_to varchar(255) NOT NULL default '',
			filter_from varchar(255) NOT NULL default '',
			filter_name varchar(255) NOT NULL default '',
			PRIMARY KEY (id_filter),
		) ENGINE=MyISAM{$db_collation};
		");
	}

	function step_new_log_activity_columns()
	{
		$db = database();

		$db->query("
		ALTER TABLE {$db_prefix}log_activity
			ADD COLUMN pm smallint(5) unsigned NOT NULL DEFAULT '0';
			ADD COLUMN email smallint(5) unsigned NOT NULL DEFAULT '0';
		");
	}

	function step_new_mail_queue_columns()
	{
		$db = database();

		$db->query("
		ALTER TABLE {$db_prefix}mail_queue
			ADD COLUMN message_id int(10)  NOT NULL DEFAULT '0';
		");
	}

	function step_update_board_profiles()
	{
		$db = database();

		$db->query("
		INSERT INTO {$db_prefix}board_permissions (id_group, id_profile, permission) VALUES (0, 1, 'postby_email');
		INSERT INTO {$db_prefix}board_permissions (id_group, id_profile, permission) VALUES (0, 2, 'postby_email');
		");
	}
}