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

	if(user::getUserData()->Admin < 4)
	{
		return user::redirect_to("");
	}

    if(isset($_POST['deleteannounce']))
    {
		$rmban = connect::$g_con->prepare('DELETE FROM `panel_news` WHERE `id` = ?');
		$rmban->execute(array($_POST['deleteannounce']));

		this::register_db_log(user::get(), "announce with id #".$_POST['deleteannounce']." was deleted by", user::GetIp());
		this::show_toastr('success', 'You deleted with success this announce.', '<b>Success</b>', 1);

        //return user::redirect_to("panel");
	}

    if(isset($_POST['deletefunction']))
    {
		$rmban = connect::$g_con->prepare('DELETE FROM `panel_groups` WHERE `groupID` = ?');
		$rmban->execute(array($_POST['deletefunction']));

		this::register_db_log(user::get(), "function with id #".$_POST['deletefunction']." was deleted by", user::GetIp());
		this::show_toastr('success', 'You deleted with success thid rank.', '<b>Success</b>', 1);

        //return user::redirect_to("panel");//pt local de pus cva detectare pt functie..get url cva pt cuv match
	}

	if(isset($_POST['set_top_server']))
	{
		$q = connect::$g_con->prepare("UPDATE `panel_settings` SET `ServersOfTheWeek`=? WHERE 1");
		$q->bindParam(1, $purifier->purify(this::xss_clean($_POST['tsetext'])));
		$q->execute();

    	this::register_db_log(user::get(), "servers top was edited by", user::GetIp());
		this::show_toastr('success', 'You edited with success servers top.', '<b>Success</b>', 1);

		//return user::redirect_to("panel");
	}

	if(isset($_POST['creazaanunt']))
	{
		$search_data = connect::$g_con->prepare("SELECT * FROM `panel_news` WHERE `title` = '".$_POST['atitle']."'");
		$search_data->execute();

		$find = $search_data->rowCount();
		if($find)
		{
			this::show_toastr('error', 'Error: There is already an annouce with this title.', 'Error', 1);

			//return user::redirect_to("panel");
		}
		else
		{
			$q = connect::$g_con->prepare("INSERT INTO `panel_news` (`text`, `date`, `by`, `title`) VALUES (?, ?, ?, ?);");
			$q->bindParam(1, $purifier->purify(this::xss_clean($_POST['atext'])));
			$q->bindParam(2, this::getDaT());
			$q->bindParam(3, user::getUserData()->Name);
			$q->bindParam(4, $purifier->purify(this::xss_clean($_POST['atitle'])));
			$q->execute();

        	this::register_db_log(user::get(), "a new announce with title: ".$_POST['atitle']." was created by", user::GetIp());
			this::show_toastr('success', 'You created with success this announce.', '<b>Success</b>', 1);

    		//return user::redirect_to("panel");
		}
	}

	if(isset($_POST['creazagrup']))
	{
		trim($_POST['gname']);
		$search_data = connect::$g_con->prepare("SELECT * FROM `panel_groups` WHERE `groupName` = ?");
		$search_data->execute(array($_POST['gname']));

		$find = $search_data->rowCount();
		if($find)
		{
			this::show_toastr('error', 'Error: There is already a group with this name.', 'Error', 1);

			//return user::redirect_to("panel");
		}
		else
		{
			trim($_POST['gflags']);
			$search_data = connect::$g_con->prepare("SELECT * FROM `panel_groups` WHERE `groupFlags` = ?");
			$search_data->execute(array($_POST['gflags']));

			$find = $search_data->rowCount();
			if($find)
			{
				this::show_toastr('error', 'Error: There is already a group with those flags.', 'Error', 1);

				//return user::redirect_to("panel");
			}
			else
			{
				trim($_POST['gpos']);
				$search_data = connect::$g_con->prepare("SELECT * FROM `panel_groups` WHERE `groupAdmin` = ?");
				$search_data->execute(array($_POST['gpos']));

				$find = $search_data->rowCount();
				if($find)
				{
					this::show_toastr('error', 'Error: There is already a group with this position.', 'Error', 1);

					//return user::redirect_to("panel");
				}
				else
				{
					trim($_POST['gcolor']);
					trim($_POST['gicon']);
					trim($_POST['gtc']);
					trim($_POST['gtf']);
					$q = connect::$g_con->prepare("INSERT INTO `panel_groups` (`groupAdmin`, `groupName`, `groupColor`, `groupFlags`, `funcIcon`, `funcColor`, `funcFontFamily`) VALUES (?, ?, ?, ?, ?, ?, ?);");
					$q->bindParam(1, $purifier->purify(this::xss_clean($_POST['gpos'])));
					$q->bindParam(2, $purifier->purify(this::xss_clean($_POST['gname'])));
					$q->bindParam(3, $purifier->purify(this::xss_clean($_POST['gcolor'])));
					$q->bindParam(4, $purifier->purify(this::xss_clean($_POST['gflags'])));
					$q->bindParam(5, $purifier->purify(this::xss_clean($_POST['gicon'])));
					$q->bindParam(6, $purifier->purify(this::xss_clean($_POST['gtc'])));
					$q->bindParam(7, $purifier->purify(this::xss_clean($_POST['gtf'])));
					$q->execute();

		        	this::register_db_log(user::get(), "a new group (".$_POST['gname'].", ".$_POST['gpos'].", ".$_POST['gcolor'].", ".$_POST['gflags'].", ".$_POST['gicon'].", ".$_POST['gtc'].", ".$_POST['gtf'].") was created by", user::GetIp());
					this::show_toastr('success', 'You created with success this rank.', '<b>Success</b>', 1);

		    		//return user::redirect_to("panel");
			    }
	    	}
        }
	}

	if(isset($_POST['setgroup']))
	{
		trim($_POST['gepos']);
		trim($_POST['gename']);
		trim($_POST['gecolor']);
		trim($_POST['geflags']);
		trim($_POST['geicon']);
		trim($_POST['getc']);
		trim($_POST['getf']);
		$q = connect::$g_con->prepare('UPDATE `panel_groups` SET `groupAdmin` = ?, `groupName` = ?, `groupColor` = ?, `groupFlags` = ?, `funcIcon` = ?, `funcColor` = ?, `funcFontFamily` = ? WHERE `groupID` = ?');
		$q->execute(array($purifier->purify(this::xss_clean($_POST['gepos'])), $purifier->purify(this::xss_clean($_POST['gename'])), $purifier->purify(this::xss_clean($_POST['gecolor'])), $purifier->purify(this::xss_clean($_POST['geflags'])), $purifier->purify(this::xss_clean($_POST['geicon'])), $purifier->purify(this::xss_clean($_POST['getc'])), $purifier->purify(this::xss_clean($_POST['getf'])), $_POST['setgroup']));

		this::register_db_log(user::get(), "group with id #(".$_POST['setgroup']."): (".$_POST['gename'].", ".$_POST['gepos'].", ".$_POST['gecolor'].", ".$_POST['geflags'].", ".$_POST['geicon'].", ".$_POST['getc'].", ".$_POST['getf'].") was edited by", user::GetIp());
		this::show_toastr('success', 'You edited with success this rank.', '<b>Success</b>', 1);

		//return user::redirect_to("panel");
	}

	if(isset($_POST['setannounce']))
	{
		this::register_db_log(user::get(), "announce with id #".$_POST['setannounce'].": old title - ".this::getSpec('panel_news', 'title', 'id', $_POST['setannounce'])." | new title - ".$_POST['aetitle']." was edited by", user::GetIp());

		$q = connect::$g_con->prepare('UPDATE `panel_news` SET `title` = ?, `text` = ?, `LastEdit_Date` = ?, `LastEdit_By_Name` = ? WHERE `id` = ?');
		$q->execute(array($purifier->purify(this::xss_clean($_POST['aetitle'])), $purifier->purify(this::xss_clean($_POST['aetext'])), this::getDaT(), user::getUserData()->name, $_POST['setannounce']));

		this::show_toastr('success', 'You edited with success this announce.', '<b>Success</b>', 1);

		//return user::redirect_to("panel");
	}

	if(isset($_POST['update_iplv']))
	{
		$q = connect::$g_con->prepare('UPDATE `panel_settings` SET `IPLoginVerify` = ? WHERE 1');
		$q->execute(array($_POST['update_iplv']));

		this::register_db_log(user::get(), "login with ip verification was ".($_POST['update_iplv']==1?'en':'dis')."abled by", user::GetIp());
		this::show_toastr('success', 'You '.($_POST['update_iplv']==1?'en':'dis').'abled login with ip verification', '<b>Success</b>', 1);

		//return user::redirect_to("panel");
	}

	if(isset($_POST['update_pm']))
	{
		$q = connect::$g_con->prepare('UPDATE `panel_settings` SET `Maintenance` = ? WHERE 1');
		$q->execute(array($_POST['update_pm']));

		this::register_db_log(user::get(), "site maintenance mode was ".($_POST['update_pm']==1?'en':'dis')."abled by", user::GetIp());
		this::show_toastr('success', 'You '.($_POST['update_pm']==1?'en':'dis').'abled site maintenance mode', '<b>Success</b>', 1);

		//return user::redirect_to("panel");
	}

	if(isset($_POST['lockann']))
	{
		$q = connect::$g_con->prepare('UPDATE `panel_news` SET `Status` = ? WHERE `id` = ?');
		$q->execute(array(0, $_POST['lockann']));

		this::register_db_log(user::get(), "announce with id #".$_POST['lockann']." was locked by", user::GetIp());
		this::show_toastr('success', 'You locked with success this announce', '<b>Success</b>', 1);

		//return user::redirect_to("panel");
	}

	if(isset($_POST['unlockann']))
	{
		$q = connect::$g_con->prepare('UPDATE `panel_news` SET `Status` = ? WHERE `id` = ?');
		$q->execute(array(1, $_POST['unlockann']));

		this::register_db_log(user::get(), "announce with id #".$_POST['unlockann']." was unlocked by", user::GetIp());
		this::show_toastr('success', 'You unlocked with success this announce', '<b>Success</b>', 1);

		//return user::redirect_to("panel");
	}
?>

<div id="creategroup" class="modal fade" role="dialog" aria-labelledby="creategroupLabel" aria-hidden="true"> <!--tabindex="-1"-->
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="creategroupLabel">Create Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Group Name</b>
						<input type="text" class="form-control" name="gname" minlength="2" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Group Power</b>
						<input type="number" class="form-control" name="gpos" minlength="1" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Group Color</b>
						<input type="text" class="form-control" name="gcolor" required minlength="2" id="cp1" required />
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Group Flags</b>
						<input type="text" class="form-control" name="gflags" minlength="1" maxlength="35" pattern="[a-z]" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Group Icon</b>
						<input type="text" name="gicon" class="form-control" id="faip" data-iconpicker-input="input#faip" minlength="10" pattern="^[- a-z]+$" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Group Text color</b>
						<input type="text" id="cp4" name="gtc" class="form-control" minlength="3" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b id="sample1">Group Text family</b>
						<br/>
						<input type="text" name="gtf" class="form-control" id="font1" minlength="4"> <!--required-->
					</div>
					<br>
					<div align="center">
						<button type="submit" name="creazagrup" class="btn btn-success waves-effect waves-light">
			                <span class="btn-label"><i class="fa fa-check"></i></span> confirm group
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
<div id="createannounce" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="createannounceLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createannounceLabel">Create announce</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Announce title</b>
						<input type="text" class="form-control" name="atitle" minlength="2" required>
					</div>
					<div class="form-group form-material floating" data-plugin="formMaterial">
						<b>Announce text</b>
						<!--<input type="text" class="form-control" name="atext" required minlength="5" required />
						<textarea name="editor1" required></textarea>-->
						<textarea class="form-control" id='news_text' name='atext' required></textarea>
						<script type="text/javascript">
							CKEDITOR.replace( 'news_text',{
								removePlugins: 'exportpdf'
							});
						</script>
					</div>
					<br>
					<div align="center">
						<button type="submit" name="creazaanunt" class="btn btn-success waves-effect waves-light">
			                <span class="btn-label"><i class="fa fa-check"></i></span> create announce
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
<div id="deletegroup" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deletegroupLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletegroupLabel">Action confirmation</h5>
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
                        <button type="submit" name="deletefunction" class="btn btn-success btn-block">Yes, delete!</button>
                    </div>
                </form>
            </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
        </div>
    </div>
</div>
<div id="edit_iplv" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="edit_iplvLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="edit_iplvLabel">Login check by ip action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div align="center">
						<button type="submit" name="update_iplv" value="<?php echo !this::getSpec('panel_settings', 'IPLoginVerify', 'ID', 1)?1:0; ?>" class="btn btn-<?php echo !this::getSpec('panel_settings', 'IPLoginVerify', 'ID', 1)?'success':'danger'; ?> btn-block">
						<?php
							echo (!this::getSpec('panel_settings', 'IPLoginVerify', 'ID', 1)?'en':'dis')."able";
						?>
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
<div id="edit_pm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="edit_pmLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="edit_pmLabel">Manage site maintenance mode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<form method="post">
					<div align="center">
						<button type="submit" name="update_pm" value="<?php echo !this::getSpec('panel_settings', 'Maintenance', 'ID', 1)?1:0; ?>" class="btn btn-<?php echo !this::getSpec('panel_settings', 'Maintenance', 'ID', 1)?'success':'danger'; ?> btn-block">
						<?php
							echo (!this::getSpec('panel_settings', 'Maintenance', 'ID', 1)?'en':'dis')."able";
						?>
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
<div id="top_servers" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="top_serversLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="top_serversLabel">Top</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<div class="tab-pane active" id="editann" role="tabpanel">
					<div class="form-group form-material floating" data-plugin="formMaterial">
					<?php
						echo this::getSpec('panel_settings', 'ServersOfTheWeek', 'ID', 1);
					?>
					</div>
					<br>
					<div align="center">
						<button type="button" data-dismiss="modal" onclick="$('#edit_top_server').modal();" class="btn btn-success waves-effect waves-light">
							<span class="btn-label"><i class="fa fa-edit"></i></span> edit top
        				</button>
        			</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div id="edit_top_server" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="edit_top_serverLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="edit_top_serverLabel">Edit top</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">X</span>
                </button>
			</div>
			<div class="modal-body">
				<div class="tab-pane active" id="editann" role="tabpanel">
					<form method="post">
						<div class="form-group form-material floating" data-plugin="formMaterial">
							<textarea class="form-control" id='ts_edit' name='tsetext' required>
							<?php
								echo this::getSpec('panel_settings', 'ServersOfTheWeek', 'ID', 1);
							?>
							</textarea>
							<script type="text/javascript">
								CKEDITOR.replace( 'ts_edit',{
									removePlugins: 'exportpdf'
								});
							</script>
						</div>
						<br>
						<div align="center">
							<button type="submit" name="set_top_server" class="btn btn-success waves-effect waves-light">
								<span class="btn-label"><i class="fa fa-check"></i></span> edit top
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

<div class="mainArea-content">
	<div class="container">
		<div class="indexRow row" id="index-ongoing-matches">
			<div class="index-title">
				<h1>Panel settings</h1>
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
												<th class="score-tab-text">ID</th>
												<th class="score-tab-text">Login with IP verification</th>
												<th class="score-tab-text">Maintenance</th>
												<th class="score-tab-text">Top</th>
											</tr>
										</thead>
										<tbody>
										<?php
											$ps = connect::$g_con->prepare("SELECT * FROM `panel_settings` ORDER BY ID DESC");
											$ps->execute();
											while($panel_setting = $ps->fetch(PDO::FETCH_OBJ))
											{
										?>
												<tr>
													<td class="score-tab-text">
													<?php
														echo $panel_setting->ID;
													?>
													</td>

													<td class="score-tab-text">
														<button type="button" class="btn btn-<?php echo $panel_setting->IPLoginVerify==1?'success':'danger'; ?> btn-sm" data-toggle="modal" data-target="#edit_iplv">
															<i class="fa-solid fa-bed"></i>
															<?php
																echo $panel_setting->IPLoginVerify;
															?>
														</button>
													</td>

													<td class="score-tab-text">
														<button type="button" class="btn btn-<?php echo $panel_setting->Maintenance==1?'success':'danger'; ?> btn-sm" data-toggle="modal" data-target="#edit_pm">
															<i class="fa-solid fa-person-digging"></i>
															<?php
																echo $panel_setting->Maintenance;
															?>
														</button>
													</td>

													<td class="score-tab-text">
														<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#top_servers">
															<i class="fa-solid fa-ranking-star"></i> Show me
														</button>
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
			</div>
		</div>
		<div class="indexRow row" id="index-ongoing-matches">
			<div class="index-title">
				<h1>Announces</h1>
				<button type="button" class="btn btn-sm btn-blue waves-effect waves-light float-right" data-toggle="modal" data-target="#createannounce">
					<font color="white">create announce</font>
				</button>
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
												<th class="score-tab-text">ID</th>
												<th class="score-tab-text">Title</th>
												<th class="score-tab-text">Created on</th>
												<th class="score-tab-text">By</th>
												<th class="score-tab-text">Last edit on</th>
												<th class="score-tab-text">Last edit by</th>
												<th class="score-tab-text">Status</th>
												<th class="score-tab-text">Actions</th>
											</tr>
										</thead>
										<tbody>
										<?php
											$ann = connect::$g_con->prepare("SELECT * FROM `panel_news` ORDER BY id DESC");
											$ann->execute();
											while($get_ann = $ann->fetch(PDO::FETCH_OBJ))
											{
										?>
												<tr>
													<td class="score-tab-text">
													<?php
														echo $get_ann->id;
													?>
													</td>

													<td class="score-tab-text">
														<a href="#ann_<?php echo $get_ann->id; ?>" data-toggle="modal" data-toggle2="tooltip" data-placement="top" title="Click to view announce '<?php echo $get_ann->title; ?>'">
														<?php
															echo $get_ann->title;
														?>
														</a>
													</td>

													<td class="score-tab-text">
													<?php
														echo $get_ann->date;
													?>
													</td>

													<td class="score-tab-text">
														<a href="<?php echo user::MakeProfileUrl(this::getSpec('admins', 'id', 'name', $get_ann->by)); ?>" target="_blank">
														<?php
															echo $get_ann->by;
														?>
														</a>
													</td>

													<td class="score-tab-text">
													<?php
														echo $get_ann->LastEdit_Date;
													?>
													</td>

													<td class="score-tab-text">
														<a href="<?php echo user::MakeProfileUrl(this::getSpec('admins', 'id', 'name', $get_ann->LastEdit_By_Name)); ?>" target="_blank">
														<?php
															echo $get_ann->LastEdit_By_Name;
														?>
														</a>
													</td>

													<td class="score-tab-text">
													<?php
														echo '<i class="fa-solid fa-'.($get_ann->Status==1?"un":"").'lock"></i>';
													?>
													</td>

													<td class="score-tab-text">
														<form method="post">
															<?php
															if(user::getUserData()->Boss >= 1)
															{
																if($get_ann->Status==1)
																{
																?>
																	<button type="submit" class="btn btn-danger btn-sm" value="<?php echo $get_ann->id; ?>" name="lockann" data-toggle="tooltip" data-placement="top" title="Lock announce '<?php echo $get_ann->title; ?>'">
																		<i class="fa-solid fa-lock"></i>
																	</button>
																<?php
																}
																if($get_ann->Status==0)
																{
																?>
																	<button type="submit" class="btn btn-success btn-sm" value="<?php echo $get_ann->id; ?>" name="unlockann" data-toggle="tooltip" data-placement="top" title="Unlock announce '<?php echo $get_ann->title; ?>'">
																		<i class="fa-solid fa-unlock"></i>
																	</button>
																<?php
																}
															}
															?>
															<button type="button" class="btn btn-<?php echo ($get_ann->Status==1)?'success':'danger'; ?> btn-sm" data-toggle="modal" data-target="#editann<?php echo $get_ann->id; ?>" data-toggle2="tooltip" data-placement="top" title="Edit announce '<?php echo $get_ann->title; ?>'">
																<i class="fa fa-edit"></i>
															</button>
															<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteann<?php echo $get_ann->id; ?>" data-toggle2="tooltip" data-placement="top" title="Delete announce '<?php echo $get_ann->title; ?>'">
																<i class="fa-solid fa-file-circle-xmark"></i>
															</button>
														</form>
													</td>
												</tr>

												<div id="deleteann<?php echo $get_ann->id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deleteannLabel<?php echo $get_ann->id; ?>" aria-hidden="true">
												    <div class="modal-dialog" role="document">
												        <div class="modal-content">
												            <div class="modal-header">
												                <h5 class="modal-title" id="deleteannLabel<?php echo $get_ann->id; ?>">Action confirmation</h5>
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
												                        <button type="submit" value="<?php echo $get_ann->id; ?>" name="deleteannounce" class="btn btn-success btn-block">Yes, delete!</button>
												                    </div>
												                </form>
												            </div>
															<div class="modal-footer">
																<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
															</div>
												        </div>
												    </div>
												</div>

												<div id="editann<?php echo $get_ann->id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editann<?php echo $get_ann->id; ?>Label" aria-hidden="true">
													<div class="modal-dialog modal-lg" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="editann<?php echo $get_ann->id; ?>Label">
																	Edit announce:
																	<?php
																		echo $get_ann->title;
																	?>
																</h5>
												                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
												                	<span aria-hidden="true">X</span>
												                </button>
															</div>
															<div class="modal-body">
																<div class="tab-pane active" id="editann" role="tabpanel">
																	<?php
																	if($get_ann->Status==1)
																	{
																	?>
																		<form method="post">
																			<div class="form-group form-material floating" data-plugin="formMaterial">
																				<b>Announce title</b>
																				<input type="text" name="aetitle" class="form-control" value="<?php echo $get_ann->title; ?>" minlength="2" required>
																			</div>
																			<div class="form-group form-material floating" data-plugin="formMaterial">
																				<b>Announce text</b>
																				<!--<input type="text" name="aetext" class="form-control" value="<?php echo $get_ann->text; ?>" minlength="2" required>-->
																				<textarea class="form-control" id='news_text_<?php echo $get_ann->id; ?>' name='aetext' required>
																				<?php
																					echo $get_ann->text;
																				?>
																				</textarea>
																				<script type="text/javascript">
																					CKEDITOR.replace( 'news_text_<?php echo $get_ann->id; ?>',{
																						removePlugins: 'exportpdf'
																					});
																				</script>
																			</div>
																			<br>
																			<div align="center">
																				<button type="submit" name="setannounce" value="<?php echo $get_ann->id; ?>" class="btn btn-success waves-effect waves-light">
																					<span class="btn-label"><i class="fa fa-check"></i></span> edit announce
													            				</button>
														        			</div>
																		</form>
																	<?php
																	}
																	else
																	{
																	?>
																		This announce is locked! So you can't edit.
																	<?php
																	}
																	?>
																</div>
															</div>
															<div class="modal-footer">
																<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
															</div>
														</div>
													</div>
												</div>
												<div id="ann_<?php echo $get_ann->id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ann_<?php echo $get_ann->id; ?>Label" aria-hidden="true">
													<div class="modal-dialog modal-lg" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="ann_<?php echo $get_ann->id; ?>Label">
																	Announce:
																	<?php
																		echo $get_ann->title;
																	?>
																</h5>
												                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
												                	<span aria-hidden="true">X</span>
												                </button>
															</div>
															<div class="modal-body">
																<div class="tab-pane active" id="editann" role="tabpanel">
																<?php
																	echo $get_ann->text;
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
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="indexRow row" id="index-ongoing-matches">
			<div class="index-title">
				<h1>Groups</h1>
				<button type="button" class="btn btn-sm btn-blue waves-effect waves-light float-right" data-toggle="modal" data-target="#creategroup">
					<font color="white">create group</font>
				</button>
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
												<th class="score-tab-text">ID</th>
												<th class="score-tab-text">Power</th>
												<th class="score-tab-text">Name</th>
												<th class="score-tab-text">Color</th>
												<th class="score-tab-text">Flags</th>
												<th class="score-tab-text">Icon</th>
												<th class="score-tab-text">Text color</th>
												<th class="score-tab-text">Text family</th>
												<th class="score-tab-text">Edit</th>
												<th class="score-tab-text">Delete</th>
											</tr>
										</thead>
										<tbody>
										<?php
											$groups = connect::$g_con->prepare("SELECT * FROM `panel_groups` ORDER BY groupAdmin DESC");
											$groups->execute();
											while($function = $groups->fetch(PDO::FETCH_OBJ))
											{
										?>
												<tr>
													<td class="score-tab-text">
													<?php
														echo $function->groupID;
													?>
													</td>
													<td class="score-tab-text">
														<span class="badge" style="background-color: <?php echo $function->groupColor; ?>;">
															<strong>
															<?php
																echo $function->groupAdmin;
															?>
															</strong>
														</span>
													</td>
													<td class="score-tab-text">
														<span class="badge" style="background-color: <?php echo $function->groupColor; ?>;">
															<strong>
															<?php
																echo $function->groupName;
															?>
															</strong>
														</span>
													</td>
													<td class="score-tab-text">
														<span class="badge" style="background-color: <?php echo $function->groupColor; ?>;">
															<strong>
															<?php
																echo $function->groupColor;
															?>
															</strong>
														</span>
													</td>
													<td class="score-tab-text">
														<span class="badge" style="background-color: <?php echo $function->groupColor; ?>;">
															<strong>
															<?php
																echo $function->groupFlags;
															?>
															</strong>
														</span>
													</td>
													<td class="score-tab-text">
														<span class="badge" style="background-color: <?php echo $function->groupColor; ?>;">
															<strong>
																<i class="<?php echo $function->funcIcon; ?>"></i>
																<?php
																	echo $function->funcIcon;
																?>
															</strong>
														</span>
													</td>
													<td class="score-tab-text">
														<span class="badge" style="background-color: <?php echo $function->groupColor; ?>;">
															<strong>
																<font color="<?php echo $function->funcColor; ?>">
																<?php
																	echo $function->funcColor;
																?>
																</font>
															</strong>
														</span>
													</td>
													<td class="score-tab-text">
														<span class="badge" style="background-color: <?php echo $function->groupColor; ?>;">
															<strong style="font-family: <?php echo $function->funcFontFamily; ?>;">
															<?php
																echo $function->funcFontFamily;
															?>
															</strong>
														</span>
													</td>
													<td class="score-tab-text">
														<button type="button" class="btn btn-success btn-xs" value="<?php echo $function->groupID; ?>" data-toggle="modal" data-target="#editgroup<?php echo $function->groupID; ?>">
															<i class="fa fa-edit"></i>
														</button>
													</td>
													<td class="score-tab-text">
														<form>
															<button type="button" class='btn btn-danger btn-circle' value="<?php echo $function->groupID; ?>" data-toggle="modal" data-target="#deletegroup">
																<i class="fa-solid fa-trash"></i>
															</button>
														</form>
													</td>
												</tr>

												<div id="editgroup<?php echo $function->groupID; ?>" class="modal fade lev" role="dialog" aria-labelledby="editgroup<?php echo $function->groupID; ?>Label" aria-hidden="true"> <!--tabindex="-1"-->
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="editgroup<?php echo $function->groupID; ?>Label">Edit Group</h5>
												                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
												                	<span aria-hidden="true">X</span>
												                </button>
															</div>
															<div class="modal-body">
																<div class="tab-pane active" id="editgroup" role="tabpanel">
																	<form method="post">
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Group Name</b>
																			<input type="text" name="gename" class="form-control" value="<?php echo $function->groupName; ?>" minlength="2" required>
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Group Power</b>
																			<input type="number" name="gepos" class="form-control" value="<?php echo $function->groupAdmin; ?>" minlength="1" required>
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Group Color</b>
																			<input type="text" id="cp2" name="gecolor" class="form-control" value="<?php echo $function->groupColor; ?>" minlength="2" required>
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Group Flags</b>
																			<input type="text" name="geflags" class="form-control" value="<?php echo $function->groupFlags; ?>" minlength="1" maxlength="35" pattern="[a-z]" required>
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Group Icon</b>
																			<input type="text" name="geicon" class="form-control" value="<?php echo $function->funcIcon; ?>" id="faip2" data-iconpicker-input="input#faip2" minlength="10" pattern="^[- a-z]+$" required>
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b>Group Text color</b>
																			<input type="text" id="cp3" name="getc" class="form-control" value="<?php echo $function->funcColor; ?>" minlength="3" required>
																		</div>
																		<div class="form-group form-material floating" data-plugin="formMaterial">
																			<b class="sample2">Group Text family</b>
																			<br/>
																			<input type="text" name="getf" class="form-control font2" value="<?php echo $function->funcFontFamily; ?>" minlength="4"> <!--required-->
																		</div>
																		<br>
																		<div align="center">
																			<button type="submit" name="setgroup" value="<?php echo $function->groupID; ?>" class="btn btn-success waves-effect waves-light">
																				<span class="btn-label"><i class="fa fa-check"></i></span> edit group
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
										?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="indexRow row" id="index-ongoing-matches">
			<div class="index-title">
				<h1>
					Last
                    <?php
                        echo this::$_perPage." Panel log".(this::$_perPage==1?'':'s');
                    ?>
				</h1>
				<a href="<?php echo this::$_PAGE_URL; ?>logs">View all logs</a>
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
												<th class="score-tab-text">ID</th>
												<th class="score-tab-text">Text</th>
												<th class="score-tab-text">Tracked by</th>
												<th class="score-tab-text">Tracked ip</th>
												<th class="score-tab-text">Date</th>
											</tr>
										</thead>
										<tbody>
										<?php
											$pl = connect::$g_con->prepare("SELECT * FROM `panel_logs` ORDER BY logID DESC".this::limit());
											$pl->execute();
											while($panel_logs = $pl->fetch(PDO::FETCH_OBJ))
											{
										?>
												<tr>
													<td class="score-tab-text">
													<?php
														echo $panel_logs->logID;
													?>
													</td>

													<td class="score-tab-text">
													<?php
														echo $panel_logs->logText;
													?>
													</td>

													<td class="score-tab-text">
														<a href="<?php echo user::MakeProfileUrl(this::getSpec('admins', 'id', 'id', $panel_logs->logById)); ?>" target="_blank">
														<?php
															echo this::getSpec('admins', 'name', 'id', $panel_logs->logById);
														?>
														</a>
													</td>

													<td class="score-tab-text">
													<?php 
														echo $panel_logs->logIP;
													?>
													</td>

													<td class="score-tab-text">
													<?php 
														echo $panel_logs->logDate;
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
				</div>
			</div>
		</div>
	</div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-fontpicker/1.4.4/jquery.fontpicker.min.js" integrity="sha512-78prOPndjZEehStvp969ihV+JiHJd3uJYFnqjJlif3Jjg749jyGdnHX/FMzIu6i5HfPbFfpAN3hvkFAnRGQAug==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js" integrity="sha512-94dgCw8xWrVcgkmOc2fwKjO4dqy/X3q7IjFru6MHJKeaAzCvhkVtOS6S+co+RbcZvvPBngLzuVMApmxkuWZGwQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="<?php echo this::$_PAGE_URL; ?>resources/js/iconpicker-1.5.0.js"></script>

<script type="text/javascript">
	function applyFont(element, fontSpec) {
		if (!fontSpec) {
			// Font was cleared
			console.log('You cleared font');

			$(element).css({
				fontFamily: 'inherit',
				fontWeight: 'normal',
				fontStyle: 'normal'
			});
			return;
		}

		console.log('You selected font: ' + fontSpec);

		// Split font into family and weight/style
		var tmp = fontSpec.split(':'),
			family = tmp[0],
			variant = tmp[1] || '400',
			weight = parseInt(variant,10),
			italic = /i$/.test(variant);

		// Apply selected font to element
		$(element).css({
			fontFamily: "'" + family + "'",
			fontWeight: weight,
			fontStyle: italic ? 'italic' : 'normal'
		});
	}

	//hmmm
	$(function () {
		$('#cp1, #cp2, #cp3, #cp4').colorpicker({
			fallbackColor: 'red'
		});

		IconPicker.Init({
			jsonUrl: '<?php echo this::$_PAGE_URL.'resources/json/iconpicker-1.5.0.json'; ?>'
		});
		IconPicker.Run('#faip, #faip2');
		$('#creategroup, .lev').on('hidden.bs.modal', function () {
		  var docBody = document.body;
		  docBody.style.overflow = 'auto';
		});
		$('#creategroup, .lev').on('shown.bs.modal', function () {
		  var docBody = document.body;
		  docBody.style.overflow = 'hidden';
		  docBody.style.overflowX = 'hidden';
		});

		$('#font1, .font2').fontpicker().on('change', function() {
		   applyFont('#sample1, .sample2', this.value);
		});
	});
</script>