{if $autounblock_loginmsg}
	<div id="popupContact">
		<a id="popupContactClose"><img alt="x" src="{$systemurl}modules/addons/autounblockcsf/x.png" /></a>
		<h1>{$autounblock_tabletitle2}</h1>
		<h3><b>{$autounblock_logtableip}:</b> {$autounblock_ip}</h3><hr/>
		{foreach from=$autounblock_csfservers item=server}
			<div id="contactArea">
				<b>{$autounblock_logtableserver}: {$server.hostName} :: {$server.hostIP}</b>
				<div class="alert-message-success">{$server.data}</div>
			</div><br/>
		{/foreach}
	</div>
	<div id="backgroundPopup"></div>
{/if}