<?php
/*
*****************************************************
*******     Addon Module AutoUnblock csf      *******
Copyright (c) GK-root.com - All rights reserved   ***
v3.2.1 25/07/2015                                 ***
For more information please refer to:             ***
http://www.gk-root.com                            ***
*****************************************************
*/
if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

require(ROOTDIR."/modules/addons/autounblockcsf/autounblockcsf_functions.php");
//update_query('tbladdonmodules',array('value'=>'2.6.7'),array('module'=>'autounblockcsf','setting'=>'version'));

function autounblockcsf_config() {
	if (isset($GLOBALS['autounblockcsf']['GET']['goback'])) {
		$linkback = "<a href='".$GLOBALS['autounblockcsf']['ENV']['HTTP_REFERER']."'>&raquo; Go Back</a><br/>";
	}
	$configarray = array(
	"name" => "AutoUnblock csf",
	"description" => "This module provides a quick and easy way to Unblock IP from cPanel servers defined in WHMCS.",
	"version" => "3.2.1",
	"author" => "GK~root",
	"language" => "english",
	"fields" => array(
		"unblock_email" => array ("FriendlyName" => "To: Email address", "Type" => "text", "Size" => "30", "Description" => "Email address for action log to be sent", ),
		"from_email" => array ("FriendlyName" => "From: Email address", "Type" => "text", "Size" => "30", "Description" => "Sender email address (Leave blank for default - Autounblock@".$GLOBALS['autounblockcsf']['ENV']['SERVER_NAME'].")", ),
		"client_alert" => array ("FriendlyName" => "Client unblock alert", "Type" => "yesno", "Size" => "30", "Description" => "Send alerts for client unblock action", "Default" => "on", ),
		"admin_alert" => array ("FriendlyName" => "Admin action alert", "Type" => "yesno", "Size" => "30", "Description" => "Send alerts for admin actions", ),
		"select_whois" => array ("FriendlyName" => "Whois service", "Type" => "dropdown", "Options" => "whois.domaintools.com,tools.whois.net/whoisbyip,www.whois-search.com/whois,www.dnsstuff.com/tools/whois", "Description" => "Select your preffer whois service for more ip information", ),
		"reseller_group" => array ("FriendlyName" => "Allow search groups", "Type" => "text", "Size" => "30", "Description" => "Show the search and remove option to this groups only (comma separated group numbers).<br/>Client Groups documentation: <a href=\"http://docs.whmcs.com/Client_Groups\" target=\"_blank\">http://docs.whmcs.com/Client_Groups</a>", ),
		"search_all" => array ("FriendlyName" => "Allow search all", "Type" => "yesno", "Size" => "30", "Description" => "Show the search and remove option to all of your hosting clients", ),
		"auto_mode" => array ("FriendlyName" => "Auto mode off", "Type" => "yesno", "Size" => "30", "Description" => "Disable automatic unblock on login - if you set this, make sure to allow the option below", ),
		"allow_all" => array ("FriendlyName" => "Show unblock link", "Type" => "yesno", "Size" => "30", "Description" => "AutoUnblock link available to clients with active accounts on the assigned servers (allredy available for search groups)", "Default" => "on", ),
		"allow_pipe" => array ("FriendlyName" => "Allow pipe unblock", "Type" => "yesno", "Size" => "30", "Description" => "You can allow local users to use AuotUnblock via mail piping (you must set email forwarding first)", ),
		"limit_unblock" => array ("FriendlyName" => "Limit clients unblock", "Type" => "text", "Size" => "10", "Description" => "Set the number of seconds clients have to wait before they can use AutoUnblock csf again", ),
		"limit_search" => array ("FriendlyName" => "Allow block search", "Type" => "yesno", "Size" => "30", "Description" => "Search ip block on the server without removal while limit is on", ),
		"addon_homepgae" => array ("FriendlyName" => "Addon Homepage", "Type" => "dropdown", "Options" => "IP Actions,AutoUnblock csf log,CSF Manager", "Description" => "You can select the admin addon home page", ),
		"conn_address" => array ("FriendlyName" => "Connection Address", "Type" => "radio", "Options" => "IP Address,Hostname", "Description" => "Change the connection address that Autounblock use to access to the server", ),
		"allow_disabled" => array ("FriendlyName" => "Allow disabled servers", "Type" => "yesno", "Size" => "30", "Description" => "Allow admin to use disabled servers", ),
	));
	return $configarray;
}

function autounblockcsf_activate() {
	# Create autounblockcsf DB Table
	$query = "CREATE TABLE `mod_autounblockcsf` (
		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`user` TEXT NOT NULL ,
		`request` TEXT NOT NULL ,
		`server` INT(10) NOT NULL ,
		`ip` TEXT NOT NULL ,
		`action` TEXT NOT NULL ,
		`description` MEDIUMTEXT NOT NULL ,
		`status` INT(1) NOT NULL DEFAULT 0 ,
		`dateandtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
	) CHARSET=utf8";
	$result = mysql_query($query);
	if ($result) {
		insert_query('tbladdonmodules',array('module'=>'autounblockcsf','setting'=>'local_key'));
		insert_query('tbladdonmodules',array('module'=>'autounblockcsf','setting'=>'server_modules'));
		insert_query('tbladdonmodules',array('module'=>'autounblockcsf','setting'=>'server_exclude'));
		insert_query('tbladdonmodules',array('module'=>'autounblockcsf','setting'=>'server_products'));
		return array('status'=>'success','description'=>'AutoUnblock csf successfully activate...');
	} else {
		return array('status'=>'error','description'=>'Error!!! AutoUnblock csf can not be activate...');
	}
}

function autounblockcsf_deactivate() {
	# Remove Custom DB Table
	$query = "DROP TABLE `mod_autounblockcsf`";
	$result = mysql_query($query);
	# Return Result
	if ($result) {
		return array('status'=>'success','description'=>'AutoUnblock csf successfully deactivate...');
	} else {
		return array('status'=>'error','description'=>'Error!!! Can not deactivate AutoUnblock csf...');
	}
}

function autounblockcsf_upgrade($vars) {
	$version = $vars['version'];
	# Update for versions under V2.6.0
	if ($version < '2.6.0') {
		$getUpgrade1 = select_query('tbladdonmodules','setting',array('module'=>'autounblockcsf','setting'=>'server_modules'));
		$rowUpgrade1 = mysql_fetch_array($getUpgrade1);
		if (!$rowUpgrade1['setting']) {
			insert_query('tbladdonmodules',array('module'=>'autounblockcsf','setting'=>'server_modules','value'=>'cpanel_extended'));
		}
	}
	# Update for versions under V2.6.5
	if ($version < '2.6.5') {
		$getUpgrade2 = select_query('tbladdonmodules','setting',array('module'=>'autounblockcsf','setting'=>'server_exclude'));
		$rowUpgrade2 = mysql_fetch_array($getUpgrade2);
		if (!$rowUpgrade2['setting']) {
			insert_query('tbladdonmodules',array('module'=>'autounblockcsf','setting'=>'server_exclude'));
		}
	}
        # Update for versions under V3.0.0
	if ($version < '3.0.0') {
		$getUpgrade3 = select_query('tbladdonmodules','setting',array('module'=>'autounblockcsf','setting'=>'server_products'));
		$rowUpgrade3 = mysql_fetch_array($getUpgrade3);
		if (!$rowUpgrade3['setting']) {
			insert_query('tbladdonmodules',array('module'=>'autounblockcsf','setting'=>'server_products'));
		}
                /* needed ?? test it
		$getUpgrade4 = select_query('tbladdonmodules','setting',array('module'=>'autounblockcsf','setting'=>'conn_address'));
		$rowUpgrade4 = mysql_fetch_array($getUpgrade4);
		if (!$rowUpgrade4['setting']) {
			insert_query('tbladdonmodules',array('module'=>'autounblockcsf','setting'=>'conn_address','setting'=>'conn_address','value'=>'IP Address'));
		}
                */
                // Delete old files
                $oldfiles = array('autounblockcsf_cusmod.php','autounblockcsf_exclude.php','autounblockcsf_licenseinfo.php','download.png','restore.png','x.png');
                foreach ($oldfiles as $filename) {
                    if (file_exists(ROOTDIR."/modules/addons/autounblockcsf/".$filename)) {
                        unlink(ROOTDIR."/modules/addons/autounblockcsf/".$filename);
                    }
                }
	}
}

function autounblockcsf_output($vars) {
	// Vars
	$modaction = $GLOBALS['autounblockcsf']['GET']['addonaction'];
	$modulelink = $vars['modulelink'];
	$version = $vars['version'];
	$LANG = $vars['_lang'];
	$baseUrl = 'addonmodules.php?'.$GLOBALS['autounblockcsf']['ENV']['QUERY_STRING'];
	$moduleHomePage = trim($vars['addon_homepgae']);

			echo '<link href="../modules/addons/autounblockcsf/css/style.css" rel="stylesheet" type="text/css">
			<link href="../modules/addons/autounblockcsf/select2/select2.css" rel="stylesheet"/>
			<script type="text/javascript" src="../modules/addons/autounblockcsf/select2/select2.min.js"></script>
			<script type="text/javascript">
				$(document).ready(function() { 
					$(".a-select").select2(); 

				});
			</script>
			';
			// Main
			if($modaction == 'ipactions' || ($moduleHomePage == 'IP Actions' && empty($modaction))) {
				include(ROOTDIR."/modules/addons/autounblockcsf/autounblockcsf_ipactions.php");
			} elseif($modaction == 'csfmanager' || ($moduleHomePage == 'CSF Manager' && empty($modaction))) {
				include(ROOTDIR."/modules/addons/autounblockcsf/autounblockcsf_csfmanager.php");
			} elseif($modaction == 'userslog' || ($moduleHomePage == 'AutoUnblock csf log' && empty($modaction))) {
				include(ROOTDIR."/modules/addons/autounblockcsf/autounblockcsf_main.php");
			} elseif($modaction == 'backups') {
				include(ROOTDIR."/modules/addons/autounblockcsf/autounblockcsf_backups.php");
			} elseif($modaction == 'servers') {
				include(ROOTDIR."/modules/addons/autounblockcsf/autounblockcsf_servers.php");
			} else {
				include(ROOTDIR."/modules/addons/autounblockcsf/autounblockcsf_main.php");
			}
}

function autounblockcsf_sidebar($vars) {
	// Vars
	$modaction = trim($GLOBALS['autounblockcsf']['GET']['addonaction']);
	$modulelink = $vars['modulelink'];
	$version = $vars['version'];
	$LANG = $vars['_lang'];
	$moduleHomePage = trim($vars['addon_homepgae']);
	if($modaction == 'ipactions' || ($moduleHomePage == 'IP Actions' && empty($modaction))) {
		$ipactiveclass = 'class="active"';
	} elseif($modaction == 'csfmanager' || ($moduleHomePage == 'CSF Manager' && empty($modaction))) {
		$csactiveclass = 'class="active"';
	} elseif($modaction == 'userslog' || ($moduleHomePage == 'AutoUnblock csf log' && empty($modaction))) {
		$usactiveclass = 'class="active"';
	} elseif($modaction == 'backups') {
		$baactiveclass = 'class="active"';
	} elseif($modaction == 'servers') {
		$seactiveclass = 'class="active"';
	}
	$sidebar = '<span class="header">'.$LANG['AUpagetitle'].' v'.$version.'</span><ul class="menu">
		<li><a '.$usactiveclass.' href="'.$modulelink.'&addonaction=userslog">'.$LANG['menulog'].'</a></li>
		<li><a '.$ipactiveclass.' href="'.$modulelink.'&addonaction=ipactions">'.$LANG['menuactions'].'</a></li>
		<li><a '.$csactiveclass.' href="'.$modulelink.'&addonaction=csfmanager">'.$LANG['menumanager'].'</a></li>
		<li><a '.$seactiveclass.' href="'.$modulelink.'&addonaction=servers">'.$LANG['configservers'].'</a></li>
		<li><a href="configaddonmods.php#autounblockcsf">'.$LANG['menuconfig'].'</a></li>
		<li><a href="http://www.gk-root.com/GK-Apps/AutoUnblock-csf/#projectdocumentation" target="_blank">'.$LANG['menuhelp'].'</a></li>
		<li><br/>&copy; GK~root</li>
	</ul>';
	return $sidebar;
}

function autounblockcsf_clientarea($vars) {
    // Vars
    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    $LANG = $vars['_lang'];
    global $remote_ip;
    $userip = $remote_ip;

    $search_ip = $GLOBALS['autounblockcsf']['POST']['search_ip'];
    if (empty($search_ip)) {
        $search_ip = $userip;
    }

    if ($_SESSION['autounblocklogin']) {
        $loginmsg = $LANG['loginmsg1'] . ' ' . $LANG['loginmsg2'];
        unset($_SESSION['autounblocklogin']);
    }

    if ($_SESSION['uid']) {
        $userLimit = $vars['limit_unblock'];
        $allowSearch = $vars['limit_search'];
        $checkLimit = autounblockcsf_checkLimit($userLimit, $allowSearch, $_SESSION['uid']);
        if ($checkLimit) {
            if ($checkLimit['autoAction'] == 'autounblock') {
                $blockmsg = $LANG['theipaddress'] . ' ' . $search_ip . ' ' . $LANG['blockmsg'];
            } elseif ($checkLimit['autoAction'] == 'grep') {
                $blockmsg = $LANG['loginmsg1'];
            }
        }
        if ($checkLimit['autoAction']) {
            $getClientServers = autounblockcsf_getClientServers($_SESSION['uid']);
            $userProductServers = autounblockcsf_userProductServers($_SESSION['uid']);
            if ($getClientServers['status'] == '0') {
                $ClientServers = $userProductServers;
            } else {
                $ClientServers = array_merge($getClientServers['servers'],$userProductServers);
            }
            foreach ($ClientServers as $key => $server) {
                if ($server['id']) {
                    $csfservers[$key] = getAutoUnblock($search_ip, $checkLimit['autoAction'], $server['id'], $_SESSION['uid'],true,$LANG);
                } elseif($server['productid'])  {
                    $csfservers[$key] = getAutoUnblock($search_ip, $checkLimit['autoAction'], $server, $_SESSION['uid'],true,$LANG);
                }
                if (isset($csfservers[$key]['log'])) {
                    if ($csfservers[$key]['log']['status'] == '1') {
                        if (isset($csfservers[$key]['log']['Date'])) {
                            $dateandtime = '<strong>' . $LANG['timendate'] . '</strong> ' . $csfservers[$key]['log']['Date'] . ' ' . $csfservers[$key]['log']['Time'] . '<br/>';
                        }
                        $csfservers[$key]['data'] = $blockmsg . '<br/><br/>' . $dateandtime . '<strong>' . $LANG['blockedreason'] . '</strong><p style="direction:ltr;text-align:left"><em>' . $csfservers[$key]['log']['Reason_Blocked'] . '</em></p>';
                    } elseif ($csfservers[$key]['log']['status'] == '0') {
                        $csfservers[$key]['data'] = $LANG['theipaddress'] . ' ' . $search_ip . ' ' . $LANG['adminblockmsg'];
                    }
                } else {
                    if ($csfservers[$key]['status'] == '0') {
                        if (!empty($LANG['cusconnerror']) && !$csfservers[$key]['iperrors']  && !$csfservers[$key]['adminblock']) {
                            $csfservers[$key]['data'] = $LANG['cusconnerror'];
                        } elseif ($csfservers[$key]['errors']) {
                            $csfservers[$key]['data'] = $csfservers[$key]['errors'];
                        }
                    } elseif (!$csfservers[$key]['allow']) {
                        $csfservers[$key]['data'] = $LANG['theipaddress'] . ' ' . $search_ip . ' ' . $LANG['cleanmsg'];
                    }
                }
            }
            //print_r($csfservers); // Debug
        }
    }
    $templateReturn = array(
        'pagetitle' => $LANG['AUpagetitle'],
        'breadcrumb' => array('index.php?m=autounblockcsf' => $LANG['menutext']),
        'templatefile' => 'autounblockcsf',
        'requirelogin' => true, # or false
        'vars' => array(
            'loginmsg' => $loginmsg,
            'presearchtext' => $LANG['presearchtext'],
            'titletext' => $LANG['titletext'],
            'searchip' => $LANG['searchip'],
            'tabletitle1' => $LANG['tabletitle1'],
            'tabletitle2' => $LANG['tabletitle2'],
            'serverstitle' => $LANG['serverstitle'],
            'resultstitle' => $LANG['resultstitle'],
            'requestip' => $search_ip,
            'csfservers' => $csfservers,
        ),
    );
    return $templateReturn;
}
?>