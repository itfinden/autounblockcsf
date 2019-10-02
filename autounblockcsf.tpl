
<hr>
{if $autounblocksearch}
	<p><b>{$presearchtext}</b></p>
	<form method="post" action="{$urlautounblock}">
	<input type="text" size="25" name="search_ip" class="form-control" style="width:350px"> <br>
	<input type="submit" value="{$searchip}" class="btn btn-success btn-large" style="margin-top:-10px;">
	</form>
{/if}
{if $limitmsg1}
	<br/>
	<div style="width:90%; margin: 0 auto; padding:10px;" class="alert alert-error">
		<p>{$limitmsg1}</p>
		<p>{$limitmsg2}</p>
	</div>
	<br/>
{/if}
<hr>
{if $csfservers}
	<div style="width:100%; margin: 0 auto">
	<h3 style="color:#555555">{$tabletitle1}</h3>
	<h4 style="color:#555555">{$tabletitle2} <span style="font-size:22px; color:#3a87ad;">{$requestip}</span></h4>
	<hr>
	<table class="table table-framed" width="100%" cellspacing="0" cellpadding="0">
		<thead>
		<tr>
			<th class="textcenter">{$serverstitle}</th>
			<th class="textcenter">{$resultstitle}</th>
		</tr>
		</thead>
		<tbody>
		{foreach from=$csfservers item=server}
			{* The $csfservers available variables are: name,hostName,hostIP *}
			<tr>
				<td width="150">{$server.hostName}<br/>{$server.hostIP}</td>
				<td width="450" class="alert alert-success" style="padding:20px;font-size:16px;font-weight:100;"><b>{$server.data}</b></td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	</div>
	<br/>
{/if}