<?php
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}

		// http://www.finalwebsites.com/forums/topic/php-file-download
		if ($GLOBALS['autounblockcsf']['GET']['download_file']) {
			$path = ROOTDIR."/modules/addons/autounblockcsf/backups/";
			$fullPath = $path.$GLOBALS['autounblockcsf']['GET']['download_file'];
			if ($fd = fopen ($fullPath, "r")) {
				$fsize = filesize($fullPath);
				$path_parts = pathinfo($fullPath);
				$ext = strtolower($path_parts["extension"]);
				switch ($ext) {
					default;
					header("Content-type: application/octet-stream");
					header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
				}
				header("Content-length: $fsize");
				header("Cache-control: private");
				while(!feof($fd)) {
					$buffer = fread($fd, 2048);
					echo $buffer;
				}
			}
			fclose ($fd);
			exit;
		}

		echo '
			<script type="text/javascript">
				function submittheform() {
					formaction = document.getElementById("sqlaction").value;
					if (formaction == "restore") {
						confirmmsg = "'.$LANG['logbackupconfirmrestore'].'";
					} else if (formaction == "delete") {
						confirmmsg = "'.$LANG['logbackupconfirmdelete'].'";
					} else if (formaction == "download") {
						document.getElementById("backups").submit();
					}
					if (confirm(confirmmsg)) {
						document.getElementById("backups").submit();
					}
				}
			</script>
		';

		if (!empty($GLOBALS['autounblockcsf']['POST']['sqlaction'])) {
			if ($GLOBALS['autounblockcsf']['POST']['sqlaction'] == 'backup') {
				$doBackup = autounblockcsf_backup('mod_autounblockcsf');
				$status = $doBackup['status'];
			} elseif ($GLOBALS['autounblockcsf']['POST']['sqlaction'] == 'restore') {
				$doRestore = autounblockcsf_restore($GLOBALS['autounblockcsf']['POST']['backupfile']);
				$status = $doRestore['status'];
			} elseif ($GLOBALS['autounblockcsf']['POST']['sqlaction'] == 'delete') {
				if ($doDelete = autounblockcsf_delete_file($GLOBALS['autounblockcsf']['POST']['backupfile'])) {
					$status = '1';
				}
			}
			if ($status == '1') {
				echo '<div class="successbox" style="font-size:16px">';
				echo $LANG['logbackupsuccess'];
				echo '</div>';
			} else {
				echo '<div class="errorbox" style="font-size:16px">';
				echo $LANG['logbackuperror'];
				echo '</div>';
			}
		}
		echo '<h2>'.$LANG['logbackuptitle'].'</h2><h3>'.$LANG['logbackupdesH'].'</h3>
			<div>
				<form id="backups" method="post" action="'.$baseUrl.'">
					<input type="hidden" id="sqlaction" name="sqlaction" value="">
					<strong style="font-size: 14px">'.$LANG['logbackupdes'].'</strong>: 
					<input onclick="this.form.sqlaction.value=this.name;submit();" type="button" name="backup" value="'.$LANG['logbackupbutton'].'" /> 
					<br/><br/>
		';
		if ($backupFiles = autounblockcsf_backup_files()) {
			echo '
				<input type="hidden" id="backupfile" name="backupfile" value="">
				<table id="datatable" class="datatable datatable-in-width" style="'.$formToggle.'">
					<tr>
						<th>'.$LANG['logbackuptable1'].'</th>
						<th>'.$LANG['logbackuptable2'].'</th>
						<th colspan="3">'.$LANG['logbackuptable3'].'</th>
					</tr>
			';
			rsort($backupFiles);
			foreach ($backupFiles as $backupFile) {
				echo '
					<tr>
						<td><strong>'.$backupFile['time'].'</strong></td>
						<td>'.autounblockcsf_returnFileSize($backupFile['size']).'</td>
						<td><a href="'.$baseUrl.'&download_file='.$backupFile['file'].'"><img src="../modules/addons/autounblockcsf/img/download.png" alt="'.$LANG['logbackupdownload'].'" title="'.$LANG['logbackupdownload'].'" border="0" height="16" width="16"></a></td>
						<td style="text-align:center"><a href="#" onclick="document.getElementById(\'sqlaction\').value=\'restore\';document.getElementById(\'backupfile\').value=\''.$backupFile['file'].'\';return submittheform();"><img src="../modules/addons/autounblockcsf/img/restore.png" alt="'.$LANG['logbackuprestore'].'" title="'.$LANG['logbackuprestore'].'" border="0" height="16" width="16"></a></td>
						<td style="text-align:center"><a href="#" onclick="document.getElementById(\'sqlaction\').value=\'delete\';document.getElementById(\'backupfile\').value=\''.$backupFile['file'].'\';return submittheform();"><img src="images/delete.gif" alt="'.$LANG['logbackupdelete'].'" title="'.$LANG['logbackupdelete'].'" border="0" height="16" width="16"></a></td>
					</tr>
				';
			}
			echo '</table><br/>';
		}
		echo '
				</form>
			</div>
		';
?>
