<?php
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}

        echo '<link rel="stylesheet" href="/modules/addons/autounblockcsf/switchery/switchery.min.css" />';
		echo '
			<script type="text/javascript">
				function actionsToggle(id,val) {
					if (val != "none") {
						document.getElementById(id).style.display = "";
					} else {
						document.getElementById(id).style.display = "none";
					}
				}
				function showhide(id) {
					if (document.getElementById(id).style.display == "none") {
						document.getElementById(id).style.display = "";
					} else {
						document.getElementById(id).style.display = "none";
					}
				}
				function showhideAll(action) {
					var elems = document.getElementById("logtable").rows;
					for(var z = 0; z < elems.length; z++) {
						if (elems[z].id != "") {
							elems[z].style.display = action;
						}
					}
				}
			</script>
			<style type="text/css">
				.trline1 {background-color:#F9F9F9;}
				.trline2 {background-color:#F1F1F1;}
			</style>
		';
		// Action Results
		$resultservers = autounblockcsf_getCpServers();
		if(!empty($GLOBALS['autounblockcsf']['POST']['actionval']) && $GLOBALS['autounblockcsf']['POST']['actionval'] != 'testconn') {
            $actionval = $GLOBALS['autounblockcsf']['POST']['actionval'];
            $server = $GLOBALS['autounblockcsf']['POST']['server'];
            if ($actionval == 'csfconfig') {
				if (isset($GLOBALS['autounblockcsf']['POST']['csftextvertion']) && $GLOBALS['autounblockcsf']['POST']['action'] == 'saveconf') {
					if ($resultservers['status'] == '1') {
						if (isset($GLOBALS['autounblockcsf']['POST']['additionalservers'])) {
							foreach ($resultservers['servers'] as $rowservers) {
								if (array_key_exists($rowservers['id'],$GLOBALS['autounblockcsf']['POST']['additionalservers'])) {
                                    $getVersion = autounblockcsf_getCSFvertion($rowservers['id'],$GLOBALS['autounblockcsf']['POST']['csftextvertion'],$rowservers['type'],$LANG);
									if ($getVersion['status'] == '1') {
										$savedData = autounblockcsf_SaveCSFconf($rowservers['id'],$rowservers['type'],$LANG);
										if ($savedData['status'] == '1') {
											echo '<div class="infobox" style="font-size:16px"><strong>'.$rowservers['hostname'].' '.$LANG['dataResults'].'</strong><br/>'.$savedData['data'].'</div>';
										} else {
											echo '<div class="errorbox" style="font-size:16px"><strong>'.$rowservers['hostname'].' '.$LANG['dataResults'].'</strong><br/>'.$savedData['data'].'</div>';
										}
									} else {
										echo '<div class="errorbox" style="font-size:16px"><strong>'.$rowservers['hostname'].' '.$LANG['dataResults'].'</strong><br/>'.$getVersion['errors'].'</div>';
									}
								}
							}
						} else {
							$savedData = autounblockcsf_SaveCSFconf($server,false,$LANG);
							if ($savedData['status'] == '1') {
								echo '<div class="infobox" style="font-size:16px">'.$savedData['data'].'</div>';
							} else {
								echo '<div class="errorbox" style="font-size:16px">'.$savedData['data'].'</div>';
							}
						}
					}
				} else {
					if ($resultservers['status'] == '1') {
						$CpCSFconf = autounblockcsf_getCSFconf($resultservers['servers'],$server,$LANG);
						if (isset($CpCSFconf['errors'])) {
							print('<div class="errorbox" style="font-size:16px">'.$CpCSFconf['errors'].'</div>');
						} else {
							print($CpCSFconf['data']);
						}
					}
				}
			} else {
                        $getResults = getAutoUnblock('',$actionval,$server,0,true,$LANG);
			if ($getResults['status'] == '1') {
				if ($getResults['title']) {
					echo '<p style="font-size:18px">'.$getResults['title'].'</p>';
				}
				if (is_array($getResults['data']) && $actionval == 'viewlogs') {
					echo '<input onclick="showhideAll(\'\')" name="csfexpand" value="'.$LANG['csfexpand'].'" type="button">  <input onclick="showhideAll(\'none\')" name="csfcollapse" value="'.$LANG['csfcollapse'].'" type="button">
					<table id="logtable" class="datatable">
						<tr>
							<th>'.$getResults['data'][1]['td'][0]['b'].'</th>
							<th>'.$getResults['data'][1]['td'][1]['b'].'</th>
							<th>Source '.$getResults['data'][1]['td'][2]['b'].'</th>
							<th>'.$getResults['data'][1]['td'][3]['b'].'</th>
							<th>'.$getResults['data'][1]['td'][4]['b'].'</th>
							<th>Destination '.$getResults['data'][1]['td'][5]['b'].'</th>
							<th>'.$getResults['data'][1]['td'][6]['b'].'</th>
						</tr>
					';
					$trline = 0;
					for ($i = 2; $i < sizeof($getResults['data']); $i=$i+2) {
						$trStyle = ' class="trline1"';
						if ($trline % 2) {
							$trStyle = ' class="trline2"';
						}
						echo '
						<tr'.$trStyle.' style="cursor:pointer" onclick="showhide(\'more'.$i.'\')">
							<td>'.substr($getResults['data'][($i+1)]['td']['span'], 0, 15).'</td>
							<td>'.$getResults['data'][$i]['td'][1].'</td>
							<td>'.$getResults['data'][$i]['td'][2].'</td>
							<td>'.$getResults['data'][$i]['td'][3].'</td>
							<td>'.$getResults['data'][$i]['td'][4].'</td>
							<td>'.$getResults['data'][$i]['td'][5].'</td>
							<td>'.$getResults['data'][$i]['td'][6].'</td>
						</tr>
						<tr'.$trStyle.' id="more'.($i).'" style="display:none"><td colspan="7" style="border-bottom:1px #cccccc dashed;text-align:left;padding:0px 15px 0px 15px;">'.$getResults['data'][($i+1)]['td']['span'].'</td></tr>
						';
						$trline++;
					}
					echo '</table><br/>';
				} else {
					if ($actionval == 'csfconfig') {
						echo $getResults['data'];
					} else {
						echo '<div class="infobox" style="font-size:16px">'. $getResults['data'].'</div>';
					}
				}
			} else {
				echo '<div class="infobox" style="font-size:16px">';
				if (is_array($getResults['errors'])) {
					foreach ($getResults['errors'] as $key=>$val) {
						echo $val.'<br/>';
					}
				} else {
					echo $getResults['errors'];
				}
				if (!empty($getResults['data'])) {echo $getResults['data'];}
				echo '</div>';
			}
                    }
                }
                
                
                ////
		if($GLOBALS['autounblockcsf']['POST']['server']) {
			$server = $GLOBALS['autounblockcsf']['POST']['server'];
			$getResults = getAutoUnblock('','testconn',$server,0,true,$LANG);
			if ($getResults['status'] != '0') {
				if (!$getResults['privileges']) {$resellerTxt = ' -'.$LANG['Resellerlimited'];} else {$resellerTxt = '';}
				$serverStatus = '<tr><td colspan="2"><div class="infobox" style="margin:2px;width:727px;font-size:16px">'.$getResults['data'].$resellerTxt.'</div></td></tr>';
			} else {
				$serverStatus = '<tr><td colspan="2"><div class="errorbox" style="margin:2px;width:727px;font-size:16px">'.$getResults['errors'].'</div></td></tr>';
			}
		}
		// End Action Results
		echo '<h2>'.$LANG['csftitle'].'</h2><h3>'.$LANG['csfdes'].'</h3>
			<div>
			<form method="post" action="'.$baseUrl.'">
				<input type="hidden" name="actionval" value="" />
				<select name="server" onchange="this.form.actionval.value=\'testconn\';submit();" class="a-select" style="width:300px">
		';
		if(!$server) {
			echo '<option value="none">'.$LANG['csfselect'].'</option>';
		}
		$formToggle = 'display:none;';
		$daid = array();
		if ($resultservers['status'] == '1') {
			foreach ($resultservers['servers'] as $rowservers) {
				if ($server == $rowservers[id]) {
					$formToggle = '';
					$select = ' selected="selected"';
				} else {
					$select = '';
				}
				if ($rowservers['type'] == 'directadmin') {
					echo '<option value="'.$rowservers[id].'"'.$select.'>'.$rowservers['name'].' (DirectAdmin)</option>';
					$daid[] = $rowservers[id];
				} elseif ($rowservers['type'] == 'cpanel') {
					echo '<option value="'.$rowservers[id].'"'.$select.'>'.$rowservers['name'].' (cPanel)</option>';
				} else {
					echo '<option value="'.$rowservers[id].'"'.$select.'>'.$rowservers['name'].' ('.ucfirst($rowservers['type']).')</option>';
					if (strstr($rowservers['type'], 'directadmin')) {
						$daid[] = $rowservers[id];
					}
				}
			}
		}
		echo '</select><br/><br/>';
		echo '
			<table id="datatable" class="table" style="'.$formToggle.'width:750px;text-align:left;border:1px #ccc solid;padding:10px;">
				'.$serverStatus;
			if ($getResults['privileges']) {
			echo '
				<tr>
					<td><input onclick="this.form.actionval.value=this.name;submit();" type="button" name="viewlogs" value="'.$LANG['buttonviewlogs'].'" class="a-btn" /></td>
					<td><strong>'.$LANG['buttonviewlogsdesc'].'</strong></td>
				</tr>
				<tr>
					<td><input onclick="this.form.actionval.value=this.name;submit();" type="button" name="csfconfig" value="'.$LANG['csfConfiguration'].'" class="a-btn" /></td>
					<td><strong>'.$LANG['csfConfigurationDes'].'</strong></td>
				</tr>
				<tr>
					<td style="padding-right:10px;"><input onclick="this.form.actionval.value=this.name;submit();" type="button" name="restartq" value="'.$LANG['buttonrestartq'].'" class="a-btn" /></td>
					<td><strong>'.$LANG['buttonrestartqdesc'].'</strong></td>
				</tr>
				<tr>
					<td><input onclick="this.form.actionval.value=this.name;submit();" type="button" name="lfdrestart" value="'.$LANG['buttonlfdrestart'].'" class="a-btn" /></td>
					<td><strong>'.$LANG['buttonlfdrestartdesc'].'</strong></td>
				</tr>';
				// Hide for DirectAdmin server - That action requires root password only via SSH.
				if (!in_array($GLOBALS['autounblockcsf']['POST']['server'], $daid)) {
					echo '<tr>
						<td><input onclick="this.form.actionval.value=this.name;submit();" type="button" name="enable" value="'.$LANG['buttonenable'].'" class="a-btn" /></td>
						<td><strong>'.$LANG['buttonenabledesc'].'</strong></td>
					</tr>
					<tr>
						<td><input onclick="this.form.actionval.value=this.name;submit();" type="button" name="disable" value="'.$LANG['buttondisable'].'" class="a-btn" /></td>
						<td><strong>'.$LANG['buttondisabledesc'].'</strong></td>
					</tr>';
				}
				echo '<tr>
					<td><input onclick="this.form.actionval.value=this.name;submit();" type="button" name="lfdstatus" value="'.$LANG['buttonlfdstatus'].'" class="a-btn" /></td>
					<td><strong>'.$LANG['buttonlfdstatusdesc'].'</strong></td>
				</tr>
				<tr>
					<td><input style="color:red" onclick="this.form.actionval.value=this.name;submit();" type="button" name="denyf" value="'.$LANG['buttonflushall'].'" class="a-btn" /></td>
					<td><strong>'.$LANG['buttonflushalldesc'].'</strong></td>
				</tr>
			';
			}
			echo '
			</table><br/>
		</form>
		</div>
		';
        echo '<script src="/modules/addons/autounblockcsf/switchery/switchery.min.js"></script>';
	echo '
            <script>
		if (Array.prototype.forEach) {
                    var elems = Array.prototype.slice.call(document.querySelectorAll(\'.js-switch\'));
                    elems.forEach(function(html) {
			var switchery = new Switchery(html);
                    });
		} else {
                    var elems = document.querySelectorAll(\'.js-switch\');
                    for (var i = 0; i < elems.length; i++) {
			var switchery = new Switchery(elems[i]);
                    }
		}
            </script>
        ';
?>