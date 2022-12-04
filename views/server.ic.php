<?php
	//https://stackoverflow.com/questions/60261311/live-console-input-output-for-a-gameserver-using-javascript-or-php
	//https://cs-bg.info/forum/viewtopic.php?t=176727
	//AMX BANS


	if(this::getSpec("panel_settings","Maintenance","ID",1))
	{
	    if(user::isLogged())
	    {
	    	if(user::getUserData()->Boss < 1)
	    	{
	        	return user::redirect_to("maintenance");
	        }
	    }
	    else
	    {
	    	return user::redirect_to("maintenance");
	    }
	}

	if(!user::isLogged()||this::$_ENABLE_RSC!=1)
	{
		return user::redirect_to("");
	}

	if(user::getUserData()->Admin < 4)
	{
		return user::redirect_to("");
	}

	//include_once('/../system/FTPM/class/FileManager.php');

	$M=new Rcon();
	$M->Connect("89.40.104.30","27015","D65rFKep");
	$check_sv_connexion=$M->Info();
	if(!$check_sv_connexion)
	{
		this::show_toastr('error', 'Your server is marked as inactive!', '<b>Error</b>', 1);

		//return user::redirect_to("");
	}

	$response='';

	if(isset($_POST['kill_sv']))
	{
		this::register_db_log(user::get(), "server was forced to shut down by", user::GetIp());
		this::show_toastr('success', 'You shut down server with success.', '<b>Success</b>', 1);

		$M->RconCommand("shutdownserver");
		//$M->Disconnect();

		//user::redirect_to("server");
	}
	if(isset($_POST['stop_sv']))
	{
		this::register_db_log(user::get(), "server was paused by", user::GetIp());
		this::show_toastr('success', 'You paused server with success.', '<b>Success</b>', 1);

		$M->RconCommand("pause");

		//user::redirect_to("server");
	}
	if(isset($_POST['resume_sv']))
	{
		this::register_db_log(user::get(), "server was unpaused by", user::GetIp());
		this::show_toastr('success', 'You unpaused server with success.', '<b>Success</b>', 1);

		$M->RconCommand("pause");

		//user::redirect_to("server");
	}
	if(isset($_POST['restart_sv']))
	{
		this::register_db_log(user::get(), "server was restarted by", user::GetIp());
		this::show_toastr('success', 'You restarted server with success.', '<b>Success</b>', 1);

		$M->RconCommand("restart");

		//user::redirect_to("server");
	}
	if(isset($_POST['crcmmd']))
	{
		$verified_cmd=$purifier->purify(this::xss_clean($_POST['crcmd']));

		$response=$M->RconCommand($verified_cmd);
        if ($response != "")
        {
            //trim($response); ehhh
            echo "
            	<script>
            		$(document).ready(function(){
            			$('#show_crcmmd_response').modal('show');
            		});
            	</script>
            	";
        }

		this::register_db_log(user::get(), "command '".$verified_cmd."' was sended to server by", user::GetIp());
		this::show_toastr('success', 'Your custom command was sended with success.', '<b>Success</b>', 1);

		//user::redirect_to("server", 15);
	}


if($check_sv_connexion)
{
if($response!='')
{
?>
<div id="show_crcmmd_response" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="show_crcmmd_responseLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="show_crcmmd_responseLabel">Response from your custom rcon command:</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<div align="center">
					<?php
						echo nl2br($response);//da..
						/*
						$lines = explode("\n",$response);
						echo $lines;
							SAU
	                    //print the result
	                    foreach($lines as $line)
	                    {
	                    	echo $lines.'<br>';
	                    }
	                    */
					?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?php
}
?>
<div id="shutdownsv" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="shutdownsvLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="shutdownsvLabel">Shut down server?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div align="center">
						<button type="submit" name="kill_sv" class="btn btn-danger btn-block">
							Yes
						</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div id="stopsv" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="stopsvLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="stopsvLabel">Pause server?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div align="center">
						<button type="submit" name="stop_sv" class="btn btn-danger btn-block">
							Yes
						</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div id="resumesv" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="resumesvLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="resumesvLabel">Unpause server?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div align="center">
						<button type="submit" name="resume_sv" class="btn btn-success btn-block">
							Yes
						</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div id="restartsv" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="restartsvLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="restartsvLabel">Restart server?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div align="center">
						<button type="submit" name="restart_sv" class="btn btn-info btn-block">
							Yes
						</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div id="customrconcommand" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="customrconcommandLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="customrconcommandLabel">Send your own command to server</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div align="center">
						<div class="form-group form-material floating" data-plugin="formMaterial">
							<b>Your command</b>
							<input type="text" class="form-control" name="crcmd" minlength="1" required>
						</div>
						<br>
						<button type="submit" name="crcmmd" class="btn btn-info btn-block">
							Send it
						</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div id="show_players" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="show_playersLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="show_playersLabel">
					<?php
					echo "Server '".this::$_SERVER_NAME."' status";
					?>
				</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<?php
				$ret=$M->ServerInfo();
				if(!$ret)
				{
				?>
					<div align="center">Error in communication with server!!</div>
				<?php
				}
				else
				{
				?>
					<div align="center">
						<?php
						echo "Players: ".$ret["activeplayers"]."/".$ret["maxplayers"];
						?>
						|
						Current Map:
						<?php
						echo $ret["map"];
						?>
						<br/>
						<br>
					</div>
					<?php
					if(!$ret["activeplayers"])
					{
					?>
						<div align="center">No active players!</div>
					<?php
					}
					else
					{
					?>
						<div class="table-overflow">
							<table id="table-board">
								<thead>
									<tr>
										<th class="score-tab-text">ID</th>
										<th class="score-tab-text">Nick</th>
										<th class="score-tab-text">SteamID</th>
										<th class="score-tab-text">Ip</th>
										<th class="score-tab-text">Frags</th>
										<th class="score-tab-text">Time</th>
										<th class="score-tab-text">Ping</th>
									</tr>
								</thead>
								<tbody>
								<?php
								for($i = 1; $i <= $ret["activeplayers"]; $i++)
								{
								?>
									<tr>
										<td class="score-tab-text">
										<?php
											echo $ret[$i]['id'];
										?>
										</td>
										<td class="score-tab-text">
										<?php
											if(this::getSpec('admins', 'id', this::IsValidSteamStr($ret[$i]['wonid'])?'auth':'name', this::IsValidSteamStr($ret[$i]['wonid'])?$ret[$i]['wonid']:$ret[$i]['name']) != '-1')
											{
												echo "<a href='".user::MakeProfileUrl(this::getSpec('admins', 'id', this::IsValidSteamStr($ret[$i]['wonid'])?'auth':'name', this::IsValidSteamStr($ret[$i]['wonid']))?$ret[$i]['wonid']:$ret[$i]['name'])."' target='_blank'>".$ret[$i]['name']."</a>";
											}
											else
											{
												echo $ret[$i]['name'];
											}
										?>
										</td>
										<td class="score-tab-text">
										<?php
											if(this::IsValidSteamStr($ret[$i]['wonid']))
											{
												echo "<a href='https://steamcommunity.com/profiles/".this::SteamStr2SteamId($ret[$i]['wonid'])."' target='_blank'>".$ret[$i]['wonid']."</a>";
											}
											else
											{
												echo 'NON-STEAM';
											}
										?>
										</td>
										<td class="score-tab-text">
										<?php
											echo $ret[$i]['adress'];
										?>
										</td>
										<td class="score-tab-text">
										<?php
											echo $ret[$i]['frag'];
										?>
										</td>
										<td class="score-tab-text">
										<?php
											echo $ret[$i]['time'];
										?>
										</td>
										<td class="score-tab-text">
										<?php
											echo $ret[$i]['ping'];
										?>
										</td>
									</tr>
								<?php
								}
								?>
								</tbody>
							</table>
						</div>
					<?php
						}
					}
					?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?php
}
?>

<div class="mainArea-content">
	<div class="container">
		<div class="indexRow row" id="index-ongoing-matches">
			<div class="index-title">
				<h1>Server Panel</h1>
			</div>
			<div class="indexRow-inner row">
				<div id="pagination-loader">
					<div id="mainArea-body">
						<div id="mainArea-content-body">
							<div id="paginations">
								<div class="table-loader">
									<div id="spinner-loader">
										<div class="spinner">
											<div class="inner one"></div>
											<div class="inner two"></div>
											<div class="inner three"></div>
										</div>
									</div>
								</div>
								<div class="table-overflow">
									<div align="center">
										<?php
										if($check_sv_connexion)
										{
										?>
										<form method="post">
											<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#shutdownsv">
												<i class="fa-solid fa-xmark"></i> Shut down server
											</button>
											<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#stopsv">
												<i class="fa-solid fa-stop"></i> Pause server
											</button>
											<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#resumesv">
												<i class="fa-solid fa-circle-play"></i> Unpause server
											</button>
											<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#restartsv">
												<i class="fa-solid fa-arrows-rotate"></i> Restart server
											</button>
											<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#customrconcommand">
												<i class="fa-solid fa-terminal"></i> Send your command to server
											</button>
											<hr class="vertical" />
											<button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#show_players">
												<i class="fa-solid fa-layer-group"></i> Show me server status
											</button>
										</form>
										<?php
										}
										else
										{
										?>
											Error in communication with server!!
										<?php
										}
										?>
									</div>
									<br/>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="indexRow row" id="index-ongoing-matches">
			<div class="index-title">
				<h1>Server FTP-Manager</h1>
			</div>
			<div class="indexRow-inner row">
				<div id="pagination-loader">
					<div id="mainArea-body">
						<div id="mainArea-content-body">
							<div id="paginations">
								<div class="table-loader">
									<div id="spinner-loader">
										<div class="spinner">
											<div class="inner one"></div>
											<div class="inner two"></div>
											<div class="inner three"></div>
										</div>
									</div>
								</div>
								<div class="table-overflow">
									<div align="center">
										<?php
											$FileManager = new FileManager();
											print $FileManager->create();
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>