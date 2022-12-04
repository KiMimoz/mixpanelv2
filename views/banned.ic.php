<?php
if(!user::isLogged())
{
	$check_ban = connect::$g_con->prepare("SELECT * FROM `bans` WHERE `victim_ip` = ? AND `unbantime` != 'NEVER' AND `banlength` > 0 ORDER BY id ASC");
	$check_ban->execute(array(user::GetIp()));
}
else
{
	$check_ban = connect::$g_con->prepare("SELECT * FROM `bans` WHERE (`victim_id` = ? OR `victim_steamid` = ? OR `victim_ip` = ? OR `victim_ip` = ?) AND `unbantime` != 'NEVER' AND `banlength` > 0 ORDER BY id ASC");
	$check_ban->execute(array(user::get(),user::getUserData()->auth,user::getUserData()->IP,user::getUserData()->LastIP));
}
if(!$check_ban->rowCount())
{
	return this::show_toastr('info', "Wow! You don't have ban..", "Hoorayy", 0, 1, "");
}
$get_ban = $check_ban->fetch(PDO::FETCH_OBJ);
?>
<div class="mainArea-content">
	<div class="container">
		<div class="indexRow row" id="index-ongoing-matches">
			<div class="index-title">
				<h1>You have a active ban..</h1>
			</div>
			<div class="indexRow-inner row">
			<?php 
				echo
				"
					Your last ban informations:<br>
						- Banned by: ".$get_ban->admin_name."(".$get_ban->admin_steamid.")<br>
						- Ban reason: ".$get_ban->reason."<br>
						- Ban length: ".$get_ban->banlength."<br>
						- Banned on: ".$get_ban->date."<br>
						- Unban on: ".($get_ban->banlength>0?$get_ban->unbantime:'NEVER')."
				";
			?>
			</div>
		</div>
	</div>
</div>