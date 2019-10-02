<?php
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}

        echo '<link rel="stylesheet" href="/modules/addons/autounblockcsf/switchery/switchery.min.css" />';
	echo '<script>
                function updateServers(fconfig,fvalue) {
                    fvalue = (fvalue === undefined) ? false : fvalue;
                    var furl;
                    if (fconfig === "pid" && fvalue === false) {
                        fvalue = $("#pid").val();
                        var pvalue = $("input[name=proallowtype]:checked").val();
                        var psvalue = $("input[name=prosslmode]").is(":checked");
                        if (psvalue === true) {
                            var psvalueT = "on";
                        } else if (psvalue === false) {
                            var psvalueT = "off";
                        }
                        furl = fconfig+"="+fvalue+"&proallowtype="+pvalue+"&ssl="+psvalueT;
                    } else if (fconfig === "pid") {
                        if (!confirm("'.$LANG['productcostumfileds'].'")) {
                            return;
                        }
                        furl = fconfig+"="+fvalue;
                    } else if (fconfig === "type") {
                        var avalue = $("input[name=allowtype-"+fvalue+"]:checked").val();
                        furl = fconfig+"="+fvalue+"&allowtype="+avalue;
                    } else {
                        furl = fconfig+"="+fvalue;
                    }
                    $(location).attr("href", "addonmodules.php?module=autounblockcsf&addonaction=servers&"+furl);
		}
                $(document).ready(function () {
                    var exid = "'.$GLOBALS['autounblockcsf']['GET']['exid'].'";
                    var type = "'.$GLOBALS['autounblockcsf']['GET']['type'].'";
                    var pid = "'.$GLOBALS['autounblockcsf']['GET']['pid'].'";
                    if (exid) {$("#customdiv").hide();$("#productdiv").hide();$("#excludediv").prev().toggleClass("expand");}
                    else if (type) {$("#excludediv").hide();$("#productdiv").hide();$("#customdiv").prev().toggleClass("expand");}
                    else if (pid) {$("#excludediv").hide();$("#customdiv").hide();$("#productdiv").prev().toggleClass("expand");}
                    else {$(".togglediv").hide();}
                    $(".toggle").on("click", function(e) {
                        $(this).toggleClass("expand");
                        $(this).next().slideToggle();
                    });
                });
        </script>';
	$excludeArr = autounblockcsf_serverExclude();
	if (!is_array($excludeArr)) {$excludeArr = array();}
        if ($GLOBALS['autounblockcsf']['GET']['exid']) {
            $serverid = $GLOBALS['autounblockcsf']['GET']['exid'];
            if (in_array($serverid, $excludeArr)) {
                unset($excludeArr[array_search($serverid,$excludeArr)]);
            } else {
                $excludeArr[] = $serverid;
            }
            $excludeArrT = implode(',', $excludeArr);
            update_query('tbladdonmodules',array('value'=>$excludeArrT),array('module'=>'autounblockcsf','setting'=>'server_exclude'));
	}
	$modulesArr = autounblockcsf_serverModules();
	if (!is_array($modulesArr)) {$modulesArr = array();}
        if ($GLOBALS['autounblockcsf']['GET']['type']) {
            $provmoduletype = $GLOBALS['autounblockcsf']['GET']['type'];
            $provmoduleallow = $GLOBALS['autounblockcsf']['GET']['allowtype'];
            if ($provmoduleallow != 'undefined') {
                if (array_key_exists($provmoduletype, $modulesArr)) {
                    unset($modulesArr[$provmoduletype]);
                } else {
                    $modulesArr[$provmoduletype] = $provmoduleallow;
                }
                $modulesArrT = http_build_query($modulesArr,'',',');
                $modulesArrT = str_replace('=', '|', $modulesArrT);
                update_query('tbladdonmodules',array('value'=>$modulesArrT),array('module'=>'autounblockcsf','setting'=>'server_modules'));
            } else {
                echo '<div class="errorbox" style="font-size:16px">'.$LANG['SupportedModuleType'].'</div>';
            }
	}
	$productArr = autounblockcsf_serverProducts();
	if (!is_array($productArr)) {$productArr = array(); $productArrT = '';}
        if ($GLOBALS['autounblockcsf']['GET']['pid']) {
            $pid = $GLOBALS['autounblockcsf']['GET']['pid'];
            $prodtypeallow = $GLOBALS['autounblockcsf']['GET']['proallowtype'];
            $prosslmode = $GLOBALS['autounblockcsf']['GET']['ssl'];
            if ($prodtypeallow != 'undefined') {
                if (array_key_exists($pid, $productArr)) {
                    unset($productArr[$pid]);
                    $getFieldSsl = select_query('tblcustomfields','id',array('relid'=>$pid,'description'=>'autounblock_secure'));
                    while($rowFieldSsl = mysql_fetch_array($getFieldSsl)) {
	                    $sslFieldID = trim($rowFieldSsl['id']);
	                    delete_query("tblcustomfields",array("id"=>$sslFieldID));
	                    delete_query("tblcustomfieldsvalues",array("fieldid"=>$sslFieldID));
					}
                    $getFieldHash = select_query('tblcustomfields','id',array('relid'=>$pid,'description'=>'autounblock_hash'));
                    while($rowFieldHash = mysql_fetch_array($getFieldHash)) {
	                    $hashFieldID = trim($rowFieldHash['id']);
	                    delete_query("tblcustomfields",array("id"=>$hashFieldID));
	                    delete_query("tblcustomfieldsvalues",array("fieldid"=>$hashFieldID));
					}
                } elseif ($prodtypeallow && $prosslmode) {
                    $productArr[$pid] = array('type'=>$prodtypeallow, 'ssl'=>$prosslmode);
                    $getFieldSsl = select_query('tblcustomfields','id',array('relid'=>$pid,'description'=>'autounblock_secure'));
                    $rowFieldSsl = mysql_fetch_array($getFieldSsl);
                    if (!$rowFieldSsl['id']) {insert_query('tblcustomfields',array('type'=>'product','relid'=>$pid,'fieldname'=>'AutoUnblock: Secure SSL','fieldtype'=>'tickbox','description'=>'autounblock_secure','adminonly'=>'on'));}
                    $getFieldHash = select_query('tblcustomfields','id',array('relid'=>$pid,'description'=>'autounblock_hash'));
                    $rowFieldHash = mysql_fetch_array($getFieldHash);
                    if (!$rowFieldHash['id']) {insert_query('tblcustomfields',array('type'=>'product','relid'=>$pid,'fieldname'=>'AutoUnblock: cPanel Access Hash or DirectAdmin admin password','fieldtype'=>'textarea','description'=>'autounblock_hash','adminonly'=>'on'));}
                } else {
                    echo '<div class="errorbox" style="font-size:16px">'.$LANG['selectServerType'].'</div>';               
                }
                if (!empty($productArr)) {
                    $productArrT = trim(serialize($productArr));
                }
                update_query('tbladdonmodules',array('value'=>$productArrT),array('module'=>'autounblockcsf','setting'=>'server_products'));
            } else {
                echo '<div class="errorbox" style="font-size:16px">'.$LANG['selectServerType'].'</div>';               
            }
        }
	// End Action Results
	// Start exclude div
        echo '<div class="toggle"><span class="toggleicon"></span>'.$LANG['excludetitle'].'</div><div id="excludediv" class="togglediv"><h3>'.$LANG['excludedesc'].'</h3>';
	echo '<table class="datatable datatable-in-width">
		<tr>
                    <th>'.$LANG['excludethname'].'</th>
                    <th>'.$LANG['excludethip'].'</th>
                    <th>'.$LANG['excludethstatus'].'</th>
		</tr>
	';
	$resultservers = autounblockcsf_getCpServers(true);
	if ($resultservers['status'] == '1') {
            foreach ($resultservers['servers'] as $rowservers) {
		if (in_array($rowservers['id'], $excludeArr)) {
                    $statusT = $LANG['buttonenable'];
                    $statusIcon = '';
		} else {
                    $statusT = $LANG['buttondisable'];
                    $statusIcon = ' checked';
		}
		echo '
                    <tr>
                        <td>'.$rowservers['name'].'</td>
                        <td>'.$rowservers['ipaddress'].'</td>
                        <td><input onchange="updateServers(\'exid\',\''.$rowservers['id'].'\');" name="rowservers" type="checkbox" class="js-switch"'.$statusIcon.' /></td>
                    </tr>
		';
            }
	}
	echo '</table><p style="font-size:12px;">'.$LANG['excludenote'].'</p><hr/></div>';
	// End exclude div
        // Start custom div
	echo '<div class="toggle"><span class="toggleicon"></span>'.$LANG['cusmodtitle'].'</div><div id="customdiv" class="togglediv"><h3>'.$LANG['cusmoddesc'].'</h3>';
	echo '<table class="datatable datatable-in-width">
		<tr>
                    <th>'.$LANG['cusmodthtype'].'</th>
                    <th>'.$LANG['cusmodthsupport'].'</th>
                    <th>'.$LANG['cusmodthstatus'].'</th>
		</tr>
	';
        $resultmodules = autounblockcsf_getProvModules();
	if ($resultmodules['status'] == '1') {
            foreach ($resultmodules['servers'] as $rowmodules) {
                $statusIcon = '';
                $disabledT = '';
                $checkC = '';
                $checkD = '';
		if (array_key_exists($rowmodules['type'], $modulesArr)) {
                    $statusT = $LANG['cusmoddelete'];
                    $statusIcon = ' checked';
                    if ($modulesArr[$rowmodules['type']] == 'cpanel') {
                        $checkC = ' checked';
                    } elseif ($modulesArr[$rowmodules['type']] == 'directadmin') {
                        $checkD = ' checked';
                    }
		} else {
                    $statusT = $LANG['cusmodadd'];
		}
		echo '
                    <tr>
                        <td>'.$rowmodules['type'].'</td>
                        <td>
                            <input'.$checkC.' style="margin:0 2px 0 2px" type="radio" id="allowtype-'.$rowmodules['type'].'" name="allowtype-'.$rowmodules['type'].'" value="cpanel"/>cPanel
                            <input'.$checkD.' style="margin:0 2px 0 2px" type="radio" id="allowtype-'.$rowmodules['type'].'" name="allowtype-'.$rowmodules['type'].'" value="directadmin"/>DirectAdmin
                        </td>
			<td><input'.$disabledT.' onchange="updateServers(\'type\',\''.$rowmodules['type'].'\');" name="rowmodules" type="checkbox" class="js-switch"'.$statusIcon.' /></td>
                    </tr>
		';
            }
	}
	echo '</table><p style="font-size:12px;">'.$LANG['cusmodnote'].'</p><hr/></div>';
	// End custom div
	// Start product div
	echo '<div class="toggle"><span class="toggleicon"></span>Product server information support</div>';
	$resultProducts = select_query('tblproducts','id,type,name,servertype',array('id'=>array('sqltype'=>'NEQ','value'=>'')));
        while ($rowProducts = mysql_fetch_array($resultProducts)) {
            $rowType = $LANG['product'.$rowProducts['type']];
            $typeS = ($rowProducts['servertype']) ? $rowType.' ('.$rowProducts['servertype'].')' : $rowType;
            if (array_key_exists($rowProducts['id'], $productArr)) {
                $typeT = ($productArr[$rowProducts['id']]['type'] == 'cp') ? 'cPanel' : 'DirectAdmin';
                $table .= '
                    <tr>
                        <td>'.$rowProducts['name'].'</td>
			<td>'.$typeT.'</td>
			<td>'.ucfirst($productArr[$rowProducts['id']]['ssl']).'</td>
			<td><input onclick="updateServers(\'pid\',\''.$rowProducts['id'].'\');return false;" type="image" src="../modules/addons/autounblockcsf/img/delete.png" /></td>
                    </tr>
		';
            } else {
                $options .= '<option value="'.$rowProducts['id'].'">'.$rowProducts['name'].' - '.$typeS.'</option>';
            }
        }
        echo '<div id="productdiv" class="togglediv"><h3>'.$LANG['serverinformation'].'</h3>';
        if ($table) {
            echo '
            <table class="datatable datatable-in-width">
		<tr>
                    <th>Product Name</th>
                    <th>Supported Module Type</th>
                    <th>Default SSL Connection</th>
                    <th>Disable</th>
		</tr>
                '.$table.'
            </table>
            <p style="font-size:12px;">'.$LANG['productwillcreate'].'</p>
            ';
        }
	echo '
            <div><h2>Add New Product:</h2>
                <strong>Select Product:</strong>
                <select id="pid" name="pid" class="a-select" style="width:300px">
                    '.$options.'
                </select>
                <br><br><strong>Server Type:</strong>
                <input style="margin:0 2px 0 2px" type="radio" name="proallowtype" value="cp"/>cPanel
                <input style="margin:0 2px 0 2px" type="radio" name="proallowtype" value="da"/>DirectAdmin
                <br><br><strong>Default SSL Connection:</strong>
                <input name="prosslmode" type="checkbox" class="js-switch" />
                <br><br><input onclick="updateServers(\'pid\');" class="green-btn btn-small" type="button" name="enable" value="'.$LANG['buttonenable'].'" />
            </div>
            <hr/></div>
        ';
        // End product div
	echo '<div style="font-size:14px;margin:30px;"><a href="configservers.php">'.$LANG['ConfigureWHMCSProducts'].'</a></div>';
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