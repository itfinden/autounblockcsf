<?php
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}

		echo '
			<script type="text/javascript">
				function confirmclearlog() {
					var clearlogs=confirm("'.$LANG['clearlogdatabase'].'");
					if (clearlogs) {
						return true;
					} else {
						return false;
					}
				}
			</script>
		';

		if ($GLOBALS['autounblockcsf']['POST']['sqlaction'] == 'deletelog') {
			mysql_query('TRUNCATE TABLE mod_autounblockcsf');
		}
		
		$orderDir = 'DESC';
		if ($GLOBALS['autounblockcsf']['GET']['orderDir']) {
			if ($GLOBALS['autounblockcsf']['GET']['orderDir'] == 'ASC') {
				$orderDir = 'DESC';
			} else {
				$orderDir = 'ASC';
			}
		}

		if (isset($GLOBALS['autounblockcsf']['POST']['server'])) {
			$_SESSION['server'] = $GLOBALS['autounblockcsf']['POST']['server'];
		}
		
		if (isset($_SESSION['server'])) {
			$server = $_SESSION['server'];
		}

		if (is_numeric($server)) {
			$sqlWhere = array("server" =>$server);
		}

		$countlog = count(autounblockcsf_getLog($sqlWhere,'','',''));
		$itemnum = $countlog;
		
		if (isset($GLOBALS['autounblockcsf']['POST']['itemnum'])) {
			$_SESSION['itemnum'] = $GLOBALS['autounblockcsf']['POST']['itemnum'];
		}
		
		if (isset($_SESSION['itemnum'])) {
			$itemnum = $_SESSION['itemnum'];
			if ($itemnum == 0) {
				$itemnum = $countlog;
			}
		} else {
			$_SESSION['itemnum'] = 30;
			$itemnum = 30;
		}

		$page = 1;
		if (isset($GLOBALS['autounblockcsf']['POST']['page'])) {
			$page = $GLOBALS['autounblockcsf']['POST']['page'];
			$limitadd = $itemnum * $page;
			$limitstart = $limitadd - $itemnum;
			$limitend = $itemnum;
		} else {
			$limitstart = 0;
			$limitend = $itemnum;
		}

		$pagescount = $countlog/$itemnum;
		$totalpages = ceil($pagescount);

		$showTotal = $limitstart.','.$limitend;

		$orderDir = 'DESC';
		if ($GLOBALS['autounblockcsf']['GET']['orderDir']) {
			if ($GLOBALS['autounblockcsf']['GET']['orderDir'] == 'ASC') {
				$orderDir = 'DESC';
			} else {
				$orderDir = 'ASC';
			}
		}

		switch ($GLOBALS['autounblockcsf']['GET']['orderBy']) :
			case  'dateandtime': $orderDirLink1 = '&orderDir='.$orderDir; $orderDirLink2 = ''; $orderDirLink3 = ''; $orderDirLink4 = ''; $orderDirLink5 = ''; $orderDirLink6 = ''; break;
			case  'server': $orderDirLink1 = ''; $orderDirLink2 = '&orderDir='.$orderDir; $orderDirLink3 = ''; $orderDirLink4 = ''; $orderDirLink5 = ''; $orderDirLink6 = ''; break;
			case  'action': $orderDirLink1 = ''; $orderDirLink2 = ''; $orderDirLink3 = '&orderDir='.$orderDir; $orderDirLink4 = ''; $orderDirLink5 = ''; $orderDirLink6 = ''; break;
			case  'ip': $orderDirLink1 = ''; $orderDirLink2 = ''; $orderDirLink3 = ''; $orderDirLink4 = '&orderDir='.$orderDir; $orderDirLink5 = ''; $orderDirLink6 = ''; break;
			case  'user': $orderDirLink1 = ''; $orderDirLink2 = ''; $orderDirLink3 = ''; $orderDirLink4 = ''; $orderDirLink5 = '&orderDir='.$orderDir; $orderDirLink6 = ''; break;
			case  'request': $orderDirLink1 = ''; $orderDirLink2 = ''; $orderDirLink3 = ''; $orderDirLink4 = ''; $orderDirLink5 = ''; $orderDirLink6 = '&orderDir='.$orderDir; break;
			default: $orderDirLink1 = ''; $orderDirLink2 = ''; $orderDirLink3 = ''; $orderDirLink4 = ''; $orderDirLink5 = ''; $orderDirLink6 = ''; break;
		endswitch;

		if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'mod_autounblock'"))==1) {
			$resultIntalled = select_query('mod_autounblockcsf','COUNT(*)');
			$dataIntalled = mysql_fetch_array($resultIntalled);
			if ($dataIntalled[0] == 0) {
				if ($GLOBALS['autounblockcsf']['POST']['importlog'] == '1') {
					$modulesArr = autounblockcsf_serverModules();
					if (!is_array($modulesArr)) {$modulesArr = array();}
					array_push($modulesArr, 'cpanel', 'directadmin');
					$modulesArrT = implode("','", $modulesArr);
					$resultServerInfo = mysql_query("SELECT id,ipaddress FROM tblservers WHERE type IN ('".$modulesArrT."') AND disabled <> 1");
					while ($rowServerInfo = mysql_fetch_assoc($resultServerInfo)) {
						$servers[$rowServerInfo['ipaddress']] = $rowServerInfo['id'];
					}
					$resultGetLog = select_query('mod_autounblock','*');
					while ($dataGetLog = mysql_fetch_assoc($resultGetLog)) {
						$serverid = $servers[$dataGetLog['server']];
						if (empty($serverid)) {
							$serverid = 0;
						}
						$replacements = array('server' => $serverid,'user' => 'client|'.$dataGetLog['user'],'action' => 'kill');
						$rowArr = array_replace($dataGetLog, $replacements);
						$logSql[] = '("'.mysql_real_escape_string($rowArr['user']).'", '.$rowArr['server'].', "'.mysql_real_escape_string($rowArr['ip']).'", "'.mysql_real_escape_string($rowArr['action']).'", "'.mysql_real_escape_string($rowArr['description']).'", "'.mysql_real_escape_string($rowArr['dateandtime']).'")';
					}
					if (mysql_query('INSERT INTO mod_autounblockcsf (user, server, ip, action, description, dateandtime) VALUES '.implode(',', $logSql))) {
						echo '<div class="infobox" style="font-size:16px">'.$LANG['Importsuccess'].'</div>';
					} else {
						echo '<div class="errorbox" style="font-size:16px">'.$LANG['Importfailed'].'</div>';
					}
				} else {
					echo '
						<h2>'.$LANG['logactivitytitle'].'</h2>
						<form method="post" action="'.$baseUrl.'">
						<input type="hidden" name="importlog" value="1">
						<p style="font-size:16px">'.$LANG['v1importmsg'].' 
						<input type="submit" value="'.$LANG['importbtn'].'" />
						</p>
						</form>
					';
				}
			} else {
				echo '
					<h2>'.$LANG['logactivitytitle'].'</h2>
					<p style="font-size:16px">'.$LANG['v1installedf'].'</p>
				';
			}
		} else {
			echo '
				<h2>'.$LANG['logactivitytitle'].'</h2>
				<div style="width:100%; height:35px;">
				<form method="post" action="'.$baseUrl.'" class="logform">
					<select name="server" onchange="submit();" class="a-select">
						<option>'.$LANG['allservers'].'</option>
			';
			$resultservers = autounblockcsf_getCpServers();
			if ($resultservers['status'] == '1') {
				foreach ($resultservers['servers'] as $rowservers) {
					if ($server == $rowservers[id]) {
						$select = ' selected="selected"';
					} else {
						$select = '';
					}
					echo '<option value="'.$rowservers[id].'"'.$select.'>'.$rowservers['name'].'</option>';
				}
			}
			echo '</select>
					<span style="padding:3px; line-height: 35px;"> </span>
				</form> 
				<form method="post" action="'.$baseUrl.'" class="logform">
					<select name="itemnum" onchange="submit();" class="a-select">
			';
			for ($i = 0; $i <= $countlog; $i += 10) {
				if ($_SESSION['itemnum'] == $i) {$select = ' selected="selected"';}else{$select = '';}
				if ($i == 0) {
					echo '<option value="'.$i.'"'.$select.'>'.$LANG['ShowAll'].'</option>';
				} else {
					echo '<option value="'.$i.'"'.$select.'>'.$i.' '.$LANG['dataResults'].'</option>';
				}
			}
			if ($totalpages > 1) {
				echo '
					<form method="post" action="'.$baseUrl.'" class="logform">
						<select name="page" onchange="submit();" class="a-select">
				';
				for ($i = 1; $i <= $totalpages; $i++) {
					if ($GLOBALS['autounblockcsf']['POST']['page'] == $i) {$select = ' selected="selected"';}else{$select = '';}
					echo '<option value="'.$i.'"'.$select.'>'.$LANG['Page'].' '.$i.'</option>';
				}
				echo '</select></form>';
			}
			echo '</select>
					<span style="padding:3px; line-height: 35px;">From total of  <b style="font-size: 14px">'.$countlog.'</b> log lines</span>
				</form>
			';
			if ($GLOBALS['autounblockcsf']['GET']['orderBy'] == 'dateandtime') {$sorting1 = 'class="ascsort"';if ($GLOBALS['autounblockcsf']['GET']['orderDir'] == 'DESC') {$sorting1 = 'class="descsort"';}} else {$sorting1 = 'class="sort"';}
			if ($GLOBALS['autounblockcsf']['GET']['orderBy'] == 'server') {$sorting2 = 'class="ascsort"';if ($GLOBALS['autounblockcsf']['GET']['orderDir'] == 'DESC') {$sorting2 = 'class="descsort"';}} else {$sorting2 = 'class="sort"';}
			if ($GLOBALS['autounblockcsf']['GET']['orderBy'] == 'action') {$sorting3 = 'class="ascsort"';if ($GLOBALS['autounblockcsf']['GET']['orderDir'] == 'DESC') {$sorting3 = 'class="descsort"';}} else {$sorting3 = 'class="sort"';}
			if ($GLOBALS['autounblockcsf']['GET']['orderBy'] == 'ip') {$sorting4 = 'class="ascsort"';if ($GLOBALS['autounblockcsf']['GET']['orderDir'] == 'DESC') {$sorting4 = 'class="descsort"';}} else {$sorting4 = 'class="sort"';}
			if ($GLOBALS['autounblockcsf']['GET']['orderBy'] == 'user') {$sorting5 = 'class="ascsort"';if ($GLOBALS['autounblockcsf']['GET']['orderDir'] == 'DESC') {$sorting5 = 'class="descsort"';}} else {$sorting5 = 'class="sort"';}
			if ($GLOBALS['autounblockcsf']['GET']['orderBy'] == 'request') {$sorting6 = 'class="ascsort"';if ($GLOBALS['autounblockcsf']['GET']['orderDir'] == 'DESC') {$sorting6 = 'class="descsort"';}} else {$sorting6 = 'class="sort"';}
			echo '
				<form onsubmit="return confirmclearlog()" method="post" action="'.$baseUrl.'" class="logformlast">
					<input type="hidden" name="sqlaction" value="deletelog">
					<input type="submit" class="btn red-btn btn-small" name="deletelog" value="Delete all logs" />
                                        <input type="button" onclick="location.href=\''.$modulelink.'&addonaction=backups\'" class="btn blue-btn btn-small" name="logbackups" value="'.$LANG['menubackups'].'" />
				</form>
				</div>
				<table class="datatable">
					<tr>
					<th '.$sorting1.' style="cursor:pointer" onclick="location.href=\''.$modulelink.'&addonaction=userslog&orderBy=dateandtime'.$orderDirLink1.'\'"><a href="'.$modulelink.'&addonaction=userslog&orderBy=dateandtime'.$orderDirLink1.'">'.$LANG['logtabletime'].'</a></th>
					<th '.$sorting2.' style="cursor:pointer" onclick="location.href=\''.$modulelink.'&addonaction=userslog&orderBy=server'.$orderDirLink2.'\'"><a href="'.$modulelink.'&addonaction=userslog&orderBy=server'.$orderDirLink2.'">'.$LANG['logtableserver'].'</a></th>
					<th '.$sorting3.' style="cursor:pointer" onclick="location.href=\''.$modulelink.'&addonaction=userslog&orderBy=action'.$orderDirLink3.'\'"><a href="'.$modulelink.'&addonaction=userslog&orderBy=action'.$orderDirLink3.'">'.$LANG['logtableaction'].'</a></th>
					<th '.$sorting4.' style="cursor:pointer" onclick="location.href=\''.$modulelink.'&addonaction=userslog&orderBy=ip'.$orderDirLink4.'\'"><a href="'.$modulelink.'&addonaction=userslog&orderBy=ip'.$orderDirLink4.'">'.$LANG['logtableip'].'</a></th>
					<th>'.$LANG['logtabledesc'].'</th>
					<th '.$sorting5.' style="cursor:pointer" onclick="location.href=\''.$modulelink.'&addonaction=userslog&orderBy=user'.$orderDirLink5.'\'"><a href="'.$modulelink.'&addonaction=userslog&orderBy=user'.$orderDirLink5.'">'.$LANG['logtableuser'].'</a></th>
					<th '.$sorting6.' style="cursor:pointer" onclick="location.href=\''.$modulelink.'&addonaction=userslog&orderBy=request'.$orderDirLink6.'\'"><a href="'.$modulelink.'&addonaction=userslog&orderBy=request'.$orderDirLink6.'">'.$LANG['logtablefrom'].'</a></th>
					</tr>
				';
				$getwhoisurl = select_query('tbladdonmodules','value',array('module'=>'autounblockcsf','setting'=>'select_whois'));
				$rowswhoisurl = mysql_fetch_array($getwhoisurl);
				$whoisurl = $rowswhoisurl['value'];
				$getLogArrs = autounblockcsf_getLog($sqlWhere,$GLOBALS['autounblockcsf']['GET']['orderBy'],$orderDir,$showTotal);
				foreach ($getLogArrs as $key=>$logArr) {
					$ipline = $logArr['ip'];
					if (!empty($ipline)) {
						$ipline = '<a href="http://'.$whoisurl.'/'.$logArr['ip'].'" target="_blank">'.$logArr['ip'].'</a>';
					}
					$requestline = $logArr['request'];
					if (autounblockcsf_validIpAddress($logArr['request'])) {
						$requestline = '<a href="http://'.$whoisurl.'/'.$logArr['request'].'" target="_blank">'.$logArr['request'].'</a>';
					}
					echo '		
						<tr>
							<td>'.$logArr['dateandtime'].'</td>
							<td>'.$logArr['servername'].'</td>
							<td>'.autounblockcsf_action2Name($logArr['action']).'</td>
							<td>'.$ipline.'</td>
							<td>'.$logArr['description'].'</td>
							<td>'.$logArr['user'].'</td>
							<td>'.$requestline.'</td>
						</tr>
					';
				}
			echo '</table>';
		}
?>
