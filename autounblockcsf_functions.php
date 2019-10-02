<?php
// v3.1.1
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function autounblockcsf_sanitizeGlobals() {
    $globalsArr['GET'] = filter_input_array(INPUT_GET, FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
    $globalsArr['POST'] = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
    if (isset($_SERVER['SCRIPT_NAME'])) {$globalsArr['ENV']['SCRIPT_NAME'] = filter_var($_SERVER['SCRIPT_NAME'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);}
    if (isset($_SERVER['QUERY_STRING'])) {$globalsArr['ENV']['QUERY_STRING'] = filter_var($_SERVER['QUERY_STRING'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);}
    if (isset($_SERVER['SERVER_NAME'])) {$globalsArr['ENV']['SERVER_NAME'] = filter_var($_SERVER['SERVER_NAME'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);}
    if (isset($_SERVER['SERVER_ADDR'])) {$globalsArr['ENV']['SERVER_ADDR'] = filter_var($_SERVER['SERVER_ADDR'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);}
    if (isset($_SERVER['LOCAL_ADDR'])) {$globalsArr['ENV']['LOCAL_ADDR'] = filter_var($_SERVER['LOCAL_ADDR'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);}
    if (isset($_SERVER['HTTP_REFERER'])) {$globalsArr['ENV']['HTTP_REFERER'] = filter_var($_SERVER['HTTP_REFERER'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);}
    $GLOBALS['autounblockcsf'] = $globalsArr;
}
autounblockcsf_sanitizeGlobals();
//if (file_exists(ROOTDIR.'/modules/addons/autounblockcsf/debug')) {echo '<pre>'.print_r($GLOBALS['autounblockcsf'],true).'</pre>';} // debug

function autounblockcsf_returnFileSize($fileSize) {
	switch ($fileSize) :
	    case ($fileSize < 1024):
	        return $fileSize.' <b>Bit</b>';
	    case ($fileSize > 1024 && $fileSize < 1048576):
	        return round($fileSize/1024, 1).' <b>Kb</b>';
	    case ($fileSize > 1048576 && $fileSize < 1073741824):
	        return round($fileSize/1048576, 1).' <b>Mb</b>';
	    case ($fileSize > 1073741824 && $fileSize < 1099511627776 ):
	        return round($fileSize/1073741824, 1).' <b>Gb</b>';
	    case ($fileSize > 1099511627776 && $fileSize < 1125899906842624):
	        return round($fileSize/1099511627776, 1).' <b>TB</b>';
	    case ($fileSize > 1125899906842624):
	        return round($fileSize/1125899906842624, 1).' <b>PB</b>';
            default:
                return $fileSize;
	endswitch;
}

function autounblockcsf_delete_file($backupFile) {
	$backupFolder = ROOTDIR.'/modules/addons/autounblockcsf/backups';
	if (unlink($backupFolder.'/'.$backupFile)) {
		return true;
	} else {
		return false;
	}
}

function autounblockcsf_backup_files() {
	$backupFolder = ROOTDIR.'/modules/addons/autounblockcsf/backups';
        $backupFolderFiles = opendir($backupFolder);
	if ($backupFolderFiles) {
		$i=0;
		while (false !== ($backupFile = readdir($backupFolderFiles))) {
			$i++;
			if ($backupFile != '.' && $backupFile != '..') {
				$backupFileParts = explode('-', $backupFile);
				$backupDate = str_replace('_', '/', $backupFileParts[1]);
				$backupTime = rtrim(str_replace('_', ':', $backupFileParts[2]), '.sql');
				$backupFiles[$i]['file'] = $backupFile;
				$backupFiles[$i]['time'] = date("d F Y H:i:s", filemtime($backupFolder.'/'.$backupFile));
				$backupFiles[$i]['size'] = filesize($backupFolder.'/'.$backupFile);
			}
		}
		closedir($backupFolder);
	}
	if (!$backupFiles) {
		return false;
	}
	return $backupFiles;
}

function autounblockcsf_restore($file) {
	$backupFolder = ROOTDIR.'/modules/addons/autounblockcsf/backups';
	$queryParts = explode(";\n", file_get_contents($backupFolder.'/'.$file));
	foreach ($queryParts as $key => $val) {
		if ($key == 0) {
    		$result['drop'] = mysql_query($val);
		} elseif ($key == 1) {
    		$result['create'] = mysql_query($val);
		} else {
    		$result['insert'] = mysql_query($val);
		}
	}
	if (!$result) {
		$output['status'] = '0';
		$output['errors'] = $result;
		return $output;
	} else {
		$lastid = mysql_insert_id();
		if ($lastid > 0) {
			$output['status'] = '1';
			$output['file'] = $file;
			$output['lastid'] = $lastid;
		} else {
			$output['status'] = '0';
			$output['errors'] = 'Cannot find logs to import';
		}
		return $output;
	}
}

function autounblockcsf_backup($name) {
	$result = mysql_query('SELECT * FROM '.$name);
	$num_fields = mysql_num_fields($result);
	if ($num_fields == 0) {
		$output['status'] = '0';
		$output['error'] = 'Cannot fetch mysql table';
		return $output;
	}
	//$name = 'mod_test'; //
	$returnFile.= 'DROP TABLE IF EXISTS '.$name.';';
	$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$name));
	$returnFile.= "\n\n".$row2[1].";\n\n";
	for ($i = 0; $i < $num_fields; $i++) {
		while($row = mysql_fetch_row($result)) {
			$returnInsert.= '(';
			for($j=0; $j<$num_fields; $j++) {
				$row[$j] = addslashes($row[$j]);
				$row[$j] = ereg_replace("\n","\\n",$row[$j]);
				if (isset($row[$j])) {$returnInsert.= '"'.$row[$j].'"' ;} else {$returnInsert.= '""';}
				if ($j<($num_fields-1)) {$returnInsert.= ',';}
			}
			$returnInsert.= ')';
			$insertArr[] = $returnInsert;
			$returnInsert = '';
		}
	}				   
	$returnFile.= 'INSERT INTO '.$name.' VALUES '.implode(',', $insertArr).';';
	$returnFile.="\n\n";
	$backupFolder = ROOTDIR.'/modules/addons/autounblockcsf/backups';
	if (!file_exists($backupFolder)) {
		if(!mkdir($backupFolder , 0755)) {
			$output['status'] = '0';
			$output['error'] = 'Cannot find or create the backup folder';
			return $output;
		}
	}
	$backupFile = 'backup_'.$name.'-'.date('d_m_Y-H_i_s').'.sql';
	if (!file_put_contents($backupFolder.'/'.$backupFile, $returnFile, LOCK_EX)) {
		$output['status'] = '0';
		$output['error'] = 'Cannot create the backup file';
		return $output;
	}
	$output['status'] = '1';
	$output['file'] = $backupFile;
	$output['folder'] = $backupFolder;
	return $output;
}

function autounblockcsf_validIpAddress($ip) {
	if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_RES_RANGE)) {
		return 'v4';
	} elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_RES_RANGE)) {
		return 'v6';
	}
}

function autounblockcsf_getTextArrayFromDomTag($tag, $page, $mode=0) {
	$dom = new DOMDocument;
	if($mode==1) {$dom->loadXML($page);} else {$dom->loadHTML($page);}
	$dom->preserveWhiteSpace = true;
	$tagContent = $dom->getElementsByTagName($tag);
	$output = array();
	foreach ($tagContent as $tagItem) {
		$output[] = $tagItem->nodeValue;
	}
	return $output;
}

function autounblockcsf_action2Name($action) {
	switch ($action) :
		case 'grep': return 'Search';
		case 'kill': return 'Unblock';
		case 'qkill': return 'Unblock';
		case 'autounblock': return 'AutoUnblock';
		case 'qallow': return 'Allow';
		case 'qdeny': return 'Deny';
		case 'qignore': return 'Ignore';
		default: return $action;
	endswitch;
}

function autounblockcsf_log2langvar($logline) {
	if (empty($logline))
		return 'ldflog_empty';
	elseif (strstr($logline, 'mod_security triggered by'))
		return 'ldflog_modsec';
	elseif (strstr($logline, 'Failed FTP login from'))
		return 'ldflog_ftp';
	elseif (strstr($logline, 'Failed cPanel login from'))
		return 'ldflog_cpanel';
	elseif (strstr($logline, 'Failed POP3 login from'))
		return 'ldflog_pop3';
	elseif (strstr($logline, 'cxs') && strstr($logline, 'FTP upload:'))
		return 'ldflog_cxsftp';
	elseif (strstr($logline, '(PERMBLOCK)') && strstr($logline, 'temp blocks in the last'))
		return 'ldflog_permbytemp';
	elseif (strstr($logline, 'Failed SMTP AUTH login from'))
		return 'ldflog_smtp';
	elseif (strstr($logline, '(htpasswd) Failed web page login from'))
		return 'ldflog_htpasswd';
	elseif (strstr($logline, 'distributed ftpd attacks on account'))
		return 'ldflog_ftpdistributed';
	elseif (strstr($logline, 'distributed imapd attacks on account'))
		return 'ldflog_imapdistributed';
	elseif (strstr($logline, 'Failed IMAP login from'))
		return 'ldflog_imap';
	elseif (strstr($logline, 'Failed SSH login from'))
		return 'ldflog_sshd';
	elseif (strstr($logline, 'Port Scan'))
		return 'ldflog_portscan';
	else
		return $logline;
}

function autounblockcsf_id2username($id,$type) {
	if ($type == 'admin') {
		$resultAdmin = select_query("tbladmins","firstname,lastname",array("id" =>$id));
		$dataAdmin = mysql_fetch_array($resultAdmin);
		return  $dataAdmin['firstname'].' '.$dataAdmin['lastname'];
	} elseif ($type == 'client' || $type == 'pipe') {
		$resultUser = select_query("tblclients","firstname,lastname",array("id" =>$id));
		$dataUser = mysql_fetch_array($resultUser);
		return $dataUser['firstname'].' '.$dataUser['lastname'];
	} else {
		return 'na';
	}	
}

function autounblockcsf_getLog($sqlWhere,$orderBy,$orderSort,$limit) {
	if (empty($sqlWhere)) {$sqlWhere = "";}
	if (empty($orderBy)) {$orderBy = 'id';}
	if (empty($orderSort)) {$orderSort = 'DESC';}
	if (empty($limit)) {$limit = "";}
	$resultLog = select_query('mod_autounblockcsf','user,server,request,ip,action,description,dateandtime',$sqlWhere,$orderBy,$orderSort,$limit);
	$i=0;
	while($rowLog = mysql_fetch_array($resultLog)){
		$i++;
		$resultserver = select_query('tblservers','name',array('id'=>$rowLog['server']));
		$rowserver = mysql_fetch_array($resultserver);

		$output[$i]['serverid'] = $rowLog['server'];
		$output[$i]['servername'] = $rowserver['name'];
		$output[$i]['dateandtime'] = $rowLog['dateandtime'];
		$output[$i]['action'] = $rowLog['action'];
		$output[$i]['ip'] = $rowLog['ip'];
		$output[$i]['description'] = $rowLog['description'];
		$output[$i]['request'] = $rowLog['request'];
		
		$userParts = explode("|",$rowLog['user']);
		if ($userParts[0] == 'admin') {
			$output[$i]['user'] = autounblockcsf_id2username($userParts[1],$userParts[0]);
		} else {
			$output[$i]['user'] = '<a href="clientssummary.php?userid='.$userParts[1].'">'.autounblockcsf_id2username($userParts[1],$userParts[0]).'</a>';
		}
		if ($userParts[0] == 'pipe') {
			$output[$i]['request'] = 'pipe';
		}
	}
	return $output;
}

function autounblockcsf_DOM2Array($curr_node) {
	$val_array = array();
	$type_array = array();
	foreach($curr_node->childNodes as $node) {
		if ($node->nodeType == XML_ELEMENT_NODE) {
			$val = autounblockcsf_DOM2Array($node);
			if (array_key_exists($node->tagName, $val_array)) {
				if (!is_array($val_array[$node->tagName]) || $type_array[$node->tagName] == 'hash') {
					$existing_val = $val_array[$node->tagName];
					unset($val_array[$node->tagName]);
					$val_array[$node->tagName][0] = $existing_val;
					$type_array[$node->tagName] = 'array';
				}
				$val_array[$node->tagName][] = $val;
			} else {
				$val_array[$node->tagName] = $val;
				if (is_array($val)) {
					$type_array[$node->tagName] = 'hash';
				}
			} // end if array key exists
		} // end if elment node
	}// end for each
	if (count($val_array) == 0) {
		return $curr_node->nodeValue;
	} else {
		return $val_array;
	}
}

function autounblockcsf_getClientServers($userid) {
	$modulesArr = autounblockcsf_serverModules();
	if (!is_array($modulesArr)) {$modulesArr = array();}
	$modulesArr['cpanel'] = '';
	$modulesArr['directadmin'] = '';
	$modulesArrT = implode("','", array_keys($modulesArr));

	$excludeArr = autounblockcsf_serverExclude();
	if (!is_array($excludeArr)) {$excludeArr = array();}
	$excludeArrT = implode("','", $excludeArr);

	$resultServerInfo = mysql_query("SELECT DISTINCT tblservers.id, tblservers.name, tblservers.ipaddress, tblservers.hostname FROM tblservers INNER JOIN tblhosting ON tblservers.id=tblhosting.server WHERE userid=".(int)$userid." AND domainstatus = 'Active' AND tblservers.type IN ('".$modulesArrT."') AND tblservers.id NOT IN ('".$excludeArrT."') ORDER BY tblservers.id");
	while ($rowServerInfo = mysql_fetch_array($resultServerInfo)) {
		$serverList['servers'][$rowServerInfo['id']] = array(
			'id'=>$rowServerInfo['id'],
			'name'=>$rowServerInfo['name'],
			'ipaddress'=>$rowServerInfo['ipaddress'],
			'hostname'=>$rowServerInfo['hostname']
		);
	}
	if (!is_array($serverList)) {
		$serverList['status'] = '0';
		$serverList['errors'] = 'Cannot retrive servers data. Server list is empty.';
	}
	return $serverList;
}

function autounblockcsf_getCpServers($getall=false) {
	if (!$_SESSION['adminid']) {
		return 'Admin function only';
	}
	$modulesArr = autounblockcsf_serverModules();
	if (!is_array($modulesArr)) {$modulesArr = array();}
	$modulesArr['cpanel'] = '';
	$modulesArr['directadmin'] = '';
	$modulesArrT = implode("','", array_keys($modulesArr));
	if ($getall) {
		$sqlText = '';
	} else {
		$excludeArr = autounblockcsf_serverExclude();
		if (!is_array($excludeArr)) {$excludeArr = array();}
		$excludeArrT = implode("','", $excludeArr);
		$sqlText = " AND id NOT IN ('".$excludeArrT."')";
		if (!autounblockcsf_allowDisabled()) {$sqlText .= " AND disabled <> 1";}
	}
	
	$resultServerInfo = mysql_query("SELECT id,name,ipaddress,hostname,type FROM tblservers WHERE type IN ('".$modulesArrT."')".$sqlText);
	while ($rowServerInfo = mysql_fetch_array($resultServerInfo)) {
		$allowType = $modulesArr[$rowServerInfo['type']];
		$moduletype = $rowServerInfo['type'];
		if (empty($allowType)) {
			$type = $moduletype;
			$real = '';
		} else {
			$type = $allowType;
			$real = $moduletype;
		}
		$serverList['servers'][$rowServerInfo['id']] = array(
			'id'=>$rowServerInfo['id'],
			'name'=>$rowServerInfo['name'],
			'ipaddress'=>$rowServerInfo['ipaddress'],
			'hostname'=>$rowServerInfo['hostname'],
			'type'=>$type,
			'realtype'=>$real
		);
	}
	if (!is_array($serverList)) {
		$serverList['status'] = '0';
		$serverList['errors'] = 'Cannot retrive servers data. Server list is empty.';
		return $serverList;
	}
	$serverList['status'] = '1';
	return $serverList;
}

/////////// V 2.6.x /////////////////
function autounblockcsf_serverModules() {
    $getCustom = select_query('tbladdonmodules','value',array('module'=>'autounblockcsf','setting'=>'server_modules'));
    $rowCustom = mysql_fetch_array($getCustom);              
    if (!empty($rowCustom['value'])) {                    
        $modulesArrP = explode(',', $rowCustom['value']);
        foreach ($modulesArrP as $value) {
            list($k,$v) = explode('|', $value);
            $modulesArr[$k] = $v;
        }
    }
    return $modulesArr;
}

function autounblockcsf_getProvModules() {
	if (!$_SESSION['adminid']) {
		return 'Admin function only';
	}
	$resultServerInfo = mysql_query("SELECT DISTINCT type FROM tblservers WHERE type NOT IN ('cpanel','directadmin','cpanelautomaticip','ahsaybackups','castcontrol','centovacast','cloudmin','dotnetpanel','enomssl','enomtruste','ensimx','fluidvm','gamecp','globalsignssl','globalsignvouchers','gsppanel','heartinternet','helm','helm4','hostingcontroller','hypervm','interworx','licensing','lxadmin','mediacp','plesk10','plesk8','plesk9','pleskreseller','resellercentral','resellerclubssl','tcadmin','veportal','virtualmin','vpsnet','websitepanel','xpanel')");
	while ($rowServerInfo = mysql_fetch_assoc($resultServerInfo)) {
		$serverList['servers'][] = array(
			'type'=>$rowServerInfo['type']
		);
	}
	if (!is_array($serverList)) {
		$serverList['status'] = '0';
		$serverList['errors'] = 'Server provisioning modules list is empty. You must have at list one active server.';
		return $serverList;
	}
	$serverList['status'] = '1';
	return $serverList;
}

/////////// V 2.6.5 /////////////////
function autounblockcsf_serverExclude() {
		$getExclude = select_query('tbladdonmodules','value',array('module'=>'autounblockcsf','setting'=>'server_exclude'));
		$rowExclude = mysql_fetch_assoc($getExclude);
		if (!empty($rowExclude['value'])) {
			$excludeArr = explode(",", $rowExclude['value']);
		}
		return $excludeArr;
}

///////////////////////////////
function autounblockcsf_DAconnect($host, $user, $pass, $accessHash, $csfaction, $secure, $customport, $csfpost = false) {
    if (empty($host) || empty($user) || empty($pass)) {
        $result['status'] = '0';
        $result['errors'] = 'Not enoght data to connect to server '.$host;
        return $result;
    }    
    if ($user == 'admin') {
        $password = html_entity_decode(decrypt($pass));
    } else {
        $password = html_entity_decode($accessHash);
    }
	//Based on: http://forum.directadmin.com/showthread.php?t=3782
	$request = '/CMD_PLUGINS_ADMIN/csf/index.html'.$csfaction;
	$method = ($csfpost) ? 'POST' : 'GET';
	$port = ($customport) ? $customport: '2222';

	$headers = '';
	//$headers[] = "Content-Type: application/x-www-form-urlencoded";
	//$headers[] = 'Content-length: '.strlen($csfpost);
	$headers[] = 'Authorization: Basic '.base64_encode("admin:$password");
	$ch = curl_init();
	if ($secure) {
		$url = 'https://'.$host.':'.$port;
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		//curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, implode(':', autounblockcsf_sslCiphers()));
	} else {
		$url = 'http://'.$host.':'.$port;
	}
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method.' '.$request.' HTTP/1.1');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Time out sec.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	if ($csfpost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $csfpost);
	}
	$result['data'] = curl_exec($ch);
	if (curl_errno($ch)) {
		$result['status'] = '0';
		$result['errors'] = curl_error($ch) . ' (Code ' . curl_errno($ch) . ') - Connection to DirectAdmin server has failed';
	} else {
		$result['status'] = '1';
	}
	curl_close($ch);
	return $result;
}
                            
function autounblockcsf_CPconnect($host, $user, $pass, $accessHash, $csfaction, $secure, $customport, $appconfig = true, $csfpost = false) {
    $decryptpass = decrypt($pass);
    if (empty($host) || empty($user) || (!empty($host) && !empty($user) && empty($decryptpass) && empty($accessHash) )) {
        $result['status'] = '0';
        $result['errors'] = 'Not enoght data to connect to server '.$host;
        return $result;
    }
    if ($appconfig) {
        $request = '/cgi/configserver/csf.cgi' . $csfaction;
    } else {
        $request = '/cgi/addon_csf.cgi' . $csfaction;
    }
    // Connect and get results
    if (!empty($accessHash)) {
        $authstr = 'WHM ' . $user . ':' . $accessHash;
    } else {
        $authstr = 'Basic ' . base64_encode($user . ':' . $decryptpass);
    }
    $ch = curl_init();
    if ($secure) {
		$port = ($customport) ? $customport: '2087';
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		//curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, implode(':', autounblockcsf_sslCiphers()));
        curl_setopt($ch, CURLOPT_URL, '' . 'https://' . $host . ':'.$port . $request);
    } else {
		$port = ($customport) ? $customport: '2086';
        curl_setopt($ch, CURLOPT_URL, '' . 'http://' . $host . ':'.$port . $request);
    }
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $curlheaders[0] = '' . 'Authorization: ' . $authstr;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheaders);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    if ($csfpost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $csfpost);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	$result['data']  = curl_exec($ch);
    if (curl_errno($ch)) {
        $result['status'] = '0';
        $result['errors'] = curl_error($ch) . ' (Code ' . curl_errno($ch) . ') - Connection to cPanel server has failed';
    } else {
        $result['status'] = '1';
    }
    curl_close($ch);
    return $result;
}

function getAutoUnblock($actionIP,$action,$serverID,$userid=0,$appconfig=true,$LANG=false) {
	// get the ip address that have perform the request
	global $remote_ip;
	$userip = $remote_ip;

	// If action is not allowed return error and quit
	$allowActions = array('grep','kill','qkill','qallow','qdeny','qignore','remove','autounblock','testconn','viewlogs','restartq','lfdstatus','lfdrestart','enable','disable','denyf','chart');
	if (!in_array($action, $allowActions)) {
		$resultData['status'] = '0';
		$resultData['errors'] = $LANG['Nosuchaction'].': '.$action;
		return $resultData;
	}
	// If ip cannot be empty return error and quit
	$allowNoipActions = array('testconn','viewlogs','restartq','lfdstatus','lfdrestart','enable','disable','denyf','chart');
	if (empty($actionIP) && !in_array($action, $allowNoipActions)) {
		$resultData['status'] = '0';
		$resultData['errors'] = $LANG['noIperr'];
		return $resultData;
	}

	// Get server info
	if (empty($serverID)) {
		$resultData['status'] = '0';
		$resultData['errors'] = $LANG['noSerErr'];
		return $resultData;
	}

	// Get server info or return error and quit
	if (is_array($serverID)) {
		$rowServerInfo = $serverID;
	} else {
		$resultServerInfo = select_query('tblservers','*',array('id'=>$serverID));
		$rowServerInfo = mysql_fetch_assoc($resultServerInfo);
	}
	if (!$rowServerInfo) {
		$resultData['status'] = '0';
		$resultData['errors'] = $LANG['csfGetConfE1'].' '.$LANG['csfGetConfE2'].': '.$serverID;
		return $resultData;
	}
	
	$modulesArr = autounblockcsf_serverModules();
	$allowType = $modulesArr[$rowServerInfo['type']];
	$servertype = (empty($allowType)) ? $rowServerInfo['type'] : $allowType;
	if ($rowServerInfo['ipaddress'])	{$hostIP = trim($rowServerInfo['ipaddress']);}
	if ($rowServerInfo['hostname'])		{$hostName = trim($rowServerInfo['hostname']);}
	if ($rowServerInfo['username'])		{$user = trim($rowServerInfo['username']);}
	if ($rowServerInfo['password'])		{$pass = trim($rowServerInfo['password']);}
	if ($rowServerInfo['accesshash'])	{$accessHash = trim($rowServerInfo['accesshash']);}
	if ($accessHash)					{$cleanAccessHash = str_replace(array("\r", "\r\n", "\n"), '', $accessHash);}
	if ($rowServerInfo['secure'])		{$secure = trim($rowServerInfo['secure']);}
	if ($rowServerInfo['port'])			{$customport = trim($rowServerInfo['port']);} else {$customport = false;}
	$resultData['hostName'] = $hostName;
	$resultData['hostIP'] = $hostIP;
            
	if (empty($actionIP)) {
		if ($action == 'testconn') {
			$csfaction = '';
		} else {
			$csfaction = '?action='.$action;
		}
	} else {
		$actionIP = inet_ntop(inet_pton(trim($actionIP)));
		$ipver = autounblockcsf_validIpAddress($actionIP);
		if (!$ipver) {
			$resultData['status'] = '0';
			$resultData['errors'] = $LANG['validIPaddress'];
			$resultData['iperrors'] = true;
			return $resultData;
		}
		if ( !empty($GLOBALS['autounblockcsf']['POST']['comment']) && ($action == 'qallow' || $action == 'qdeny') ) {
			$comment = str_replace(' ', '%20', $GLOBALS['autounblockcsf']['POST']['comment']);
			$csfaction = '?action='.$action.'&ip='.$actionIP.'&comment='.$comment;
		} else {
			$csfaction = '?action='.$action.'&ip='.$actionIP;
		}
	}
	if ($action	== 'autounblock') {
		$action	= 'remove';
		$autounblock = '1';
	}
	if ($action	== 'remove') {
		$csfaction = '?action=grep&ip='.$actionIP;
	}
	//if ($action	== 'chart') {
		//$csfaction = '?action=grep&text=1336488580';
	//}
	
	$connAddress = autounblockcsf_connaddress();
	if ($connAddress) {
		if ($connAddress == 'ip' && !empty($hostIP)) {
			$host = $hostIP;
		} elseif ($connAddress == 'ip' && empty($hostIP)) {
			$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE3'].'!';
			return $result;
		} elseif ($connAddress == 'host' && !empty($hostName)) {
			$host = $hostName;
		} elseif ($connAddress == 'host' && empty($hostName)) {
			$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE4'].'!';
			return $result;
		} else {
			$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$connAddress.' '.$host;
			return $result;
		}
	} else {
		$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE5'].'!';
		return $result;
	}
	if ($servertype == 'directadmin') {
		$data = autounblockcsf_DAconnect($host,$user,$pass,$accessHash,$csfaction,$secure,$customport);
	} elseif ($servertype == 'cpanel') {
		$data = autounblockcsf_CPconnect($host,$user,$pass,$cleanAccessHash,$csfaction,$secure,$customport);
		if ($data['status'] == '0') {
			// If app config is not active on cPanel a 404 page is returned 
			$arr404 = autounblockcsf_getTextArrayFromDomTag('h1', $data['data']);
			if ($arr404[0] == 'HTTP error 404') {
				$data = autounblockcsf_CPconnect($host,$user,$pass,$cleanAccessHash,$csfaction,$secure,$customport,false);
			}
		}
	}
	$resultData['status'] = $data['status'];
	// If connection error then quit, else continue
	if ($resultData['status'] != '1') {
		$resultData['errors'] = $data['errors'];
		return $resultData;
	} elseif ( strstr($data['data'], 'The login is invalid') || strstr($data['data'], 'DirectAdmin Login Page') ) {
		$resultData['status'] = '0';
		$resultData['errors'] = 'The server login details is invalid';
		return $resultData;
	} else {
		if ($data['data'] !== false) {
			if ($_SESSION['uid'] && $userid > 0) {
				$scriptUser = 'client';
				$scriptUserLog = 'client|'.$_SESSION['uid'];
				$scriptUID = $_SESSION['uid'];
			} elseif (!isset($_SESSION['uid']) && $userid > 0) {
				$scriptUser = 'pipe';
				$scriptUserLog = 'pipe|'.$userid;
				$scriptUID = $userid;
			} elseif ($_SESSION['adminid'] && $userid == 0) {
				$scriptUser = 'admin';
				$scriptUserLog = 'admin|'.$_SESSION['adminid'];
				$scriptUID = $_SESSION['adminid'];
			} else {
				$scriptUser = 'na';
				$resultData['status'] = '0';
				$resultData['data'] = $scriptUserLog;
				$resultData['errors'] = $LANG['noValUser'];
				return $resultData;
			}
			$dataArr = explode("\n", $data['data']);
			$resultData['user'] = $scriptUser;
			//if (file_exists(ROOTDIR.'/modules/addons/autounblockcsf/debug')) {print_r($dataArr);} ///////////////// Debug
			if (empty($actionIP) && ($action == 'testconn')) {
				$printMessage = '';
				if ($servertype == 'directadmin') {
					$returnArr = autounblockcsf_getTextArrayFromDomTag('th', $data['data']);
				} else {
					$returnArr = autounblockcsf_getTextArrayFromDomTag('table', $data['data']);
				}
				$returnArrTxt = trim(strip_tags($returnArr[0]));
				if (strstr($returnArrTxt, 'Firewall Status: Enabled and Running')) {
					$printMessage = $hostName.' '.str_replace("Enabled and Running", '<br/><strong style="color:green">Enabled and Running</strong>', $returnArrTxt);
				} elseif (strstr($returnArrTxt, 'Firewall Status: Disabled and Stopped')) {
					$printMessage = $hostName.' '.str_replace("Disabled and Stopped", '<br/><strong style="color:red">Disabled and Stopped</strong>', $returnArrTxt);
				} elseif (strstr($returnArrTxt, 'csf - ConfigServer Firewall')) {
					$printMessage = 'csf - ConfigServer Firewall';
				} elseif (strstr($data['data'], 'You do not have access to ConfigServer Firewall')) {
					$resultData['status'] = '0';
					$resultData['errors'] = 'You do not have access to ConfigServer Firewall.<br/><span style="font-size:12px">Suggestion: Verify that privileges has been assigned to the reseller accounts by editing the csf.resellers file.<span>';
				} elseif (strstr($data['data'], 'You do not have permission to access this page')) {
					$resultData['status'] = '0';
					$resultData['errors'] = 'You do not have permission to access this page.<br/><span style="font-size:12px">Suggestion: Make sure that "ConfigServer Security & Firewall (Reseller UI)" is checked in the reseller privileges.<span>';
				} else {
					$resultData['status'] = '0';
					$resultData['errors'] = 'Unknown Error';
				}
				
				if ($resultData['status'] != '0') {
					if ($user == 'root' || $servertype == 'directadmin') {
						$resultData['privileges'] = true;
					} else {
						if (cifisroot($user,$pass,$cleanAccessHash,$host,$secure,$customport)) {
							$resultData['privileges'] = true;
						} else {
							$resultData['privileges'] = false;
						}
					}
				}
				$resultData['action'] = $action;
				$resultData['data'] = $printMessage;
				//if (file_exists(ROOTDIR.'/modules/addons/autounblockcsf/debug')) {print_r($returnArr); echo '<br/><br/>'.$returnArrTxt;} ///////////////// Debug
				return $resultData;
			}

			if (empty($actionIP) && ($action == 'chart')) {
				$doc = new DOMDocument();
				$doc->preserveWhiteSpace = false;
				$doc->loadHTML($data['data']);
				$arrFromDom = autounblockcsf_DOM2Array($doc->documentElement);
				$resultData['action'] = $action;
				$resultData['data'] = $arrFromDom['body']['table']['tr'][0]['td']['p'][0]['img'];
				return $resultData;
			}

			if (empty($actionIP) && ($action == 'viewlogs')) {
				if ($servertype == 'directadmin') {
					$printMessage = '';
					$doc = new DOMDocument();
					$doc->preserveWhiteSpace = false;
					$doc->loadHTML($data['data']);
					$arrFromDom = autounblockcsf_DOM2Array($doc->documentElement);
					$DAarrFromDom = $arrFromDom['body']['table']['tr']['td']['table']['tr']['td'][1]['table']['tr'][1]['td']['table'][1]['tr']['td'];
					if (trim($DAarrFromDom['p'][0]) == 'No logs entries found') {
						$resultData['data'] = $DAarrFromDom['p'][0];
					} elseif (is_array($DAarrFromDom['p'][0])) {
						$resultData['title'] = 'Last 100 iptables logs on '.$hostName.', latest: <strong>'.$DAarrFromDom['p'][0]['big']['b'][0].'</strong> oldest: <strong>'.$DAarrFromDom['p'][0]['big']['b'][1].'</strong>';
						$resultData['data'] = $DAarrFromDom['table']['tr'];
					} else {
						$resultData['status'] = '0';
						$resultData['errors'] = '<strong style="color:red">'.$LANG['CannotRetLogs'].'</strong>';
					}
					$resultData['action'] = $action;
					return $resultData;
				} else {		
					$printMessage = '';
					$doc = new DOMDocument();
					$doc->preserveWhiteSpace = false;
					$doc->loadHTML($data['data']);
					$arrFromDom = autounblockcsf_DOM2Array($doc->documentElement);
					if (trim($arrFromDom['body']['p'][0]) == 'No logs entries found') {
						$resultData['data'] = $arrFromDom['body']['p'][0];
					} elseif (is_array($arrFromDom['body']['p'][0])) {
						$resultData['title'] = 'Last 100 iptables logs on '.$hostName.', latest: <strong>'.$arrFromDom['body']['p'][0]['big']['b'][0].'</strong> oldest: <strong>'.$arrFromDom['body']['p'][0]['big']['b'][1].'</strong>';
						$resultData['data'] = $arrFromDom['body']['table']['tr'];
					} else {
						$resultData['status'] = '0';
						$resultData['errors'] = '<strong style="color:red">'.$LANG['CannotRetLogs'].'</strong>';
					}
					$resultData['action'] = $action;
					return $resultData;
				}
			}

			if ( empty($actionIP) && ($action == 'restartq' || $action == 'lfdstatus' || $action == 'lfdrestart' || $action == 'enable' || $action == 'disable' || $action == 'denyf') ) {
				$printMessage = '';
				$returnArr = autounblockcsf_getTextArrayFromDomTag('pre', $data['data']);
				$returnArrTxt = trim(strip_tags($returnArr[0]));
				$actionArr = autounblockcsf_getTextArrayFromDomTag('p', $data['data']);
				if ($servertype == 'cpanel') {
					$actionArrTxt = trim(strip_tags($actionArr[0]));
				} else {
					$actionArrTxt = trim(strip_tags($actionArr[0]));
				}
				if ($action == 'restartq') {
					if (strstr($actionArrTxt, 'Restarting csf via lfd')) {
						$printMessage = 'Restarting csf via lfd on '.$hostName.'...<br/><br/>lfd will restart csf within the next 5 seconds';
					} else {
						$printMessage = 'Restarting csf via lfd on '.$hostName.'... <strong style="color:red">Failed</strong>';
						$resultData['status'] = '0';
					}
				} elseif ($action == 'lfdstatus') {
					if (strstr($actionArrTxt, 'Show lfd status')) {
						$printMessage = 'Show lfd status on '.$hostName.'...<br/><br/>'.str_replace("\n", '<br/>', $returnArrTxt);
					} else {
						$printMessage = 'Show lfd status on '.$hostName.'... <strong style="color:red">Failed</strong>';
						$resultData['status'] = '0';
					}
				} elseif ($action == 'lfdrestart') {
					if ( strstr($actionArrTxt, 'Restarting lfd') || strstr($actionArrTxt, 'Signal lfd to restart') ) {
						if ($servertype == 'directadmin') {
							$returnArrTxt = trim(strip_tags($actionArr[3]));
						}
						$printMessage = 'Restarting lfd on '.$hostName.'...<br/><br/>'.str_replace("\n", '<br/>', $returnArrTxt);
					} else {
						$printMessage = 'Restarting lfd on '.$hostName.'... <strong style="color:red">Failed</strong>';
						$resultData['status'] = '0';
					}
				} elseif ($action == 'enable') {
					if (strstr($actionArrTxt, 'Enabling csf')) {
						if (strstr($returnArrTxt, 'csf and lfd are not disabled')) {
							$printMessage = 'Enabling csf on '.$hostName.'...<br/><br/>csf and lfd are not disabled!';
						} else {
							$printMessage = 'Enabling csf on '.$hostName.'...<br/><br/>Done!';
						}
					} else {
						$printMessage = 'Enabling csf on '.$hostName.'... <strong style="color:red">Failed</strong>';
						$resultData['status'] = '0';
					}
				} elseif ($action == 'disable') {
					if (strstr($actionArrTxt, 'Disabling csf')) {
						$printMessage = 'Disabling csf on '.$hostName.'...<br/><br/>Done!';
					} else {
						$printMessage = 'Disabling csf on '.$hostName.'... <strong style="color:red">Failed</strong>';
						$resultData['status'] = '0';
					}
				} elseif ($action == 'denyf') {
					if (strstr($actionArrTxt, 'Removing all entries from csf.deny')) {
						$printMessage = 'Removing all entries from csf.deny on '.$hostName.'...<br/><br/>'.str_replace("\n", '<br/>', $returnArrTxt);
					} else {
						$printMessage = 'Removing all entries from csf.deny on '.$hostName.'... <strong style="color:red">Failed</strong>';
						$resultData['status'] = '0';
					}
				}
				$resultData['action'] = $action;
				$resultData['data'] = $printMessage;
				//print_r($actionArr); echo '<br/><br/>'.$actionArr[0];//Debug
				return $resultData;
			}

			// Return Action Results
			if ($action == 'kill' || $action == 'qkill') {
				foreach($dataArr as $logLine) {
					$cleanline = trim(strip_tags($logLine));
					if (strstr($cleanline, $actionIP)) {
						$printMessage .= $cleanline.'<br/>';
					}
				}
			}
			if ($action == 'grep' || $action == 'remove') {
				$i=0;
				foreach($dataArr as $logLine) {
					if (strstr($logLine, $actionIP)) {
						$cleanline = trim(strip_tags($logLine));
						//$iparr[] = $cleanline; // Debug
						if (strstr($cleanline, 'DENYIN') || strstr($cleanline, 'csf.deny: '.$actionIP) || strstr($cleanline, 'Temporary Blocks: IP:'.$actionIP)) {
							$resultData['line'] = $cleanline;
							if (strstr($cleanline, 'csf.deny: '.$actionIP)) {
								$log_ipParts = explode(' ',trim(substr($cleanline, 0, strpos($cleanline, '#'))));
								$log_ip = trim($log_ipParts[1]);
								$log_desc = trim(substr($cleanline,strpos($cleanline,'#')+2, strrpos($cleanline,'-')-strpos($cleanline,'#')-2));
								$log_time = strrchr($cleanline,'-');
								$log_timeParts = explode(' ',trim(substr($log_time,2)));
								$log_date = trim($log_timeParts[0]).' '.trim($log_timeParts[1]).' '.trim($log_timeParts[2]).' '.trim($log_timeParts[4]);
								$log_time = trim($log_timeParts[3]);
								$printMessage .= $log_date.' - '.$log_time.'<br />'.$hostName.' - '.$hostIP.'<br />'.$log_ip.'<br />'.$log_desc.'<br/>';
							} elseif (strstr($cleanline, 'DENYIN') || strstr($cleanline, 'Temporary Blocks: IP:'.$actionIP)) {
								$printMessage = $cleanline;
								$log_desc = $cleanline;
								$log_ip = $actionIP;
							}
                                                        if ($printMessage) {
                                                            $resultData['log']['Host_Name'] = $hostName;
                                                            $resultData['log']['Server_IP'] = $hostIP;
                                                            $resultData['log']['Date'] = $log_date;
                                                            $resultData['log']['Time'] = $log_time;
                                                            $resultData['log']['Client_IP'] = $log_ip;
                                                            $resultData['log']['Reason_Blocked'] = $log_desc;
                                                            $resultData['log']['status'] = '1';
                                                        }
							if ($action == 'remove') {
								$allowUnblock = '1';
								if ((strstr($cleanline, 'Manually denied') || strstr($cleanline, '(Manually added)')) && $scriptUser != 'admin') {
									$allowUnblock = '0';
									$printMessage = 'Remove failed...<br/>'.$printMessage;
									$resultData['log']['status'] = '0';
								} else {
									$printMessage = 'Removing rule...<br/>'.$printMessage;
								}
							}
						} elseif ( ($i==0) && strstr($cleanline, 'ALLOWIN') ) {
                                                        $resultData['allow'] = true;
                                                        $printMessage = 'The IP addresses '.$actionIP.' is allowed in the server firewall';
                                                        $i++;
						} elseif ( ($i==0) && strstr($cleanline, 'No matches found for') ) {
							if (!strstr($data['data'], 'DENYIN')) {
								$printMessage = 'Searching for '.$actionIP.'...<br/>No matches found in iptables';
								$resultData['line'] = 'No matches found in iptables';
								$i++;
							}
                                                }
					}
				} 
				// end foreach
				//$resultData['line'] = $resultData['line'].'<br/><br/>'.print_r($iparr,true);//Debug
				//$printMessage = $printMessage.'<br/><br/>'.print_r($iparr,true);//Debug
                //print_r($iparr); //Debug
			} elseif ($action == 'qallow' || $action == 'qdeny' || $action == 'qignore') {
				foreach($dataArr as $logLine) {
					if (strstr($logLine, 'ConfigServer Security') && $action != 'qignore' && $servertype == 'directadmin') {
						continue;
					} elseif (strstr($logLine, 'ConfigServer Security') && $action == 'qignore' && $servertype == 'directadmin') {
						$texpartS = explode("Ignoring", trim(strip_tags($logLine)));
						//$removeThis = $texpartS[0];
						$cleanline = 'Ignoring '.$texpartS[1];
					} else {
						$cleanline = trim(strip_tags($logLine));
					}
					if (strstr($cleanline, $actionIP)) {
						if ($action == 'qallow') {
							if (strstr($cleanline, 'Allowing '.$actionIP) || strstr($cleanline, 'Removing') || strstr($cleanline, 'Adding') || strstr($cleanline, 'add failed')) {
								$printMessage .= $cleanline.'<br/>';
							}
						} elseif ($action == 'qdeny') {
							if (strstr($cleanline, 'Blocking '.$actionIP) || strstr($cleanline, 'Adding') || strstr($cleanline, 'deny failed')) {
								$printMessage .= $cleanline.'<br/>';
							}
						} else {
							$printMessage .= $cleanline.'<br/>';
						}
					}
				}
				if (strstr($data['data'], 'deny failed:') || strstr($data['data'], 'add failed:')) {
					$resultData['status'] = '0';
				}
			}

			// End Return Action Results
			if ($allowUnblock == '1') {
				if ($user == 'root' || $servertype == 'directadmin') {
					$action = 'kill';
				} else {
					if (cifisroot($user,$pass,$cleanAccessHash,$host,$secure,$customport)) {
						$action = 'kill';
					} else {
						$action = 'qkill';
					}
				}
				$getKillResults = getAutoUnblock($actionIP,$action,$serverID,$userid,true,$LANG);
				if ($getKillResults['status'] == '1') {
					$printMessage = $getKillResults['data'];
				}
			}

			if (empty($printMessage)) {
				$resultData['status'] = '0';
				//$printMessage = 'No massage...<br/>';
			}

			// Insert log to the database
			if ($resultData['status'] == '1' && ( $action == 'qallow' || $action == 'qdeny' || $action == 'qignore'  || ( ( $action == 'kill' || $action == 'qkill') && $allowUnblock == '1') ) ) {
				if ($autounblock == '1') {
					$actionText = 'autounblock';
				} else {
					$actionText = $action;
				}
				$newRow = insert_query('mod_autounblockcsf',array('user'=>$scriptUserLog,'server'=>$serverID,'request'=>$userip,'ip'=>$actionIP,'action'=>$actionText,'description'=>$resultData['line']));
				if ($newRow) {
					$resultAlertInfo = select_query('tbladdonmodules','setting,value',array('module'=>'autounblockcsf'));
					while ($rowAlertInfo = mysql_fetch_assoc($resultAlertInfo)) {
						if ($rowAlertInfo['setting'] == 'unblock_email') {$emailA = $rowAlertInfo['value'];}
						if ($rowAlertInfo['setting'] == 'from_email') {$emailF = $rowAlertInfo['value'];}
						if ($rowAlertInfo['setting'] == 'client_alert') {$clientA = $rowAlertInfo['value'];}
						if ($rowAlertInfo['setting'] == 'admin_alert') {$adminA = $rowAlertInfo['value'];}
					}
					if (!empty($emailA)) {
						if ($scriptUser == 'admin' && ($adminA)) {
							$sendalert = true;
						} elseif (($scriptUser == 'client' || $scriptUser == 'pipe') && $clientA) {
							$sendalert = true;
						}
						if ($sendalert == true) {
							if ($action == 'kill' || $action == 'qkill') {
								$serveranswer = 'Removing rule...<br/>'.$resultData['line'].'<br/>';
							} else {
								$serveranswer = $printMessage;
							}
							$massage = '<p style="font-size:13px">Email alert has been triggered by '.ucfirst($scriptUser).' '.autounblockcsf_id2username($scriptUID,$scriptUser).' - '.$scriptUID.'</p><br/>
							<strong>Server:</strong> '.$hostName.' '.$hostIP.'<br/>
							<strong>Time:</strong> '.date('D M j Y G:i:s').'<br/>
							<strong>Action:</strong> '.autounblockcsf_action2Name($actionText).'<br/>
							<strong>Action IP:</strong> '.$actionIP.'<br/>
							<strong>Request IP:</strong> '.$userip.'<br/>
							<strong>Server Results:</strong><br/>
							'.$serveranswer.'<br/>
							--------------<br/>
							AutoUnblock csf
							';
							if (empty($emailF)) {$emailF = 'Autounblock@'.$GLOBALS['autounblockcsf']['ENV']['SERVER_NAME'];}
							$subject = 'Autounblock csf: '.$hostName.' '.autounblockcsf_action2Name($actionText).' '.$actionIP;
							$headers = "MIME-Version: 1.0" . "\r\n"; 
							$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
							$headers .= "From: $emailF " . "\r\n";
							mail($emailA,$subject,$massage,$headers);
						}
					}
				}
			}

			$resultData['action'] = $actionText;
			$resultData['data'] = $printMessage;
			// end results
		} else {
			// if data is empty
			$resultData['status'] = '0';
			$resultData['data'] = 'No data';
		}
	}
	return $resultData;
}

function cifisroot($user,$pass,$accessHash,$host,$secure,$customport) {
	$request = '/xml-api/listips';
	// Connect and get results
	if ($accessHash ) {
		$authstr = 'WHM ' . $user . ':' . $accessHash;
	} else {
		$authstr = 'Basic ' . base64_encode($user . ':' . $pass);
	}
	$ch = curl_init();
	if ($secure) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		//curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, implode(':', autounblockcsf_sslCiphers()));
		$port = ($customport) ? $customport: '2087';
        curl_setopt($ch, CURLOPT_URL, '' . 'https://' . $host . ':'.$port . $request);
	} else {
		$port = ($customport) ? $customport: '2086';
        curl_setopt($ch, CURLOPT_URL, '' . 'http://' . $host . ':'.$port . $request);
	}
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$curlheaders[0] = '' . 'Authorization: ' . $authstr;
	curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheaders);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	$data = curl_exec($ch);
	curl_close($ch);
	$listips = simplexml_load_string($data);
	if ($listips->statusmsg == 'Permission Denied') {
		return false;
	} else {
		return true;
	}
}

function autounblockcsf_serverProducts() {
    $productsArr = false;
    $getProducts = select_query('tbladdonmodules','value',array('module'=>'autounblockcsf','setting'=>'server_products'));
    $rowProducts = mysql_fetch_assoc($getProducts);
    if (!empty($rowProducts['value'])) {
        $productsArr = unserialize(trim($rowProducts['value']));
    }
    return $productsArr;
}

function autounblockcsf_getCSFconf($validservers,$serverID,$LANG) {
    $result = false;
    if ($serverID) {
        $modtype = false;
        $similarServers = '';
        $multipleText = '';
        $resultServerInfo = select_query('tblservers','*',array('id'=>$serverID));
        $rowServerInfo = mysql_fetch_assoc($resultServerInfo);
        if (!$rowServerInfo) {
            $result['errors'] = $LANG['csfGetConfE1'].' '.$LANG['csfGetConfE2'].': '.$serverID;
            return $result;
        }
        $hostIP = $rowServerInfo['ipaddress'];
        $hostName = $rowServerInfo['hostname'];
        $user = trim($rowServerInfo['username']);
        if ($validservers[$serverID]['type']) {$modtype = trim($validservers[$serverID]['type']);}
        if (!$modtype) {$modtype = trim($rowServerInfo['type']);}
        $pass = trim($rowServerInfo['password']);
        $accessHash = trim($rowServerInfo['accesshash']);
        $cleanAccessHash = str_replace(array("\r", "\r\n", "\n"), '', $accessHash);
        $secure = $rowServerInfo['secure'];
		if ($rowServerInfo['port'])	{$customport = trim($rowServerInfo['port']);} else {$customport = false;}
        $csfaction = '?action=conf';
	$connAddress = autounblockcsf_connaddress();
        if ($connAddress) {
            if ($connAddress == 'ip' && !empty($hostIP)) {
                $host = $hostIP;
            } elseif ($connAddress == 'ip' && empty($hostIP)) {
                $result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE3'].'!';
                return $result;
            } elseif ($connAddress == 'host' && !empty($hostName)) {
                $host = $hostName;
            } elseif ($connAddress == 'host' && empty($hostName)) {
                $result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE4'].'!';
                return $result;
            } else {
                $result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$connAddress.' '.$host;
                return $result;
            }
        } else {
            $result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE5'].'!';
            return $result;
        }
        if ($modtype == 'cpanel') {
            $data = autounblockcsf_CPconnect($host,$user,$pass,$cleanAccessHash,$csfaction,$secure,$customport);
        } elseif ($modtype == 'directadmin') {
            $data = autounblockcsf_DAconnect($host,$user,$pass,$accessHash,$csfaction,$secure,$customport);
        } else {
            $result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfServerType'].' '.$modtype.' '.$LANG['csfSTnotSupp'].'!';
        }
        if ($data['status'] != '1') {
            if ($data['status'] == '0') {
                $result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$data['errors'];
            }
            return $result;
        } else {
            if ($modtype == 'cpanel') {
                foreach ($validservers as $arrKey => $servers) {
                    foreach ($servers as $key => $value) {
                        if ($key == 'type' && $value == 'directadmin') {
                            unset($validservers[$arrKey]);
                        } elseif ($key == 'type' && $value == 'cpanel') {
                            if ($validservers[$arrKey]['id'] == $serverID) {$thisServer = ' checked';} else {$thisServer = '';}
                            $similarServers .= "<input ".$thisServer." name=\"additionalservers[".$validservers[$arrKey]['id']."]\" value=\"".$validservers[$arrKey]['name']."\" type=\"checkbox\" class=\"js-switch\"><span style=\"margin:0 10px;\">".$validservers[$arrKey]['name']."</span>";
                        }
                    }
                }
            } elseif ($modtype == 'directadmin') {
                foreach ($validservers as $arrKey => $servers) {
                    foreach ($servers as $key => $value) {
                        if ($key == 'type' && $value == 'cpanel') {
                            unset($validservers[$arrKey]);
                            continue;
                        } elseif ($key == 'type' && $value == 'directadmin') {
                            if ($validservers[$arrKey]['id'] == $serverID) {$thisServer = ' checked';} else {$thisServer = '';}
                            $similarServers .= "<input ".$thisServer." name=\"additionalservers[".$validservers[$arrKey]['id']."]\" value=\"".$validservers[$arrKey]['name']."\" type=\"checkbox\" class=\"js-switch\"><span style=\"margin:0 10px;\">".$validservers[$arrKey]['name']."</span>";
                        }
                    }
                }
            }
            if (!empty($similarServers)) {
                $multipleText = '<h2 style=\"font-weight: bold; color: black;\">'.$LANG['csfConSave'].':</h2>';
                $similarServers = '<div class="value-default">'.$similarServers.'</div><br/>';
            }
            $getVer = autounblockcsf_getTextArrayFromDomTag('pre', $data['data']);
            $result['csfvertion'] = substr($getVer[0], 5);
            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->loadHTML($data['data']);
            $csfForm = $doc->saveHTML($doc->getElementsByTagName('fieldset')->item(0));
            $csfForm = trim(str_replace(array("<form action=\"csf.cgi\" method=\"post\">", "<form action=\"/CMD_PLUGINS_ADMIN/csf/index.html\" method=\"post\">"), "<form action='addonmodules.php?module=autounblockcsf&addonaction=csfmanager' method='post'><input type='hidden' name='csftextvertion' value='".$result['csfvertion']."' /><input type='hidden' name='actionval' value='csfconfig' /><input type='hidden' name='server' value='".$serverID."' />", $csfForm));
            $csfForm = trim(str_replace("</form>", "<br/><div style=\"width: 95%; text-align: center; margin: auto auto;\">".$multipleText." ".$similarServers."<input class=\"input\" value=\"Change\" type=\"submit\"></div></form>", $csfForm));

            if ($modtype == 'cpanel') {
                $result['data'] = $doc->saveHTML($doc->getElementsByTagName('style')->item(1));
                $result['data'] .= $doc->saveHTML($doc->getElementsByTagName('script')->item(0));
                $result['data'] .= $doc->saveHTML($doc->getElementsByTagName('style')->item(2));
                $result['data'] .= $csfForm;
                $result['data'] .= $doc->saveHTML($doc->getElementsByTagName('script')->item(2));
            } elseif ($modtype == 'directadmin') {
                $result['data'] = $doc->saveHTML($doc->getElementsByTagName('style')->item(0));
                $result['data'] .= $doc->saveHTML($doc->getElementsByTagName('script')->item(2));
                $result['data'] .= $doc->saveHTML($doc->getElementsByTagName('style')->item(1));
                $result['data'] .= $csfForm;
                $result['data'] .= $doc->saveHTML($doc->getElementsByTagName('script')->item(4));
            }
        }
    }
    if (!$result) {
        $result['errors'] = $LANG['csfGetConfE6'].'!';
    }
    return $result;
}

function autounblockcsf_getCSFvertion($serverid,$textvertion,$modtype=false,$LANG) {
    $res['status'] = '0';
    if (!empty($serverid) && !empty($textvertion)) {
        if (is_numeric($serverid)) {
            $resultServerInfo = select_query('tblservers', '*', array('id' => $serverid));
            $rowServerInfo = mysql_fetch_assoc($resultServerInfo);
            if (!$rowServerInfo) {
                $res['errors'] = $LANG['csfVerError1'];
                return $res;
            }
            $hostIP = $rowServerInfo['ipaddress'];
            $hostName = $rowServerInfo['hostname'];
            if (!$modtype) {$modtype = trim($rowServerInfo['type']);}
            $user = trim($rowServerInfo['username']);
            $pass = trim($rowServerInfo['password']);
            $accessHash = trim($rowServerInfo['accesshash']);
            $cleanAccessHash = str_replace(array("\r", "\r\n", "\n"), '', $accessHash);
            $secure = $rowServerInfo['secure'];
			if ($rowServerInfo['port'])	{$customport = trim($rowServerInfo['port']);} else {$customport = false;}
            $csfaction = '';
			$connAddress = autounblockcsf_connaddress();
			if ($connAddress) {
				if ($connAddress == 'ip' && !empty($hostIP)) {
					$host = $hostIP;
				} elseif ($connAddress == 'ip' && empty($hostIP)) {
					$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE3'].'!';
					return $result;
				} elseif ($connAddress == 'host' && !empty($hostName)) {
					$host = $hostName;
				} elseif ($connAddress == 'host' && empty($hostName)) {
					$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE4'].'!';
					return $result;
				} else {
					$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$connAddress.' '.$host;
					return $result;
				}
			} else {
				$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE5'].'!';
				return $result;
			}
            if ($modtype == 'cpanel') {
                $data = autounblockcsf_CPconnect($host, $user, $pass, $cleanAccessHash, $csfaction, $secure, $customport);
            } elseif ($modtype == 'directadmin') {
                $data = autounblockcsf_DAconnect($host, $user, $pass, $accessHash, $csfaction, $secure, $customport);
            } else {
                $res['errors'] = $LANG['csfVerError2'].'!<br/>'.$LANG['csfServerType'].' '.$modtype.' '.$LANG['csfSTnotSupp'].'!';
            }
            if ($data['status'] == '0') {
                $res['errors'] = $LANG['csfVerError2'].'.<br/>' . $data['errors'];
            } elseif ($data['status'] == '1') {
                $getVer = autounblockcsf_getTextArrayFromDomTag('pre', $data['data']);
                $lTrueVertion = substr($getVer[0], 5);
                if ($lTrueVertion == $textvertion) {
                    $res['status'] = '1';
                } else {
                    $res['errors'] = $LANG['csfVerError3'].'!';
                }                
            }
        }
    }
    if (!$res['errors']) {
        $res['errors'] = $LANG['csfVerError2'].'!';
    }
    return $res;
}

function autounblockcsf_SaveCSFconf($serverid,$modtype=false,$LANG) {
    $res['status'] = '0';
    if (!empty($GLOBALS['autounblockcsf']['POST'])) {
        if (is_numeric($serverid)) {
            $resultServerInfo = select_query('tblservers','*',array('id'=>$serverid));
            $rowServerInfo = mysql_fetch_assoc($resultServerInfo);
            if (!$rowServerInfo) {return false;}
            $hostIP = $rowServerInfo['ipaddress'];
            $hostName = $rowServerInfo['hostname'];
            if (!$modtype) {$modtype = trim($rowServerInfo['type']);}
            $user = trim($rowServerInfo['username']);
            $accessHash = trim($rowServerInfo['accesshash']);
            $cleanAccessHash = str_replace(array("\r", "\r\n", "\n"), '', $accessHash);
            $secure = $rowServerInfo['secure'];
            $pass = trim($rowServerInfo['password']);
			if ($rowServerInfo['port'])	{$customport = trim($rowServerInfo['port']);} else {$customport = false;}
            $csfaction = '';
            $csfpost = '';
            foreach ($GLOBALS['autounblockcsf']['POST'] as $k=>$v) {
                if ($k == 'token') {continue;}
                if ($k == 'csftextvertion') {continue;}
                if ($k == 'actionval') {continue;}
                if ($k == 'server') {continue;}  
                if ($k == 'additionalservers') {continue;}  
                $csfpost .= trim($k).'='.trim(urlencode($v)).'&';
            }
            $csfpost = rtrim($csfpost, '&');

			$connAddress = autounblockcsf_connaddress();
			if ($connAddress) {
				if ($connAddress == 'ip' && !empty($hostIP)) {
					$host = $hostIP;
				} elseif ($connAddress == 'ip' && empty($hostIP)) {
					$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE3'].'!';
					return $result;
				} elseif ($connAddress == 'host' && !empty($hostName)) {
					$host = $hostName;
				} elseif ($connAddress == 'host' && empty($hostName)) {
					$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE4'].'!';
					return $result;
				} else {
					$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$connAddress.' '.$host;
					return $result;
				}
			} else {
				$result['errors'] = $LANG['csfGetConfE1'].'!<br/>'.$LANG['csfGetConfE5'].'!';
				return $result;
			}
            if ($modtype == 'cpanel') {
                $data = autounblockcsf_CPconnect($host,$user,$pass,$cleanAccessHash,$csfaction,$secure,$customport,true,$csfpost);
            } elseif ($modtype == 'directadmin') {
                $data = autounblockcsf_DAconnect($host,$user,$pass,$accessHash,$csfaction,$secure,$customport,$csfpost);
            } else {
                $res['data'] = $LANG['csfConfNoSaved'].'!<br/>'.$LANG['csfServerType'].' '.$modtype.' '.$LANG['csfSTnotSupp'].'!';
            }
            if ($data['status'] == '1') {
                if (preg_match("/Changes saved. You should restart both csf and lfd./i", $data['data'])) {
                    $res['status'] = '1';
                    $getResults = getAutoUnblock('','restartq',$serverid,0,true,$LANG);
                    sleep(2);
                    if ($getResults['status'] == '1') {
                        $res['data'] = $LANG['csfConfChSaved'].'.<br>'.$getResults['data'];
                    } else {
                        $res['data'] = $LANG['csfConfChSaved'].'. '.$LANG['csfConfRestMsg'].'.<br><br>'.$getResults['data'];
                    }
                } else {
                    $res['data'] = $LANG['csfConfNoSaved'].'.<br>';
                }
            } elseif ($data['status'] == '0') {
                $res['data'] = $LANG['csfConfNoSaved'].'!<br>'.$data['errors'];
            }
        } else {
            $res['data'] = $LANG['csfConfNoSaved'].'!<br>Server ID is not valid.';
        }
    } else {
        $res['data'] = $LANG['csfConfNoSaved'].'!<br>Post is empty.';
    }
    return $res;
}

function autounblockcsf_connaddress() {
        $connAddress = false;
	$getConnaddress = select_query('tbladdonmodules','value',array('module'=>'autounblockcsf','setting'=>'conn_address'));
	$rowConnaddress = mysql_fetch_assoc($getConnaddress);
        $connAddressVal = trim($rowConnaddress['value']);
        if ($connAddressVal == 'IP Address') {
            $connAddress = 'ip';
        } elseif ($connAddressVal == 'Hostname') {
            $connAddress = 'host';
        }
	return $connAddress;
}

function autounblockcsf_userProductServers($uid=false,$productArr=false) {
    $result = array();
    if (!$productArr) {$productArr = autounblockcsf_serverProducts();}
    if ($uid && $productArr) {
        $productArrKeys = array_keys($productArr);
        $productArrKeysText = join("','",$productArrKeys);
        $resultproducts = mysql_query("SELECT id,packageid,domain,username,password,dedicatedip FROM tblhosting WHERE userid=".(int)$uid." AND packageid IN ('".$productArrKeysText."') AND domainstatus = 'Active'");
        while($rowproducts = mysql_fetch_assoc($resultproducts)){
            // Get custom ssl info
            $getFieldSsl = select_query('tblcustomfields','id',array('relid'=>$rowproducts['packageid'],'description'=>'autounblock_secure'));
            $rowFieldSsl = mysql_fetch_assoc($getFieldSsl);
            $sslFieldID = trim($rowFieldSsl['id']);
            $getValSsl = select_query('tblcustomfieldsvalues','value',array('fieldid'=>$sslFieldID,'relid'=>$rowproducts['id']));
            $rowValSsl = mysql_fetch_assoc($getValSsl);
            if (isset($rowValSsl['value'])) {
                $sslVal = trim($rowValSsl['value']);
            } else {
                if ($productArr[$rowproducts['packageid']]['ssl'] == 'on') {
                    $sslVal = $productArr[$rowproducts['packageid']]['ssl'];
                } else {
                    $sslVal = '';
                }
            }
            // Get custom hash info
            $getFieldHash = select_query('tblcustomfields','id',array('relid'=>$rowproducts['packageid'],'description'=>'autounblock_hash'));
            $rowFieldHash = mysql_fetch_assoc($getFieldHash);
            $hashFieldID = trim($rowFieldHash['id']);
            $getValHash = select_query('tblcustomfieldsvalues','value',array('fieldid'=>$hashFieldID,'relid'=>$rowproducts['id']));
            $rowValHash = mysql_fetch_assoc($getValHash);
            $hashVal = trim($rowValHash['value']);
            if ($productArr[$rowproducts['packageid']]['type'] == 'cp') {
                $type = 'cpanel';
            } elseif ($productArr[$rowproducts['packageid']]['type'] == 'da') {
                $type = 'directadmin';
            }
            $result[$rowproducts['id']] = array(
                'productid'=>$rowproducts['id'],
                'packageid'=>$rowproducts['packageid'],
                'hostname'=>$rowproducts['domain'],
                'username'=>$rowproducts['username'],
                'password'=>$rowproducts['password'],
                'ipaddress'=>$rowproducts['dedicatedip'],
                'type'=>$type,
                'secure'=>$sslVal,
                'accesshash'=>$hashVal
            );
        }
    }
    return $result;
}

function autounblockcsf_checkLimit($userLimit, $allowSearch, $uid) {
    $userLimit = (int)trim($userLimit);
    if (!empty($userLimit)) {
        $results = false;
        $resultsUsage = select_query('mod_autounblockcsf', 'dateandtime', array('user' => 'client|' . $uid), 'dateandtime', 'DESC', '0,1');
        $rowUsage = mysql_fetch_assoc($resultsUsage);
        $lastUsage = (int)(strtotime($rowUsage['dateandtime']));
        $now = time();
        $secFromLastUsage = $now - $lastUsage;
        if ($secFromLastUsage < $userLimit) {
            if (!empty($allowSearch)) {
                $results['autoAction'] = 'grep';
            }
            $secToWait = $userLimit - $secFromLastUsage;
            $results['secToWait'] = $secToWait;
        } elseif ($secFromLastUsage > $userLimit) {
            $results['autoAction'] = 'autounblock';
        }
    } else {
        $results['autoAction'] = 'autounblock';
    }
    return $results;
}

function autounblockcsf_allowDisabled() {
	$allow = false;
	$getAllowDisabled = select_query('tbladdonmodules','value',array('module'=>'autounblockcsf','setting'=>'allow_disabled'));
	$rowAllowDisabled = mysql_fetch_assoc($getAllowDisabled);
	$allowDisabledVal = trim($rowAllowDisabled['value']);
	if ($allowDisabledVal == 'on') {
		$allow = true;
	}
	return $allow;
}

function autounblockcsf_sslCiphers() {
	$arrayCiphers = array(
		'DHE-RSA-AES256-SHA',
		'DHE-DSS-AES256-SHA',
		'AES256-SHA:KRB5-DES-CBC3-MD5',
		'KRB5-DES-CBC3-SHA',
		'EDH-RSA-DES-CBC3-SHA',
		'EDH-DSS-DES-CBC3-SHA',
		'DES-CBC3-SHA:DES-CBC3-MD5',
		'DHE-RSA-AES128-SHA',
		'DHE-DSS-AES128-SHA',
		'AES128-SHA:RC2-CBC-MD5',
		'KRB5-RC4-MD5:KRB5-RC4-SHA',
		'RC4-SHA:RC4-MD5:RC4-MD5',
		'KRB5-DES-CBC-MD5',
		'KRB5-DES-CBC-SHA',
		'EDH-RSA-DES-CBC-SHA',
		'EDH-DSS-DES-CBC-SHA:DES-CBC-SHA',
		'DES-CBC-MD5:EXP-KRB5-RC2-CBC-MD5',
		'EXP-KRB5-DES-CBC-MD5',
		'EXP-KRB5-RC2-CBC-SHA',
		'EXP-KRB5-DES-CBC-SHA',
		'EXP-EDH-RSA-DES-CBC-SHA',
		'EXP-EDH-DSS-DES-CBC-SHA',
		'EXP-DES-CBC-SHA',
		'EXP-RC2-CBC-MD5',
		'EXP-RC2-CBC-MD5',
		'EXP-KRB5-RC4-MD5',
		'EXP-KRB5-RC4-SHA',
		'EXP-RC4-MD5:EXP-RC4-MD5'
	);
	return $arrayCiphers;
}
?>