<?php
/* WARNING: You don't need to run or use this file.  Run upgrade.php script instead. */

class Upgrade_Elk_1_0_mysql
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
						'description' => 'Add new settings...',
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
				)
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
				(\'enable_disregard\', 0);
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
}