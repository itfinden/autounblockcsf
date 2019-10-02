<?php
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}

		$resultservers = autounblockcsf_getCpServers();
		// Action Results
		if(!empty($GLOBALS['autounblockcsf']['POST']['actionval'])) {
			$action = $GLOBALS['autounblockcsf']['POST']['actionval'];
			$actionIP = $GLOBALS['autounblockcsf']['POST']['actionIP'];
			$server = $GLOBALS['autounblockcsf']['POST']['server'];
			if (!empty($action) && autounblockcsf_validIpAddress($actionIP)) {
				if ($server == 'all') {
					if ($resultservers['status'] == '1') {
						foreach ($resultservers['servers'] as $serverval) {
							$getResults = getAutoUnblock($actionIP,$action,$serverval['id'],0,true,$LANG);
							if ($getResults['status'] == '1') {
								echo '<div class="infobox" style="font-size:16px">';
								echo '<strong>'.$serverval['name'].' '.$serverval['ipaddress'].':</strong><br/>';
								if ($getResults['log']['status'] == '1') {
									if ($getResults['action'] == 'kill' || $getResults['action'] == 'qkill') {
										echo 'Succesfuly unblocked:<br/>';
									}
									echo $getResults['line'];
								} else {
									echo $getResults['data'];
								}
								echo '</div>';
							} else {
								echo '<div class="errorbox" style="font-size:16px">';
								echo '<strong>Results for '.$serverval['name'].' '.$serverval['ipaddress'].':</strong><br/>';
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
					} else {
						echo '<div class="errorbox" style="font-size:16px">';
						if (is_array($resultservers['errors'])) {
							foreach ($resultservers['errors'] as $key=>$val) {
								echo $val.'<br/>';
							}
						} else {
							echo $resultservers['errors'];
						}
						if (!empty($resultservers['data'])) {echo $resultservers['data'];}
						echo '</div>';
					}
				} else {
					$getResults = getAutoUnblock($actionIP,$action,$server,0,true,$LANG);
					if ($getResults['status'] == '1') {
						echo '<div class="infobox" style="font-size:16px">';
						echo '<strong>'.$getResults['hostName'].' '.$getResults['hostIP'].':</strong><br/>';
						if ($getResults['log']['status'] == '1') {
							if ($getResults['action'] == 'kill' || $getResults['action'] == 'qkill') {
								echo $LANG['unblocksuccess'].'<br/>';
							}
							echo $getResults['line'];
						} else {
							echo $getResults['data'];
						}
						echo '</div>';
					} else {
						echo '<div class="errorbox" style="font-size:16px">';
						if ($getResults['hostName']) {
							echo '<strong>'.$getResults['hostName'].' '.$getResults['hostIP'].':</strong><br/>';
						}
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
			} elseif (!empty($action)) {
				echo '<div class="errorbox" style="font-size:16px">';
				echo '<strong>'.$LANG['validIPaddress'].'</strong>';
				echo '</div>';
			}
		}
		?>
		<script type="text/javascript">
		function IpFildeV() {
			var ipfilde = document.getElementById("actionIP").value;
			if (ipfilde == '') {
				alert('<?php echo $LANG['IPcannotbeempty']; ?>');
				return false;
			} else {
				document.getElementById("ipcationform").submit();
			}
		}
		</script>
		<?php
		// End Action Results
		echo '<h2>'.$LANG['ipacttitle'].'</h2><h3>'.$LANG['ipactdes'].'</h3>
			<div>
				<form method="post" action="'.$baseUrl.'" id="ipcationform"">
					<table class="table">
						<tr><td class="aip-liner"><strong>'.$LANG['selectserver'].'</strong></td>
						<td class="aip-liner">
						<select name="server" class="a-select" style="width:300px">
						<option value="all">'.$LANG['allservers'].'</option>
		';
		
		if ($resultservers['status'] == '1') {
			foreach ($resultservers['servers'] as $rowservers) {
				if ($rowservers[id] == $GLOBALS['autounblockcsf']['POST']['server']) {
					$select = ' selected="selected"';
				} else {
					$select = '';
				}
				if ($rowservers['type'] == 'directadmin') {
					echo '<option value="'.$rowservers[id].'"'.$select.'>'.$rowservers['name'].' (DirectAdmin)</option>';
				} elseif ($rowservers['type'] == 'cpanel') {
					echo '<option value="'.$rowservers[id].'"'.$select.'>'.$rowservers['name'].' (cPanel)</option>';
				} else {
					echo '<option value="'.$rowservers[id].'"'.$select.'>'.$rowservers['name'].' ('.ucfirst($rowservers['type']).')</option>';
				}
				unset($rowservers);
			}
		}

		echo '
					</select></td></tr>
					<tr>
						<td class="aip-liner"><strong>'.$LANG['enterip'].'</strong></td>
						<td class="aip-liner"><input type="text" size="30" name="actionIP" id="actionIP" class="a-input" required/></td>
					</tr>
					<tr>
						<td class="aip-liner"><strong>'.$LANG['comment'].'</strong></td>
						<td class="aip-liner"><input type="text" size="30" name="comment" class="a-input" placeholder="'.$LANG['commenteg'].'" /></td>
					</tr>
					<input type="hidden" name="actionval" value="">
					<tr><td colspan="2">
						<br/><input class="a-btn" onclick="this.form.actionval.value=this.name;IpFildeV();" type="button" name="grep" value="'.$LANG['buttonsearch'].'" />
						<input class="a-btn green-btn" onclick="this.form.actionval.value=this.name;IpFildeV();" type="button" name="remove" value="'.$LANG['buttonunblock'].'" />
						<input class="a-btn blue-btn" onclick="this.form.actionval.value=this.name;IpFildeV();" type="button" name="qallow" value="'.$LANG['buttonqallow'].'" />
						<input class="a-btn red-btn" onclick="this.form.actionval.value=this.name;IpFildeV();" type="button" name="qdeny" value="'.$LANG['buttonqdeny'].'" />
						<input class="a-btn orange-btn" onclick="this.form.actionval.value=this.name;IpFildeV();" type="button" name="qignore" value="'.$LANG['buttonqignore'].'" />
					</td></tr>
	    			<tr><td colspan="2">
						<br/><strong>'.$LANG['buttonsearch'].':</strong> '.$LANG['buttonsearchdesc'].'<br/>
						<strong>'.$LANG['buttonunblock'].':</strong> '.$LANG['buttonunblockdesc'].'<br/>
						<strong>'.$LANG['buttonqallow'].':</strong> '.$LANG['buttonqallowdesc'].'<br/>
						<strong>'.$LANG['buttonqdeny'].':</strong> '.$LANG['buttonqdenydesc'].'<br/>
						<strong>'.$LANG['buttonqignore'].':</strong> '.$LANG['buttonqignoredesc'].'<br/>
					</td></tr>
				</table>
				<p style="font-size:12px;">'.$LANG['commentnote'].'</p>
			</form>
			</div>
		';
?>