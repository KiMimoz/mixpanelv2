<?php
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

	if(!isset(this::$_url[1]))
	{
		return user::redirect_to("");
	}

	if(!isset(this::$_url[1]) && user::isLogged())
	{
		if(this::IsValidSteamStr(user::getUserData()->auth)&&!empty(user::getUserData()->steamid64))
		{
			return user::redirect_to(this::$_PAGE_URL.'profile/'.user::getUserData()->steamid64.'');
		}
		return user::redirect_to(this::$_PAGE_URL.'profile/'.user::getUserData()->id.'');
	}
	else
	{
		$user = User::where('auth', this::$_url[1])->orWhere('id', (int) this::$_url[1])->first();
	}

	$q = connect::$g_con->prepare('SELECT * FROM `admins` WHERE `id` = ? OR `auth` = ? OR `name` = ? OR `email` = ? OR `steamid64` = ?');
	$q->execute(array(this::$_url[1],this::$_url[1],this::$_url[1],this::$_url[1],this::$_url[1]));
	if(!$q->rowCount())
	{
	    this::show_toastr('info', 'This player does not exist!', 'Info');

	    return user::redirect_to("");
	}
	$data = $q->fetch(PDO::FETCH_OBJ);

	$profile = connect::$g_con->prepare("SELECT * FROM `mix_sys_stats` WHERE `SteamID` = ?");
	$profile->execute(array($data->auth));
	$row2 = $profile->fetch(PDO::FETCH_OBJ);

	$kills = "".$row2->Kills."";
	$deaths = "".$row2->Deaths."";
	$result = $kills-$deaths;

	$puncte = connect::$g_con->prepare("SELECT * FROM `points_sys` WHERE `SteamID` = ?");
	$puncte->execute(array($data->auth));
	$points = $puncte->fetch(PDO::FETCH_OBJ);

	$have_access=0;//=))
	if(user::isLogged())
	{
		$have_access=(user::getUserData()->Admin >= 3)?1:(user::getUserData()->Boss>=1)?2:0;
	}

	if($have_access>0)
	{
		if(isset($_POST['down_adm']))
		{
			if(user::getData($_POST['down_adm'],'warn') > 0)
			{
				$prep = connect::prepare('UPDATE `admins` SET `warn` = `warn`-1 WHERE `id`=?');
				$prep->execute(array($_POST['down_adm']));

				this::register_db_log(user::get(), "1 warn was deleted from ".this::getSpec('admins','name','id',$_POST['down_adm'])." by", user::GetIp());
				this::register_db_notification($data->id, $data->name, user::getUserData()->name." deleted 1 warn from you", user::get(), user::getUserData()->name, this::getLinkPath());
				this::show_toastr('success', 'You removed 1 warn with success.', '<h3 class="text-success">Success</h3>', 1);
			}
			else
			{
				this::show_toastr('error', "This target doesn't have warns.", 'Attention', 1);
			}

			//return user::redirect_to(this::$_PAGE_URL.'/profile/'.$data->id);
		}

		if(isset($_POST['up_adm']))
		{
			if(user::getData($_POST['up_adm'],'warn') < 3)
			{
				$prep = connect::prepare('UPDATE `admins` SET `warn` = `warn`+1 WHERE `id`=?');
				$prep->execute(array($_POST['up_adm']));

				this::register_db_log(user::get(), "1 warn was added to ".this::getSpec('admins','name','id',$_POST['up_adm'])." by", user::GetIp());
				this::register_db_notification($data->id, $data->name, user::getUserData()->name." added 1 warn to you", user::get(), user::getUserData()->name, this::getLinkPath());
				this::show_toastr('success', 'You added 1 warn with success.', '<h3 class="text-success">Success</h3>', 1);
			}
			else
			{
				this::show_toastr('error', 'This target already have 3 warns.', 'Attention', 1);
			}

			//return user::redirect_to(this::$_PAGE_URL.'/profile/'.$data->id);
		}

		if(isset($_POST['resetwarns']))
		{
			if(this::getSpec('admins','warn','id',$_POST['resetwarns'])<=0)//ehh
			{
				this::show_toastr('error', 'This target already have 0 warns', '<b>Error</b>');

				//return user::redirect_to(this::$_PAGE_URL.'profile/'.$data->id);
			}
			else
			{
				$prep = connect::prepare('UPDATE `admins` SET `warn` = 0 WHERE `id`=?');
				$prep->execute(array($_POST['resetwarns']));

				this::register_db_log(user::get(), "all warns was reseted from ".this::getSpec('admins','name','id',$_POST['resetwarns'])." by", user::GetIp());
				this::register_db_notification($data->id, $data->name, user::getUserData()->name." reseted your warns", user::get(), user::getUserData()->name, this::getLinkPath());
				this::show_toastr('success', 'You deleted with success all warns for this target.', '<b>Success</b>', 1);
			}

			//return user::redirect_to(this::$_PAGE_URL.'/profile/'.$data->id);
		}

		if(isset($_POST['removeadmin']))
		{
			this::register_db_log(user::get(), "account of ".this::getSpec('admins','name','id',$_POST['removeadmin'])." was deleted by", user::GetIp());

			$prep = connect::prepare('DELETE FROM `admins` WHERE `id`=?');
			$prep->execute(array($_POST['removeadmin']));

			this::show_toastr('success', 'You deleted with success this account!', '<b>Success</b>', 1);

			//return user::redirect_to(this::$_PAGE_URL.'/profile/'.$data->id);
		}

		if(isset($_POST['setname']))
		{
			trim($_POST['nametext']);
			$user = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `name` = ?");
			$user->execute(array($_POST['nametext']));

			$find = $user->rowCount(); 
			if($find)
			{
				this::show_toastr('error', 'This nick already exists.', 'Error', 1);

				//return user::redirect_to(this::$_PAGE_URL.'/profile/'.$data->id);
			}
			else
			{
				this::register_db_log(user::get(), "nick ".this::getSpec('admins','name','id',$_POST['setname'])." was changed to ".$_POST['nametext']." by", user::GetIp());
				this::register_db_notification($data->id, $data->name, user::getUserData()->name." changed your old nick to ".$_POST['nametext'], user::get(), user::getUserData()->name, this::getLinkPath());

				$user = connect::$g_con->prepare("UPDATE `admins` SET `name`=? WHERE `id` = ?");
				$user->execute(array($_POST['nametext'],$_POST['setname']));

				this::show_toastr('success', 'You edited with success this nick.', '<b>Success</b>', 1);

				//return user::redirect_to(this::$_PAGE_URL.'/profile/'.$data->id);
			}
		}

		if(isset($_POST['password_submit']))
		{
			trim($_POST['password']);
			$user = connect::$g_con->prepare("UPDATE `admins` SET `password`=? WHERE `id` = ?");
			$user->execute(array($_POST['password'],$_POST['password_submit']));

			this::register_db_log(user::get(), "password of ".this::getSpec('admins','name','id',$_POST['password_submit'])." was changed by", user::GetIp());
			this::register_db_notification($data->id, $data->name, user::getUserData()->name." changed your old password to ".$_POST['password'], user::get(), user::getUserData()->name, this::getLinkPath());
			this::show_toastr('success', 'You edited with success this password.', '<b>Success</b>', 1);

			//return user::redirect_to(this::$_PAGE_URL.'/profile/'.$data->id);
		}

		if(isset($_POST['email_submit']))
		{
	        if(!filter_var($_POST['email2'], FILTER_VALIDATE_EMAIL))
	        {
	            this::show_toastr('error', 'This is not a valid email address.', 'Error', 1);

	            //return user::redirect_to(this::$_PAGE_URL.'profile/'.$data->id);
	        }
	        else
	        {
				$user = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `email` = ?");
				$user->execute(array($_POST['email2']));

				$find = $user->rowCount(); 
				if($find)
				{
					this::show_toastr('error', 'This email already exists.', 'Error', 1);

					//return user::redirect_to(this::$_PAGE_URL.'/profile/'.$data->id);
				}
				else
				{
					trim($_POST['email2']);
					$user = connect::$g_con->prepare("UPDATE `admins` SET `email` = ? WHERE `id` = ?");
					$user->execute(array($_POST['email2'],$_POST['email_submit']));

					this::register_db_log(user::get(), "email of ".this::getSpec('admins','name','id',$_POST['email_submit'])." was changed to ".$_POST['email2']." by", user::GetIp());
					this::register_db_notification($data->id, $data->name, user::getUserData()->name." changed your old email to ".$_POST['email2'], user::get(), user::getUserData()->name, this::getLinkPath());

					this::show_toastr('success', 'You edited with success this email.', '<b>Success</b>', 1);

					//return user::redirect_to(this::$_PAGE_URL.'/profile/'.$data->id);
				}
			}
		}

		if(isset($_POST['removeaccess']))
		{
			$user = connect::$g_con->prepare("UPDATE `admins` SET `Admin` = ?, `access` = ? WHERE `id` = ?");
			$user->execute(array(0,'z',$_POST['removeaccess']));

			this::register_db_log(user::get(), "access from ".this::getSpec('admins','name','id',$_POST['removeaccess'])." was removed by", user::GetIp());
			this::register_db_notification($data->id, $data->name, user::getUserData()->name." removed your access", user::get(), user::getUserData()->name, this::getLinkPath());
			this::show_toastr('success', 'You removed admin with success.', '<b>Success</b>', 1);

			//return user::redirect_to(this::$_PAGE_URL.'/profile/'.$data->id);
		}
	}
?>

<div class="mainArea-content">
	<div class="container">
		<div id="mainArea-body">
			<div id="mainArea-content-body">
				<div id="playerProfile">
					<div class="profileField profileField-header">
						<div class="playerAvatar">
							<img alt=""<?php echo $data->online==1?' class="won-border-color" ':' '; ?>src="<?php echo user::GetSteamAvatar($data->id,2); ?>">
						</div>
						<div class="playerContent">
							<div class="player-content-row">
								<div class="playerName">
									<span class="name">
									<?php
										echo $data->name;
									?>
									</span>
									<?php
									if(this::IsValidSteamStr($data->auth))
									{
									?>
										<span class="playerBadge" data-tippy-content="This user has been verified, and their identity has been confirmed." aria-expanded="false">
											<i class="fa-brands fa-steam"></i>
										</span>
									<?php
									}
									?>
								</div>
								<div class="player-content-info">
									<div class="playerInfo-row">
										<i class="fa-solid fa-gavel"></i>
										<div class="playerInfo-inner">
											<span>Mode</span>
											<b>
												<?php
												echo $points->Mode;
												?>
											</b>
										</div>
									</div>
									<div class="playerInfo-row">
										<i class="fa-solid fa-ranking-star"></i>
										<div class="playerInfo-inner">
											<span>Rank</span>
											<b>
												<?php
												echo $points->Rank;
												?>
											</b>
										</div>
									</div>
									<div class="playerInfo-row">
										<i class="fa-solid fa-arrows-to-dot"></i>
										<div class="playerInfo-inner">
											<span>Points</span>
											<b>
												<?php
												echo $points->Points;
												?>
											</b>
										</div>
									</div>
									<div class="playerInfo-row">
										<i class="fa-play fas" aria-hidden="true"></i>
										<div class="playerInfo-inner playerInfo-inner-resp">
											<span>Last Played</span>
											<b>
												<?php
												echo $points->LastOnline;
												?>
											</b>
										</div>
									</div>
								</div>
							</div>
							<div class="player-content-row2">
								<div class="player-info-left">
									<div class="player-info-row">
										<span class="pi-type">
											<?php
											echo this::IsValidSteamStr($data->auth)?'SteamID64(unique)':'ID';
											?>
										</span>
										<span class="pi-value">
											<?php
											echo this::IsValidSteamStr($data->auth)?this::SteamStr2SteamId($data->auth):$data->id;
											?>
										</span>
									</div>
									<div class="player-info-row">
										<span class="pi-type">From</span>
										<span class="pi-value">
											<?php
											echo !empty($points->Country)?$points->Country:user::getLocation($data->IP,1);
											?>
										</span>
									</div>
									<div class="player-info-row">
										<span class="pi-type">Joined</span>
										<span class="pi-value">
											<?php
											echo $points->FirstJoined;
											?>
										</span>
									</div>
								</div>
								<?php
								if(this::IsValidSteamStr($data->auth))
								{
								?>
									<div class="player-info-right">
										<div class="player-steam-info">
											<a href="https://steamcommunity.com/profiles/<?php echo this::SteamStr2SteamId($data->auth); ?>" target="_blank">
												<i class="fa-solid fa-arrow-up-right-from-square"></i>
											</a>
										</div>
									</div>
								<?php
								}
								?>
							</div>
						</div>
					</div>
					<div class="profileField profileField-level">
						<div class="table-overflow">
							<div class="playerLevel">
								<div class="playerLevel-row" data-status="<?php echo ($points->Points >= 0 && $points->Points <= 1000)?'completed':'incomplete'; ?>">
									<div class="level">
										<div class="level-layer-one">
										</div>
										<div class="level-layer-text">1</div>
										<div class="level-layer-two" style="background: conic-gradient(#a9a2a4, rgb(163 155 157 / 20%) 28%);">
										</div>
									</div>
									<span class="levelText">0 - 1000</span>
									<div class="level-progress">
										<span style="width: <?php echo ($points->Points >= 0 && $points->Points <= 1000)?'100':'0'; ?>%;"></span>
									</div>
								</div>
								<div class="playerLevel-row" data-status="<?php echo ($points->Points >= 1001 && $points->Points <= 1400)?'completed':'incomplete'; ?>">
									<div class="level">
										<div class="level-layer-one">
										</div>
										<div class="level-layer-text">2</div>
										<div class="level-layer-two" style="background: conic-gradient(#a9a2a4, rgb(163 155 157 / 20%) 36%);">
										</div>
									</div>
									<span class="levelText">1001 - 1400</span>
									<div class="level-progress">
										<span style="width: <?php echo ($points->Points >= 1001 && $points->Points <= 1400)?'100':'0'; ?>%;"></span>
									</div>
								</div>
								<div class="playerLevel-row" data-status="<?php echo ($points->Points >= 14001 && $points->Points <= 1800)?'completed':'incomplete'; ?>">
									<div class="level">
										<div class="level-layer-one"></div>
										<div class="level-layer-text">3</div>
										<div class="level-layer-two" style="background: conic-gradient(#a9a2a4, rgb(163 155 157 / 20%) 44%);">
										</div>
									</div>
									<span class="levelText">1401 - 1800</span>
									<div class="level-progress">
										<span style="width: <?php echo ($points->Points >= 14001 && $points->Points <= 1800)?'100':'0'; ?>%;"></span>
									</div>
								</div>
								<div class="playerLevel-row" data-status="<?php echo ($points->Points >= 1801 && $points->Points <= 2200)?'completed':'incomplete'; ?>">
									<div class="level">
										<div class="level-layer-one">
										</div>
										<div class="level-layer-text">4</div>
										<div class="level-layer-two" style="background: conic-gradient(#62a716, rgb(163 155 157 / 20%) 52%);">
										</div>
									</div>
									<span class="levelText">1801 - 2200</span>
									<div class="level-progress">
										<span style="width: <?php echo ($points->Points >= 1801 && $points->Points <= 2200)?'100':'0'; ?>%;"></span>
									</div>
								</div>
								<div class="playerLevel-row" data-status="<?php echo ($points->Points >= 2201 && $points->Points <= 2600)?'completed':'incomplete'; ?>">
									<div class="level">
										<div class="level-layer-one">
										</div>
										<div class="level-layer-text">5</div>
										<div class="level-layer-two" style="background: conic-gradient(#62a716, rgb(163 155 157 / 20%) 60%);">
										</div>
									</div>
									<span class="levelText">2201 - 2600</span>
									<div class="level-progress">
										<span style="width: <?php echo ($points->Points >= 2201 && $points->Points <= 2600)?'100':'0'; ?>%;"></span>
									</div>
								</div>
								<div class="playerLevel-row" data-status="<?php echo ($points->Points >= 2601 && $points->Points <= 3000)?'completed':'incomplete'; ?>">
									<div class="level">
										<div class="level-layer-one">
										</div>
										<div class="level-layer-text">6</div>
										<div class="level-layer-two" style="background: conic-gradient(#62a716,rgb(163 155 157 / 20%) 68%);">
										</div>
									</div>
									<span class="levelText">2601 - 3000</span>
									<div class="level-progress">
										<span style="width: <?php echo ($points->Points >= 2601 && $points->Points <= 3000)?'100':'0'; ?>%;"></span>
									</div>
								</div>
								<div class="playerLevel-row" data-status="<?php echo ($points->Points >= 3001 && $points->Points <= 3400)?'completed':'incomplete'; ?>">
									<div class="level">
										<div class="level-layer-one"></div>
										<div class="level-layer-text">7</div>
										<div class="level-layer-two" style="background: conic-gradient(#e96b12,#e96b12, rgb(163 155 157 / 20%) 76%);"></div>
									</div>
									<span class="levelText">3001 - 3400</span>
									<div class="level-progress">
										<span style="width: <?php echo ($points->Points >= 3001 && $points->Points <= 3400)?'100':'0'; ?>%;"></span>
									</div>
								</div>
								<div class="playerLevel-row" data-status="<?php echo ($points->Points >= 3401 && $points->Points <= 3800)?'completed':'incomplete'; ?>">
									<div class="level">
										<div class="level-layer-one">
										</div>
										<div class="level-layer-text">8</div>
										<div class="level-layer-two" style="background: conic-gradient(#e96b12,#e96b12 30%, rgb(163 155 157 / 20%));">
										</div>
									</div>
									<span class="levelText">3401 - 3800</span>
									<div class="level-progress">
										<span style="width: <?php echo ($points->Points >= 3401 && $points->Points <= 3800)?'100':'0'; ?>%;"></span>
									</div>
								</div>
								<div class="playerLevel-row" data-status="<?php echo ($points->Points >= 3801 && $points->Points <= 4200)?'completed':'incomplete'; ?>">
									<div class="level">
										<div class="level-layer-one">
										</div>
										<div class="level-layer-text">9</div>
										<div class="level-layer-two" style="background: conic-gradient(#e96b12,#e96b12 54%, rgb(163 155 157 / 20%));"></div>
									</div>
									<span class="levelText">3801 - 4200</span>
									<div class="level-progress">
										<span style="width: <?php echo ($points->Points >= 3801 && $points->Points <= 4200)?'100':'0'; ?>%;"></span>
									</div>
								</div>
								<div class="playerLevel-row" data-status="<?php echo ($points->Points >= 4201 && $points->Points <= 4600)?'completed':'incomplete'; ?>">
									<div class="level">
										<div class="level-layer-one"></div>
										<div class="level-layer-text">10</div>
										<div class="level-layer-two" style="background: #c91d1d;"></div>
									</div>
									<span class="levelText">4201 - 4600</span>
									<div class="level-progress">
										<span style="width: <?php echo ($points->Points >= 4201 && $points->Points <= 4600)?'100':'0'; ?>%;"></span>
									</div>
								</div>
							</div> 
						</div>
					</div>
					<div class="profileField profileField-stats">
						<nav class="pf-statsHead">
							<li>
								<button class="active pf-tab" onclick="changeTab(event, 'player-stats', 'profileTab', 'pf-tab')">Stats & Matches</button>
							</li>
							<li>
								<button class="pf-tab" onclick="changeTab(event, 'player-maps', 'profileTab', 'pf-tab')">Maps</button>
							</li>
							<li>
								<button class="pf-tab" onclick="changeTab(event, 'player-achievements', 'profileTab', 'pf-tab')">Achievements</button>
							</li>
							<?php
							if($have_access>0)
							{
							?>
								<li>
									<button class="pf-tab" onclick="changeTab(event, 'admin-tools', 'profileTab', 'pf-tab')">Admin tools</button>
								</li>
							<?php
							}
							?>
						</nav>
						<div class="pf-statsBody">
							<div class="profileTab" id="player-stats">
								<div class="stats-player">
									<div class="stats-row">
										<span class="type">
										<?php
											echo $points->Matches;
										?>
										</span>
										<span class="value">Matches</span>
									</div>
									<div class="stats-row">
										<span class="type">
										<?php
											echo $result;
										?>
										</span>
										<span class="value">K/D</span>
									</div>
									<div class="stats-row">
										<span class="type">
										<?php
											echo $row2->Wins;
										?>
										</span>
										<span class="value">Wins</span>
									</div>
									<div class="stats-row">
										<span class="type">
										<?php
											echo $row2->Kills;
										?>
										</span>
										<span class="value">Kills</span>
									</div>
									<div class="stats-row">
										<span class="type">
										<?php
											echo $row2->Deaths;
										?>
										</span>
										<span class="value">Deaths</span>
									</div>
									<div class="stats-row">
										<span class="type">
										<?php
											echo $row2->HS;
										?>
										</span>
										<span class="value">Headshots</span>
									</div>
								</div>
								<div id="pagination-loader">
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
											<table id="table-board">
												<thead>
													<tr>
														<th>Map</th>
														<th class="score-tab-text">Mode</th>
														<th class="score-tab-text">Kills</th>
														<th class="score-tab-text">Deaths</th>
														<th class="score-tab-text">HS / HS%</th>
														<th class="score-tab-text">K/D / K/R</th>
														<th class="score-tab-text">Result</th>
														<th class="score-tab-text">Duration</th>
													</tr>
												</thead>
												<tbody>
												<?php
													$profile2 = connect::$g_con->prepare("SELECT * FROM `mix_sys_stats` WHERE `SteamID` = ? ORDER BY `MatchID` DESC".this::limit());
													$profile2->execute(array($data->auth));
													while($row = $profile2->fetch(PDO::FETCH_OBJ))
													{
														$kills = "".$row->Kills."";//dc asa cva??
														$deaths = "".$row->Deaths."";
														$kd = $kills / $deaths;
														$result = $kills-$deaths;

														$matchid = connect::$g_con->prepare("SELECT * FROM `mix_sys_match` WHERE `MatchID` = ?");
														$matchid->execute(array($row->MatchID));
														$match = $matchid->fetch(PDO::FETCH_OBJ);
														//while($match = $matchid->fetch(PDO::FETCH_OBJ)) {
												?>
															<tr data-redirect="<?php echo this::$_PAGE_URL; ?>match/<?php echo $match->MatchID; ?>" id="player-target">
																<td class="profile-map-tab">
																	<div class="profile-map">
																		<img alt="" id="match-profile-map" src="https://image.gametracker.com/images/maps/160x120/cs/<?php echo $match->Map; ?>.jpg"> <span class="profile-match-score"><?php echo $match->CTScore; ?>:<?php echo $match->TScore; ?></span>
																	</div>
																	<div class="match-profile-map">
																		<span class="map-name">
																		<?php
																			echo $match->Map;
																		?>
																		</span>
																		<span class="match-id">
																		<?php
																			echo "#".$match->MatchID;
																		?>
																		</span>
																	</div>
																</td>
																<td class="score-tab-text">
																<?php
																	echo $match->Mode;
																?>
																</td>
																<td class="score-tab-text">
																<?php
																	echo $row->Kills;
																?>
																</td>
																<td class="score-tab-text">
																<?php
																	echo $row->Deaths;
																?>
																</td>
																<td class="score-tab-text">
																<?php
																	echo $row->HS;
																?>
																	/
																<?php
																	echo ($row->HS*100)/100;
																?>
																</td>
																<td class="score-tab-text">
																<?php
																	echo $kd;
																?>
																	/
																<?php
																	echo ($kd*100)/100;
																?>
																</td>
																<td class="score-tab-text">
																	<?php
																	if($result <= 0)
																	{
																	?>
																		<span class="profile-match-loss">
																		<?php
																			echo "Lose (".$result."P)";
																		?>
																		</span>
																	<?php
																	}
																	else
																	{
																	?>
																		<span class="profile-match-win">
																			<i class="fa-trophy fas" aria-hidden="true"></i>
																			<?php
																				echo "Win (".$result."P)";
																			?>
																		</span>
																	<?php
																	}
																	?>
																</td>
																<td class="score-tab-text">
																<?php
																	echo $row->Duration;
																?>
																</td>
															</tr>
												<?php
														//}
													}
												?>
												</tbody>
											</table>
										</div>
									</div>
									<?php
									echo this::create(connect::rows('mix_sys_stats'));
									?>
								</div>
							</div>
						</div>
						<div class="profileTab" id="player-maps">
							<div class="table-overflow">
								<table id="table-board">
									<thead>
										<tr>
											<th class="score-tab-text">#</th>
											<th>Map</th>
											<th class="score-tab-text">Matches</th>
											<th class="score-tab-text">WIN/LOSS</th>
											<th class="score-tab-text">% wins</th>
											<th class="tab-text-spacer"></th>
											<th class="score-tab-text">ROUNDS</th>
											<th class="score-tab-text">WIN/LOSS</th>
											<th class="score-tab-text">% WINS</th>
											<th class="score-tab-text">Team</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$profile3 = connect::$g_con->prepare("SELECT * FROM `mix_sys_stats` WHERE `SteamID` = ? ORDER BY `MatchID` DESC".this::limit());
										$profile3->execute(array($data->auth));
										while($row3 = $profile3->fetch(PDO::FETCH_OBJ))
										{
											$matchid2 = connect::$g_con->prepare("SELECT * FROM `mix_sys_match` WHERE `MatchID` = ?");
											$matchid2->execute(array($row3->MatchID));
											$match2 = $matchid2->fetch(PDO::FETCH_OBJ);
											//while($match2 = $matchid2->fetch(PDO::FETCH_OBJ)) {
												$query_meciuri = connect::$g_con->prepare("SELECT COUNT(*) FROM `mix_sys_match` WHERE `MatchID` = ? AND `Map` = ?");
												$query_meciuri->execute(array($match2->MatchID, $match2->Map));
												$count_meciuri = $query_meciuri->fetchColumn();
									?>
												<tr data-redirect="<?php echo this::$_PAGE_URL; ?>match/<?php echo $match2->MatchID; ?>" id="player-target">
													<td class="score-tab-text">
													<?php
														echo $match2->MatchID;
													?>
													</td>
													<td class="tab-min-width">
													<?php
														echo $match2->Map;
													?>
													</td>
													<td class="score-tab-text">
													<?php
														echo $count_meciuri;
													?>
													</td>
													<td class="score-tab-text">
														<span class="win-text">
														<?php
															echo $row3->Wins;
														?>
														</span>
														<span class="tab-text-spacer">/</span>
														<span class="loss-text">
														<?php
															echo $row3->Lose;
														?>
														</span>
													</td>
													<td class="score-tab-text">
													<?php
														echo $row3->Wins/100;
													?>
													%
													</td>
													<td class="tab-text-spacer">|</td>
													<td class="score-tab-text">
													<?php
														echo $match2->Rounds;
													?>
													</td>
													<td class="score-tab-text">
														<span class="win-text">
														<?php
															echo $row3->Rounds_Wins;
														?>
														</span> 
														<span class="tab-text-spacer">/</span> 
														<span class="loss-text">
														<?php
															echo $row3->Rounds_Lose;
														?>
														</span>
													</td>
													<td class="score-tab-text">
													<?php
														echo ($row3->Rounds_Wins*100)/100;
													?>
														%
													</td>
													<td class="score-tab-text">
													<?php
														echo $row3->Team;
													?>
													</td>
												</tr>
									<?php
											//}
										}
									?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="profileTab" id="player-achievements">
							<div class="achievements-row">
								<div class="achievement-box not-completed">
									<img src="<?php echo this::$_PAGE_URL; ?>resources/images/rewards/dust2-veteran.png">
									<div class="achievement-content">
										<span class="title">Dust II Veteran</span> <span class="desc">Win 100 Matches</span>
									</div>
								</div>
								<div class="achievement-box not-completed">
									<img src="<?php echo this::$_PAGE_URL; ?>resources/images/rewards/tuscan-veteran.png">
									<div class="achievement-content">
										<span class="title">Tuscan Veteran</span> <span class="desc">Win 100 Matches</span>
									</div>
								</div>
								<div class="achievement-box not-completed">
									<img src="<?php echo this::$_PAGE_URL; ?>resources/images/rewards/inferno-veteran.png">
									<div class="achievement-content">
										<span class="title">Inferno Veteran</span> <span class="desc">Win 100 Matches</span>
									</div>
								</div>
								<div class="achievement-box not-completed">
									<img src="<?php echo this::$_PAGE_URL; ?>resources/images/rewards/mirage-veteran.png">
									<div class="achievement-content">
										<span class="title">Mirage Veteran</span> <span class="desc">Win 100 Matches</span>
									</div>
								</div>
								<div class="achievement-box not-completed">
									<img src="<?php echo this::$_PAGE_URL; ?>resources/images/rewards/50matches-badge.png">
									<div class="achievement-content">
										<span class="title">On Fire</span> <span class="desc">Play 50 matches</span>
									</div>
								</div>
								<div class="achievement-box not-completed">
									<img src="<?php echo this::$_PAGE_URL; ?>resources/images/rewards/asist.png">
									<div class="achievement-content">
										<span class="title">Support</span> <span class="desc">Kill 500 players</span>
									</div>
								</div>
								<div class="achievement-box not-completed">
									<img src="<?php echo this::$_PAGE_URL; ?>resources/images/rewards/frag.png">
									<div class="achievement-content">
										<span class="title">Killer</span> <span class="desc">Kill 500 players</span>
									</div>
								</div>
								<div class="achievement-box not-completed">
									<img src="<?php echo this::$_PAGE_URL; ?>resources/images/rewards/hs.png">
									<div class="achievement-content">
										<span class="title">Sharp Killer</span> <span class="desc">Kill 500 Player HS</span>
									</div>
								</div>
								<div class="achievement-box not-completed">
									<img src="<?php echo this::$_PAGE_URL; ?>resources/images/rewards/mvp.png">
									<div class="achievement-content">
										<span class="title">The Most Valued</span> <span class="desc">Get 500 MVP's</span>
									</div>
								</div>
								<div class="achievement-box not-completed">
									<img src="<?php echo this::$_PAGE_URL; ?>resources/images/rewards/firstwin.png">
									<div class="achievement-content">
										<span class="title">Your first game</span> <span class="desc">Win your first game</span>
									</div>
								</div>
							</div>
						</div>

						<div class="profileTab" id="admin-tools">
							<form method="post">
								<div align="center">
									<?php
									if($data->Boss<=0)
									{
										if($data->warn<3)
										{
									?>
											<button type="submit" name="up_adm" class="btn btn-success btn-lg" value="<?php echo $data->id; ?>">
												<i class="fa-solid fa-circle-plus"></i> Add 1 warn
											</button>
									<?php
										}

										if($data->warn>0)
										{
									?>
											<button type="submit" name="down_adm" class="btn btn-primary btn-lg" value="<?php echo $data->id; ?>">
												<i class="fa-solid fa-circle-minus"></i> Delete 1 warn
											</button>
									<?php
										}
									}
									if($data->warn>0)
									{
									?>
										<button type="submit" name="resetwarns" class="btn btn-info btn-lg" value="<?php echo $data->id; ?>">
											<i class="fa-solid fa-eraser"></i> Reset warns
										</button>
									<?php
									}
									if($have_access>1)
									{
										if($data->Boss<=0)
										{
									?>
											<button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#deleteaccount">
												<i class="fa fa-trash"></i> Delete account
											</button>
									<?php
										}
									?>
										<button type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#manageadmin">
											<i class="fa-solid fa-face-meh-blank"></i> Manage admin<!--ehh-->
										</button>
									<?php
									}
									if(!empty($data->name)&&$data->Boss<=0)
									{
									?>
										<button type="button" class="btn btn-secondary btn-lg" data-toggle="modal" data-target="#changename">
											<i class="fa-solid fa-file-signature"></i> Edit nick
										</button>
									<?php
									}
									if(!empty($data->email)&&$data->Boss<=0)
									{
									?>
										<button type="button" class="btn btn-dark btn-lg" data-toggle="modal" data-target="#changeemail">
											<i class="fa-solid fa-square-envelope"></i> Edit email
										</button>
									<?php
									}
									if(!empty($data->password)&&$data->Boss<=0)
									{
									?>
										<button type="button" class="btn btn-light btn-lg" data-toggle="modal" data-target="#changepassword">
											<i class="fa-solid fa-passport"></i> Edit password
										</button>
									<?php
									}
									?>
								</div>
							</form>
							<br><br/>
						</div>
						<?php
						if($have_access>1)
						{
							if($data->Boss<=0)
							{
						?>
								<div id="deleteaccount" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deleteaccountLabel" aria-hidden="true">
								    <div class="modal-dialog" role="document">
								        <div class="modal-content">
								            <div class="modal-header">
								                <h5 class="modal-title" id="deleteaccountLabel">Action confirmation</h5>
								                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
								                	<span aria-hidden="true">X</span>
								                </button>
								            </div>
								            <div class="modal-body">
								                <form method="post">
								                    <div class="form-group">
								                        <h4 align="center">Are you sure?</h4>
								                    </div>
								                    <hr>
								                    <div align="center">
								                        <button type="submit" name="removeadmin" value="<?php echo $data->id; ?>" class="btn btn-success btn-block">Yes, delete!</button>
								                    </div>
								                </form>
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
						<div id="manageadmin" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="manageadminLabel" aria-hidden="true">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
								    <div class="modal-header">
								        <h5 class="modal-title" id="manageadminLabel">
							        	<?php
							        		echo this::$_SERVER_NAME;
							        	?>
								        </h5>
						                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						                	<span aria-hidden="true">X</span>
						                </button>
								    </div>
									<?php
										$q = connect::$g_con->prepare("SELECT * FROM `panel_groups` ORDER BY `groupAdmin` DESC");
										$q->execute();
									?>
									    <div class="modal-body" align="center">
									        <h3 class="text-black">Manage access for
									        	<font color="red">
										        <?php
										        	echo $data->name;
										        ?>
									        	</font>
									        </h3>
									        <br>
									        <?php
										        while($function = $q->fetch(PDO::FETCH_OBJ))
										        {
													if(isset($_POST['set'.$function->groupAdmin.'']))
													{
														$q2 = connect::$g_con->prepare('UPDATE `admins` SET `access` = ?, `Admin` = ? WHERE `id` = ?');
														$q2->execute(array($function->groupFlags,$function->groupAdmin,$_POST['set'.$function->groupAdmin.'']));

												        this::register_db_log(user::get(), " group ".$function->groupName." was assigned to ".this::getSpec('admins','name','id',$_POST['set'.$function->groupAdmin.''])." by", user::GetIp());
														this::register_db_notification($data->id,$data->name,'Your new group is: '.$function->groupName,user::get(),$logwho,this::getLinkPath());
														this::show_toastr('success', ''.$loglast.' rank was changed with success to '.$function->groupName.'', '<h3>Success</h3>', 1);

														//return user::redirect_to("owner");
													}

													if($data->Admin!=$function->groupAdmin)
													{
											?>
														<br>
												        <form method="post">
												          <button type="submit" value="<?php echo $data->id; ?>" class="btn btn-info btn-block" style="width: 50%;" name="set<?php echo $function->groupAdmin; ?>">
												          <?php
												          	echo $function->groupName;
												          ?>
												          </button>
												      	</form>
											<?php
													}
											?>
											        <br>
										  	<?php
												}
											?>
									        <br>
									        <?php
										        if($data->Admin > 0)
										        {
									        ?>
											        <form method="post">
											          <button type="submit" value="<?php echo $data->id; ?>" class="btn btn-danger btn-block" style="width: 50%;" name="removeaccess">Remove Admin</button>
											        </form>
									    	<?php
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
						if($data->Boss<=0)
						{
							if(!empty($data->name))
							{
						?>
								<div id="changename" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="changenameLabel" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
								                <h5 class="modal-title" id="changenameLabel">
								                	Change
								                	<font color="red">
								                	<?php
								                		echo $data->name;
								                	?>
								                	</font>'s nick?
								                </h5>
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
								            </div>
								            <div class="modal-body" align="center">
										        <form method="post">
											        <input type="text" name="nametext" class="form-control" placeholder="type new nick" minlength="3" maxlength="33" required>
											        <p></p>
											        <button type="submit" value="<?php echo $data->id; ?>" class="btn btn-primary btn-block" name="setname">
											        	<i class="fa fa-edit"></i> SUBMIT
											        </button>
										        </form>
								            </div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
											</div>
										</div>
									</div>
								</div>
						<?php
							}
							if(!empty($data->password))
							{
						?>
								<div id="changepassword" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="changepasswordLabel" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
								            <div class="modal-header">
								                <h4 class="modal-title" id="changepasswordLabel">
								                	Change
								                	<font color="red">
								                	<?php
								                		echo $data->name;
								                	?>
								                	</font>'s password?
								                </h4>
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
								            </div>
											<div class="modal-body">
												<form method="post">
													<center>
														<b>Current password:
															<font color="red">
															<?php
																echo $data->password;
															?>
															</font>
														</b>
													</center>

													<br/><br/>

													<input type="text" name="password" class="form-control" placeholder="type new password" minlength="3" maxlength="15" required>

													<p></p>

													<button type="submit" name="password_submit" value="<?php echo $data->id; ?>" class="btn btn-primary btn-block">
														<i class="fa fa-fa-edit"></i> SUBMIT
													</button>
												</form>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
											</div>
										</div>
									</div>
								</div>
						<?php
							}
							if(!empty($data->email))
							{
						?>
								<div id="changeemail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="changeemailLabel" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
								            <div class="modal-header">
								                <h4 class="modal-title" id="changeemailLabel">
								                	Change
								                	<font color="red">
								                	<?php
								                		echo $data->name;
								                	?>
								                	</font>'s email?
								                </h4>
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
								            </div>
											<div class="modal-body">
												<form method="post">
													<center>
														<b>Current email:
															<font color="red">
															<?php
																echo $data->email;
															?>
															</font>
														</b>
													</center>

													<br/><br/>

													<input type="email" name="email2" class="form-control" placeholder="type new email" required>

													<p></p>

													<button type="submit" name="email_submit" value="<?php echo $data->id; ?>" class="btn btn-primary btn-block">
														<i class="fa fa-fa-edit"></i> SUBMIT
													</button>
												</form>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
											</div>
										</div>
									</div>
								</div>
						<?php 
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>