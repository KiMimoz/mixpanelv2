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

	if(!isset(this::$_url[1]))//ce
	{
		return user::redirect_to("");
	}

	if(!isset(this::$_url[1]) && user::isLogged())//ce
	{
		return user::redirect_to("matches");//drq
	}
	else
	{
		$user = User::where('auth', this::$_url[1])->orWhere('id', (int) this::$_url[1])->first();
	}

	$q = connect::$g_con->prepare('SELECT * FROM `mix_sys_match` WHERE `MatchID` = ?');
	$q->execute(array(this::$_url[1]));
	if(!$q->rowCount())
	{
	    this::show_toastr('error', '<h3>This Match does not exist!</h3>', 'Error');
	    return user::redirect_to("matches");
	}
	$data = $q->fetch(PDO::FETCH_OBJ);
?>

<div class="mainArea-content">
	<div class="container">
		<div class="row">
			<div class="match-row">
				<h1>
				<?php
					echo "#".$data->MatchID." | ".this::$_SERVER_NAME;
				?>
				</h1>
				<div class="match-body">
					<div class="col-match-body">
						<div class="match-team match-team-ct">
							<h1 class="match-team-title">TEAM BLUE</h1>
							<h1 class="match-team-title-responsive">BLUE</h1>
							<span class="match-<?php echo $data->CTScore > $data->TScore?'winned':'lost'; ?>">
							<?php
							if($data->Status==1)
							{
								echo $data->CTScore > $data->TScore?'WON':'LOSS';
							}
							else
							{
								echo $data->CTScore > $data->TScore?'LEAD WITH':'ARE LED WITH';
							}
							?>
							</span>
						</div>
					</div>
					<div class="col-center col-match-body">
						<div class="match-status-info">
							<span class="match-ct-score match-score<?php echo $data->CTScore > $data->TScore?' match-winned':''; ?>">
							<?php
								echo $data->CTScore;
							?>
							</span>
							<div class="match-info">
								<span class="match-status status-<?php echo $data->Status==1?'ended':'ongoing'; ?>">
									Match
									<?php
										echo $data->Status==1?'ended':'ongoing';
									?>
								</span>
								<span>
								<?php
								if($data->Status==1)
								{
									echo $data->Duration;
								}
								else
								{
									echo 'Time elapsed: ';
								}
								?>
								</span>
							</div>
							<span class="match-tero-score match-score<?php echo $data->TScore > $data->CTScore?' match-winned':''; ?>">
							<?php
								echo $data->TScore;
							?>
							</span>
						</div>
					</div>
					<div class="col-match-body">
						<div class="match-team match-team-tero">
							<h1 class="match-team-title">TEAM RED</h1>
							<h1 class="match-team-title-responsive">RED</h1>
							<span class="match-<?php echo $data->TScore > $data->CTScore?'winned':'lost'; ?>">
							<?php
							if($data->Status==1)
							{
								echo $data->TScore > $data->CTScore?'WON':'LOSS';
							}
							else
							{
								echo $data->TScore > $data->CTScore?'LEAD WITH':'ARE LED WITH';
							}
							?>
							</span>
						</div>
					</div>
				</div>
			</div>
			<nav id="match-navbar">
				<li>
					<span>
					<?php
						echo $data->Mode;
					?>
					MODE
					</span>
				<li>
					<button class="active stabs" onclick="changeTab(event, 'overview', 'tabcontents', 'stabs')">Overview</button>
				</li>
				<li>
					<button class="stabs" onclick="changeTab(event, 'scoreboard', 'tabcontents', 'stabs')">Scoreboard</button>
				</li>
			</nav>
			<div class="match-teams tabcontents" id="overview">
				<div class="match-team-rows row">
					<div class="col-lg-4">
					<?php
					$match = connect::$g_con->prepare("SELECT * FROM `mix_sys_stats` WHERE `MatchID` = ? AND `Team` = ?");
					$match->execute(array($data->MatchID,'BLUE'));
					while($row = $match->fetch(PDO::FETCH_OBJ))
					{
						$points = connect::$g_con->prepare("SELECT * FROM `points_sys` WHERE `ID` = ?");
						$points->execute(array($row->ID));
						$row2 = $points->fetch(PDO::FETCH_OBJ);
						//while($row2 = $points->fetch(PDO::FETCH_OBJ)) {
							$kills = "".$row->Kills."";
							$deaths = "".$row->Deaths."";
							$kd = $kills / $deaths;

							$result = $kills-$deaths;

							$gradient_color_1="";
							$gradient_color_2="";
							$gradient_percent_1=0;
							$gradient_percent_2=0;
				?>
							<div class="team-player" data-redirect="<?php echo user::MakeProfileUrl($row->ID); ?>" id="player-target">
								<div class="team-player-header">
									<div class="team-player-header-left">
										<img class="team-border-<?php echo $data->CTScore > $data->TScore?'won':'loss'; ?> team-player-avatar" src="<?php echo user::GetSteamAvatar($row->ID,2); ?>">
										<div class="team-player-content">
											<span class="player-name">
											<?php
												echo $row->Name;
											?>
											</span>
											<?php
											if($row->Dropped>0)
											{
											?>
												<span class="player-banned-title">Leaver</span>
											<?php
											}
											?>
										</div>
									</div>
									<div class="team-player-header-right">
										<div class="level-mini">
											<div class="level-layer-one-mini"></div>
											<div class="level-layer-text-mini">
											<?php
												switch ($row->Points)
												{
													case ($row->Points>=0&&$row->Points<=1000):
													{
														echo '1';
														$gradient_color_1='a9a2a4';
														$gradient_percent_1=28;
														break;
													}
													case ($row->Points>=1001&&$row->Points<=1400):
													{
														echo '2';
														$gradient_color_1='a9a2a4';
														$gradient_percent_1=36;
														break;
													}
													case ($row->Points>=1401&&$row->Points<=1800):
													{
														echo '3';
														$gradient_color_1='a9a2a4';
														$gradient_percent_1=44;
														break;
													}
													case ($row->Points>=1801&&$row->Points<=2200):
													{
														echo '4';
														$gradient_color_1='62a716';
														$gradient_percent_1=52;
														break;
													}
													case ($row->Points>=2201&&$row->Points<=2600):
													{
														echo '5';
														$gradient_color_1='62a716';
														$gradient_percent_1=60;
														break;
													}
													case ($row->Points>=2601&&$row->Points<=3000):
													{
														echo '6';
														$gradient_color_1='62a716';
														$gradient_percent_1=68;
														break;
													}
													case ($row->Points>=3001&&$row->Points<=3400):
													{
														echo '7';
														$gradient_color_1=$gradient_color_2='e96b12';
														$gradient_percent_1=76;
														break;
													}
													case ($row->Points>=3401&&$row->Points<=3800):
													{
														echo '8';
														$gradient_color_1=$gradient_color_2='e96b12';
														$gradient_percent_1=30;
														$gradient_percent_2=20;
														break;
													}
													case ($row->Points>=3801&&$row->Points<=4200):
													{
														echo '9';
														$gradient_color_1=$gradient_color_2='e96b12';
														$gradient_percent_1=54;
														$gradient_percent_2=20;
														break;
													}
													case ($row->Points>=4201&&$row->Points<=4600):
													{
														echo '10';
														$gradient_color_1='c91d1d';
														break;
													}
												}
											?>
											</div>
											<div class="level-layer-two-mini" style=
											"
												background: 
												<?php
													if($row->Points>=4201&&$row->Points<=4600)
													{
														echo $gradient_color_1;
													}
													else
													{
														if ($row->Points>=0&&$row->Points<=1000)
														{
															echo 'conic-gradient(#'.$gradient_color_1.', rgb(163 155 157 / 20%) '.$gradient_percent_1.'%)';
														}
														if ($row->Points>=1001&&$row->Points<=1400)
														{
															echo 'conic-gradient(#'.$gradient_color_1.', rgb(163 155 157 / 20%) '.$gradient_percent_1.'%)';
														}
														if ($row->Points>=1401&&$row->Points<=1800)
														{
															echo 'conic-gradient(#'.$gradient_color_1.', rgb(163 155 157 / 20%) '.$gradient_percent_1.'%)';
														}
														if ($row->Points>=1801&&$row->Points<=2200)
														{
															echo 'conic-gradient(#'.$gradient_color_1.', rgb(163 155 157 / 20%) '.$gradient_percent_1.'%)';
														}
														if ($row->Points>=2201&&$row->Points<=2600)
														{
															echo 'conic-gradient(#'.$gradient_color_1.', rgb(163 155 157 / 20%) '.$gradient_percent_1.'%)';
														}
														if ($row->Points>=2601&&$row->Points<=3000)
														{
															echo 'conic-gradient(#'.$gradient_color_1.', rgb(163 155 157 / 20%) '.$gradient_percent_1.'%)';
														}
														if ($row->Points>=3001&&$row->Points<=3400)
														{
															echo 'conic-gradient(#'.$gradient_color_1.',#'.$gradient_color_2.' rgb(163 155 157 / 20%) '.$gradient_percent_1.'%)';
														}
														if ($row->Points>=3401&&$row->Points<=3800)
														{
															echo 'conic-gradient(#'.$gradient_color_1.',#'.$gradient_color_2.' '.$gradient_percent_1.'% rgb(163 155 157 / 20%) '.$gradient_percent_2.'%)';
														}
														if ($row->Points>=3801&&$row->Points<=4200)
														{
															echo 'conic-gradient(#'.$gradient_color_1.',#'.$gradient_color_2.' '.$gradient_percent_1.'% rgb(163 155 157 / 20%) '.$gradient_percent_2.'%)';
														}
													}
												?>;
											"></div>
										</div>
										<span class="player-points">
										<?php
											echo $row->Points;
										?>
										</span>
									</div>
								</div>
								<div class="team-player-body">
									<div class="player-body-head">
										<span class="col-row:25 col:left">Overall</span>
										<span class="col-row:75 col:left">Player Stats</span>
									</div>
									<div class="player-body-body">
										<div class="col-row:25">
											<span class="p-value">
											<?php
												echo $row2->Matches;
											?>
											</span>
											<span class="p-title">Matches</span>
										</div>
										<div class="col-row:75 player-stats-col">
											<div class="player-row-stats">
												<span class="p-value">
												<?php
													echo $result;
												?>
												%
												</span>
												<span class="p-title">Win Rate</span>
											</div>
											<div class="player-row-stats">
												<span class="p-value">
												<?php
													echo $row->Kills;
												?>
												/
												<?php
													echo $row->HS;
												?>
												%
												</span>
												<span class="p-title">Avg. Kills / HS</span>
											</div>
											<div class="player-row-stats">
												<span class="p-value">
												<?php
													echo $row->Deaths;
												?>
												</span>
												<span class="p-title">Avg. K/D</span>
											</div>
										</div>
									</div>
								</div>
							</div>
					<?php
						//}
					}
					?>
					</div>
					<div class="col-lg-4 padd-lr:20 text-center">
						<div class="match-informations">
							<div class="match-map">
								<img class="playmap" src="https://image.gametracker.com/images/maps/160x120/cs/<?php echo $data->Map; ?>.jpg">
								<div class="match-map-content">
									<span class="map">
									<?php
										echo $data->Map;
									?>
									</span>
									<div class="match-started">
										<span class="text">Match started on -</span>
										<span class="time">
										<?php
											echo $data->Started;
										?>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
					<?php
					$match2 = connect::$g_con->prepare("SELECT * FROM `mix_sys_stats` WHERE `MatchID` = ? AND `Team` = ?");
					$match2->execute(array($data->MatchID,'RED'));
					while($row3 = $match2->fetch(PDO::FETCH_OBJ))
					{
						$points2 = connect::$g_con->prepare("SELECT * FROM `points_sys` WHERE `ID` = ?");
						$points2->execute(array($row3->ID));
						$row4 = $points2->fetch(PDO::FETCH_OBJ);
						//while($row4 = $points2->fetch(PDO::FETCH_OBJ)) {
							$kills2 = "".$row3->Kills."";
							$deaths2 = "".$row3->Deaths."";
							$kd2 = $kills2 / $deaths2;

							$result2 = $kills2-$deaths2;

							$gradient_color_12="";
							$gradient_color_22="";
							$gradient_percent_12=0;
							$gradient_percent_22=0;
					?>
							<div class="team-player" data-redirect="<?php echo user::MakeProfileUrl($row3->ID); ?>" id="player-target">
								<div class="team-player-header">
									<div class="team-player-header-left">
										<img class="team-border-<?php echo $data->TScore > $data->CTScore?'won':'loss'; ?> team-player-avatar" src="<?php echo user::GetSteamAvatar($row3->ID,2); ?>">
										<div class="team-player-content">
											<span class="player-name">
											<?php
												echo $row3->Name;
											?>
											</span>
											<?php
											if($row3->Dropped>0)
											{
											?>
												<span class="player-banned-title">Leaver</span>
											<?php
											}
											?>
										</div>
									</div>
									<div class="team-player-header-right">
										<div class="level-mini">
											<div class="level-layer-one-mini"></div>
											<div class="level-layer-text-mini">
											<?php
												switch ($row4->Points)
												{
													case ($row4->Points>=0&&$row4->Points<=1000):
													{
														echo '1';
														$gradient_color_12='a9a2a4';
														$gradient_percent_12=28;
														break;
													}
													case ($row4->Points>=1001&&$row4->Points<=1400):
													{
														echo '2';
														$gradient_color_12='a9a2a4';
														$gradient_percent_12=36;
														break;
													}
													case ($row4->Points>=1401&&$row4->Points<=1800):
													{
														echo '3';
														$gradient_color_12='a9a2a4';
														$gradient_percent_12=44;
														break;
													}
													case ($row4->Points>=1801&&$row4->Points<=2200):
													{
														echo '4';
														$gradient_color_12='62a716';
														$gradient_percent_12=52;
														break;
													}
													case ($row4->Points>=2201&&$row4->Points<=2600):
													{
														echo '5';
														$gradient_color_12='62a716';
														$gradient_percent_12=60;
														break;
													}
													case ($row4->Points>=2601&&$row4->Points<=3000):
													{
														echo '6';
														$gradient_color_12='62a716';
														$gradient_percent_12=68;
														break;
													}
													case ($row4->Points>=3001&&$row4->Points<=3400):
													{
														echo '7';
														$gradient_color_12=$gradient_color_22='e96b12';
														$gradient_percent_12=76;
														break;
													}
													case ($row4->Points>=3401&&$row4->Points<=3800):
													{
														echo '8';
														$gradient_color_12=$gradient_color_22='e96b12';
														$gradient_percent_12=30;
														$gradient_percent_22=20;
														break;
													}
													case ($row4->Points>=3801&&$row4->Points<=4200):
													{
														echo '9';
														$gradient_color_12=$gradient_color_22='e96b12';
														$gradient_percent_12=54;
														$gradient_percent_22=20;
														break;
													}
													case ($row4->Points>=4201&&$row4->Points<=4600):
													{
														echo '10';
														$gradient_color_12='c91d1d';
														break;
													}
												}
											?>
											</div>
											<div class="level-layer-two-mini" style=
											"
												background: 
												<?php
													if($row4->Points>=4201&&$row4->Points<=4600)
													{
														echo $gradient_color_12;
													}
													else
													{
														if ($row4->Points>=0&&$row4->Points<=1000)
														{
															echo 'conic-gradient(#'.$gradient_color_12.', rgb(163 155 157 / 20%) '.$gradient_percent_12.'%)';
														}
														if ($row4->Points>=1001&&$row4->Points<=1400)
														{
															echo 'conic-gradient(#'.$gradient_color_12.', rgb(163 155 157 / 20%) '.$gradient_percent_12.'%)';
														}
														if ($row4->Points>=1401&&$row4->Points<=1800)
														{
															echo 'conic-gradient(#'.$gradient_color_12.', rgb(163 155 157 / 20%) '.$gradient_percent_12.'%)';
														}
														if ($row4->Points>=1801&&$row4->Points<=2200)
														{
															echo 'conic-gradient(#'.$gradient_color_12.', rgb(163 155 157 / 20%) '.$gradient_percent_12.'%)';
														}
														if ($row4->Points>=2201&&$row4->Points<=2600)
														{
															echo 'conic-gradient(#'.$gradient_color_12.', rgb(163 155 157 / 20%) '.$gradient_percent_12.'%)';
														}
														if ($row4->Points>=2601&&$row4->Points<=3000)
														{
															echo 'conic-gradient(#'.$gradient_color_12.', rgb(163 155 157 / 20%) '.$gradient_percent_12.'%)';
														}
														if ($row4->Points>=3001&&$row4->Points<=3400)
														{
															echo 'conic-gradient(#'.$gradient_color_12.',#'.$gradient_color_22.' rgb(163 155 157 / 20%) '.$gradient_percent_12.'%)';
														}
														if ($row4->Points>=3401&&$row4->Points<=3800)
														{
															echo 'conic-gradient(#'.$gradient_color_12.',#'.$gradient_color_22.' '.$gradient_percent_12.'% rgb(163 155 157 / 20%) '.$gradient_percent_22.'%)';
														}
														if ($row4->Points>=3801&&$row4->Points<=4200)
														{
															echo 'conic-gradient(#'.$gradient_color_12.',#'.$gradient_color_22.' '.$gradient_percent_12.'% rgb(163 155 157 / 20%) '.$gradient_percent_22.'%)';
														}
													}
												?>;
											"></div>
										</div>
										<span class="player-points">
										<?php
											echo $row4->Points;
										?>
										</span>
									</div>
								</div>
								<div class="team-player-body">
									<div class="player-body-head">
										<span class="col-row:25 col:left">Overall</span>
										<span class="col-row:75 col:left">Player Stats</span>
									</div>
									<div class="player-body-body">
										<div class="col-row:25">
											<span class="p-value">
											<?php
												echo $row4->Matches;
											?>
											</span>
											<span class="p-title">Matches</span>
										</div>
										<div class="col-row:75 player-stats-col">
											<div class="player-row-stats">
												<span class="p-value">
												<?php
													echo $result2;
												?>
												%
												</span>
												<span class="p-title">Win Rate</span>
											</div>
											<div class="player-row-stats">
												<span class="p-value">
												<?php
													echo $row3->Kills;
												?>
												/
												<?php
													echo $row3->HS;
												?>
												%
												</span>
												<span class="p-title">Avg. Kills / HS</span>
											</div>
											<div class="player-row-stats">
												<span class="p-value">
												<?php
													echo $row3->Deaths;
												?>
												</span>
												<span class="p-title">Avg. K/D</span>
											</div>
										</div>
									</div>
								</div>
							</div>
					<?php
						//}
					}?>
					</div>
				</div>
			</div>
			<div class="match-scoreboard tabcontents" id="scoreboard">
				<div class="score-team-title">
					<h1 class="match-team-title">Team Blue</h1>
					<h1 class="match-team-title-responsive">Team Blue</h1>
					<span class="match-<?php echo $data->CTScore > $data->TScore?'winned':'lost'; ?>">
					<?php
						if($data->Status==1)
						{
							echo $data->CTScore > $data->TScore?'WON':'LOSS';
						}
						else
						{
							echo $data->CTScore > $data->TScore?'LEADS':'ARE LEAD';
						}
					?>
					</span>
				</div>
				<div class="scoreboard-overflow">
					<table id="table-board">
						<thead>
							<tr>
								<th>Nick</th>
								<th class="score-tab-text">Kills</th>
								<th class="score-tab-text">Deaths</th>
								<th class="score-tab-text">K/D Ratio</th>
								<th class="score-tab-text">Headshots</th>
								<th class="score-tab-text">Headshots %</th>
								<th class="score-tab-text">MVP's</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$match3 = connect::$g_con->prepare("SELECT * FROM `mix_sys_stats` WHERE `MatchID` = ? AND `Team` = ?");
						$match3->execute(array($data->MatchID,'BLUE'));
						while($row5 = $match3->fetch(PDO::FETCH_OBJ))
						{
							$kills3 = "".$row5->Kills."";
							$deaths3 = "".$row5->Deaths."";
							$headshots = "".$row5->HS."";
							$kd3 = $kills3 / $deaths3;

							$result3 = $kills3-$deaths3;

							$head = $headshots/100;
						?>
							<tr data-redirect="<?php echo user::MakeProfileUrl($row5->ID); ?>" id="player-target">
								<td class="score-player-name">
									<img src="<?php echo user::GetSteamAvatar($row5->ID,2); ?>">
									<span>
									<?php
										echo $row5->Name;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $row5->Kills;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $row5->Deaths;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $result3;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $row5->HS;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $head;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $row5->MVP;
									?>
									</span>
								</td>
							</tr>
						<?php
						}
						?>
						</tbody>
					</table>
				</div>
				<span class="scoreboard-devider"></span>
				<div class="score-team-title">
					<h1 class="match-team-title">Team Red</h1>
					<h1 class="match-team-title-responsive">Team Red</h1>
					<span class="match-<?php echo $data->TScore > $data->CTScore?'winned':'lost'; ?>">
					<?php
						if($data->Status==1)
						{
							echo $data->TScore > $data->CTScore?'WON':'LOSS';
						}
						else
						{
							echo $data->TScore > $data->CTScore?'LEADS':'ARE LEAD';
						}
					?>
					</span>
				</div>
				<div class="scoreboard-overflow">
					<table id="table-board">
						<thead>
							<tr>
								<th>Nick</th>
								<th class="score-tab-text">Kills</th>
								<th class="score-tab-text">Deaths</th>
								<th class="score-tab-text">K/D Ratio</th>
								<th class="score-tab-text">Headshots</th>
								<th class="score-tab-text">Headshots %</th>
								<th class="score-tab-text">MVP's</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$match4 = connect::$g_con->prepare("SELECT * FROM `mix_sys_stats` WHERE `MatchID` = ? AND `Team` = ?");
						$match4->execute(array($data->MatchID,'RED'));
						while($row6 = $match4->fetch(PDO::FETCH_OBJ))
						{
							$kills4 = "".$row6->Kills."";
							$deaths4 = "".$row6->Deaths."";
							$headshots2 = "".$row6->HS."";
							$kd4 = $kills4 / $deaths4;

							$result4 = $kills-$deaths;

							$head2 = $headshots2/100;
						?>
							<tr data-redirect="<?php echo user::MakeProfileUrl($row6->ID); ?>" id="player-target">
								<td class="score-player-name">
									<img src="<?php echo user::GetSteamAvatar($row6->ID,2); ?>">
									<span>
									<?php
										echo $row6->Name;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $row6->Kills;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $row6->Deaths;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $result4;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $row6->HS;
									?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
										<?php
										echo $head2;
										?>
									</span>
								</td>
								<td class="score-tab-text">
									<span>
									<?php
										echo $row6->MVP;
									?>
									</span>
								</td>
							</tr>
						<?php
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>