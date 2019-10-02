<?php

// v3.2.0
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}

add_hook("AdminHomeWidgets", 33, "widget_autounblockcsf");
function widget_autounblockcsf($vars) {
    require(ROOTDIR."/modules/addons/autounblockcsf/autounblockcsf_functions.php");
    $getLogArrs = autounblockcsf_getLog('', '', '', '0,5');
    $getServersResults = autounblockcsf_getCpServers();
    if ($getServersResults['status'] == '1') {
        $serverOpt = '<option value="all">All</option>';
        foreach ($getServersResults['servers'] as $rowservers) {
            if ($rowservers[id] == $GLOBALS['autounblockcsf']['POST']['server']) {
                $select = ' selected="selected"';
            } else {
                $select = '';
            }
            if ($rowservers['type'] == 'directadmin') {
                $serverOpt .= '<option value="' . $rowservers[id] . '"' . $select . '>' . $rowservers['name'] . ' (DirectAdmin)</option>';
            } elseif ($rowservers['type'] == 'cpanel_extended') {
                $serverOpt .= '<option value="' . $rowservers[id] . '"' . $select . '>' . $rowservers['name'] . ' (cPanel extended)</option>';
            } elseif ($rowservers['type'] == 'cpanel') {
                $serverOpt .= '<option value="' . $rowservers[id] . '"' . $select . '>' . $rowservers['name'] . ' (cPanel)</option>';
            } else {
                $serverOpt .= '<option value="' . $rowservers[id] . '"' . $select . '>' . $rowservers['name'] . ' (' . ucfirst($rowservers['type']) . ')</option>';
            }
            unset($rowservers);
        }
    }
    if ($GLOBALS['autounblockcsf']['POST']['getautounblock']) {
        $action = $GLOBALS['autounblockcsf']['POST']['actionval'];
        $actionIP = $GLOBALS['autounblockcsf']['POST']['actionIP'];
        $server = $GLOBALS['autounblockcsf']['POST']['server'];
        if (!empty($action) && autounblockcsf_validIpAddress($actionIP)) {
            if ($action != 'clear') {
                if ($getServersResults['status'] == '1') {
                    if ($server == 'all') {
                        foreach ($getServersResults['servers'] as $serverval) {
                            $getResults = getAutoUnblock($actionIP, $action, $serverval['id']);
                            if ($getResults['status'] == '1') {
                                echo '<div class="infobox">';
                                echo '<strong>' . $serverval['name'] . ' ' . $serverval['ipaddress'] . ':</strong><br/>';
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
                                echo '<div class="errorbox">';
                                echo '<strong>Results for ' . $serverval['name'] . ' ' . $serverval['ipaddress'] . ':</strong><br/>';
                                if (is_array($getResults['errors'])) {
                                    foreach ($getResults['errors'] as $key => $val) {
                                        echo $val . '<br/>';
                                    }
                                } else {
                                    echo $getResults['errors'];
                                }
                                if (!empty($getResults['data'])) {
                                    echo $getResults['data'];
                                }
                                echo '</div>';
                            }
                        }
                    } else {
                        $getResults = getAutoUnblock($actionIP, $action, $server);
                        if ($getResults['status'] == '1') {
                            echo '<div class="infobox">';
                            echo '<strong>' . $getResults['hostName'] . ' ' . $getResults['hostIP'] . ':</strong><br/>';
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
                            echo '<div class="errorbox">';
                            echo '<strong>Results for ' . $getResults['hostName'] . ' ' . $getResults['hostIP'] . ':</strong><br/>';
                            if (is_array($getResults['errors'])) {
                                foreach ($getResults['errors'] as $key => $val) {
                                    echo $val . '<br/>';
                                }
                            } else {
                                echo $getResults['errors'];
                            }
                            if (!empty($getResults['data'])) {
                                echo $getResults['data'];
                            }
                            echo '</div>';
                        }
                    }
                } else {
                    echo '<div class="errorbox">';
                    if (is_array($getServersResults['errors'])) {
                        foreach ($getServersResults['errors'] as $key => $val) {
                            echo $val . '<br/>';
                        }
                    } else {
                        echo $getServersResults['errors'];
                    }
                    if (!empty($getServersResults['data'])) {
                        echo $getServersResults['data'];
                    }
                    echo '</div>';
                }
                echo '<div style="text-align:center;padding-top:2px;"><a style="cursor:pointer" onclick="searchunblock(\'clear\');return false">Clear Results</a></div><br/>';
            }
        } else {
            echo 'Please use a valid IP address...<br/><br/>';
        }
        exit;
    }

    $content = '
		<table style="text-align:center" width="100%" bgcolor="#cccccc" cellspacing="1">
			<tr bgcolor=#efefef>
				<th>Time</th>
				<th>Action</th>
				<th>IP address</th>
				<th>Admin/Client</th>
			</tr>
	';
    foreach ($getLogArrs as $logArr) {
        $content .= '
			<tr bgcolor="#ffffff">
				<td>' . $logArr['dateandtime'] . '</td>
				<td>' . autounblockcsf_action2Name($logArr['action']) . '</td>
				<td>' . $logArr['ip'] . '</td>
				<td>' . $logArr['user'] . '</td>
			</tr>
		';
    }
    $content .= '</table><div style="text-align:right;padding-top:5px;padding-bottom:2px;"><a href="addonmodules.php?module=autounblockcsf&addonaction=userslog">View all &raquo;</a></div>';
    # Ajax Div
    $content .= '<div id="getresults" style="text-align:center;width="100%"></div>
		<div style="text-align:center;width="100%">
			<input type="text" size="30" id="actionIP" required="required" placeholder="IP address"/>
			<select name="server" id="server">' . $serverOpt . '</select><br/><br/>
			<input onclick="searchunblock(this.name);return false" type="button" name="grep" value="Search" />		
			<input onclick="searchunblock(this.name);return false" type="button" name="remove" value="Search & Release" />
		</div>
	';

    $jscode = 'function searchunblock(action) {
		if (action != "clear")
			$("#getresults").html("' . str_replace('"', '\"', $vars['loading']) . '<br/><br/>");
		$.post("index.php", {
				getautounblock: 1,
				server: $("#server").val(),
				actionIP: $("#actionIP").val(),
				actionval: (action)
			},
			function(data){
				jQuery("#getresults").html(data);
			}
		);
	}';

    return array('title' => 'AutoUnblock csf', 'content' => $content, 'jscode' => $jscode);
}

add_hook("ClientAreaPage", 73, "autounblockcsf_hook_addtopages");
function autounblockcsf_hook_addtopages($vars) {
    global $remote_ip;
    if ($_SESSION['uid']) {
        $userid = $_SESSION['uid'];
        $groupid = $vars['clientsdetails']['groupid'];
        if ($GLOBALS['autounblockcsf']['GET']['m'] != 'autounblockcsf') {require(ROOTDIR."/modules/addons/autounblockcsf/autounblockcsf_functions.php");}
        $getCliServersResults = autounblockcsf_getClientServers($userid);
        $userProductServers = autounblockcsf_userProductServers($userid);
        if ($getCliServersResults['status'] == '0') {
            $ClientServers = $userProductServers;
        } else {
            $ClientServers = array_merge($getCliServersResults['servers'],$userProductServers);
        }
        $custlanguage = $vars['language'];
		if (file_exists(ROOTDIR."/modules/addons/autounblockcsf/lang/".$custlanguage.".php")) {
			require(ROOTDIR."/modules/addons/autounblockcsf/lang/".$custlanguage.".php");
		} else {
			require(ROOTDIR."/modules/addons/autounblockcsf/lang/english.php");
		}
        $moduleSetting = select_query('tbladdonmodules', 'setting,value', array('module' => 'autounblockcsf'));
        while ($rowsSetting = mysql_fetch_assoc($moduleSetting)) {
            if ($rowsSetting['setting'] == 'search_all' && $rowsSetting['value'] == 'on' && !empty($ClientServers)) {
                $searchGroup = 'on';
            } elseif ($rowsSetting['setting'] == 'reseller_group' && !empty($rowsSetting['value']) && !empty($ClientServers)) {
                $searchGroupsArr = explode(",", $rowsSetting['value']);
                if (in_array($groupid, $searchGroupsArr)) {
                    $searchGroup = 'on';
                }
            }
            if ($rowsSetting['setting'] == 'allow_all' && $rowsSetting['value'] == 'on' && !empty($ClientServers)) {
                $autounblockLink = 'on';
            }
            if ($rowsSetting['setting'] == 'limit_unblock') {
                $userLimit = $rowsSetting['value'];
            }
            if ($rowsSetting['setting'] == 'limit_search') {
                $allowSearch = $rowsSetting['value'];
            }
        }
        if ($searchGroup == 'on' || $autounblockLink == 'on') {
            $_SESSION['autounblockmenu'] = 'on';
           	$templateReturn['autounblockmenu'] = $_ADDONLANG['menutext'];
            $templateReturn['urlautounblock'] = 'index.php?m=autounblockcsf';
            if ($searchGroup == 'on') {
                $templateReturn['autounblocksearch'] = 'on';
            }
        } else {
        	if (isset($_SESSION['autounblockmenu'])) {unset($_SESSION['autounblockmenu']);}
        }
        if (!empty($userLimit)) {
            $resultsUsage = select_query('mod_autounblockcsf', 'dateandtime', array('user' => 'client|' . $userid), 'dateandtime', 'DESC', '0,1');
            $rowUsage = mysql_fetch_array($resultsUsage);
            $lastUsage = strtotime($rowUsage['dateandtime']);
            $now = time();
            $secDiff = $now - $lastUsage;
            $secToWait = $userLimit - $secDiff;
            $waitParts = explode(' ', gmdate("z H i s", $secToWait));
            $addHours = 0;
            if ($waitParts[0] > 0) {
                $addHours = $waitParts[0] * 24;
            }
            $timeToWait = $_ADDONLANG['limitmsg2'] . ' ' . ($waitParts[1] + $addHours) . ':' . $waitParts[2] . ':' . $waitParts[3] . ' ' . $_ADDONLANG['limitdesc'];
            if ($secDiff > $userLimit) {
                $autoAction = 'autounblock';
                $popTitle = $_ADDONLANG['tabletitle2'];
                $logtableip = $_ADDONLANG['auactionip'];
                $blockmsg = $_ADDONLANG['theipaddress'] . ' ' . $remote_ip . ' ' . $_ADDONLANG['blockmsg'];
            } else {
                if (!empty($allowSearch)) {
                    $autoAction = 'grep';
                } else {
                    unset($templateReturn['autounblocksearch']);
                    unset($searchGroup);
                }
                if ($secDiff != 0) {
                    $popTitle = $_ADDONLANG['limitmsg1'];
                    $logtableip = $timeToWait . '<br/>' . $_ADDONLANG['auactionip'];
                    $blockmsg = $_ADDONLANG['loginmsg1'];
                    $templateReturn['limitmsg1'] = $_ADDONLANG['limitmsg1'];
                    $templateReturn['limitmsg2'] = $timeToWait;
                }
            }
        } else {
            $autoAction = 'autounblock';
            $popTitle = $_ADDONLANG['tabletitle2'];
            $logtableip = $_ADDONLANG['auactionip'];
            $blockmsg = $_ADDONLANG['theipaddress'] . ' ' . $remote_ip . ' ' . $_ADDONLANG['blockmsg'];
        }
        ///////////////////////////////////////////////
        if ($_SESSION['autounblocklogin'] == 'on') {
            foreach ($ClientServers as $key => $server) {
                $csfservers[$key] = getAutoUnblock($remote_ip, $autoAction, $server['id'], $userid);
                if (isset($csfservers[$key]['log'])) {
                    if ($csfservers[$key]['log']['status'] == '1') {
                        if (isset($csfservers[$key]['log']['Date'])) {
                            $dateandtime = '<b>' . $_ADDONLANG['timendate'] . '</b> ' . $csfservers[$key]['log']['Date'] . ' ' . $csfservers[$key]['log']['Time'] . '<br/>';
                        }
                        $csfservers[$key]['data'] = $blockmsg . '<br/>' . $dateandtime . '<b>' . $_ADDONLANG['blockedreason'] . '</b> <em>' . $_ADDONLANG[autounblockcsf_log2langvar($csfservers[$key]['log']['Reason_Blocked'])] . '</em>';
                    } elseif ($csfservers[$key]['log']['status'] == '0') {
                        $csfservers[$key]['data'] = $_ADDONLANG['theipaddress'] . ' ' . $remote_ip . ' ' . $_ADDONLANG['adminblockmsg'];
                    }
                } else {
                    unset($csfservers[$key]);
                }
            }
            //unset($_SESSION['autounblocklogin']);
            $templateReturn['autounblock_loginmsg'] = $_ADDONLANG['loginmsg1'] . ' ' . $_ADDONLANG['loginmsg2'];
            $templateReturn['autounblock_tabletitle2'] = $popTitle;
            $templateReturn['autounblock_ip'] = $remote_ip;
            $templateReturn['autounblock_csfservers'] = $csfservers;
            $templateReturn['autounblock_logtableserver'] = $_ADDONLANG['auactionserver'];
            $templateReturn['autounblock_logtableip'] = $logtableip;
        }
        /////////////////////////////////////////////////////
        return $templateReturn;
    }
}

add_hook("ClientLogin", 53, "autounblockcsf_hook_login");
function autounblockcsf_hook_login($vars) {
    if ($GLOBALS['autounblockcsf']['GET']['m'] != 'autounblockcsf') {
        require(ROOTDIR."/modules/addons/autounblockcsf/autounblockcsf_functions.php");
    }
            global $remote_ip;
            $userid = $vars['userid'];
            $getCliServersResults = autounblockcsf_getClientServers($userid);
            $userProductServers = autounblockcsf_userProductServers($userid);
	        if ($getCliServersResults['status'] == '0') {
	            $ClientServers = $userProductServers;
	        } else {
	            $ClientServers = array_merge($getCliServersResults['servers'],$userProductServers);
	        }
            if (empty($ClientServers)) {return false;}
			$resultUser = select_query("tblclients","groupid",array("id" =>$userid));
			$dataUser = mysql_fetch_array($resultUser);            
        	$groupid = $dataUser['groupid'];
            $moduleSetting = select_query('tbladdonmodules', 'setting,value', array('module' => 'autounblockcsf'));
            while ($rowsSetting = mysql_fetch_assoc($moduleSetting)) {
	            if ($rowsSetting['setting'] == 'search_all' && $rowsSetting['value'] == 'on') {
	                $searchGroup = 'on';
	            } elseif ($rowsSetting['setting'] == 'reseller_group' && !empty($rowsSetting['value'])) {
	                $searchGroupsArr = explode(",", $rowsSetting['value']);
	                if (in_array($groupid, $searchGroupsArr)) {
	                    $searchGroup = 'on';
	                }
	            }
	            if ($rowsSetting['setting'] == 'allow_all' && $rowsSetting['value'] == 'on') {
	                $autounblockLink = 'on';
	            }
                if ($rowsSetting['setting'] == 'auto_mode' && $rowsSetting['value'] == 'on') {
                	$quitSearch = true;
                }
                if ($rowsSetting['setting'] == 'limit_unblock') {
                    $userLimit = $rowsSetting['value'];
                }
                if ($rowsSetting['setting'] == 'limit_search') {
                    $allowSearch = $rowsSetting['value'];
                }
            }
	        if ($searchGroup == 'on' || $autounblockLink == 'on') {
                $_SESSION['autounblockmenu'] = 'on';
	        }
            if ($quitSearch) {return false;}
            if (!empty($userLimit)) {
                $resultsUsage = select_query('mod_autounblockcsf', 'dateandtime', array('user' => 'client|' . $userid), 'dateandtime', 'DESC', '0,1');
                $rowUsage = mysql_fetch_array($resultsUsage);
                $lastUsage = strtotime($rowUsage['dateandtime']);
                $now = time();
                $secDiff = $now - $lastUsage;
                if (empty($allowSearch) && $secDiff < $userLimit) {
                    return false;
                }
            }
            foreach ($ClientServers as $key => $server) {
                $csfservers[$key] = getAutoUnblock($remote_ip, 'grep', $server['id'], $userid);
                if (isset($csfservers[$key]['log'])) {
                    $_SESSION['autounblocklogin'] = 'on';
                    break;
                }
            }
}

add_hook("ClientAreaHeadOutput", 63, "autounblockcsf_hook_head");
function autounblockcsf_hook_head($vars) {
	if ($_SESSION['autounblocklogin'] == 'on') {
        unset($_SESSION['autounblocklogin']);
		$head_return = '';
		$head_return = '<!-- AutoUnblock csf popup -->
<script src="'.$vars[systemurl].'modules/addons/autounblockcsf/autounblockpopup.js"></script>
<link href="'.$vars[systemurl].'modules/addons/autounblockcsf/autounblockpopup.css" rel="stylesheet">';
		return $head_return;
	}
}

use WHMCS\View\Menu\Item as MenuItem;
add_hook('ClientAreaPrimaryNavbar', 93, function (MenuItem $primaryNavbar) {
	if (isset($_SESSION['autounblockmenu'])) {
	$client = Menu::context('client');
	if (!is_null($client) && !is_null($primaryNavbar->getChild('Support'))) {
		$custlanguage = "{$client->language}";
		if (file_exists(ROOTDIR."/modules/addons/autounblockcsf/lang/".$custlanguage.".php")) {
			require(ROOTDIR."/modules/addons/autounblockcsf/lang/".$custlanguage.".php");
		} else {
			require(ROOTDIR."/modules/addons/autounblockcsf/lang/english.php");
		}
		$primaryNavbar->getChild('Support')->addChild('autounblockcsf', array(
				'label' => $_ADDONLANG['menutext'],
				'uri' => 'index.php?m=autounblockcsf',
				'order' => '100',
			));
	}}
});
?>