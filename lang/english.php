<?php
# Admin side
// Sidebar menu
$_ADDONLANG['menulog'] = "AutoUnblock csf log";
$_ADDONLANG['menuactions'] = "IP Actions";
$_ADDONLANG['menumanager'] = "CSF Manager";
$_ADDONLANG['menubackups'] = "Log Backups";
$_ADDONLANG['menuinfo'] = "License Info";
$_ADDONLANG['configservers'] = "Manage Servers";
$_ADDONLANG['menuconfig'] = "Configuration";
$_ADDONLANG['menuhelp'] = "Documentation";

// AutoUnblock popup
$_ADDONLANG['auactionserver'] = "Server name";
$_ADDONLANG['auactionip'] = "Your Ip address is";

// Log Page
$_ADDONLANG['logactivitytitle'] = "AutoUnblock csf activity log";
$_ADDONLANG['logtabletime'] = "Time";
$_ADDONLANG['logtableserver'] = "Server";
$_ADDONLANG['logtableaction'] = "Action";
$_ADDONLANG['logtableip'] = "IP address";
$_ADDONLANG['logtabledesc'] = "Log";
$_ADDONLANG['logtableuser'] = "Admin/Client";
$_ADDONLANG['logtablefrom'] = "Request from";

// IP actions
$_ADDONLANG['unblocksuccess'] = "Successfully unblocked:";
$_ADDONLANG['ipacttitle'] = "IP Actions";
$_ADDONLANG['ipactdes'] = "By IP Actions you can block, release, allow and ignore IP address.";
$_ADDONLANG['enterip'] = "Enter IP Address:";
$_ADDONLANG['selectserver'] = "Select Server:";
$_ADDONLANG['allservers'] = "All servers";
$_ADDONLANG['buttonsearch'] = "Search";
$_ADDONLANG['buttonsearchdesc'] = "Search iptables for IP address";
$_ADDONLANG['buttonunblock'] = "Search & Release";
$_ADDONLANG['buttonunblockdesc'] = "Remove IP address from the firewall (temp and perm blocks)";
$_ADDONLANG['buttonqallow'] = "Quick Allow";
$_ADDONLANG['buttonqallowdesc'] = "Allow IP address through the firewall and add to the allow file (csf.allow)";
$_ADDONLANG['buttonqdeny'] = "Quick Deny";
$_ADDONLANG['buttonqignore'] = "Quick Ignore";
$_ADDONLANG['buttonqignoredesc'] = "Ignore IP address in lfd, add to the ignore file (csf.ignore) and restart lfd";

// CSF Manager
$_ADDONLANG['csftitle'] = "CSF Manager";
$_ADDONLANG['csfdes'] = "Manage the regular operations in the CSF by your WHMCS.";
$_ADDONLANG['csfselect'] = "Select server to continue...";
$_ADDONLANG['csfexpand'] = "Expand All";
$_ADDONLANG['csfcollapse'] = "Collapse All";
$_ADDONLANG['buttonstatistics'] = "View Statistics";
$_ADDONLANG['buttonstatisticsdesc'] = "View lfd blocking statistics";
$_ADDONLANG['buttonviewlogs'] = "View iptables Log";
$_ADDONLANG['buttonviewlogsdesc'] = "View the last 100 iptables log lines";
$_ADDONLANG['buttonrestartq'] = "Firewall Quick Restart";
$_ADDONLANG['buttonrestartqdesc'] = "Restart the csf iptables firewall via lfd";
$_ADDONLANG['buttonlfdstatus'] = "lfd Status";
$_ADDONLANG['buttonlfdstatusdesc'] = "Display lfd status";
$_ADDONLANG['buttonlfdrestart'] = "lfd Restart";
$_ADDONLANG['buttonlfdrestartdesc'] = "Restart lfd";
$_ADDONLANG['buttonenable'] = "Enable";
$_ADDONLANG['buttonenabledesc'] = "Enables csf and lfd if previously Disabled";
$_ADDONLANG['buttondisable'] = "Disable";
$_ADDONLANG['buttondisabledesc'] = "Completely disables csf and lfd";
$_ADDONLANG['buttonflushall'] = "Flush all";
$_ADDONLANG['buttonflushalldesc'] = "Removes and unblocks all entries in csf.deny (excluding those marked \"do not delete\") and all temporary IP entries (blocks and allows)";

// Log backup
$_ADDONLANG['logbackupconfirmrestore'] = "Restore will overwrite your corrent log. Continue?";
$_ADDONLANG['logbackupconfirmdelete'] = "Delete backup file?";
$_ADDONLANG['logbackupsuccess'] = "Success";
$_ADDONLANG['logbackuperror'] = "Error";
$_ADDONLANG['logbackuptitle'] = "Log backup manager";
$_ADDONLANG['logbackupdesH'] = "With log backup manager you can back up and restore the log of the module.";
$_ADDONLANG['logbackupdes'] = "Create, import and download backup files of your log";
$_ADDONLANG['logbackupbutton'] = "Create backup";
$_ADDONLANG['logbackuptable1'] = "Backup file date";
$_ADDONLANG['logbackuptable2'] = "File size";
$_ADDONLANG['logbackuptable3'] = "Actions";
$_ADDONLANG['logbackupdownload'] = "Download";
$_ADDONLANG['logbackuprestore'] = "Restore";
$_ADDONLANG['logbackupdelete'] = "Delete";

// License Info
$_ADDONLANG['autounblock_licenseinfo'] = "License Info";
$_ADDONLANG['autounblock_licenseinfodes'] = "Detailed information about your software license.";
$_ADDONLANG['autounblock_registeredto'] = "Registered To";
$_ADDONLANG['autounblock_licensekey'] = "License Key";
$_ADDONLANG['autounblock_licensetype'] = "License Type";
$_ADDONLANG['autounblock_validdomain'] = "Valid Domain";
$_ADDONLANG['autounblock_validip'] = "Valid IP";
$_ADDONLANG['autounblock_validdirectory'] = "Valid Directory";
$_ADDONLANG['autounblock_created'] = "Created";
$_ADDONLANG['autounblock_expires'] = "Expires";

# Client side
$_ADDONLANG['menutext'] = "Unblock IP address";
$_ADDONLANG['loginmsg1'] = "Your IP address is blocked from accessing to your service server.";
$_ADDONLANG['loginmsg2'] = "The reason you redirected to this page is to remove your ip block automatically.";
$_ADDONLANG['titletext'] = "Search and remove firewall ip block on servers with active hosting accounts.";
$_ADDONLANG['presearchtext'] = "Search &amp; removal of blocked IP addresses on servers with hosting accounts.<br />To continue please type in IP address:";
$_ADDONLANG['tabletitle1'] = "Results:";
$_ADDONLANG['tabletitle2'] = "Searching block for ip address:";
$_ADDONLANG['serverstitle'] = "Account server";
$_ADDONLANG['resultstitle'] = "IP block results";
$_ADDONLANG['theipaddress'] = "The IP address";
$_ADDONLANG['cleanmsg'] = "is not listed as blocked from access to the server.";
$_ADDONLANG['blockmsg'] = "was listed as blocked from access to the hosting server.<br />Blockage was removed and no further action is required on your part.";
$_ADDONLANG['timendate'] = "Blocked time:";
$_ADDONLANG['blockedreason'] = "Ip block server log:";
$_ADDONLANG['adminblockmsg'] = "was blocked by the server administrator and can not be removed automatically.<br />Please contact support to resolve the problem.";
$_ADDONLANG['limitmsg1'] = "Dear customer, unblocking is not possible now because the number of times for this action is limited.";
$_ADDONLANG['limitmsg2'] = "You will be able to use the automatic ip unblock again in";
$_ADDONLANG['limitdesc'] = "(Hours:minutes:seconds)";
$_ADDONLANG['searchip'] = "Search &amp; Remove";

// log results
$_ADDONLANG['ldflog_empty'] = "cannot show the reason blocked";
$_ADDONLANG['ldflog_modsec'] = "blocked has been triggered by mod_security. for more information, please contact support.";
$_ADDONLANG['ldflog_ftp'] = "You have reached to the maximum failed FTP login attempts";
$_ADDONLANG['ldflog_cpanel'] = "You have reached to the maximum failed cPanel login attempts";
$_ADDONLANG['ldflog_pop3'] = "You have reached to the maximum failed POP3 login attempts";
$_ADDONLANG['ldflog_cxsftp'] = "The file you have uploaded to the server is potentially malicious script";
$_ADDONLANG['ldflog_permbytemp'] = "Permanent block has been triggered due to multiple temporary blocks";
$_ADDONLANG['ldflog_smtp'] = "You have reached to the maximum failed SMTP login attempts";
$_ADDONLANG['ldflog_htpasswd'] = "Failed htpasswd (web page) login";
$_ADDONLANG['ldflog_ftpdistributed'] = "Distributed login FTP attacks on the account";
$_ADDONLANG['ldflog_imapdistributed'] = "Distributed login IMAP attacks on the account";
$_ADDONLANG['ldflog_imap'] = "You have reached to the maximum failed IMAP login attempts";

# Version 2.6 Lang variables
$_ADDONLANG['cusmodmenu'] = "Custom Server Modules";
$_ADDONLANG['cusmodtitle'] = "Custom provisioning modules support for cPanel and DirectAdmin servers";
$_ADDONLANG['cusmoddesc'] = "With the help of this tool you can add AutoUnblock csf support to any custom cPanel & DirectAdmin server modules.";
$_ADDONLANG['cusmodadd'] = "Enable";
$_ADDONLANG['cusmoddelete'] = "Disable";
$_ADDONLANG['cusmodthtype'] = "Provisioning Module Type";
$_ADDONLANG['cusmodthstatus'] = "Module Status";
$_ADDONLANG['cusmodnote'] = "* To enable custom cPanel & DirectAdmin server provisioning module, first add it as active server.";

# Version 2.6.2 Lang variables
$_ADDONLANG['ldflog_sshd'] = "You have reached to the maximum failed ssh login attempts";
$_ADDONLANG['ldflog_portscan'] = "Port scan detected, you have reached to the maximum hits allowed";

# Version 2.6.5 Lang variables
$_ADDONLANG['buttonqdenydesc'] = "Block IP address in the firewall and add to the deny file (csf.deny)";
$_ADDONLANG['excludemenu'] = "Exclude Servers";
$_ADDONLANG['excludetitle'] = "Exclude specific servers from AutoUnblock csf";
$_ADDONLANG['excludedesc'] = "If you have servers without csf installation you can exclude them from working with AutoUnblock csf here";
$_ADDONLANG['excludethname'] = "Server Name";
$_ADDONLANG['excludethip'] = "IP Address";
$_ADDONLANG['excludethstatus'] = "Server Status";
$_ADDONLANG['excludenote'] = "* Checked servers will work with AutoUnblock csf";
$_ADDONLANG['comment'] = "Comment (Allow & Deny):";
$_ADDONLANG['commenteg'] = "e.g. do not delete";
$_ADDONLANG['commentnote'] = "* Adding the \"do not delete\" comment to a deny will exlude this ip from flush all blocks action";

# Version 3.0.0 Lang variables
$_ADDONLANG['cusconnerror'] = "Block search has failed on this server.<br />Please contact support or try again later."; // Empty the value to return the server error as is
$_ADDONLANG['cusmodthsupport'] = "Supported Module Type";
$_ADDONLANG['producthostingaccount'] = "Hosting Account";
$_ADDONLANG['productreselleraccount'] = "Reseller Account";
$_ADDONLANG['productserver'] = "Dedicated/VPS Server";
$_ADDONLANG['productother'] = "Other Product/Service";
$_ADDONLANG['AUpagetitle'] = "AutoUnblock csf";
$_ADDONLANG['Resellerlimited'] = "Reseller limited privileges";
$_ADDONLANG['csfConfiguration'] = "CSF Configuration";
$_ADDONLANG['csfConfigurationDes'] = "Edit the settings of this server and save it to multiple servers at once.";
$_ADDONLANG['csfConfChSaved'] = "Changes saved";
$_ADDONLANG['csfConfNoSaved'] = "Unable to save data";
$_ADDONLANG['csfConfRestMsg'] = "You should restart both csf and lfd";
$_ADDONLANG['csfServerType'] = "Server type";
$_ADDONLANG['csfSTnotSupp'] = "is not supported";
$_ADDONLANG['csfVerError1'] = "Cannot retrive info from the database";
$_ADDONLANG['csfVerError2'] = "Version test failed";
$_ADDONLANG['csfVerError3'] = "Version does not match";
$_ADDONLANG['csfGetConfE1'] = "Cannot retrive data";
$_ADDONLANG['csfGetConfE2'] = "for server id";
$_ADDONLANG['csfGetConfE3'] = "The server IP address value is empty";
$_ADDONLANG['csfGetConfE4'] = "The server Hostname value is empty";
$_ADDONLANG['csfGetConfE5'] = "Connection address value is not set";
$_ADDONLANG['csfGetConfE6'] = "Cannot retrive CSF Configuration from the server";
$_ADDONLANG['csfConSave'] = "Save This Settings To The Following Servers";
$_ADDONLANG['dataResults'] = "Results";
$_ADDONLANG['validIPaddress'] = "Please use a valid IP address...";
$_ADDONLANG['IPcannotbeempty'] = "IP address filde can not be empty!!!";
$_ADDONLANG['clearlogdatabase'] = "Are you sure you want to clear your log database?";
$_ADDONLANG['Importsuccess'] = "Import success...";
$_ADDONLANG['Importfailed'] = "Import failed...";
$_ADDONLANG['v1importmsg'] = "Version 1.x is installed on this system.<br/>To continue, you must deactivate it first.<br/>You may import your activity log before deactivating:";
$_ADDONLANG['importbtn'] = "Import Log";
$_ADDONLANG['v1installedf'] = "Version 1.x is installed on this system.<br/>To continue, you must deactivate it first.<br/>Your log has been imported and its safe to deactivate your AutoUnblock version 1.x.";
$_ADDONLANG['ShowAll'] = "Show All";
$_ADDONLANG['Page'] = "Page";
$_ADDONLANG['importbtn'] = "Import Log";
$_ADDONLANG['productcostumfileds'] = "AutoUnblock csf product costum fileds and they saved data to will be deleted. Continue?";
$_ADDONLANG['SupportedModuleType'] = "You need to select the Supported Module Type to add the server.";
$_ADDONLANG['selectServerType'] = "You need to select the Server Type to add the product.";
$_ADDONLANG['serverinformation'] = "The server information will be taken from the client product";
$_ADDONLANG['productwillcreate'] = "* Adding a product will create two new custom fields to the client product for additional settings.";
$_ADDONLANG['ConfigureWHMCSProducts'] = "Configure WHMCS Products/Services Servers";
$_ADDONLANG['widgetResfor'] = "Results for";
$_ADDONLANG['widgetClear'] = "Clear Results";
$_ADDONLANG['widgetViewall'] = "View all";
$_ADDONLANG['Nosuchaction'] = "No such action";
$_ADDONLANG['noIperr'] = "IP address cannot be empty";
$_ADDONLANG['noSerErr'] = "Server cannot be empty";
$_ADDONLANG['noValUser'] = "Not a valid user";
$_ADDONLANG['firewallStatus'] = "Firewall Status";
$_ADDONLANG['CannotRetLogs'] = "Cannot retrieve iptables logs";
?>