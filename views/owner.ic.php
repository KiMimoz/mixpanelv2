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

	if(!user::isLogged())
	{
		return user::redirect_to("");
	}

	if(user::getUserData()->Admin < 3)
	{
		return user::redirect_to("");
	}

	if(isset($_POST['createaccount']))
	{
		trim($_POST['steamid']);//ehhh...forced gen
		$mystring = "".$purifier->purify(this::xss_clean($_POST['steamid']))."";//kkt
		if(this::IsValidSteamStr($mystring))
		{
			$user = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `auth` = ?");
			$user->execute(array($_POST['steamid']));

			$find = $user->rowCount(); 
			if($find)
			{
				this::show_toastr('error', 'This SteamID already exists.', 'Error', 1);

				//return user::redirect_to("owner");
			}
			else
			{
				trim($_POST['name']);
				$user = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `name` = ?");//pot si pt pw =))
				$user->execute(array($_POST['name']));

				$find = $user->rowCount(); 
				if($find)
				{
					this::show_toastr('error', 'This nick already exists.', 'Error', 1);

					//return user::redirect_to("owner");
				}
				else
				{
					trim($_POST['email']);
			        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			        {
			            this::show_toastr('error', 'This is not a valid email address.', 'Error', 1);

			            //return user::redirect_to("owner");
			        }
			        else
			        {
						$user = connect::$g_con->prepare("SELECT * FROM `admins` WHERE `email` = ?");
						$user->execute(array($_POST['email']));

						$find = $user->rowCount(); 
						if($find)
						{
							this::show_toastr('error', 'This email already exists.', 'Error', 1);

							//return user::redirect_to("owner");
						}
						else
						{
							$user = connect::$g_con->prepare("SELECT * FROM `bans` WHERE (`victim_name` = ? OR `victim_steamid`) AND `unbantime` != 'NEVER' AND `banlength` > 0 ORDER BY id ASC");
							$user->execute(array($_POST['name'], $_POST['steamid']));

							$find = $user->rowCount(); 
							if($find)
							{
								this::show_toastr('error', 'This account is banned!', 'Error', 1);

								//return user::redirect_to("owner");
							}
							else
							{
								trim($_POST['password']);
								trim($_POST['adm_lvl']);
								trim($_POST['access']);
								trim($_POST['flags']);

								this::register_db_log(user::get(), "account for ".$_POST['name']."(".$_POST['steamid']."|".$_POST['email']."|".$_POST['access']."|".$_POST['flags'].") was created with success by", user::GetIp());

								$q = connect::$g_con->prepare("INSERT INTO `admins`(`auth`, `name`, `password`, `Admin`, `email`, `access`, `flags`, `steamid64`) VALUES (?,?,?,?,?,?,?,?)");
								$q->bindParam(1, $purifier->purify(this::xss_clean($_POST['steamid'])));
								$q->bindParam(2, $purifier->purify(this::xss_clean($_POST['name'])));
								$q->bindParam(3, $purifier->purify(this::xss_clean($_POST['password'])));
								$q->bindParam(4, $_POST['adm_lvl']);
								$q->bindParam(5, $purifier->purify(this::xss_clean($_POST['email'])));
								$q->bindParam(6, $purifier->purify(this::xss_clean($_POST['access'])));
								$q->bindParam(7, $purifier->purify(this::xss_clean($_POST['flags'])));
								$q->bindParam(8, $purifier->purify(this::xss_clean(this::SteamStr2SteamId($_POST['steamid']))));
								$q->execute();

								this::show_toastr('success', 'You created new account with success.', '<b>Success</b>', 1);

								//return user::redirect_to("owner");
							}
						}
					}
				}
			}
		}
		else
		{
			this::show_toastr('error', 'Wrong SteamID.', '<b>Error:</b>', 1);

			//return user::redirect_to("owner");
		}
	}
?>

<div id="crearecontstaff" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="crearecontstaffLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="crearecontstaffLabel">Create account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Nick:</b>
						<input type="text" class="form-control" name="name" minlength="3" maxlength="33" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>SteamID:</b>
						<input type="text" class="form-control" name="steamid" minlength="8" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Email:</b>
						<input type="email" class="form-control" name="email" minlength="8" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Admin Level:</b>
						<input type="number" class="form-control" name="adm_lvl" minlength="0" maxlength="2" value="0">
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Admin Access:</b>
						<input type="text" class="form-control" name="access" minlength="0" maxlength="5" value="" pattern="[a-z]">
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Admin Flags:</b>
						<input type="text" class="form-control" name="flags" minlength="1" maxlength="35" value="z" pattern="[a-z]" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Password:</b>
						<input type="password" class="form-control" name="password" minlength="5" maxlength="15" required>
					</div>
					<br>
					<div align="center">
						<button type="submit" name="createaccount" class="btn btn-success waves-effect waves-light">
			                <span class="btn-label"><i class="fa fa-check"></i></span> confirm account
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
				<h1>Owner Panel</h1>

				<button type="button" class="btn btn-outline-success waves-effect waves-dark float-right" data-toggle="modal" data-target="#crearecontstaff">
					create account
				</button>

				<form id="search_form3">
					<div id="search-content">
						<input type="text" autocomplete="off" name="searchuser3" id="searchuser3" placeholder="Type Nick/SteamID" required>
						<div class="search"></div>
					</div>
				</form>
				
				<?php
				if(isset($_POST["searchuser"]))
				{
				?>
					<button type="button" class="btn btn-outline-success waves-effect waves-dark float-right" onclick="window.location.href='<?php echo this::$_PAGE_URL; ?>owner'">
					    back
					</button>
				<?php
				}
				?>
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
												<th>ID</th>
												<th>Nick</th>
												<th>Group</th>
												<th>SteamID</th>
												<th>Warns</th>
												<th>First login</th>
												<th>Last login</th>
												<th>First ip</th>
												<th>Last ip</th>
											</tr>
										</thead>
										<tbody id="search_returned_data3">
										<?php
											$adm = connect::$g_con->prepare("SELECT * FROM `admins` ORDER BY `Admin` DESC".this::limit());
											$adm->execute();
											while($row = $adm->fetch(PDO::FETCH_OBJ))
											{
										?>
												<tr>
													<td>
													<?php
														echo $row->id;
													?>
													</td>
													<td>
														<i class="fa fa-circle text-<?php echo $row->online == 0?'danger':'success'; ?>" data-toggle="tooltip" data-placement="top" title="o<?php echo $row->online == 0?'ff':'n'; ?>line"></i>
														<a href="<?php echo user::MakeProfileUrl($row->id); ?>" target="_blank">
														<?php
															echo $row->name;
														?>
														</a>
													</td>
													<td>
													<?php
														$groups = connect::$g_con->prepare("SELECT * FROM `panel_groups` WHERE `groupAdmin` = ? ORDER BY `groupAdmin` ASC");
														$groups->execute(array($row->Admin));
														$function = $groups->fetch(PDO::FETCH_OBJ);
														echo '
															<span class="badge" style="background-color: '.$function->groupColor.'; color: '.$function->funcColor.';">
																<font style="font-family: '.$function->funcFontFamily.';">
																	<i class="'.$function->funcIcon.'"></i> <strong>'.$function->groupName.'</strong>
																</font>
															</span>
															';
													?>
													</td>
													<td>
													<?php
														echo $row->auth;
													?>
													</td>
													<td>
													<?php
														echo $row->warn;
													?>
													</td>
													<td>
													<?php
														echo $row->FirstPanelRegister;
													?>
													</td>
													<td>
													<?php
														echo $row->LastPanelLogin;
													?>
													</td>
													<td>
													<?php
														echo $row->IP;
													?>
													</td>
													<td>
													<?php
														echo $row->LastIP;
													?>
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
					<?php
						echo this::create(connect::rows('admins'));
					?>

					<!--<div class="pagination-wrapper">
							<div class="pagination pagination-centered">
								<ul>
									<li class="active">
										<a class="disabled" href="#">1</a>
									</li>
									<li>
										<a href="/players/page-2">2</a>
									</li>
									<li>
										<a href="/players/page-3">3</a>
									</li>
									<li class="disabled">
										<a class="disabled" href="#">...</a>
									</li>
									<li>
										<a href="/players/page-35">35</a>
									</li>
									<li>
										<a href="/players/page-36">36</a>
									</li>
									<li>
										<a href="/players/page-37">37</a>
									</li>
									<li>
										<a href="/players/page-2">→</a>
									</li>
								</ul>
							</div>
						</div>-->
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#search_form3').submit(function (e)
	{
		e.preventDefault();
		//e.stopPropagation();

		var loading_effect=document.getElementById('paginations');
		if(typeof(loading_effect) != 'undefined' && loading_effect != null)
		{
			$(loading_effect).addClass('loader');
		}

	    var searchfor3=document.getElementById('searchuser3').value;

	    $.ajax({
	    	url: "<?php echo this::$_PAGE_URL; ?>search_ajax_callback.php",
	        type: "post",
	        data:
	        {
	           'searchuser3': searchfor3
	        },
	        success: function (html)
	        {
				if(typeof(loading_effect) != 'undefined' && loading_effect != null)
				{
					$(loading_effect).removeClass('loader');//da plm
				}
				$('#search_returned_data3').html(html);
	        }
	    });

		var created_pagination=document.getElementById('pag_created');
		if(typeof(created_pagination) != 'undefined' && created_pagination != null)
		{
			$(created_pagination).html("<button type='button' class='btn btn-outline-success waves-effect waves-dark float-right' data-direct='<?php echo this::$_PAGE_URL; ?>owner' id='target'>back</button>");
		}

	    return false;
	});
</script>