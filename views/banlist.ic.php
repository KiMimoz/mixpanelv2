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

$have_access=0;//ehh
if(user::isLogged())
{
	if(user::getUserData()->Admin >= 3)
	{
		$have_access=1;

		if(isset($_POST['createban']))
		{
			if((empty($_POST['steamid'])&&empty($_POST['name'])&&empty($_POST['ip']))||(empty($_POST['steamid'])&&empty($_POST['ip'])))
			{
				this::show_toastr('error', 'You need to fill at least IP or SteamID!!', 'Error');

				return user::redirect_to("banlist");
			}

	        if(!this::IsValidIP($_POST['ip'])&&!empty($_POST['ip']))
	        {
	            this::show_toastr('error', 'This is not a valid ip address.', 'Error');

	            return user::redirect_to("banlist");
	        }

	        if(!empty($_POST['steamid']))
	        {
	        	trim($_POST['steamid']);
				$mystring = "".$purifier->purify(this::xss_clean($_POST['steamid']))."";//kkt
				if(this::IsValidSteamStr($mystring))
				{
					$user = connect::$g_con->prepare("SELECT * FROM `bans` WHERE `victim_steamid` = ?");
					$user->execute(array($_POST['steamid']));

					$find = $user->rowCount();
					if($find)
					{
						this::show_toastr('error', 'This SteamID already exists.', 'Error');

						return user::redirect_to("banlist");
					}
				}
				else
				{
					this::show_toastr('error', 'This is not a valid steamid', 'Error');

					return user::redirect_to("banlist");
				}
			}

			if(!empty($_POST['name']))
			{
				trim($_POST['name']);
				$user = connect::$g_con->prepare("SELECT * FROM `bans` WHERE `victim_name` = ?");
				$user->execute(array($_POST['name']));

				$find = $user->rowCount();
				if($find)
				{
					this::show_toastr('error', 'This nick already exists.', 'Error');

					return user::redirect_to("banlist");
				}
			}

			trim($_POST['ip']);
			$user = connect::$g_con->prepare("SELECT * FROM `bans` WHERE `victim_ip` = ?");
			$user->execute(array($_POST['ip']));

			$find = $user->rowCount();
			if($find)
			{
				this::show_toastr('error', 'This ip already exists.', 'Error');

				return user::redirect_to("banlist");
			}

			trim($_POST['id']);
			trim($_POST['name']);//da
			trim($_POST['steamid']);
			trim($_POST['ip']);
			trim($_POST['length']);
			trim($_POST['reason']);

			this::register_db_log(user::get(), "ban to".$_POST['steamid']."(#".$_POST['id']."|".$_POST['name']."|".$_POST['ip'].") for ".$_POST['length']." minu. with reas. ".$_POST['reason']." was created by", user::GetIp());
			//this::register_db_notification($_POST['setban'], this::getSpec('admins','name','id',$_POST['setban']), user::getUserData()->name." edited your ban informations", user::get(), user::getUserData()->name, this::getLinkPath());

			$q = connect::$g_con->prepare("INSERT INTO `bans`(`victim_id`, `victim_name`, `victim_steamid`, `victim_ip`, `banlength`, `reason`, `admin_id`, `admin_name`, `admin_steamid`, `admin_ip`, `date`, `unbantime`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
			$q->bindParam(1, $_POST['id']);
			$q->bindParam(2, $purifier->purify(this::xss_clean($_POST['name'])));
			$q->bindParam(3, $purifier->purify(this::xss_clean($_POST['steamid'])));
			$q->bindParam(4, $purifier->purify(this::xss_clean($_POST['ip'])));
			$q->bindParam(5, $_POST['length']);
			$q->bindParam(6, $purifier->purify(this::xss_clean($_POST['reason'])));
			$q->bindParam(7, user::get());
			$q->bindParam(8, user::getUserData()->name);
			$q->bindParam(9, user::getUserData()->auth);
			$q->bindParam(10, user::GetIp());
			$q->bindParam(11, this::getDaT());
			$q->bindParam(12, this::getDaT(time()+$_POST['length']));
			$q->execute();

			this::show_toastr('success', 'You added this ban with success.', '<b>Success</b>', 1);

			//return user::redirect_to("banlist");
		}

	    if(isset($_POST['removeban']))
	    {
			$rmban = connect::$g_con->prepare('DELETE FROM `bans` WHERE `id` = ?');
			$rmban->execute(array($_POST['removeban']));

	        this::register_db_log(user::get(), "ban with id #".$_POST['removeban']."of [".this::getSpec('bans','victim_name','id',$_POST['removeban'])."|".this::getSpec('bans','victim_steamid','id',$_POST['removeban'])."|".this::getSpec('bans','victim_ip','id',$_POST['removeban'])."] was deleted by", user::GetIp());

	        this::register_db_notification($_POST['removeban'], this::getSpec('admins','name','id',$_POST['removeban']), user::getUserData()->name." deleted your ban", user::get(), user::getUserData()->name, this::getLinkPath());
			this::show_toastr('success', 'You deleted with success this ban.', '<b>Success</b>', 1);

	        //return user::redirect_to("banlist");
		}

		if(isset($_POST['setban']))
		{
			trim($_POST['beid']);
			trim($_POST['bename']);
			trim($_POST['besteam']);
			trim($_POST['beip']);
			trim($_POST['belength']);
			trim($_POST['beunbantime']);

			this::register_db_log(user::get(), "ban with id #(".$_POST['setban']."): (".$_POST['beid'].", ".$_POST['bename'].", ".$_POST['besteam'].", ".$_POST['beip'].", ".$_POST['belength'].", ".$_POST['beunbantime'].") of ".this::getSpec('bans','victim_name','id',$_POST['setban'])."(".this::getSpec('bans','victim_steamid','id',$_POST['setban'])."|".this::getSpec('bans','victim_ip','id',$_POST['setban']).") was edited by", user::GetIp());

			$q = connect::$g_con->prepare('UPDATE `bans` SET `victim_id` = ?, `victim_name` = ?, `victim_steamid` = ?, `victim_ip` = ?, `banlength` = ?, `unbantime` = ? WHERE `id` = ?');
			$q->execute(array($_POST['beid'], $purifier->purify(this::xss_clean($_POST['bename'])), $purifier->purify(this::xss_clean($_POST['besteam'])), $purifier->purify(this::xss_clean($_POST['beip'])), $_POST['belength'], $purifier->purify(this::xss_clean($_POST['beunbantime'])), $_POST['setban']));

			this::register_db_notification($_POST['setban'], this::getSpec('admins','name','id',$_POST['setban']), user::getUserData()->name." edited your ban informations", user::get(), user::getUserData()->name, this::getLinkPath());
			this::show_toastr('success', 'You edited with success this ban.', '<b>Success</b>', 1);

			//return user::redirect_to("banlist");
		}
	}
}
?>

<div id="adaugareban" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="adaugarebanLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="adaugarebanLabel">Create ban</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Nick(a nick to be identified just):</b>
						<input type="text" class="form-control" name="name" minlength="3" maxlength="33" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>SteamID:</b>
						<input type="text" class="form-control" name="steamid" minlength="8">
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Ip:</b>
						<input type="text" class="form-control" name="ip" minlength="3">
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Ban length(in minu./0=perm.):</b>
						<input type="number" class="form-control" name="length" minlength="0" maxlength="2" value="5" pattern="[0-9]" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Reason:</b>
						<input type="text" class="form-control" name="reason" minlength="5" maxlength="35" value="UNSPECIFIED" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Panel ID(search..if doesn't exists, leave 0):</b>
						<input type="number" class="form-control" name="id" minlength="0" maxlength="2" value="0" pattern="[0-9]">
					</div>
					<br>
					<div align="center">
						<button type="submit" name="createban" class="btn btn-success waves-effect waves-light">
			                <span class="btn-label"><i class="fa fa-check"></i></span> confirm ban
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

<div class="mainArea-content">
	<div class="container">
		<div class="indexRow row" id="index-ongoing-matches">
			<div class="index-title">
				<h1>Ban list</h1>
				<?php
				if($have_access==1)
				{
				?>
					<button type="button" class="btn btn-outline-success waves-effect waves-dark float-right" data-toggle="modal" data-target="#adaugareban">
						create ban
					</button>
				<?php
				}
				?>
				<form id="search_form2">
					<div id="search-content">
						<input type="text" autocomplete="off" name="searchuser2" id="searchuser2" placeholder="Search with Nick/IP/SteamID" required>
						<div class="search"></div>
					</div>
				</form>
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
									<table id="table-board">
										<thead>
											<tr>
												<th class="score-tab-text">#</th>
												<th>Victim Nick</th>
												<th class="score-tab-text top-player-color">Victim Auth</th>
												<th class="score-tab-text">Expire</th>
												<th class="score-tab-text">Reason</th>
												<th class="score-tab-text">Admin Nick</th>
												<th class="score-tab-text">Admin Steam</th>
												<th class="score-tab-text">Banned at</th>
												<?php
												if($have_access==1)
												{
												?>
													<th class="score-tab-text">Actions</th>
												<?php
												}
												?>
											</tr>
										</thead>
										<tbody id="search_returned_data2">
										<?php
										$q = connect::$g_con->prepare('SELECT * FROM `bans` ORDER BY `id` DESC'.this::limit());
										$q->execute();
										while($row = $q->fetch(PDO::FETCH_OBJ))
										{
										?>
											<tr>
												<td class="score-tab-text">
												<?php
													echo $row->id;
												?>
												</td>
												<td class="score-player-name" data-redirect="<?php echo user::MakeProfileUrl($row->victim_id); ?>" id="player-target">
                                                    <?php 
                                                        echo user::GetSteamAvatar($row->victim_id);
                                                    ?>
													<span>
													<?php
														echo $row->victim_name;
													?>
													</span>
												</td>
												<td class="score-tab-text">
												<?php
													echo $row->victim_steamid;
												?>
												</td>
												<td class="score-tab-text">
												<?php
													echo $row->unbantime;
												?>
												</td>
												<td class="score-tab-text">
												<?php
													echo $row->reason;
												?>
												</td>
												<td class="score-player-name" data-redirect="<?php echo user::MakeProfileUrl($row->admin_id); ?>" id="player-target">
                                                    <?php 
                                                        echo user::GetSteamAvatar($row->admin_id);
                                                    ?>
													<span>
													<?php
														echo $row->admin_name;
													?>
													</span>
												</td>
												<td class="score-tab-text">
												<?php
													echo $row->admin_steamid;
												?>
												</td>
												<td class="score-tab-text">
												<?php
													echo $row->date;
												?>
												</td>
												<?php
												if($have_access==1)
												{
												?>
													<td class="score-tab-text">
														<form method="post">
															<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#editban<?php echo $row->id; ?>" data-toggle2="tooltip" data-placement="top" title="Edit ban">
																<i class="fa fa-edit"></i>
															</button>

															<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteban<?php echo $row->id; ?>" data-toggle2="tooltip" data-placement="top" title="Delete ban">
																<i class="fa-solid fa-file-circle-xmark"></i>
															</button>
														</form>
													</td>
												<?php
												}
												?>
											</tr>
											<?php
											if($have_access==1)
											{
											?>
												<div id="deleteban<?php echo $row->id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deletebanLabel<?php echo $row->id; ?>" aria-hidden="true">
												    <div class="modal-dialog" role="document">
												        <div class="modal-content">
												            <div class="modal-header">
												                <h5 class="modal-title" id="deletebanLabel<?php echo $row->id; ?>">Action confirmation</h5>
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
												                        <button type="submit" name="removeban" value="<?php echo $row->id; ?>" class="btn btn-success btn-block">Yes, delete!</button>
												                    </div>
												                </form>
												            </div>
															<div class="modal-footer">
																<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
															</div>
												        </div>
												    </div>
												</div>
												<div id="editban<?php echo $row->id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editban<?php echo $row->id; ?>Label" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="editban<?php echo $row->id; ?>Label">Edit ban</h5>
												                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
												                	<span aria-hidden="true">X</span>
												                </button>
															</div>
															<div class="modal-body">
																<div class="tab-pane active" id="editban" role="tabpanel">
																	<form method="post">
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Nick(a nick to be identified just)</b>
																			<input type="text" name="bename" class="form-control" value="<?php echo $row->victim_name; ?>" minlength="1" maxlength="33">
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Steamid</b>
																			<input type="text" name="besteam" class="form-control" value="<?php echo $row->victim_steamid; ?>" minlength="8">
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Ip</b>
																			<input type="text" name="beip" class="form-control" value="<?php echo $row->victim_ip; ?>" minlength="3">
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Ban length(in minu./0=perm.)</b>
																			<input type="number" name="belength" class="form-control" value="<?php echo $row->banlength; ?>" minlength="0" maxlength="2" pattern="[0-9]" required>
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Unbantime</b>
																			<input type="text" name="beunbantime" class="form-control" value="<?php echo $row->unbantime; ?>" required>
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Panel ID(search..if doesn't exists, leave 0):</b>
																			<input type="number" class="form-control" name="beid" minlength="0" maxlength="2" value="<?php echo $row->victim_id; ?>" pattern="[0-9]">
																		</div>
																		<br>
																		<div align="center">
																			<button type="submit" name="setban" value="<?php echo $row->id; ?>" class="btn btn-success waves-effect waves-light">
																				<span class="btn-label"><i class="fa fa-check"></i></span> edit ban
												            				</button>
													        			</div>
																	</form>
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
										}
										?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<?php
						echo this::create(connect::rows('bans'));
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#search_form2').submit(function (e)
	{
		e.preventDefault();

		var loading_effect=document.getElementById('paginations');
		if(typeof(loading_effect) != 'undefined' && loading_effect != null)
		{
			$(loading_effect).addClass('loader');
		}

	    var searchfor2=document.getElementById('searchuser2').value;

	    $.ajax
	    ({
	        type: "post",
	        url: "<?php echo this::$_PAGE_URL; ?>search_ajax_callback.php",
	        data:
	        {
	           'searchuser2': searchfor2,
	           'USER_HAVE_ACCESS': <?php echo $have_access; ?>
	        },
	        success: function (html)
	        {
				if(typeof(loading_effect) != 'undefined' && loading_effect != null)
				{
					$(loading_effect).removeClass('loader');//da plm
				}
				$('#search_returned_data2').html(html);
	        }
	    });

		var created_pagination=document.getElementById('pag_created');
		if(typeof(created_pagination) != 'undefined' && created_pagination != null)
		{
			$(created_pagination).html("<button type='button' class='btn btn-outline-success waves-effect waves-dark float-right' data-direct='<?php echo this::$_PAGE_URL; ?>banlist' id='target'>back</button>");
		}

	    return false;
	});
</script>